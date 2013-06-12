<?php
/**
 * Instance to call custom API calls
 */
class Kohana_Piwik_Api {

	const FORMAT_JSON  = 'JSON';  // JSON format
	const STATUS_XML   = 'XML';   // XML format

	/**
	 * @var  string  The format to use
	 */
	protected $_format = self::FORMAT_JSON;

	/**
	 * @var  Kohana_Config_Group  The piwik config
	 */
	protected $_config;


	public function __construct(Kohana_Config_Group $piwik_config=NULL)
	{
		// No piwik config supplied
		if ( ! $piwik_config)
		{
			// Create default config instance
			$this->_config = $piwik_config = Kohana::$config->load('piwik');
		}
	}

	/**
	 * Get the piwik url being used
	 *
	 * @return  string
	 */
	public function url()
	{
		return $this->_config->get('url');
	}

	/**
	 * Call the API using a method with optional data.
	 *
	 * @param  string  $method
	 * @param  array   $data      asocc array
	 */
	public function call($method, $data=array())
	{
		// Get URL
		$url = $this->_config->get('url');

		// Request query
		$query = URL::query(array_merge(
			array(
				'module'      =>  'API',
				'method'      =>  $method,
				'format'      =>  $this->_format,
				'token_auth'  =>  $this->_config->get('token_auth', 'anonymous')),
			$data
		), FALSE);

		// Build request URL
		$url .= $query;

		//--- Use curl. Somehow Kohana Request did not work

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

		// Requesting JSON
		if ($this->_format === self::FORMAT_JSON)
		{
			// Return decoded data
			$output = json_decode($output, TRUE);

			// Error
			if (Arr::get($output, 'result') === 'error')
			{
				throw new Exception($output['message']);
			}
		}

		return $output;
	}
}