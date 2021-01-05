<?php

namespace Shemi\Core\View;

class Template implements \ArrayAccess
{

	protected $templatePath;

	protected $environment;

	protected $content;

	private $stack = [];

	protected $blocks = [];

	protected $extends = null;

	protected $_variables = [];

	public function __construct($path = null)
	{
		$this->templatePath = $path;
		$this->environment = null;

		$this->content = new Block();
	}

	/**
	 * Creates a template within an environment
	 * @param Environment $environment
	 * @param string $path
	 *
	 * @return Template
	 */
	public static function withEnvironment(Environment $environment, $path)
	{
		return (new static($path ? $environment->getTemplatePath($path) : null))
			->setEnvironment($environment);
	}

	/**
	 * Allows this template to extend another template.
	 * A template can only extend one other template at a time however
	 * you can extend a template extending another template etc.
	 *
	 * If a template extending another does not define a content block
	 * then the output of the extending template will become the content
	 * block of the extended template.
	 *
	 * @param string $path
	 */
	public function extend($path)
	{
		if ($path === null) {
			return;
		}

		else if ($this->environment !== null) {
			if ($this->templatePath === $this->environment->getTemplatePath($path)) {
				return;
			}

			$this->extends = Template::withEnvironment($this->environment, $path);
		}

		else if ($this->templatePath !== $path) {
			$this->extends = new Template($path);
		}
	}

	/**
	 * Indicates the start of a block.
	 * If a value for the block is defined, it does not need to be closed with endblock
	 * @param string $name name of the block
	 * @param string $value optional value for the block
	 *
	 * @throws \LogicException
	 */
	public function block($name = null, $value = null)
	{

		if ($value) {
			if(! $name) {
				throw new \LogicException(sprintf("You are assigning a value of %s to a block with no name!", $value));
			}

			$block = new Block($name);
			$block->setContent($value);
			$this->blocks[$name] = $block;

			return;
		}

		if (! empty($this->stack)) {
			$content = ob_get_contents();

			foreach ($this->stack as &$b) {
				$b->append($content);
			}
		}

		ob_start();

		$block = new Block($name);

		array_push($this->stack, $block);
	}

	/**
	 * Indicates the end of a block, and optionally accepts a filter to apply to the content.
	 * Returns the block as a string.
	 * @param \Closure $filter function to apply to block contents
	 * @return Block object
	 */
	public function endblock(\Closure $filter = null)
	{
		$content = ob_get_clean();

		//nested blocks
		foreach ($this->stack as &$b) {
			$b->append($content);
		}

		$block = array_pop($this->stack);

		if ($filter) {
			$block->setContent($filter($block->getContent()));
		}

		if ($name = $block->getName()) {
			$this->blocks[$block->getName()] = $block;
		}

		return $block;
	}

	/**
	 * Gets the blocks.
	 * @return array Block[]
	 */
	public function getBlocks()
	{
		if (! $this['content']) {
			$this['content'] = $this->content;
		}

		else {
			$this['content'] = $this['content'].$this->content;
		}

		return $this->blocks;
	}

	/**
	 * Sets the blocks.
	 * @param array $blocks
	 */
	public function setBlocks(array $blocks)
	{
		$this->blocks = $blocks;
	}

	/**
	 * Renders a template and returns it as a string.
	 * @param array $variables
	 * @return string
	 */
	public function render(array $variables = [])
	{
		$this->_variables = $variables;

		if ($this->templatePath) {
			$_file = $this->templatePath;

			if (! file_exists($_file)) {
				throw new \InvalidArgumentException(sprintf("Could not render.  The file %s could not be found", $_file));
			}

			extract($variables, EXTR_SKIP);

			ob_start();

			require($_file);

			$this->content->append(ob_get_clean());
		}

		//extending another template
		if ($this->extends) {
			$this->extends->setBlocks($this->getBlocks());

			return (string) $this->extends->render();
		}

		return (string) $this->content;

	}

	public function include($path, $variables = [])
	{
		$variables = array_merge(
			$this->_variables,
			$variables
		);

		echo static::withEnvironment($this->environment, $path)
			->render($variables);
	}

	/**
	 * Sets template environment
	 * @param Environment $environment
	 *
	 * @return Template
	 */
	public function setEnvironment(Environment $environment)
	{
		$this->environment = $environment;

		return $this;
	}

	/**
	 * Magic isset
	 * @param string $id
	 * @return boolean
	 */
	public function __isset($id)
	{
		return isset($this->environment->$id);
	}

	/**
	 * Magic getter
	 * @param string $name
	 * @return string
	 */
	public function __get($name)
	{
		return $this->environment->$name;
	}

	/**
	 * Magic setter
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$this->environment->$name = $value;
	}

	/**
	 * ArrayAccess offsetExists
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->blocks[$offset]);
	}

	/**
	 * ArrayAccess offsetGet
	 * @param string $offset
	 * @return boolean|Block
	 */
	public function offsetGet($offset)
	{
		if (isset($this->blocks[$offset])) {
			return $this->blocks[$offset];
		}

		return false;
	}

	/**
	 * ArrayAccess offsetSet
	 * @param string $offset
	 * @param string-castable $value
	 */
	public function offsetSet($offset, $value)
	{
		if (isset($this->blocks[$offset])) {
			$this->blocks[$offset]->setContent((string)$value);
		}

		else {
			$block = new Block($offset);
			$block->setContent((string)$value);
			$this->blocks[$offset] = $block;
		}
	}

	/**
	 * ArrayAccess offsetUnset
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->blocks[$offset]);
	}

}