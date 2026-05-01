<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

const OSS_DEBUG = false;
class oss {

	const basePath = DISCUZ_ROOT.'source/class/oss/';

	public static function loadOSS($param) {
		$type = $param['oss_type'];
		$param['oss_id'] = trim($param['oss_id']);
		$param['oss_key'] = trim($param['oss_key']);
		$param['oss_bucket_url'] = self::_formaturl($param['oss_bucket_url']);
		$param['oss_url'] = self::_formaturl($param['oss_url']);

		if(str_starts_with($type, 'plugin:')) {
			$pluginid = substr($type, 7);
			if(!file_exists(DISCUZ_PLUGIN($pluginid).'/oss/init.php')) {
				return false;
			}
			$path = DISCUZ_PLUGIN($pluginid).'/oss';
			$c = 'oss_plugin_'.$pluginid;
		} else {
			return false;
		}

		require_once $path.'/init.php';
		return call_user_func([$c, 'load'], $param);
	}

	public static function writeCache($file) {
		global $_G;
		if($_G['setting']['ftp']['on'] != 2) {
			return;
		}
		$dir = DISCUZ_DATA.'./cache/';
		$descDir = DISCUZ_DATA.'./attachment/cache/';
		if(file_exists($dir.$file)) {
			dmkdir(dirname($descDir.$file));
			copy($dir.$file, $descDir.$file);
			ftpcmd('upload', 'cache/'.$file);
		}
	}

	private static function _formaturl($url) {
		$url = $url.(str_ends_with($url, '/') ? '' : '/');
		preg_match('/^https?/i', $url) || ($url = 'https://'.$url);
		return $url;
	}

}

abstract class oss_base {
	public $oss_info;
	public $oss_client;

	static $oss_server_name;

	public function gmt_iso8601($time) {
		$dtStr = date('c', $time);
		$mydatetime = new DateTime($dtStr);
		$expiration = $mydatetime->format(DateTime::ISO8601);
		$pos = strpos($expiration, '+');
		$expiration = substr($expiration, 0, $pos);
		return $expiration.'Z';
	}

	public static function check_file($url, $timeout = 15, $encodetype = 'URLENCODE') {
		global $_G;
		$return = '';
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			$ch = curl_init();

			$httpheader[] = 'User-Agent: '.$_SERVER['HTTP_USER_AGENT'];
			$httpheader[] = 'Referer: '.$_G['siteurl'];

			if($httpheader) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 1);

			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			$data = curl_exec($ch);
			$status = curl_getinfo($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			if($errno || $status['http_code'] != 200) {
				return;
			} else {
				$GLOBALS['filesockheader'] = substr($data, 0, $status['header_size']);
				$data = substr($data, $status['header_size']);
				return $data;
			}
		}
	}

	abstract public function testOSS();

	abstract public function setCors();

	abstract public function RefererConfig();

	abstract public function isObject($object);

	abstract public function setAcl($object, $Acl = null);

	abstract public function uploadFile($file, $object, $Acl = null);

	abstract public function getFilesList($prefix = '', $marker = '', $limit = 100, $delimiter = '');

	abstract public function uploadData($data, $object, $Acl = null);


	abstract public function renameObject($oldObject, $newObject, $MimeType = null);

	abstract public function deleteFile($objects);

	abstract public function downFile($file, $object);

	abstract public function signUrl($object);

	abstract public function getPolicy($dir, $object, $length = 1048576000);

	abstract public function getCallback($object);

	abstract public function getImgStyle($style);


}