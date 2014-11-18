<?php

namespace Message\Mothership\CMS\Blog;

use Message\Cog\ValueObject\Authorship;

/**
 * Class Comment
 * @package Message\Mothership\CMS\Blog
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Comment
{
	/**
	 * @var int
	 */
	private $_id;

	/**
	 * @var int
	 */
	private $_pageID;

	/**
	 * @var int
	 */
	private $_userID;

	/**
	 * @var string
	 */
	private $_name;

	/**
	 * @var string
	 */
	private $_email;

	/**
	 * @var string
	 */
	private $_content;

	/**
	 * @var string
	 */
	private $_ipAddress;

	/**
	 * @var string
	 */
	private $_status;

	/**
	 * @var \Message\Cog\ValueObject\Authorship
	 */
	private $_authorship;

	public function __construct()
	{
		$this->_authorship = new Authorship;
	}

	/**
	 * @param $id
	 * @throws \InvalidArgumentException
	 */
	public function setID($id)
	{
		if (!is_numeric($id)) {
			throw new \InvalidArgumentException('ID must be numeric, non-numeric ' . gettype($id) . ' given');
		}

		$this->_id = (int) $id;
	}

	/**
	 * @return int
	 */
	public function getID()
	{
		return $this->_id;
	}

	/**
	 * @param mixed $content
	 * @throws \InvalidArgumentException
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setContent($content)
	{
		if (!is_string($content)) {
			throw new \InvalidArgumentException('Content must be a string, ' . gettype($content) . ' given');
		}

		$this->_content = $content;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getContent()
	{
		return $this->_content;
	}

	/**
	 * @param mixed $email
	 * @throws \InvalidArgumentException
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setEmail($email)
	{
		if (!is_string($email)) {
			throw new \InvalidArgumentException('Email address must be a string, ' . gettype($email) . ' given');
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new \InvalidArgumentException('`' . $email . '`is not a valid email address!');
		}

		$this->_email = $email;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->_email;
	}

	/**
	 * @param mixed $ipAddress
	 * @throws \InvalidArgumentException
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setIpAddress($ipAddress)
	{
		if (!is_string($ipAddress)) {
			throw new \InvalidArgumentException('IP address must be a string, ' . gettype($ipAddress) . ' given');
		}
		if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
			throw new \InvalidArgumentException('`' . $ipAddress . '` is not a valid IP address!');
		}

		$this->_ipAddress = $ipAddress;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIpAddress()
	{
		return $this->_ipAddress;
	}

	/**
	 * @param mixed $name
	 * @throws \InvalidArgumentException
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setName($name)
	{
		if (!is_string($name)) {
			throw new \InvalidArgumentException('Name must be a string, ' . gettype($ipAddress) . ' given');
		}

		$this->_name = $name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param mixed $pageID
	 * @throws \InvalidArgumentException
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setPageID($pageID)
	{
		if (!is_numeric($pageID)) {
			throw new \InvalidArgumentException('Page ID must be a numeric, non-numeric' . gettype($pageID) . ' given');
		}

		$this->_pageID = (int) $pageID;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPageID()
	{
		return $this->_pageID;
	}

	/**
	 * @param mixed $status
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setStatus($status)
	{
		$this->_status = $status;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->_status;
	}

	/**
	 * @param mixed $userID
	 * @throws \InvalidArgumentException
	 *
	 * @return Comment         return $this for chainability
	 */
	public function setUserID($userID)
	{
		if (null !== $userID && !is_numeric($userID)) {
			throw new \InvalidArgumentException('User ID must be numeric, non-numeric ' . gettype($userID) . ' given');
		}

		$this->_userID = (int) $userID;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUserID()
	{
		return $this->_userID;
	}

}