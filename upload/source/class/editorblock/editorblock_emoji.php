<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_emoji {

	var $version = '1.1.5';
	var $name = 'Emoji';
	var $available = 1; 
	var $columns = 0; 
	var $identifier = 'emoji';
	var $description = 'Emoji';
	var $filename = 'emoji';
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
   tools_emoji: {
	emoji: {
		class: EmojiInlineTool,
		config: {
		    'editorid': 'editorjs',
		    'title': 'แทรกอิโมจิ',
		    'defaultLocale': 'zh-CN',
		    'locales': {
		      'zh-CN': { name: 'จีนตัวย่อ', status: true },
		      'zh-TW': { name: 'จีนตัวเต็ม', status: true },
		      'en': { name: 'English', status: true },
		      'demo': { name: 'ภาษา Demo', status: false }
		    },
		    i18n: {
		       messages: {
		          'demo': {
			        categories: {
			          'smileys-emotion': 'Demo อีโมจิและความรู้สึก',
			          'people-body': 'Demo บุคคลและร่างกาย',
			          'animals-nature': 'Demo สัตว์และธรรมชาติ',
			          'food-drink': 'Demo อาหารและเครื่องดื่ม',
			          'travel-places': 'Demo สถานที่และการเดินทาง',
			          'activities': 'Demo กิจกรรม',
			          'objects': 'Demo สิ่งของ',
			          'symbols': 'Demo สัญลักษณ์',
			          'flags': 'Demo ธง'
			        },
			        skinTones: {
			          'default': 'Demo เริ่มต้น',
			          'light': 'Demo ผิวขาว',
			          'medium-light': 'Demo ผิวขาวเหลือง',
			          'medium': 'Demo ผิวสองสี',
			          'medium-dark': 'Demo ผิวแทน',
			          'dark': 'Demo ผิวเข้ม'
			        },
			        statusMessages: {
			          loading: 'Demo กำลังโหลด...',
			          noEmoji: 'Demo ไม่มีอีโมจิในหมวดหมู่นี้'
			        }
			  },
		       },
		    },
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
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
EOF;
	}

}