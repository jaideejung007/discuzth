<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_tradelog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_tradelog';
		$this->_pk = 'orderid';

		parent::__construct();
	}

	public function count_by_status($status) {
		$status = $status >= 0 ? 'WHERE status='.intval($status) : '';
		return DB::fetch_first('SELECT COUNT(*) AS num, SUM(price) AS pricesum, SUM(credit) AS creditsum, SUM(tax) AS taxsum FROM %t %i', [$this->_table, $status]);
	}

	public function fetch_all_by_status($status, $start, $limit) {
		$status = $status >= 0 ? 'WHERE status='.intval($status) : '';
		return DB::fetch_all('SELECT * FROM %t %i ORDER BY lastupdate DESC LIMIT %d, %d', [$this->_table, $status, $start, $limit]);
	}

	public function clear_failure($days) {
		DB::query('DELETE FROM %t WHERE buyerid>0 AND status=0 AND lastupdate<%d', [$this->_table, TIMESTAMP - intval($days) * 86400]);
	}

	public function expiration_payed($days) {
		$expiration = TIMESTAMP - intval($days) * 86400;
		$logs = DB::fetch_all('SELECT * FROM %t WHERE buyerid>0 AND status=4 AND lastupdate<%d', [$this->_table, $expiration]);
		$members = [];
		foreach($logs as $log) {
			$members[$log['buyerid']]['extcredits'.$log['basecredit']] += $log['credit'];
		}
		foreach($members as $uid => $data) {
			updatemembercount($uid, $data);
		}
		DB::query('DELETE FROM %t WHERE buyerid>0 AND status=4 AND lastupdate<%d', [$this->_table, $expiration]);
	}

	public function expiration_finished($days) {
		$expiration = TIMESTAMP - intval($days) * 86400;
		$logs = DB::fetch_all('SELECT * FROM %t WHERE sellerid>0 AND status=5 AND lastupdate<%d', [$this->_table, $expiration]);
		$members = [];
		foreach($logs as $log) {
			$members[$log['sellerid']]['extcredits'.$log['basecredit']] += $log['credit'];
		}
		foreach($members as $uid => $data) {
			updatemembercount($uid, $data);
		}
		DB::query('DELETE FROM %t WHERE sellerid>0 AND status=5 AND lastupdate<%d', [$this->_table, $expiration]);
	}

	public function fetch_last($uid) {
		return DB::fetch_first("SELECT * FROM %t WHERE buyerid=%d AND status!=0 AND buyername!='' ORDER BY lastupdate DESC LIMIT 1", [$this->_table, $uid]);
	}

	public function fetch_all_log($viewtype, $uid, $tid, $pid, $ratestatus, $typestatus, $start, $limit) {
		$sql = ($tid ? 'tl.tid=\''.dintval($tid).'\' AND '.($pid ? 'tl.pid=\''.dintval($pid).'\' AND ' : '') : '').
			('tl.'.($viewtype == 'sell' ? 'sellerid' : 'buyerid').'='.intval($uid)).' '.
			($ratestatus = $ratestatus ? 'AND (tl.ratestatus=0 OR tl.ratestatus='.intval($ratestatus).')' : '').
			($typestatus = $typestatus ? 'AND tl.status IN ('.dimplode($typestatus).')' : '');
		return DB::fetch_all('SELECT tl.*, tr.aid, t.subject AS threadsubject FROM %t tl, %t t, %t tr WHERE %i
			AND tl.tid=t.tid AND tr.pid=tl.pid AND tr.tid=tl.tid ORDER BY tl.lastupdate DESC LIMIT %d, %d',
			[$this->_table, 'forum_thread', 'forum_trade', $sql, $start, $limit]);
	}

	public function count_log($viewtype, $uid, $tid, $pid, $ratestatus, $typestatus) {
		$sql = ($tid ? 'tl.tid=\''.dintval($tid).'\' AND '.($pid ? 'tl.pid=\''.dintval($pid).'\' AND ' : '') : '').
			('tl.'.($viewtype == 'sell' ? 'sellerid' : 'buyerid').'='.intval($uid)).' '.
			($ratestatus = $ratestatus ? 'AND (tl.ratestatus=0 OR tl.ratestatus='.intval($ratestatus).')' : '').
			($typestatus = $typestatus ? 'AND tl.status IN ('.dimplode($typestatus).')' : '');
		return DB::result_first('SELECT COUNT(*) FROM %t tl WHERE %i', [$this->_table, $sql]);
	}

}

