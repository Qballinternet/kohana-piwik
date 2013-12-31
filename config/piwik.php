<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// url to piwik installation
	'url' => 'http://piwik.example.com',
	'idSite' => 1,	// id of tracked site
	'token_auth' => '',	// auth token

	// Run through shell? Requires curl and requests will miss return data
	// This will prevent PHP waiting for completion
	'shell_exec' => FALSE,

);