<?php

class I18n
{
	private $language = 'en';
	private $cache = array();

	public function language($name)
	{
		$this->language = strtolower($name);
	}

	public function getDict()
	{
		if (!isset($this->cache[$this->language])) {
			$dict = array();
			if (file_exists('../dict/' . $this->language . '.inc')) {
				include_once '../dict/' . $this->language . '.inc';
			}
			$this->cache[$this->language] = $dict;
		}
		return $this->cache[$this->language];
	}

	public function text($text)
	{
		if (!isset($this->cache[$this->language])) {
			$this->getDict();
		}

		if (isset($this->cache[$this->language][$text])) {
			return $this->cache[$this->language][$text];
		} else {
			return $text;
		}
	}

	function t($params, $content, $smarty, &$repeat, $template)
	{
		if (isset($content)) {
			return $this->text(trim($content));
		}
	}

}