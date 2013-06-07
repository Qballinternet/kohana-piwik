<?php
class Kohana_Piwik {

	/**
	 * Get a Piwik tracker instance
	 *
	 * @param  $piwik_config  The config to use, defaults to its own config
	 */
	static public function tracker(Kohana_Config_Group $piwik_config=NULL)
	{
		// No piwik config supplied
		if ( ! $piwik_config)
		{
			// Create default config instance
			$piwik_config = Kohana::$config->load('piwik');
		}

		// Create new piwik instance
		$t = new PiwikTracker(
			$piwik_config->get('idSite'), $piwik_config->get('url'));

		// Has auth token
		if ($token_auth = $piwik_config->get('token_auth'))
		{
			$t->setTokenAuth($token_auth);
		}

		// Return tracker
		return $t;
	}
}