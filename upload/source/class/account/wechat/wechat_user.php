<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class wechat_user {
	private $token = [];
	private $tools = NULL;

	public function __construct($token) {
		$this->token = $token;
		$this->tools = new wechat_tools();
	}

	
	public function setToken($token = NULL) {
		$this->token = $token;
	}

	
	public function getToken() {
		return $this->token;
	}


	public function getAuthUrl($query_data, $scopeType = 1) {
		$query_data['response_type'] = 'code';
		$query_data['scope'] = $scopeType ? 'snsapi_userinfo' : 'snsapi_base';
		$query_data['state'] = uniqid();
		
		
		return 'https://open.weixin.qq.com/connect/oauth2/authorize?'.http_build_query($query_data).'#wechat_redirect';
	}


	
	public function getAuthUser() {
		$url = 'https://api.weixin.qq.com/sns/userinfo';
		$data = ['access_token' => $this->token['access_token'], 'openid' => $this->token['openid'], 'lang' => 'zh_CN'];
		$result = $this->tools->httpRequest($url, $data);
		if($result) {
			if(!isset($result['errcode'])) {
				$result['success'] = TRUE;
				return ($result);
			} else {
				$result['success'] = FALSE;
				return ($result);
			}
		} else {
			return (['success' => FALSE, 'errmsg' => 'Query fails!', 'errcode' => -2, 'userid' => '']);
		}
	}
}

