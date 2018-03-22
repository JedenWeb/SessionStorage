<?php

namespace JedenWeb\SessionStorage;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DatabaseStorage implements \SessionHandlerInterface
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EntityRepository
	 */
	private $sessions;


	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->sessions = $entityManager->getRepository(Session::class);
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
