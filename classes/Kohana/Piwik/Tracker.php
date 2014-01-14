<?php
/**
 * Instance to call custom API calls
 */
class Kohana_Piwik_Tracker extends PiwikTracker {


	/**
	 * @var boolean  Possible values: curl or php. Curl method requires curl
     *               on server and php method requires setting the piwik.php
     *               binary path. Warning: no return data available!
	 */
	public $shell_mode;

    /**
     * @var  string  The binary path to use for php shell mode
     */
    public $binary_path;

	/**
	 * See original class. Added shell param to use shell in background without
	 * return data. This may speed up requests!! Requires curl on the server
	 * to be installed.
	 */
    protected function sendRequest($url, $method = 'GET', $data = null, $force = false)
    {
    	// No shell call
    	if ( ! $this->shell_mode OR ! in_array($this->shell_mode, array('curl','php')))
    	{
    		return parent::sendRequest($url, $method, $data, $force);
    	}

    	// Shell call
        if ($this->shell_mode === 'curl')
        {
            // if doing a bulk request, store the url
            if ($this->doBulkRequests && !$force) {
                $this->storedTrackingActions[]
                    = $url
                    . (!empty($this->userAgent) ? ('&ua=' . urlencode($this->userAgent)) : '')
                    . (!empty($this->acceptLanguage) ? ('&lang=' . urlencode($this->acceptLanguage)) : '');
                return true;
            }

            $response = '';

            if (!$this->cookieSupport) {
                $this->requestCookie = '';
            }

            $cmd = "curl -H 'Content-Type: application/json' "
                . "-m ".$this->requestTimeout." "
                . "-H 'User-Agent: ".$this->userAgent."' "
                . "-H 'Accept-Language: ".$this->acceptLanguage."' "
                . "-H 'Cookie: ".$this->requestCookie."' "
                . " -d '" . $data . "' " . "'" . $url . "' ";

            switch ($method) {
                case 'POST':
                    $cmd .= "-X POST ";
                    break;
                default:
                    break;
                }

            // only supports JSON data
            if (!empty($data)) {
                $cmd .= "-H 'Cookie: ".$this->requestCookie."' ";
                $cmd .= "-H 'Content-Type: application/json' ";
                $cmd .= "-H 'Expect: ' ";
            }

            $cmd .= " > /dev/null 2>&1 &";
            exec($cmd);
        }
        // php call
        else
        {
            // Get query string
            $url_parts = parse_url($url);
            $url_query = $url_parts['query'];

            // Escape double quotes to prevent commandline breaks
            $url_query = str_replace('"', '\"', $url_query);

            // Build cmd
            $cmd = "export QUERY_STRING='$url_query'; "
                . 'php -e -r \'parse_str($_SERVER["QUERY_STRING"], $_GET); '
                    . "include \"".$this->binary_path."\";' > /dev/null 2>&1 &";

            // Exec
            exec($cmd);
        }

        // Return nothing
        return;
}
}