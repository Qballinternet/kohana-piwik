<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// url to piwik installation
	'url' => 'http://piwik.example.com',
	'idSite' => 1,	// id of tracked site
	'token_auth' => '',	// auth token

	// Run through shell?  Possible values: php or curl. For curl the server
	// needs curl installed, for php make sure setting the binary_path(piwik.php)
	// These options make sure php will not wait for the request to complete.
	// WARNING: php mode does not support bulk processing
	'shell_mode'    => NULL,
	'binary_path'   => NULL,
);