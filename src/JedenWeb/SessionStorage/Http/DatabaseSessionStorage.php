<?php

namespace JedenWeb\SessionStorage\Http;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DatabaseSessionStorage extends Nette\Object implements Nette\Http\ISessionStorage
{

	/** @var Nette\Database\SelectionFactory */
	private $selectionFactory;


	/**
	 * @param Nette\Database\SelectionFactory
	 */
	public function __construct(Nette\Database\SelectionFactory $selectionFactory)
	{
		$this->selectionFactory = $selectionFactory;
	}
	
	
	/**
	 * @internal  Create table in database
	 * Create database table.
	 */
	public function install()
	{
		$this->selectionFactory->getConnection()->query("
			CREATE TABLE IF NOT EXISTS `session` (
				`id` varchar(64) NOT NULL,
				`timestamp` int(11) NOT NULL,
				`data` longtext NOT NULL,
				PRIMARY KEY (`id`),
				KEY `timestamp` (`timestamp`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}


	/**
	 * @param string
	 * @param string
	 */
	public function open($savePath, $sessionName)
	{
		$id = session_id();
		$connection = $this->selectionFactory->getConnection();

		while (!$connection->query("SELECT IS_FREE_LOCK('session_$id') AS free")->fetch()->free);

		$connection->query("SELECT GET_LOCK('session_$id', 1)");

		return TRUE;
	}

	
	/**
	 * @param int
	 * @return string
	 */
	public function read($id)
	{		
		if ($data = $this->selectionFactory->table('session')->get($id)) {
			$data = $data->data;
		} else {
			$data = "";
		}

		return $data;
	}

	
	/**
	 * @param int
	 * @param string
	 * @throws \Nette\InvalidStateException
	 */
	public function write($id, $data = "")
	{
		if ($row = $this->selectionFactory->table('session')->get($id)) {
			$row->update(array(
				'timestamp' => time(),
				'data' => $data,
			));
		} else {
			$this->selectionFactory->table('session')->insert(array(
				'id' => $id,
				'timestamp' => time(),
				'data' => $data,
			));
		}

		return TRUE;
	}

	
	/**
	 * @param int
	 * @return boolean
	 */
	public function clean($max)
	{		
		$this->selectionFactory->table('session')->where("timestamp < ?", ( time() - $max ))->delete();

		return TRUE;
	}
	

	/**
	 * @return boolean
	 */
	public function close()
	{
		$id = session_id();

		$this->selectionFactory->getConnection()->query("SELECT RELEASE_LOCK('session_$id')");

		return TRUE;
	}
	
	
	/**
	 * @param mixed
	 * @return boolean
	 */
	public function remove($id)
	{
		if ($row = $this->selectionFactory->table('session')->get($id)) {
			$row->delete();
		}

		return TRUE;
	}
	
	
	/**
	 * @return boolean
	 */
	public function destroy()
	{
		return (bool) $this->selectionFactory->table('session')->delete();
	}


	/**
	 * Called when instance is released from memory
	 */
	public function __destruct()
	{
		session_write_close();
	}

}
