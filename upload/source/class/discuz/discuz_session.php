<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class discuz_session {

	public $sid = null;
	public $var;
	public $isnew = false;
	private $newguest = ['sid' => 0, 'ip' => '',
		'uid' => 0, 'username' => '', 'groupid' => 7, 'invisible' => 0, 'action' => 0,
		'lastactivity' => 0, 'fid' => 0, 'tid' => 0, 'lastolupdate' => 0];

	private $old = ['sid' => '', 'ip' => '', 'uid' => 0];

	private $table;

	public function __construct($sid = '', $ip = '', $uid = 0) {
		$this->old = ['sid' => $sid, 'ip' => $ip, 'uid' => $uid];
		$this->var = $this->newguest;

		$enable_mem = !C::memory()->gotcluster && C::memory()->gotset &&
			C::memory()->gothash && C::memory()->goteval && C::memory()->gotsortedset;
		if($enable_mem) {
			$this->table = new memory_common_session();
		} else {
			$this->table = C::t('common_session');
		}

		if(!empty($ip)) {
			$this->init($sid, $ip, $uid);
		}
	}

	public function set($key, $value) {
		if(isset($this->newguest[$key])) {
			$this->var[$key] = $value;
		}
	}

	public function get($key) {
		if(isset($this->newguest[$key])) {
			return $this->var[$key];
		}
	}

	public function init($sid, $ip, $uid) {
		$this->old = ['sid' => $sid, 'ip' => $ip, 'uid' => $uid];
		$session = [];
		if($sid) {
			$session = $this->table->fetch($sid, $ip, $uid);
		}

		if(empty($session) || $session['uid'] != $uid) {
			$session = $this->create($ip, $uid);
		}

		$this->var = $session;
		$this->sid = $session['sid'];
	}

	public function create($ip, $uid) {

		$this->isnew = true;
		$this->var = $this->newguest;
		$this->set('sid', random(6));
		$this->set('uid', $uid);
		$this->set('ip', $ip);
		$uid && $this->set('invisible', getuserprofile('invisible'));
		$this->set('lastactivity', time());
		$this->sid = $this->var['sid'];

		return $this->var;
	}

	public function delete() {

		return $this->table->delete_by_session($this->var, getglobal('setting/onlinehold'), 60);

	}

	public function update() {
		if($this->sid !== null) {

			if($this->isnew) {
				$this->delete();
				$this->table->insert($this->var, false, false, true);
			} else {
				$this->table->update($this->var['sid'], $this->var);
			}
			setglobal('sessoin', $this->var);
			dsetcookie('sid', $this->sid, 86400);
		}
	}

	public function count($type = 0) {
		return $this->table->count($type);
	}

	public function fetch_member($ismember = 0, $invisible = 0, $start = 0, $limit = 0) {
		return $this->table->fetch_member($ismember, $invisible, $start, $limit);
	}

	public function count_invisible($type = 1) {
		return $this->table->count_invisible($type);
	}

	public function update_max_rows($max_rows) {
		return $this->table->update_max_rows($max_rows);
	}

	public function clear() {
		return $this->table->clear();
	}

	public function count_by_fid($fid) {
		return $this->table->count_by_fid($fid);
	}

	public function fetch_all_by_fid($fid, $limit = 0) {
		$data = [];
		if(!($fid = dintval($fid))) {
			return $data;
		}
		$onlinelist = getglobal('cache/onlinelist');
		foreach($this->table->fetch_all_by_fid($fid, $limit) as $online) {
			if($online['uid']) {
				$online['icon'] = $onlinelist[$online['groupid']] ?? $onlinelist[0];
			} else {
				$online['icon'] = $onlinelist[7];
				$online['username'] = $onlinelist['guest'];
			}
			$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
			$data[$online['uid']] = $online;
		}
		return $data;
	}

	public function fetch_by_uid($uid) {
		return $this->table->fetch_by_uid($uid);
	}

	public function fetch_all_by_uid($uids, $start = 0, $limit = 0) {
		return $this->table->fetch_all_by_uid($uids, $start, $limit);
	}

	public function update_by_uid($uid, $data) {
		return $this->table->update_by_uid($uid, $data);
	}

	public function count_by_ip($ip) {
		return $this->table->count_by_ip($ip);
	}

	public function fetch_all_by_ip($ip, $start = 0, $limit = 0) {
		return $this->table->fetch_all_by_ip($ip, $start, $limit);
	}

	public static function updatesession() {
		static $updated = false;
		if(!$updated) {
			global $_G;
			$ulastactivity = 0;
			if($_G['uid']) {
				if($_G['cookie']['ulastactivity']) {
					$ulastactivity = authcode($_G['cookie']['ulastactivity'], 'DECODE');
				} else {
					$ulastactivity = getuserprofile('lastactivity');
					dsetcookie('ulastactivity', authcode($ulastactivity, 'ENCODE'), 31536000);
				}
			}
			$ulastactivity = (int)$ulastactivity;
			$oltimespan = (int)$_G['setting']['oltimespan'];
			$lastolupdate = (int)C::app()->session->var['lastolupdate'];
			if($_G['uid'] && $oltimespan && (int)TIMESTAMP - ($lastolupdate ? $lastolupdate : $ulastactivity) > $oltimespan * 60) {
				$isinsert = false;
				if(C::app()->session->isnew) {
					$oldata = table_common_onlinetime::t()->fetch($_G['uid']);
					if(empty($oldata)) {
						$isinsert = true;
					} else if(TIMESTAMP - $oldata['lastupdate'] > $oltimespan * 60) {
						table_common_onlinetime::t()->update_onlinetime($_G['uid'], $oltimespan, $oltimespan, TIMESTAMP);
					}
				} else {
					$isinsert = !table_common_onlinetime::t()->update_onlinetime($_G['uid'], $oltimespan, $oltimespan, TIMESTAMP);
				}
				if($isinsert) {
					table_common_onlinetime::t()->insert([
						'uid' => $_G['uid'],
						'thismonth' => $oltimespan,
						'total' => $oltimespan,
						'lastupdate' => TIMESTAMP,
					]);
				}
				C::app()->session->set('lastolupdate', TIMESTAMP);
			}
			foreach(C::app()->session->var as $k => $v) {
				if(isset($_G['member'][$k]) && $k != 'lastactivity') {
					C::app()->session->set($k, $_G['member'][$k]);
				}
			}

			foreach($_G['action'] as $k => $v) {
				C::app()->session->set($k, $v);
			}

			C::app()->session->update();

			if($_G['uid'] && TIMESTAMP - $ulastactivity > 21600) {
				if($oltimespan && TIMESTAMP - $ulastactivity > 43200) {
					$onlinetime = table_common_onlinetime::t()->fetch($_G['uid']);
					table_common_member_count::t()->update($_G['uid'], ['oltime' => round(intval($onlinetime['total']) / 60)]);
				}
				dsetcookie('ulastactivity', authcode(TIMESTAMP, 'ENCODE'), 31536000);
				table_common_member_status::t()->update($_G['uid'], ['lastip' => $_G['clientip'], 'port' => $_G['remoteport'], 'lastactivity' => TIMESTAMP, 'lastvisit' => TIMESTAMP]);
			}
			$updated = true;
		}
		return $updated;
	}
}

