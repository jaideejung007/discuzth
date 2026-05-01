<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_forumperm {

	private $formula = '';
	private $permstr = '';

	private $formula_cells = [];

	const CacheKey = 'forumperm_';
	const CacheTTL = 300;

	function __construct($permstr) {
		$this->permstr = $permstr;
	}

	public function check($groupid = 0) {
		if($groupid) {
			return preg_match("/(^|\t)(".$groupid.")(\t|$)/", $this->permstr);
		}

		self::get_group();
		self::get_verify();
		self::get_tag();
		self::get_account();
		self::get_plugin();

		self::init_formula();

		return self::run_formula();
	}

	public static function clear_cache($uid) {
		memory('rm', $uid, self::CacheKey.'tag_');
		memory('rm', $uid, self::CacheKey.'account_');
		memory('rm', $uid, self::CacheKey.'verify_');
	}

	private function get_group() {
		static $got = null;
		if($got !== null) {
			$this->formula_cells = array_merge($this->formula_cells, $got);
			return;
		}
		global $_G;

		$newCells = [];
		$newCells['g'.$_G['groupid']] = 1;
		foreach(explode("\t", $_G['member']['extgroupids']) as $extgroupid) {
			if($extgroupid = intval(trim($extgroupid))) {
				if($groupterms['ext'][$extgroupid] && $groupterms['ext'][$extgroupid] < TIMESTAMP) {
					continue;
				}
				$newCells['g'.$extgroupid] = 1;
			}
		}
		$newCells['group'] = 1;

		$this->formula_cells = array_merge($this->formula_cells, $got = $newCells);
	}

	private function get_verify() {
		static $got = null;
		if($got !== null) {
			$this->formula_cells = array_merge($this->formula_cells, $got);
			return;
		}
		global $_G;

		$newCells = [];
		if($_G['setting']['verify']['enabled'] && $_G['uid']) {
			static $member_verify = null;
			if($member_verify === null) {
				$_v = memory('get', self::CacheKey.'verify_'.$_G['uid']);
				if(!$_v) {
					$member_verify = table_common_member_verify::t()->fetch($_G['uid']);
					memory('set', self::CacheKey.'verify_'.$_G['uid'], [$member_verify, time()], self::CacheTTL);
				} else {
					$member_verify = $_v[0];
				}
			}

			if($member_verify) {
				foreach($_G['setting']['verify'] as $vid => $verify) {
					if(!$verify['available']) {
						continue;
					}
					if($member_verify['verify'.$vid] == 1) {
						$newCells['v'.$vid] = 1;
						$newCells['verify'] = 1;
					}
				}
			}
		}

		$this->formula_cells = array_merge($this->formula_cells, $got = $newCells);
	}

	private function get_tag() {
		static $got = null;
		if($got !== null) {
			$this->formula_cells = array_merge($this->formula_cells, $got);
			return;
		}
		global $_G;

		static $member_tags = null;
		if($member_tags === null && $_G['uid']) {
			$_v = memory('get', self::CacheKey.'tag_'.$_G['uid']);
			if(!$_v) {
				$member_tags = table_common_tagitem::t()->select(0, $_G['uid'], 'uid');
				memory('set', self::CacheKey.'tag_'.$_G['uid'], [$member_tags, time()], self::CacheTTL);
			} else {
				$member_tags = $_v[0];
			}
		}

		$newCells = [];
		foreach($member_tags as $row) {
			$newCells['t'.$row['tagid']] = 1;
			$newCells['tag'] = 1;
		}

		$this->formula_cells = array_merge($this->formula_cells, $got = $newCells);
	}

	private function get_plugin() {
		static $got = null;
		if($got !== null) {
			$this->formula_cells = array_merge($this->formula_cells, $got);
			return;
		}
		global $_G;

		if(empty($_G['setting']['plugins']['perm'])) {
			return;
		}

		$newCells = [];
		foreach($_G['setting']['plugins']['perm'] as $k => $v) {
			if(!class_exists($v['class'])) {
				continue;
			}
			$c = new $v['class']();
			if(!method_exists($c, 'fetch_perm')) {
				continue;
			}
			if($c->fetch_perm($_G['uid'])) {
				$newCells['p_'.$k] = 1;
				$newCells['plugin_'.$v['pluginid']] = 1;
			}
		}

		$this->formula_cells = array_merge($this->formula_cells, $got = $newCells);
	}

	private function get_account() {
		static $got = null;
		if($got !== null) {
			$this->formula_cells = array_merge($this->formula_cells, $got);
			return;
		}
		global $_G;

		static $member_accounts = null;
		if($member_accounts === null && $_G['uid']) {
			$_v = memory('get', self::CacheKey.'account_'.$_G['uid']);
			if(!$_v) {
				$member_accounts = table_common_member_account::t()->fetch_all_by_uid($_G['uid'], false);
				memory('set', self::CacheKey.'account_'.$_G['uid'], [$member_accounts, time()], self::CacheTTL);
			} else {
				$member_accounts = $_v[0];
			}
		}

		$newCells = [];
		foreach($member_accounts as $row) {
			$newCells['a'.$row['atype']] = 1;
			$newCells['account'] = 1;
		}

		$this->formula_cells = array_merge($this->formula_cells, $got = $newCells);
	}

	private function init_formula() {
		if(preg_match("/(^|\t)_formula\[(.+?)\]/", $this->permstr, $r)) {
			$this->permstr = $r[0];
			$this->formula = $r[2];
		} else {
			$this->formula = 'group or tag or verify or account';
		}

		$formulaitems = [];
		foreach(explode("\t", $this->permstr) as $item) {
			if(is_numeric($item)) {
				$formulaitems['g'.$item] = 'g'.$item;
			} elseif(in_array(substr($item, 0, 1), ['a', 't', 'v']) && is_numeric(substr($item, 1))) {
				$formulaitems[$item] = $item;
			} elseif(preg_match('/^_([a|t|v])\[(.+?)\]$/', $item, $r)) {
				$formulaitem = [];
				foreach(explode(',', $r[2]) as $v) {
					$formulaitem[] = $r[1].$v;
					unset($formulaitems[$r[1].$v]);
				}
				$formulaitems[] = implode(' and ', $formulaitem);
			} else if(preg_match('/^p_\w+$/', $item) || preg_match('/^plugin_\w+$/', $item)) {
				$formulaitems[$item] = $item;
			}
		}
		if($formulaitems) {
			$this->formula = implode(' or ', $formulaitems);
		}
	}

	private function run_formula() {
		$formula = [];
		$p = 0;
		$this->formula = str_replace(['(', ')'], [' ( ', ' ) '], $this->formula);
		foreach(explode(' ', $this->formula) as $c) {
			if(!$c) {
				continue;
			} elseif(preg_match('/^(or|and)$/', $c)) {
				$formula[] = str_replace(['or', 'and'], ['||', '&&'], $c);
			} elseif(preg_match('/^(g|t|v|a)-?\d+$/', $c) ||
				preg_match('/^(group|tag|verify|account)$/', $c) ||
				preg_match('/^p_\w+$/', $c) || preg_match('/^plugin_\w+$/', $c)) {
				$formula[] = !empty($this->formula_cells[$c]) ? 'TRUE' : 'FALSE';
			} elseif($c == '(') {
				$p++;
				$formula[] = '(';
			} elseif($c == ')') {
				$p--;
				$formula[] = ')';
			} else {
				return false;
			}
		}
		if($p != 0) {
			return false;
		}
		$formulastr = implode(' ', $formula);
		@eval("\$result = ($formulastr) ? TRUE : FALSE;");
		return $result;
	}

}