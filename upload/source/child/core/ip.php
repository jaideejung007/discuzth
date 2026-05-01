<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

if(str_contains($ip, '/')) {
	[$ip, $netmask] = explode('/', $ip, 2);
}

if(!self::validate_ip($ip)) {
	$return = 'Invalid';
} elseif(!(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) !== false)) {
	$return = lang('ipdb', 0);
} elseif(!(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false)) {
	$return = 'Reserved';
} else {
	if(array_key_exists('ipdb', $_G['config']) && array_key_exists('setting', $_G['config']['ipdb'])) {
		$s = $_G['config']['ipdb']['setting'];
		if(!empty($s['fullstack'])) {
			$c = 'ip_'.$s['fullstack'];
		} else if(!empty($s['ipv4']) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			$c = 'ip_'.$s['ipv4'];
		} else if(!empty($s['ipv6']) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			$c = 'ip_'.$s['ipv6'];
		} else if(!empty($s['default'])) {
			$c = 'ip_'.$s['default'];
		} else {
			$c = 'ip_system';
		}
	} else {
		$c = 'ip_system';
	}
	$ipobject = $c::getInstance();
	if($ipobject === NULL) {
		$return = 'Error';
	} else {
		if($simple && method_exists($ipobject, 'convertSimple')) {
			$return = $ipobject->convertSimple($ip);
		} else {
			$return = $ipobject->convert($ip);
		}
	}
}