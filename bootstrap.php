<?php

\spl_autoload_register(function($className) {
	$rootDir = __DIR__ . DIRECTORY_SEPARATOR . 'src';
	$namespace = 'FMADM\\Topsis';

	$className = strtr(
		str_replace($namespace, $rootDir, $className),
		'\\',
		DIRECTORY_SEPARATOR
	) . '.php';

	if ($file = stream_resolve_include_path($className)) {
		require_once $file;
	}
});