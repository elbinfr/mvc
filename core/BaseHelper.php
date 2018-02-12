<?php

function url($url) 
{
	return '/' . APP_PATH . '/index.php' . $url;
}

function path($path)
{
	return '/' . APP_PATH . $path;
}
