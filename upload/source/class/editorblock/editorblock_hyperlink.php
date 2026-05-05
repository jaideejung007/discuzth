<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_hyperlink {

	var $version = '1.1.1';
	var $name = 'ลิงก์';
	var $available = 1; 
	var $columns = 0; 
	var $identifier = 'hyperlink';
	var $description = 'ลิงก์';
	var $filename = 'hyperlink';
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
   tools_hyperlink: {
        hyperlink: {
		  class: Hyperlink,
		  config: {
			shortcut: 'CMD+L',
			target: '_blank',
			rel: 'nofollow',
			availableTargets: ['_blank', '_self', '_parent', '_top'],
			availableRels: ['alternate', 'author', 'bookmark', 'external', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'noopener', 'prev', 'search', 'tag'],
			validate: false,
		  }
		},
		link: function() {}
   }
}
EOF;
	}

	function getI18n() {
		return <<<EOF

EOF;
	}

	function getStyle() {
		return <<<EOF
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
EOF;
	}

}