<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$profilevalidate = [
	'telephone' => '/^((\\(?\\d{2,3}\\)?)|(\\d{2,3}-)?)\\d{6,8}$/', /*discuzth*/
	'mobile' => '/^(\\+)?(66)?0?(6|8|9)\\d{1}(-)?\\d{7}$/', /*discuzth*/
	'zipcode' => '/^\\d{5,6}$/',
	'revenue' => '/^\\d+$/',
	'height' => '/^\\d{1,3}$/',
	'weight' => '/^\\d{1,3}$/',
	'qq' => '/^[1-9]*[1-9][0-9]*$/'
];

