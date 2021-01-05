<?php

namespace Shemi\Core\View;

class Environment
{
	private $templateDir;

	private $extension;

	private $variables;

	public function __construct($templateDir, $extension = '')
	{
		$this->templateDir = $templateDir;
		$this->extension = $extension;
		$this->layout = null;
		$this->variables = [];
	}

	/**
	 * Render a template.
	 * @param $path
	 * @param array $variables
	 * @return string
	 */
	public function render($path, array $variables = [])
	{
		$template = Template::withEnvironment($this, $path);

		return $template->render($variables);
	}

	/**
	 * Creates an empty template in this environment
	 * @param null $path
	 *
	 * @return Template
	 */
	public function template($path = null)
	{
		return Template::withEnvironment($this, $path);
	}

	/**
	 * Gets the path of the template in this environment
	 * @param string $template
	 *
	 * @return string
	 */
	public function getTemplatePath($template)
	{
		$template = str_replace('.', DIRECTORY_SEPARATOR, $template);

		return $this->getTemplateDir().DIRECTORY_SEPARATOR.$template.$this->getExtension();
	}

	/**
	 * Get the template directory
	 * @return string
	 */
	public function getTemplateDir()
	{
		return $this->templateDir;
	}

	/**
	 * Get the extension
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * Set the extension
	 * @param string $extension
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	/**
	 * Magic isset
	 * @param string $id
	 * @return boolean
	 */
	public function __isset($id)
	{
		return isset($this->variables[$id]);
	}

	/**
	 * Magic getter
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		return $this->variables[$key];
	}

	/**
	 * Magic setter
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$this->variables[$key] = $value;
	}

}