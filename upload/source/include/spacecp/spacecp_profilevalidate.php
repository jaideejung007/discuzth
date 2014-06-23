<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: spacecp_profilevalidate.php 6790 2010-03-25 12:30:53Z cnteacher $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$profilevalidate = array(
/*jaideejung007*/	'telephone' => '/^((\\(?\\d{2,3}\\)?)|(\\d{2,3}-)?)\\d{6,8}$/',
/*jaideejung007*/	'mobile' => '/^(\+)?(66)?0?(8|9)?\d{1}(-)?\d{7}$/',
	'zipcode' => '/^\\d{5,6}$/',
	'revenue' => '/^\\d+$/',
	'height' => '/^\\d{1,3}$/',
	'weight' => '/^\\d{1,3}$/',
	'qq' => '/^[1-9]*[1-9][0-9]*$/'
);

?>