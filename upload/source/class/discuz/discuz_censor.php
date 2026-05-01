<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

const DISCUZ_CENSOR_SUCCEED = 0;
const DISCUZ_CENSOR_BANNED = 1;
const DISCUZ_CENSOR_MODERATED = 2;
const DISCUZ_CENSOR_REPLACED = 3;

class discuz_censor {
	var $table = 'common_word';
	var $censor_words = [];
	var $bbcodes_display;
	var $result;
	var $words_found = [];

	var $highlight;

	public function __construct() {
		global $_G;
		loadcache(['censor', 'bbcodes_display']);
		$this->censor_words = !empty($_G['cache']['censor']) ? $_G['cache']['censor'] : [];
		$this->bbcodes_display = $_G['cache']['bbcodes_display'][$_G['groupid']];
	}

	public static function & instance() {
		static $instance;
		if(!$instance) {
			$instance = new self();
		}
		return $instance;
	}

	function highlight($message, $badwords_regex) {
		$color = $this->highlight;
		if(empty($color)) {
			return $message;
		}
		$message = preg_replace($badwords_regex, '<span style="color: '.$color.';">\\1</span>', $message);
		return $message;
	}

	function check(&$message, $modword = NULL) {
		$limitnum = 500;
		$this->words_found = [];
		$bbcodes = 'b|i|color|size|font|align|list|indent|email|hide|quote|code|free|table|tr|td|img|swf|attach|payto|float'.($this->bbcodes_display ? '|'.implode('|', array_keys($this->bbcodes_display)) : '');
		if(is_array($this->censor_words['banned']) && !empty($this->censor_words['banned'])) {
			foreach($this->censor_words['banned'] as $banned_words) {
				if(preg_match_all($banned_words, @preg_replace(["/\[($bbcodes)=?(.*)\]/iU", "/\[\/($bbcodes)\]/i"], ['${2}', ''], $message), $matches)) {
					$this->words_found = $matches[0];
					$this->result = DISCUZ_CENSOR_BANNED;
					$this->words_found = array_unique($this->words_found);
					$message = $this->highlight($message, $banned_words);
					return DISCUZ_CENSOR_BANNED;
				}
			}
		}
		if(is_array($this->censor_words['mod']) && !empty($this->censor_words['mod'])) {
			if($modword !== NULL) {
				$message = preg_replace($this->censor_words['mod'], $modword, $message);
			}
			foreach($this->censor_words['mod'] as $mod_words) {
				if(is_string($message) && preg_match_all($mod_words, @preg_replace(["/\[($bbcodes)=?(.*)\]/iU", "/\[\/($bbcodes)\]/i"], ['${2}', ''], $message), $matches)) {
					$this->words_found = $matches[0];
					$this->result = DISCUZ_CENSOR_MODERATED;
					$message = $this->highlight($message, $mod_words);
					$this->words_found = array_unique($this->words_found);
					return DISCUZ_CENSOR_MODERATED;
				}
			}
		}
		if(!empty($this->censor_words['filter'])) {
			$i = 0;
			while($find_words = array_slice($this->censor_words['filter']['find'], $i, $limitnum)) {
				if(empty($find_words)) break;
				$replace_words = array_slice($this->censor_words['filter']['replace'], $i, $limitnum);
				$i += $limitnum;
				$message = preg_replace($find_words, $replace_words, $message);
			}
			$this->result = DISCUZ_CENSOR_REPLACED;
			return DISCUZ_CENSOR_REPLACED;
		}
		$this->result = DISCUZ_CENSOR_SUCCEED;
		return DISCUZ_CENSOR_SUCCEED;
	}

	function modbanned() {
		return $this->result == DISCUZ_CENSOR_BANNED;
	}

	function modmoderated() {
		return $this->result == DISCUZ_CENSOR_MODERATED;
	}

	function modreplaced() {
		return $this->result == DISCUZ_CENSOR_REPLACED;
	}

	function modsucceed() {
		return $this->result == DISCUZ_CENSOR_SUCCEED;
	}
}