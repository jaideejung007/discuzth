<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_xml extends discuz_block {

	var $blockdata = [];

	function __construct($xmlid = null) {
		if(!empty($xmlid)) {
			if(!($blockxml = table_common_block_xml::t()->fetch($xmlid))) {
				return;
			}
			$this->blockdata = $blockxml;
			$this->blockdata['data'] = (array)dunserialize($blockxml['data']);
		} else {
			foreach(table_common_block_xml::t()->range() as $value) {
				$one = $value;
				$one['data'] = (array)dunserialize($value['data']);
				$this->blockdata[] = $one;
			}
		}
	}

	function name() {
		return dhtmlspecialchars($this->blockdata['data']['name']);
	}

	function blockclass() {
		return dhtmlspecialchars($this->blockdata['data']['blockclass']);
	}

	function fields() {
		return dhtmlspecialchars($this->blockdata['data']['fields']);
	}

	function getsetting() {
		return dhtmlspecialchars($this->blockdata['data']['getsetting']);
	}

	function getdata($style, $parameter) {
		$parameter = $this->cookparameter($parameter);
		$array = [];
		foreach($parameter as $key => $value) {
			if(is_array($value)) {
				$parameter[$key] = implode(',', $value);
			}
		}
		$parameter['clientid'] = $this->blockdata['clientid'];
		$parameter['op'] = 'getdata';
		$parameter['charset'] = CHARSET;
		$parameter['version'] = $this->blockdata['version'];
		$xmlurl = $this->blockdata['url'];
		$parse = parse_url($xmlurl);
		if(!empty($parse['host'])) {
			define('IN_ADMINCP', true);
			require_once libfile('function/importdata');
			$importtxt = @dfsockopen($xmlurl, 0, create_sign_url($parameter, $this->blockdata['key'], $this->blockdata['signtype']));
		} else {
			$ctx = stream_context_create(['http' => ['timeout' => 20]]);
			$importtxt = @file_get_contents($xmlurl, false, $ctx);
		}
		if($importtxt) {
			require libfile('class/xml');
			$array = xml2array($importtxt);
		}
		$idtype = 'xml_'.$this->blockdata['id'];
		foreach($array['data'] as $key => $value) {
			$value['idtype'] = $idtype;
			$array['data'][$key] = $value;
		}
		if(empty($array['data'])) $array['data'] = null;
		return $array;
	}

}

