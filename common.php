<?php

function isMobile()
{
	if (defined('DESKTOP')){
		return false;
	}

	if (defined('MOBILE')){
		return true;
	}

	if (preg_match('/(android|iphone|ipod|phone)/i', $_SERVER['HTTP_USER_AGENT']))
		return true;
	else
		return false;
}

function catch_handler($error_level, $error_message,
                      $error_file, $error_line, $error_context)
{
    echo '<pre>';
    echo json_encode(
        array(
            error_level => $error_level,
            error_message => $error_message,
            error_file => $error_file,
            error_line => $error_line,
            error_context => $error_context
        )
    );
    echo '</pre>';
}