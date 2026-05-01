<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_usergroup extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_usergroup';
		$this->_pk = 'groupid';

		parent::__construct();
	}

	public function fetch_by_credits($credits, $type = 'member') {
		if(is_array($credits)) {
			$creditsf = intval($credits[0]);
			$creditse = intval($credits[1]);
		} else {
			$creditsf = $creditse = intval($credits);
		}
		return DB::fetch_first('SELECT grouptitle, groupid FROM %t WHERE '.($type ? DB::field('type', $type).' AND ' : '').'%d>=creditshigher AND %d<creditslower LIMIT 1', [$this->_table, $creditsf, $creditse]);
	}

	public function fetch_by_credits_special($credits, $groupid) {
		if(is_array($credits)) {
			$creditsf = intval($credits[0]);
			$creditse = intval($credits[1]);
		} else {
			$creditsf = $creditse = intval($credits);
		}
		return DB::fetch_first('SELECT grouptitle, groupid FROM %t WHERE upgroupid=%d AND %d>=creditshigher AND %d<creditslower LIMIT 1', [$this->_table, $groupid, $creditsf, $creditse]);
	}

	public function fetch_all_by_type($type = '', $radminid = null, $allfields = false) {
		$parameter = [$this->_table];
		$wherearr = [];
		if(!empty($type)) {
			$parameter[] = $type;
			$wherearr[] = is_array($type) ? 'type IN(%n)' : 'type=%s';
		}
		if($radminid !== null) {
			$parameter[] = $radminid;
			$wherearr[] = 'radminid=%d';
		}
		$wheresql = !empty($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::fetch_all('SELECT '.($allfields ? '*' : 'groupid, grouptitle').' FROM %t '.$wheresql, $parameter, $this->_pk);
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			$unbuffered = $unbuffered === false ? '' : $unbuffered;
			return $this->update_usergroup($val, $data, $unbuffered);
		}
	}

	public function update_usergroup($id, $data, $type = '') {
		if(!is_array($data) || !$data || !is_array($data) || !$id) {
			return null;
		}
		$condition = DB::field('groupid', $id);
		if($type) {
			$condition .= ' AND '.DB::field('type', $type);
		}
		return DB::update($this->_table, $data, $condition);
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			$unbuffered = $unbuffered === false ? '' : $unbuffered;
			return $this->delete_usergroup($val, $unbuffered);
		}
	}

	public function delete_usergroup($id, $type = '') {
		if(!$id) {
			return null;
		}
		$condition = DB::field('groupid', $id);
		if($type) {
			$condition .= ' AND '.DB::field('type', $type);
		}
		return DB::delete($this->_table, $condition);
	}


	public function fetch_all_by_groupid($gid) {
		if(!$gid) {
			return null;
		}
		return DB::fetch_all('SELECT groupid FROM %t WHERE groupid IN (%n) AND type=\'special\' AND radminid>0', [$this->_table, $gid], $this->_pk);
	}

	public function fetch_all_by_not_groupid($gid) {
		return DB::fetch_all('SELECT groupid, type, grouptitle, creditshigher, radminid FROM %t WHERE type=\'member\' AND creditshigher=\'0\' OR (groupid NOT IN (%n) AND radminid<>\'1\' AND type<>\'member\') ORDER BY (creditshigher<>\'0\' || creditslower<>\'0\'), creditslower, groupid', [$this->_table, $gid], $this->_pk);
	}

	public function fetch_all_not($gid, $creditnotzero = false) {
		return DB::fetch_all('SELECT groupid, radminid, type, grouptitle, creditshigher, creditslower FROM %t WHERE groupid NOT IN (%n) ORDER BY '.($creditnotzero ? "(creditshigher<>'0' || creditslower<>'0'), " : '').'creditshigher, groupid', [$this->_table, $gid], $this->_pk);
	}

	public function fetch_new_groupid($fetch = false) {
		$sql = 'SELECT groupid, grouptitle FROM '.DB::table($this->_table)." WHERE type='member' AND creditslower>'0' ORDER BY creditslower LIMIT 1";
		if($fetch) {
			return DB::fetch_first($sql);
		} else {
			return DB::result_first($sql);
		}
	}

	public function fetch_all($ids, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_usergroup($ids);
		}
	}

	public function fetch_all_usergroup($ids) {
		if(!$ids) {
			return null;
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('groupid', $ids).' ORDER BY type, radminid, creditshigher', [$this->_table], $this->_pk);
	}

	public function fetch_all_switchable($ids) {
		if(!$ids) {
			return null;
		}
		return DB::fetch_all('SELECT * FROM %t WHERE (type=\'special\' AND `system`<>\'private\' AND radminid=\'0\') OR groupid IN (%n) ORDER BY type, `system`', [$this->_table, $ids], $this->_pk);
	}

	public function range_orderby_credit() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY upgroupid, (creditshigher<>\'0\' || creditslower<>\'0\'), creditslower, groupid', [$this->_table], $this->_pk);
	}

	public function range_orderby_creditshigher() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY upgroupid, creditshigher', [$this->_table], $this->_pk);
	}

	public function fetch_all_by_radminid($radminid, $glue = '>', $orderby = 'type') {
		$ordersql = '';
		if($ordersql = DB::order($orderby, 'DESC')) {
			$ordersql = ' ORDER BY '.$ordersql;
		}
		return DB::fetch_all('SELECT * FROM %t WHERE %i', [$this->_table, DB::field('radminid', dintval($radminid, true), $glue).$ordersql], 'groupid');
	}

	public function fetch_table_struct($result = 'FIELD') {
		$datas = [];
		$query = DB::query('DESCRIBE %t', [$this->_table]);
		while($data = DB::fetch($query)) {
			$datas[$data['Field']] = $result == 'FIELD' ? $data['Field'] : $data;
		}
		return $datas;
	}

	public function buyusergroup_exists() {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE type='special' and `system`>0", [$this->_table]);
	}
}

