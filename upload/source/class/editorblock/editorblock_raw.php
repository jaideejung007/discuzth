<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_raw {

	var $version = '1.0.3';
	var $name = 'HTML代码';
	var $available = 1; 
	var $columns = 1; 
	var $identifier = 'raw';
	var $description = 'HTML代码';
	var $filename = 'raw';
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
{
    "data" : {
        "html": "<div style=\"background: #000; color: #fff; font-size: 30px; padding: 50px;\">Any HTML code</div>",
    },
    "id": "ZT8S70Q34G", // 区块id
    "type": "raw" // 区块类型
}
EOF;
	}

	
	function getConfig() {
		return <<<EOF
{
   tools_raw: {
      raw: {
         class: RawTool,
         placeholder: "请输入HTML代码...",
         tunes: ['anchorTune', 'hideTune']
      },
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
<style type="text/css">
.ce-block {
    margin-bottom: 20px;
}
.ce-block__content,.ce-toolbar__content {
	/* max-width:calc(100% - 50px) */
	margin-left: auto;
    margin-right: auto;
}
.ce-rawtool__textarea {
  width: 100%;
  min-height: 300px;
  resize: vertical;
  border-radius: 8px;
  border: 0;
  background-color: #1e2128;
  font-family: Menlo, Monaco, Consolas, Courier New, monospace;
  font-size: 12px;
  line-height: 1.6;
  letter-spacing: -0.2px;
  color: #a1a7b6;
  overscroll-behavior: contain;
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
	<div class="ce-block__content">
		<div class="cdx-block ce-rawtool">
			<textarea class="ce-rawtool__textarea cdx-input">
{data.html}
			</textarea>
		</div>
	</div>
</div>
EOF;
	}

}