<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_ratelog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_ratelog';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_by_uid_pid($uid, $pid) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND pid=%d LIMIT 1', [$this->_table, $uid, $pid]);
	}

	public function fetch_all_by_pid($pid, $sort = 'DESC') {
		if(is_array($pid)) {
			$pid = array_map('intval', (array)$pid);
		}
		$wheresql = is_array($pid) ? 'pid IN(%n)' : 'pid=%d';
		return DB::fetch_all("SELECT * FROM %t WHERE $wheresql ORDER BY dateline $sort", [$this->_table, $pid]);
	}

	public function fetch_all_sum_score($uid, $dateline) {
		return DB::fetch_all('SELECT extcredits, SUM(ABS(score)) AS todayrate FROM %t WHERE uid=%d AND dateline>=%d GROUP BY extcredits', [$this->_table, $uid, $dateline]);
	}

	public function count_by_uid_pid($uid, $pid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND pid=%d LIMIT 1', [$this->_table, $uid, $pid]);
	}

	public function delete_by_pid_uid_extcredits_dateline($pid = null, $uid = null, $extcredits = null, $dateline = null) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($pid !== null) {
			$parameter[] = $pid;
			$wherearr[] = 'pid=%d';
		}
		if($uid !== null) {
			$parameter[] = $uid;
			$wherearr[] = 'uid=%d';
		}
		if($extcredits !== null) {
			$parameter[] = $extcredits;
			$wherearr[] = 'extcredits=%d';
		}
		if($dateline !== null) {
			$parameter[] = $dateline;
			$wherearr[] = 'dateline=%d';
		}
		if(!empty($wherearr)) {
			$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
			return DB::query("DELETE FROM %t $wheresql", $parameter, true, true);
		}
		return false;
	}

	public function fetch_postrate_by_pid($pids, $postlist, $postcache, $ratelogrecord) {
		$pids = array_map('intval', (array)$pids);
		$query = DB::query('SELECT * FROM '.DB::table('forum_ratelog').' WHERE pid IN ('.dimplode($pids).') ORDER BY dateline DESC');
		$ratelogs = [];
		while($ratelog = DB::fetch($query)) {
			if(!is_array($postlist[$ratelog['pid']]['ratelog']) || count($postlist[$ratelog['pid']]['ratelog']) < $ratelogrecord) {
				$ratelogs[$ratelog['pid']][$ratelog['uid']]['username'] = $ratelog['username'];
				$ratelogs[$ratelog['pid']][$ratelog['uid']]['score'][$ratelog['extcredits']] += $ratelog['score'];
				empty($ratelogs[$ratelog['pid']][$ratelog['uid']]['reason']) && $ratelogs[$ratelog['pid']][$ratelog['uid']]['reason'] = dhtmlspecialchars($ratelog['reason']);
				$postlist[$ratelog['pid']]['ratelog'][$ratelog['uid']] = $ratelogs[$ratelog['pid']][$ratelog['uid']];
			}
			$postcache[$ratelog['pid']]['rate']['ratelogs'] = $postlist[$ratelog['pid']]['ratelog'];
			$postcache[$ratelog['pid']]['rate']['extcredits'][$ratelog['extcredits']] = $postlist[$ratelog['pid']]['ratelogextcredits'][$ratelog['extcredits']] += $ratelog['score'];
			if(!$postlist[$ratelog['pid']]['totalrate'] || !in_array($ratelog['uid'], $postlist[$ratelog['pid']]['totalrate'])) {
				$postlist[$ratelog['pid']]['totalrate'][] = $ratelog['uid'];
			}
			$postcache[$ratelog['pid']]['rate']['totalrate'] = $postlist[$ratelog['pid']]['totalrate'];
		}
		return [$ratelogs, $postlist, $postcache];
	}

}

