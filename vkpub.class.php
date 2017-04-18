<?php
include('access.php');

class VkpubResponse {
	
}

class VkpubGroup {
	public function __construct($id) {

	}
}

class Vkpub {
    private $accessToken = '';
    private $secret = '';
    private $apiVersion = '5.63';
    private $lastResponse;
    private $group;

    public function __construct() {
		$preset = json_decode(file_get_contents('preset.php'));
		if (!is_object($preset)) {
			die('preset is not object!');
		}
        $this->lastResponse = new VkpubResponse();
        $this->logPath = $_SERVER['DOCUMENT_ROOT'].'/log';
    }
    
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    public function setSecret($secret) {
        $this->secret = $secret;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getSecret() {
        return $this->secret;
    }

    private function getSig($methodName, $requestParams) {
        $getParams = http_build_query($requestParams);
        $methodString = '/method/'.$methodName.'?'. $getParams;
        $sig = md5($methodString.$this->getSecret());
        return $sig;
    }

    public function sendRequest($methodName, $requestParams) {
        $requestParams['v'] = $this->apiVersion;
        $requestParams['access_token'] = $this->getAccessToken();
        $getParams = http_build_query($requestParams);
        $methodString = '/method/'.$methodName.'?'. $getParams;
        $requestUri = 'https://api.vk.com'.$methodString.'&sig='.$this->getSig($methodName, $requestParams);
        $response = file_get_contents($requestUri);
        $vkResponse = json_decode($response);
		
		if (!is_object($vkResponse)) {
			return null;
		}

        if (is_object($vkResponse)) {
			if (isset($vkResponse->error) && is_object($vkResponse->error)) {
				echo '<p>Error code: '.$vkResponse->error->error_code.'</p>';
				echo '<p>Error message: '.$vkResponse->error->error_msg.'</p>';
				echo '<p>Request params: '.print_r($vkResponse->error->request_params, true).'</p>';
			} else {
				$this->logResponse($methodName, $vkResponse);
			}
        }
        return $vkResponse;
    }

    public function printR($variable) {
        return '<pre>'.print_r($variable, true).'</pre>';
    }

    public function logResponse($methodName, $response) {
        $microtime = microtime(true);
        $microtime = str_replace(' ', '', $microtime);
        $methodName = str_replace('.', '_', $methodName);
        $this->logPath = str_replace('..', '', $this->logPath);
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0777, false);
        }
        if (!file_exists($this->logPath)) {
            return false;
        }
        $filename = $methodName.'_'.$microtime.'.log';
        $fullPath = $this->logPath.'/'.$filename;
        if (is_object($response)) {
            file_put_contents($fullPath, print_r($response, true));
            return true;
        } else {
            echo '*';
        }
        return false;
    }
    
    public function isSuccessResponse($response) {
        if (is_object($response)) {
            if (isset($response->error) && is_object($response->error))
            return true;
        }
        return false;
    }
	
	public function getGroupInfo($group) {

	}
		
};
