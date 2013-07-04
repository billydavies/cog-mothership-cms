<?php

namespace Message\Mothership\CMS\Field\Type;

use Message\Mothership\CMS\Field\Field;
use Message\Cog\Form\Handler;

use Message\Cog\Service\ContainerInterface;
use Message\Cog\Service\ContainerAwareInterface;

/**
 * A field for text written in a rich text markup language.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class RichText extends Field implements ContainerAwareInterface
{

	protected $_engines = array(
		'markdown',
	);

	public $_engine = 'markdown';

	public function __toString()
	{
		if ('markdown' === $this->_engine) {
			return $this->_services['markdown.parser']->transformMarkdown($this->_value);
		}

		return parent::__toString();
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
		$form->add($this->getName(), 'textarea', $this->getLabel(), array(
			'attr' => array('data-help-key' => $this->_getHelpKeys()),
		));
	}

	/**
	 * Set the rendering engine to use.
	 *
	 * @param string $engine Identifier for the rendering engine
	 *
	 * @return RichText      Returns $this for chainability
	 *
	 * @throws \InvalidArgumentException If the engine is not recognised
	 */
	public function setEngine($engine)
	{
		$engine = strtolower($engine);

		if (!in_array($engine, $this->_engines)) {
			throw new \InvalidArgumentException(sprintf('Rich text engine `%s` does not exist.', $engine));
		}

		$this->_engine = $engine;

		return $this;
	}
}