<?php

function isMobile()
{
	if (defined('DESKTOP')) {
		return false;
	}

	if (defined('MOBILE')) {
		return true;
	}

	if (preg_match('/(android|iphone|ipod|phone)/i', $_SERVER['HTTP_USER_AGENT']))
		return true;
	else
		return false;
}

