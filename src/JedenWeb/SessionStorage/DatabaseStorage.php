<?php

namespace JedenWeb\SessionStorage;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DatabaseStorage extends Nette\Object implements Nette\Http\ISessionStorage
{

	/** @var Nette\Database\Context */
	private $context;


	public function __construct(Nette\Database\Context $context)
	{
		$this->context = $context;
	}


	/**
	 * @internal
	 * Create database table.
	 */
	public function install()
	{
		$this->context->getConnection()->query('
			CREATE TABLE IF NOT EXISTS `session` (
				`id` varchar(64) NOT NULL,
				`timestamp` int(11) NOT NULL,
				`data` longtext NOT NULL,
				PRIMARY KEY (`id`),
				KEY `timestamp` (`timestamp`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		');
	}


	/**
	 * @param string
	 * @param string
	 * @return boolean
	 */
	public function open($savePath, $sessionName)
	{
		$id = session_id();
		$connection = $this->context->getConnection();

		while (!$connection->query("SELECT IS_FREE_LOCK('session_$id') AS free")->fetch()->free);

		$connection->query("SELECT GET_LOCK('session_$id', 1)");

		return TRUE;
	}

	
	/**
	 * @param int
	 * @return string
	 */
	public function read($sessionId)
	{		
		if ($data = $this->context->table('session')->get($sessionId)) {
			$data = $data->data;
		} else {
			$data = '';
		}

		return $data;
	}


	/**
	 * @param int
	 * @param string
	 * @throws \Nette\InvalidStateException
	 * @return boolean
	 */
	public function write($sessionId, $data = '')
	{
		if ($row = $this->context->table('session')->get($sessionId)) {
			$row->update(array(
				'timestamp' => time(),
				'data' => $data,
			));
		} else {
			$this->context->table('session')->insert(array(
				'id' => $sessionId,
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
		return (bool) $this->context->table('session')
				->where('timestamp < ?', ( time() - $max ))
				->delete();
	}


	/**
	 * @return boolean
	 */
	public function close()
	{
		$id = session_id();

		$this->context->getConnection()->query("SELECT RELEASE_LOCK('session_$id')");

		return TRUE;
	}


	/**
	 * @param mixed
	 * @return boolean
	 */
	public function remove($id)
	{
		if ($row = $this->context->table('session')->get($id)) {
			$row->delete();
		}

		return TRUE;
	}


	/**
	 * @return boolean
	 */
	public function destroy($sessionId)
	{
		return (bool) $this->context->table('session')
				->where('id = ?', $sessionId)
				->delete();
	}


	/**
	 * Called when instance is released from memory
	 */
	public function __destruct()
	{
		session_write_close();
	}

}
