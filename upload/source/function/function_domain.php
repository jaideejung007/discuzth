<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function domaincheck($domain, $domainroot, $domainlength, $msgtype = 1) {

	if(strlen($domain) < $domainlength) {
		showmessage('domain_length_error', '', ['length' => $domainlength], ['return' => true]);
	}
	if(strlen($domain) > 30) {
		$msgtype ? showmessage('two_domain_length_not_more_than_30_characters', '', [], ['return' => true]) : cpmsg('two_domain_length_not_more_than_30_characters', '', 'error');
	}
	if(!preg_match('/^[a-z0-9]*$/', $domain)) {
		$msgtype ? showmessage('only_two_names_from_english_composition_and_figures', '', [], ['return' => true]) : cpmsg('only_two_names_from_english_composition_and_figures', '', 'error');
	}

	if($msgtype && isholddomain($domain)) {
		showmessage('domain_be_retained', '', [], ['return' => true]);
	}

	if(existdomain($domain, $domainroot)) {
		$msgtype ? showmessage('two_domain_have_been_occupied', '', [], ['return' => true]) : cpmsg('two_domain_have_been_occupied', '', 'error');
	}

	return true;
}

function isholddomain($domain) {
	global $_G;

	$domain = strtolower($domain);
	$holdmainarr = empty($_G['setting']['holddomain']) ? ['www'] : explode('|', $_G['setting']['holddomain']);
	$ishold = false;
	foreach($holdmainarr as $value) {
		if(!str_contains($value, '*')) {
			if(strtolower($value) == $domain) {
				$ishold = true;
				break;
			}
		} else {
			$value = str_replace('*', '.*?', $value);
			if(@preg_match("/$value/i", $domain)) {
				$ishold = true;
				break;
			}
		}
	}
	return $ishold;
}

function existdomain($domain, $domainroot) {
	global $_G;

	$exist = false;
	if(table_common_domain::t()->count_by_domain_domainroot($domain, $domainroot)) {
		$exist = true;
	}
	return $exist;
}

