<?php
/**
 * Instance to call custom API calls
 */
class Kohana_Piwik_Tracker extends PiwikTracker {


	/**
	 * @var boolean  Whether to use shell exec to speed up requests. Warning:
	 *               no return data available!
	 */
	public $shell_exec = false;

	/**
	 * See original class. Added shell param to use shell in background without
	 * return data. This may speed up requests!! Requires curl on the server
	 * to be installed.
	 */
    protected function sendRequest($url, $method = 'GET', $data = null, $force = false)
    {
    	// No shell call
    	if ( ! $this->shell_exec)
    	{
    		return parent::sendRequest($url, $method, $data, $force);
    	}

    	// Shell call

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
            $cmd .= "-H 'Content-Type: application/json ";
            $cmd .= "-H 'Expect: ";
        }

        $cmd .= " > /dev/null 2>&1 &";
        exec($cmd);
        return;
}
}