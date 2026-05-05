<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_inlinecode {

	var $version = '1.0.1';
	var $name = 'โค้ดในแถว';
	var $available = 1; 
	var $columns = 1; 
	var $global_css = 1; 
	var $identifier = 'inlinecode';
	var $description = 'โค้ดในแถว';
	var $filename = 'inline-code';
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
   tools_inlinecode: {
        inlinecode: InlineCode,
   },
   i18n: {
	    messages: {
	        toolNames: {
	           'Inlinecode': 'โค้ดในแถว',
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
.inline-code {
  background: rgba(250, 239, 240, 0.78);
  color: #b44437;
  padding: 3px 4px;
  border-radius: 5px;
  margin: 0 1px;
  font-family: inherit;
  font-size: 0.86em;
  font-weight: 500;
  letter-spacing: 0.3px;
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