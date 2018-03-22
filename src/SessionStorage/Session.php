<?php

namespace JedenWeb\SessionStorage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="timestamp_idx", columns={"timestamp"})})
 * @author Pavel Jurásek
 */
class Session
{

	/**
	 * @ORM\Column(type="string", length=64)
	 * @ORM\Id()
	 * @var string
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	protected $timestamp;

	/**
	 * @ORM\Column(type="text", nullable=TRUE)
	 * @var string
	 */
	protected $data;

}
