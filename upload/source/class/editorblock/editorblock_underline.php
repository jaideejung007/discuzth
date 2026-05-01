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
	var $name = '下划线';
	var $available = 1; 
	var $columns = 1; 
	var $identifier = 'underline';
	var $description = '用于给文本增加下划线';
	var $filename = 'underline';
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">云诺</a>';
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
	           'Underline': '下划线',
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