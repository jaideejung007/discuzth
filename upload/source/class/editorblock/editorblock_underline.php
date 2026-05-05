<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_underline {

	var $version = '1.0.1';
	var $name = 'ขีดเส้นใต้';
	var $available = 1; 
	var $columns = 1; 
	var $identifier = 'underline';
	var $description = 'ใช้สำหรับขีดเส้นใต้ข้อความ';
	var $filename = 'underline';
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">Yunnuo</a>';
	var $type = '0'; 

	function __construct() {

	}

	function getsetting() {
		global $_G;
		$settings = [];
		return $settings;
	}

	function setsetting(&$blocknew, &$parameters) {
	}

	function getParameter() {
		return <<<EOF
EOF;
	}

	
	function getConfig() {
		return <<<EOF
{
   tools_underline: {
        underline: Underline,
   },
   i18n: {
	    messages: {
	        toolNames: {
	           'Underline': 'ขีดเส้นใต้',
	        }
	    },
   },
}
EOF;
	}

	function getI18n() {
		return <<<EOF

EOF;
	}

	function getStyle() {
		return <<<EOF
<style type="text/css">
.cdx-underline {
    text-decoration: underline;
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
EOF;
	}

}