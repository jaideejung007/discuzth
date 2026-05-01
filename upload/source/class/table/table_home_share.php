<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_share extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_share';
		$this->_pk = 'sid';

		parent::__construct();
	}

	public function fetch_by_id_idtype($id) {
		if(!$id) {
			return null;
		}
		return DB::fetch_first('SELECT * FROM %t WHERE %i', [$this->_table, DB::field('sid', $id)]);
	}

	public function update_dateline_by_id_idtype_uid($id, $idtype, $dateline, $uid) {
		$uid = dintval($uid);
		if(empty($idtype) || empty($id) || empty($uid)) {
			return 0;
		}
		return DB::update($this->_table, ['dateline' => $dateline], DB::field($idtype, $id).' AND '.DB::field('uid', $uid));
	}

	public function fetch_by_type($type) {
		return DB::fetch_first('SELECT * FROM %t WHERE type=%s', [$this->_table, $type]);
	}

	public function fetch_by_sid_uid($sid, $uid) {
		return DB::fetch_first('SELECT * FROM %t WHERE sid=%d AND uid=%d', [$this->_table, $sid, $uid]);
	}

	public function fetch_all_by_uid($uids, $start = 0, $limit = 0) {
		$uids = dintval($uids);
		if($uids) {
			return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('uid', $uids).' ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table], $this->_pk);
		}
		return [];
	}

	public function fetch_all_by_sid_uid_type($sid = 0, $uids = 0, $type = '', $start = 0, $limit = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($sid) {
			$parameter[] = $sid;
			$wherearr[] = 'sid=%d';
		}
		if(!empty($uids)) {
			$uids = dintval($uids, true);
			$parameter[] = $uids;
			$wherearr[] = is_array($uids) ? 'uid IN(%n)' : 'uid=%d';
		}
		if(!empty($type)) {
			$parameter[] = $type;
			$wherearr[] = 'type=%s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';

		return DB::fetch_all('SELECT * FROM %t '.$wheresql.' ORDER BY dateline DESC '.DB::limit($start, $limit), $parameter, $this->_pk);
	}

	public function fetch_all_by_username($users) {
		if(!empty($users)) {
			return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('username', $users), [$this->_table], $this->_pk);
		}
		return [];
	}

	public function fetch_all_by_status($status = 0, $start = 0, $limit = 1000) {
		return DB::fetch_all('SELECT * FROM %t WHERE `status` = %d ORDER BY '.$this->_pk.' '.DB::limit($start, $limit), [$this->_table, $status]);
	}

	public function fetch_all_search($sid = 0, $uids = 0, $type = '', $starttime = 0, $endtime = 0, $starthot = 0, $endhot = 0, $start = 0, $limit = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($sid) {
			$sid = dintval($sid, true);
			$parameter[] = $sid;
			$wherearr[] = is_array($sid) ? 'sid IN(%n)' : 'sid=%d';
		}
		if($uids) {
			$uids = dintval($uids, true);
			$parameter[] = $uids;
			$wherearr[] = is_array($uids) ? 'uid IN(%n)' : 'uid=%d';
		}
		if(!empty($type)) {
			$parameter[] = $type;
			$wherearr[] = 'type=%s';
		}
		if($starttime) {
			$parameter[] = $starttime;
			$wherearr[] = 'dateline>=%d';
		}
		if($endtime) {
			$parameter[] = $endtime;
			$wherearr[] = 'dateline<=%d';
		}
		if($starthot) {
			$parameter[] = $starthot;
			$wherearr[] = 'hot>=%d';
		}
		if($endhot) {
			$parameter[] = $endhot;
			$wherearr[] = 'hot<=%d';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::fetch_all('SELECT * FROM %t '.$wheresql.' ORDER BY dateline DESC '.DB::limit($start, $limit), $parameter, $this->_pk);
	}

	public function count_by_type($type) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE type=%s', [$this->_table, $type]);
	}

	public function count_by_uid_itemid_type($uid = null, $itemid = null, $type = null) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($uid !== null) {
			$parameter[] = $uid;
			$wherearr[] = 'uid=%d';
		}
		if($itemid !== null) {
			$parameter[] = $itemid;
			$wherearr[] = 'itemid=%d';
		}
		if($type !== null) {
			$parameter[] = $type;
			$wherearr[] = 'type=%s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first('SELECT COUNT(*) FROM %t '.$wheresql, $parameter);
	}

	public function count_by_sid_uid_type($sid = 0, $uids = 0, $type = '') {
		$parameter = [$this->_table];
		$wherearr = [];
		if($sid) {
			$parameter[] = $sid;
			$wherearr[] = 'sid=%d';
		}
		if(!empty($uids)) {
			$uids = dintval($uids, true);
			$parameter[] = $uids;
			$wherearr[] = is_array($uids) ? 'uid IN(%n)' : 'uid=%d';
		}
		if(!empty($type)) {
			$parameter[] = $type;
			$wherearr[] = 'type=%s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';

		return DB::result_first('SELECT COUNT(*) FROM %t '.$wheresql, $parameter);
	}

	public function count_by_search($sid = 0, $uids = 0, $type = '', $starttime = 0, $endtime = 0, $starthot = 0, $endhot = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($sid) {
			$sid = dintval($sid, true);
			$parameter[] = $sid;
			$wherearr[] = is_array($sid) ? 'sid IN(%n)' : 'sid=%d';
		}
		if($uids) {
			$uids = dintval($uids, true);
			$parameter[] = $uids;
			$wherearr[] = is_array($uids) ? 'uid IN(%n)' : 'uid=%d';
		}
		if(!empty($type)) {
			$parameter[] = $type;
			$wherearr[] = 'type=%s';
		}
		if($starttime) {
			$parameter[] = $starttime;
			$wherearr[] = 'dateline>=%d';
		}
		if($endtime) {
			$parameter[] = $endtime;
			$wherearr[] = 'dateline<=%d';
		}
		if($starthot) {
			$parameter[] = $starthot;
			$wherearr[] = 'hot>=%d';
		}
		if($endhot) {
			$parameter[] = $endhot;
			$wherearr[] = 'hot<=%d';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first('SELECT COUNT(*) FROM %t '.$wheresql, $parameter);
	}

	public function delete_by_uid($uids) {
		$uids = dintval($uids, true);
		if($uids) {
			return DB::query('DELETE FROM %t WHERE '.DB::field('uid', $uids), [$this->_table]);
		}
		return 0;
	}

	public function update_hot_by_sid($sid, $hotuser) {
		return DB::query('UPDATE %t SET hot=hot+1, hotuser=%s WHERE sid=%d', [$this->_table, $hotuser, $sid]);
	}

}

