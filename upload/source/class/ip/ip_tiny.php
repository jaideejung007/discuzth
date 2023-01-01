<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: ip_tiny.php 1587 2019-12-03 12:00:00Z opensource $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class ip_tiny_init_exception extends Exception {}

class ip_tiny { /* jaideejung007 */

	private static $instance = NULL;
	private $jdzfp = NULL;

	private function __construct() {
		require_once constant("DISCUZ_ROOT").'./data/ipdata/geoip2.phar';

		$ipdatafile = constant("DISCUZ_ROOT").'./data/ipdata/GeoLite2-City.mmdb';

		if($this->jdzfp === NULL && $this->jdzfp = new GeoIp2\Database\Reader($ipdatafile)) {
		}
		if($this->jdzfp === FALSE) {
			throw new ip_tiny_init_exception();
		}

	}

	function __destruct() {
		if ($this->jdzfp) {
			unset($this->jdzfp);
		}
	}

	public static function getInstance() {
		if (!self::$instance) {
			try {
				self::$instance = new ip_tiny();
			} catch (Exception $e) {
				return NULL;
			}
		}
		return self::$instance;
	}

	public function convert($ip) {

		try {
			$jdzrecord = $this->jdzfp->city($ip);
			$return = $jdzrecord->city->name; // ชื่อเมือง/นคร/เขต ผลลัพธ์: 'Ban Dan'
			$return .= ($jdzrecord->city->name == NULL ? "" : ", ".$jdzrecord->mostSpecificSubdivision->name); // ชื่อจังหวัด/รัฐ ผลลัพธ์: 'Surin'
			$return .= ($jdzrecord->mostSpecificSubdivision->name == NULL ? $jdzrecord->country->name : ", ".$jdzrecord->country->name); // ชื่อประเทศ ผลลัพธ์: 'United States'

		} catch (Exception $e) {
			$return = 'ERR';
		}
		if(!@$return) {
			$return = '??';
		}
		return '- '.$return;
	}

}
?>