<?php

namespace Message\Mothership\CMS\Field\Type;

use Message\Mothership\CMS\Field\Field;

use Message\Mothership\FileManager\File\Type as FileType;

use Message\Cog\Form\Handler;
use Message\Cog\Filesystem;
use Message\Cog\Service\ContainerInterface;
use Message\Cog\Service\ContainerAwareInterface;

/**
 * A field for a file in the file manager database.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class File extends Field implements ContainerAwareInterface
{
	protected $_services;

	protected $_allowedTypes;

	public function __toString()
	{
		return $this->getValue()->getPublicUrl();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->_services = $container;
	}

	public function getFormField(Handler $form)
	{
		$form->add($this->getName(), 'file', $this->getLabel(), array(
			'attr'       => array('data-help-key' => $this->_getHelpKeys()),
			'data_class' => 'Message\\Mothership\\FileManager\\File\\File',
		));
	}

	public function setAllowedTypes($types)
	{
		if (!is_array($types)) {
			$types = array($types);
		}

		$this->_allowedTypes = $types;

		return $this;
	}

	public function getValue()
	{
		return $this->_services['file_manager.file.loader']->getByID((int) $this->_value);
	}
}