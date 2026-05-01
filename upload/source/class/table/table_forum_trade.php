<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_trade extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_trade';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_thread_goods($tid, $pid = 0) {
		$pidsql = $pid ? ' AND '.DB::field('pid', $pid) : '';
		return DB::fetch_all("SELECT * FROM %t WHERE tid=%d $pidsql ORDER BY displayorder", [$this->_table, $tid]);
	}

	public function fetch_counter_thread_goods($tid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE tid=%d', [$this->_table, $tid]);
	}

	public function fetch_all_for_seller($sellerid, $limit = 10, $tid = 0) {
		$tidsql = $tid ? ' AND '.DB::field('tid', $tid) : '';
		return DB::fetch_all("SELECT * FROM %t WHERE sellerid=%d $tidsql ORDER BY displayorder DESC LIMIT %d", [$this->_table, $sellerid, $limit]);
	}

	public function fetch_first_goods($tid) {
		return DB::fetch_first('SELECT * FROM %t WHERE tid=%d ORDER BY displayorder DESC LIMIT 1', [$this->_table, $tid]);
	}

	public function fetch_goods($tid, $pid, $orderby = '', $ascdesc = 'asc', $start = 0, $limit = 0) {
		if(empty($pid)) {
			return [];
		}
		if($tid) {
			$tidsql = DB::field('tid', $tid).' AND ';
		}
		if($orderby) {
			$ordersql = ' ORDER BY '.DB::order($orderby, $ascdesc);
		}
		return DB::fetch_first("SELECT * FROM %t WHERE $tidsql ".DB::field('pid', $pid).$ordersql.DB::limit($start, $limit), [$this->_table]);
	}

	public function fetch_all_statvars($fieldname, $limit = 10) {
		if(empty($fieldname)) {
			return [];
		}
		return DB::fetch_all('SELECT subject, tid, pid, seller, sellerid, SUM(%s) as %s
		FROM '.DB::table('forum_trade').'
		WHERE %s>0
		GROUP BY sellerid
		ORDER BY %s DESC '.DB::limit($limit), [$fieldname, $fieldname, $fieldname]);
	}

	public function update_closed($expiration) {
		DB::query("UPDATE %t SET closed='1' WHERE expiration>0 AND expiration<%d", [$this->_table, $expiration]);
	}

	public function check_goods($pid) {
		return DB::result_first('SELECT count(*) FROM %t WHERE pid=%d', [$this->_table, $pid]);
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			return $this->update_trade($val, $data, $unbuffered);
		}
	}

	public function update_trade($tid, $pid, $data) {
		if(empty($data) || !is_array($data)) {
			return false;
		}
		DB::update('forum_trade', $data, ['tid' => $tid, 'pid' => $pid]);
	}

	public function update_counter($tid, $pid, $items, $price, $credit, $amount = 0) {
		DB::query('UPDATE %t SET totalitems=totalitems+\'%d\', tradesum=tradesum+\'%d\', credittradesum=credittradesum+\'%d\', amount=amount+\'%d\' WHERE tid=%d AND pid=%d', [$this->_table, $items, $price, $credit, $amount, $tid, $pid]);
	}

	public function delete_by_id_idtype($ids, $idtype) {
		if(empty($ids) || empty($idtype)) {
			return false;
		}
		DB::delete($this->_table, DB::field($idtype, $ids));
	}

	public function fetch_all_for_search($digestltd, $fids, $topltd, $sqlsrch, $start = 0, $limit = 0) {
		return DB::fetch_all('SELECT tr.tid, tr.pid, t.closed FROM '.DB::table('forum_trade').' tr INNER JOIN '.DB::table('forum_thread')." t ON tr.tid=t.tid AND $digestltd t.".DB::field('fid', $fids)." $topltd WHERE$sqlsrch ORDER BY tr.pid DESC".DB::limit($start, $limit));
	}

	public function fetch_all_for_space($wheresql, $ordersql, $count = 0, $start = 0, $limit = 0) {
		if(empty($wheresql)) {
			return [];
		}
		if($count) {
			return DB::result_first('SELECT COUNT(*) FROM '.DB::table('forum_trade')." t WHERE $wheresql");
		}
		if($ordersql && is_string($ordersql)) {
			$ordersql = ' ORDER BY '.$ordersql;
		}
		return DB::fetch_all('SELECT t.* FROM '.DB::table('forum_trade').' t
				INNER JOIN '.DB::table('forum_thread')." th ON t.tid=th.tid AND th.displayorder>='0'
				WHERE $wheresql $ordersql ".DB::limit($start, $limit));
	}
}

