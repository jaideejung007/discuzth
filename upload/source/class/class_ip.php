<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class ip {

	function __construct() {
	}

	
	public static function to_display($ip) {
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return '['.$ip.']';
		}
		return $ip;
	}

	
	public static function to_ip($ip) {
		if(strlen($ip) == 0) return $ip;
		if(preg_match('/(.*?)\[((.*?:)+.*)\](.*)/', $ip, $m)) { 
			if(filter_var($m[2], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$ip = $m[1].$m[2].$m[4];
			}
		}
		return $ip;
	}

	
	public static function validate_ip($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) !== false;
	}

	
	public static function validate_cidr($str, &$new_str) {
		if(str_contains($str, '/')) {
			[$newip, $mask] = explode('/', $str);
			if($mask <= 0) {
				return FALSE;
			}
			$newmask = intval($mask);
			$newip = self::to_ip($newip);
			if(!self::validate_ip($newip)) {
				return FALSE;
			}
			if($newmask > 128 || ($newmask > 32 && !str_contains($newip, ':'))) {
				return FALSE;
			}
			$new_str = $newip.'/'.$mask;
			return TRUE;
		}
		return FALSE;
	}

	
	public static function calc_cidr_range($str, $as_hex = false) {
		if(self::validate_cidr($str, $str)) {
			[$ip, $prefix] = explode('/', $str);
		} elseif(filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$ip = $str;
			$prefix = 32;
		} elseif(filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$ip = $str;
			$prefix = 128;
		} else {
			return FALSE;
		}

		$ip_bytes = unpack('C*', inet_pton($ip));
		$total_bytes = count($ip_bytes);
		$num_diff_bits = 8 * $total_bytes - $prefix;
		if($num_diff_bits >= 0) {
			$num_same_bytes = $prefix >> 3;
			$same_bytes = array_slice($ip_bytes, 0, $num_same_bytes);
			$diff_bytes_start = ($total_bytes === $num_same_bytes) ? [] : array_fill(0, $total_bytes - $num_same_bytes, 0);
			$diff_bytes_end = ($total_bytes === $num_same_bytes) ? [] : array_fill(0, $total_bytes - $num_same_bytes, 255);
			$start_same_bits = $prefix % 8;
			if($start_same_bits !== 0) {
				$vary_byte = $ip_bytes[$num_same_bytes];
				$diff_bytes_start[0] = $vary_byte & bindec(str_pad(str_repeat('1', $start_same_bits), 8, '0', STR_PAD_RIGHT));
				$diff_bytes_end[0] = $diff_bytes_start[0] + bindec(str_repeat('1', 8 - $start_same_bits));
			}

			$start_array = array_merge($same_bytes, $diff_bytes_start);
			$end_array = array_merge($same_bytes, $diff_bytes_end);
			if($as_hex) {
				if($total_bytes < 16) {
					$start_array = array_merge(array_fill(0, 16 - $total_bytes, 0), $start_array);
					$end_array = array_merge(array_fill(0, 16 - $total_bytes, 0), $end_array);
				}
				$start = unpack('H*hex', join(array_map('chr', $start_array)))['hex'];
				$end = unpack('H*hex', join(array_map('chr', $end_array)))['hex'];
				return [$start, $end];
			} else {
				$start = call_user_func_array('pack', array_merge(['C*'], $start_array));
				$end = call_user_func_array('pack', array_merge(['C*'], $end_array));
				return [$start, $end];
			}
		}

		return FALSE;
	}

	
	public static function ip_to_hex_str($ip) {
		if(!self::validate_ip($ip)) {
			return false;
		}
		$ip_bytes = unpack('C*', inet_pton($ip));
		$total_bytes = count($ip_bytes);
		if($total_bytes < 16) {
			$ip_bytes = array_merge(array_fill(0, 16 - $total_bytes, 0), $ip_bytes);
		}
		return unpack('H*hex', join(array_map('chr', $ip_bytes)))['hex'];
	}

	

	public static function check_ip($requestIp, $ips) {
		if(!self::validate_ip($requestIp)) {
			return false;
		}
		if(!\is_array($ips)) {
			$ips = [$ips];
		}
		$method = substr_count($requestIp, ':') > 1 ? 'check_ip6' : 'check_ip4';
		foreach($ips as $ip) {
			if(self::$method($requestIp, $ip)) {
				return true;
			}
		}
		return false;
	}

	public static function check_ip6($requestIp, $ip) {
		if(str_contains($ip, '/')) {
			[$address, $netmask] = explode('/', $ip, 2);
			if('0' === $netmask) {
				return (bool)unpack('n*', @inet_pton($address));
			}
			if($netmask < 1 || $netmask > 128) {
				return false;
			}
		} else {
			$address = $ip;
			$netmask = 128;
		}
		$bytesAddr = unpack('n*', @inet_pton($address));
		$bytesTest = unpack('n*', @inet_pton($requestIp));
		if(!$bytesAddr || !$bytesTest) {
			return false;
		}
		for($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
			$left = $netmask - 16 * ($i - 1);
			$left = ($left <= 16) ? $left : 16;
			$mask = ~(0xffff >> $left) & 0xffff;
			if(($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
				return false;
			}
		}
		return true;
	}

	public static function check_ip4($requestIp, $ip) {
		if(str_contains($ip, '/')) {
			[$address, $netmask] = explode('/', $ip, 2);
			if('0' === $netmask) {
				return false;
			}
			if($netmask < 0 || $netmask > 32) {
				return false;
			}
		} else {
			$address = $ip;
			$netmask = 32;
		}
		if(false === ip2long($address)) {
			return false;
		}
		return 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
	}

	
	public static function convert($ip, $simple = false) {
		$return = '';
		require childfile('ip', 'global/core');
		return $return;
	}

	public static function checkaccess($ip, $accesslist) {
		return preg_match('/^('.str_replace(["\r\n", ' '], ['|', ''], preg_quote($accesslist, '/')).')/', $ip);
	}

	public static function checkbanned($ip) {
		global $_G;

		if(array_key_exists('security', $_G['config']) && array_key_exists('useipban', $_G['config']['security']) && $_G['config']['security']['useipban'] == 0) {
			return false;
		}

		if($_G['setting']['ipaccess'] && !self::checkaccess($ip, $_G['setting']['ipaccess'])) {
			return true;
		}

		return table_common_banned::t()->check_banned(TIMESTAMP, $ip);
	}

}

