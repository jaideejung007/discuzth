<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class qq_tools {

	const tokenKey = 'qqToken';

	function __construct($argument = FALSE) {

	}

	
	public function httpRequest($url, $parameters = [], $method = 'get', $postData = [], $headerData = '') {
		$method = strtolower($method);
		return match ($method) {
			'get' => $this->httpGetRequest($url, $parameters, $headerData),
			'post' => $this->httpPostRequest($url, $parameters, $postData, $headerData),
			default => FALSE,
		};
	}

	
	private function httpGetRequest($url, $parameters = NULL) {
		if(empty($url)) {
			return FALSE;
		}
		
		if(!empty($parameters) && is_array($parameters) && count($parameters)) {
			$is_first = TRUE;
			foreach($parameters as $key => $value) {
				if($is_first) {
					$url .= '?'.$key.'='.urlencode($value);
					$is_first = FALSE;
				} else {
					$url .= '&'.$key.'='.urlencode($value);
				}
			}
		}

		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		$result = curl_exec($ch);
		if($error = curl_error($ch)) {
			die($error);
		}

		return json_decode($result, TRUE);
	}

	
	private function httpPostRequest($url, $parameters = [], $postData = [], $headerData = []) {
		if(empty($url)) {
			return FALSE;
		}
		
		if(!empty($parameters) && is_array($parameters) && count($parameters)) {
			$is_first = TRUE;
			foreach($parameters as $key => $value) {
				if($is_first) {
					$url .= '?'.$key.'='.urlencode($value);
					$is_first = FALSE;
				} else {
					$url .= '&'.$key.'='.urlencode($value);
				}
			}
		}

		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_POST, TRUE);
		
		if(!empty($postData)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		if($headerData) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
		}

		
		$result = curl_exec($ch);
		if($error = curl_error($ch)) {
			die($error);
		}

		return json_decode($result, TRUE);
	}

	
	public function uploadFileByPost($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		if($error = curl_error($ch)) {
			die($error);
		}

		return json_decode($result, TRUE);
	}

	
	public function curlRequest($url, $post = '', $cookie = '', $returnCookie = 0) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_REFERER, 'http://XXX');
		if($post) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		}
		if($cookie) {
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$data = curl_exec($curl);
		if(curl_errno($curl)) {
			return curl_error($curl);
		}
		if($returnCookie) {
			list($header, $body) = explode("\r\n\r\n", $data, 2);
			preg_match_all('/Set\-Cookie:([^;]*);/', $header, $matches);
			$info['cookie'] = substr($matches[1][0], 1);
			$info['content'] = $body;
			return $info;
		} else {
			return $data;
		}
	}

	
	public function saveAccessToken($corpid, $corpsecret, $token) {
		if(empty($corpid) || empty($corpsecret) || empty($token)) {
			return FALSE;
		}

		$result = memory('get', self::tokenKey.$corpid);

		$result = json_decode($result, TRUE);
		$key = $corpid.$corpsecret;
		$result[$key] = [$token, time()];
		if(memory('set', self::tokenKey.$corpid, json_encode($result))) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	
	public function getAccessToken($corpid, $corpsecret) {
		if(empty($corpid) || empty($corpsecret)) {
			return FALSE;
		}

		$result = memory('get', self::tokenKey.$corpid);
		if(empty($result)) {
			return FALSE;
		}

		$result = json_decode($result, TRUE);
		$key = $corpid.$corpsecret;
		if(isset($result[$key])) {
			if(time() - 7200 > $result[$key][1]) {
				
				return FALSE;
			} else {
				
				return $result[$key][0];
			}
		} else {
			return FALSE;
		}
	}

	
	public function json_encode_ex($value) {
		return json_encode($value, JSON_UNESCAPED_UNICODE);
	}

}

