<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member extends discuz_table_archive {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member';
		$this->_pk = 'uid';
		$this->_pre_cache_key = 'common_member_';

		parent::__construct();
	}

	public function update_credits($uid, $credits) {
		if($uid) {
			$data = ['credits' => intval($credits)];
			DB::update($this->_table, $data, ['uid' => intval($uid)], 'UNBUFFERED');
			$this->update_cache($uid, $data);
		}
	}

	public function update_by_groupid($groupid, $data) {
		$uids = [];
		$groupid = dintval($groupid, true);
		if($groupid && $this->_allowmem) {
			$uids = array_keys($this->fetch_all_by_groupid($groupid));
		}
		if($groupid && !empty($data) && is_array($data)) {
			DB::update($this->_table, $data, DB::field('groupid', $groupid), 'UNBUFFERED');
		}
		if($uids) {
			$this->update_cache($uids, $data);
		}
	}

	public function update_username($uid, $username) {
		if($uid) {
			$data = ['username' => $username];
			DB::update($this->_table, $data, ['uid' => intval($uid)], 'UNBUFFERED');
			$this->update_cache($uid, $data);
		}
	}

	public function increase($uids, $setarr) {
		$uids = dintval((array)$uids, true);
		$sql = [];
		$allowkey = ['credits', 'newpm', 'newprompt'];
		foreach($setarr as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE '.DB::table($this->_table).' SET '.implode(',', $sql).' WHERE uid IN ('.dimplode($uids).')', 'UNBUFFERED');
			$this->increase_cache($uids, $setarr);
		}
	}

	public function fetch_by_username($username, $fetch_archive = 0) {
		$user = [];
		if($username) {
			$user = DB::fetch_first('SELECT * FROM %t WHERE username=%s', [$this->_table, $username]);
			if(isset($this->membersplit) && $fetch_archive && empty($user)) {
				$user = C::t($this->_table.'_archive')->fetch_by_username($username, 0);
			}
		}
		return $user;
	}

	public function fetch_by_loginname($loginname, $fetch_archive = 0) {
		$user = [];
		if($loginname) {
			$user = DB::fetch_first('SELECT * FROM %t WHERE loginname=%s', [$this->_table, $loginname]);
			if(isset($this->membersplit) && $fetch_archive && empty($user)) {
				$user = C::t($this->_table.'_archive')->fetch_by_loginname($username, 0);
			}
		}
		return $user;
	}

	public function fetch_all_by_username($usernames, $fetch_archive = 1) {
		$users = [];
		if(!empty($usernames)) {
			$users = DB::fetch_all('SELECT * FROM %t WHERE username IN (%n)', [$this->_table, (array)$usernames], 'username');
			if(isset($this->membersplit) && $fetch_archive && count($usernames) !== count($users)) {
				$users += C::t($this->_table.'_archive')->fetch_all_by_username($usernames, 0);
			}
		}
		return $users;
	}

	public function fetch_all_by_loginname($loginnames, $fetch_archive = 1) {
		$users = [];
		if(!empty($loginnames)) {
			$users = DB::fetch_all('SELECT * FROM %t WHERE loginname IN (%n)', [$this->_table, (array)$loginnames], 'loginname');
			if(isset($this->membersplit) && $fetch_archive && count($loginnames) !== count($users)) {
				$users += C::t($this->_table.'_archive')->fetch_all_by_loginname($loginnames, 0);
			}
		}
		return $users;
	}

	public function fetch_uid_by_username($username, $fetch_archive = 0) {
		$uid = 0;
		if($username) {
			$uid = DB::result_first('SELECT uid FROM %t WHERE username=%s', [$this->_table, $username]);
			if(isset($this->membersplit) && $fetch_archive && empty($uid)) {
				$uid = C::t($this->_table.'_archive')->fetch_uid_by_username($username, 0);
			}
		}
		if(!$uid) {
			$his = table_common_member_username_history::t()->fetch($username);
			if($his) {
				$uid = $his['uid'];
			}
		}
		return $uid;
	}

	public function fetch_uid_by_loginname($loginname, $fetch_archive = 0) {
		$uid = 0;
		if($loginname) {
			$uid = DB::result_first('SELECT uid FROM %t WHERE loginname=%s', [$this->_table, $loginname]);
			if(isset($this->membersplit) && $fetch_archive && empty($uid)) {
				$uid = C::t($this->_table.'_archive')->fetch_uid_by_loginname($loginname, 0);
			}
		}
		return $uid;
	}

	public function fetch_all_uid_by_username($usernames, $fetch_archive = 1) {
		$uids = [];
		if($usernames) {
			foreach($this->fetch_all_by_username($usernames, $fetch_archive) as $username => $value) {
				$uids[$username] = $value['uid'];
			}
		}
		return $uids;
	}

	public function fetch_all_by_secmobile($secmobicc, $secmobile, $fetch_archive = 1) {
		$users = [];
		$users = DB::fetch_all('SELECT * FROM %t WHERE secmobicc = %d AND secmobile = %d ORDER BY regdate DESC', [$this->_table, $secmobicc, $secmobile]);
		if(isset($this->membersplit) && $fetch_archive) {
			$users += C::t($this->_table.'_archive')->fetch_all_by_secmobile($secmobicc, $secmobile, 0);
		}
		return $users;
	}

	public function fetch_all_by_adminid($adminids, $fetch_archive = 1) {
		$users = [];
		$adminids = dintval((array)$adminids, true);
		if($adminids) {
			$users = DB::fetch_all('SELECT * FROM %t WHERE adminid IN (%n) ORDER BY adminid, uid', [$this->_table, (array)$adminids], $this->_pk);
			if(isset($this->membersplit) && $fetch_archive) {
				$users += C::t($this->_table.'_archive')->fetch_all_by_adminid($adminids, 0);
			}
		}
		return $users;
	}

	public function fetch_all_username_by_uid($uids) {
		$users = [];
		if(($uids = dintval($uids, true))) {
			foreach($this->fetch_all($uids) as $uid => $value) {
				$users[$uid] = $value['username'];
			}
		}
		return $users;
	}

	public function fetch_all_by_uid($uids, $fields = []) {
		$users = [];
		if(($uids = dintval($uids, true))) {
			foreach($this->fetch_all($uids) as $uid => $value) {
				foreach($fields as $field) {
					$users[$uid][$field] = $value[$field];
				}
			}
		}
		return $users;
	}

	public function count_by_groupid($groupid) {
		return $groupid ? DB::result_first('SELECT COUNT(*) FROM %t WHERE '.DB::field('groupid', $groupid), [$this->_table]) : 0;
	}

	public function fetch_all_by_groupid($groupid, $start = 0, $limit = 0) {
		$users = [];
		if(($groupid = dintval($groupid, true))) {
			$users = DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('groupid', $groupid).' '.DB::limit($start, $limit), null, 'uid');
		}
		return $users;
	}

	public function fetch_all_groupid() {
		return DB::fetch_all('SELECT DISTINCT(groupid) FROM '.DB::table($this->_table), null, 'groupid');
	}

	public function fetch_all_by_allowadmincp($val, $glue = '=') {
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('allowadmincp', intval($val), $glue), NULL, 'uid');
	}

	public function update_admincp_manage($uids) {
		if(($uids = dintval($uids, true))) {
			$data = DB::query('UPDATE '.DB::table($this->_table).' SET allowadmincp=allowadmincp | 1 WHERE uid IN ('.dimplode($uids).')');
			$this->reset_cache($uids);
			return $data;
		}
		return false;
	}

	public function clean_admincp_manage($uids) {
		if(($uids = dintval($uids, true))) {
			$data = DB::query('UPDATE '.DB::table($this->_table).' SET allowadmincp=allowadmincp & 0xFE WHERE uid IN ('.dimplode($uids).')');
			$this->reset_cache($uids);
			return $data;
		}
		return false;
	}

	public function fetch_all_ban_by_groupexpiry($timestamp) {
		return ($timestamp = intval($timestamp)) ? DB::fetch_all('SELECT uid, groupid, credits FROM '.DB::table($this->_table)." WHERE groupid IN ('4', '5') AND groupexpiry>'0' AND groupexpiry<'$timestamp'", [], 'uid') : [];
	}

	public function count($fetch_archive = 1) {
		$count = DB::result_first('SELECT COUNT(*) FROM %t', [$this->_table]);
		if(isset($this->membersplit) && $fetch_archive) {
			$count += C::t($this->_table.'_archive')->count(0);
		}
		return $count;
	}

	public function fetch_by_email($email, $fetch_archive = 0) {
		$user = [];
		if($email) {
			$user = DB::fetch_first('SELECT * FROM %t WHERE email=%s', [$this->_table, $email]);
			if(isset($this->membersplit) && $fetch_archive && empty($user)) {
				$user = C::t($this->_table.'_archive')->fetch_by_email($email, 0);
			}
		}
		return $user;
	}

	public function fetch_all_by_email($emails, $fetch_archive = 1) {
		$users = [];
		if(!empty($emails)) {
			$users = DB::fetch_all('SELECT * FROM %t WHERE %i', [$this->_table, DB::field('email', $emails)], 'email');
			if(isset($this->membersplit) && $fetch_archive && count($emails) !== count($users)) {
				$users += C::t($this->_table.'_archive')->fetch_all_by_email($emails, 0);
			}
		}
		return $users;
	}

	public function count_by_email($email, $fetch_archive = 0) {
		$count = 0;
		if($email) {
			$count = DB::result_first('SELECT COUNT(*) FROM %t WHERE email=%s', [$this->_table, $email]);
			if(isset($this->membersplit) && $fetch_archive) {
				$count += C::t($this->_table.'_archive')->count_by_email($email, 0);
			}
		}
		return $count;
	}

	public function fetch_all_by_like_username($username, $start = 0, $limit = 0) {
		$data = [];
		if($username) {
			$data = DB::fetch_all('SELECT * FROM %t WHERE username LIKE %s'.DB::limit($start, $limit), [$this->_table, stripsearchkey($username).'%'], 'uid');
		}
		return $data;
	}

	public function count_by_like_username($username) {
		return !empty($username) ? DB::result_first('SELECT COUNT(*) FROM %t WHERE username LIKE %s', [$this->_table, stripsearchkey($username).'%']) : 0;
	}


	public function fetch_runtime() {
		return DB::result_first('SELECT (MAX(regdate)-MIN(regdate))/86400 AS runtime FROM '.DB::table($this->_table));
	}

	public function count_admins() {
		return DB::result_first('SELECT COUNT(*) FROM '.DB::table($this->_table)." WHERE adminid<>'0' AND adminid<>'-1'");
	}

	public function count_by_regdate($timestamp) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE regdate>%d', [$this->_table, $timestamp]);
	}

	public function fetch_all_stat_memberlist($username, $orderby = '', $sort = '', $start = 0, $limit = 0) {
		$orderby = in_array($orderby, ['uid', 'credits', 'regdate', 'gender', 'username', 'posts', 'lastvisit'], true) ? $orderby : 'uid';
		$sql = '';

		$sql = !empty($username) ? " WHERE username LIKE '".addslashes(stripsearchkey($username))."%'" : '';

		$memberlist = [];
		$query = DB::query('SELECT m.uid, m.username, mp.gender, m.email, m.regdate, ms.lastvisit, mc.posts, m.credits
			FROM '.DB::table($this->_table).' m
			LEFT JOIN '.DB::table('common_member_profile').' mp ON mp.uid=m.uid
			LEFT JOIN '.DB::table('common_member_status').' ms ON ms.uid=m.uid
			LEFT JOIN '.DB::table('common_member_count')." mc ON mc.uid=m.uid
			$sql ORDER BY ".DB::order($orderby, $sort).DB::limit($start, $limit));
		while($member = DB::fetch($query)) {
			$member['usernameenc'] = rawurlencode($member['username']);
			$member['regdate'] = dgmdate($member['regdate']);
			$member['lastvisit'] = dgmdate($member['lastvisit']);
			$memberlist[$member['uid']] = $member;
		}
		return $memberlist;
	}

	public function delete_no_validate($uids) {
		if(($uids = dintval($uids, true))) {
			$delnum = $this->delete($uids);
			table_common_member_field_forum::t()->delete($uids);
			table_common_member_field_home::t()->delete($uids);
			table_common_member_status::t()->delete($uids);
			table_common_member_count::t()->delete($uids);
			table_common_member_profile::t()->delete($uids);
			table_common_member_validate::t()->delete($uids);
			table_common_member_account::t()->delete_by_uid($uids);
			return $delnum;
		}
		return false;
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false, $null1 = null, $null2 = null, $null3 = null, $null4 = 0, $null5 = 0, $secmobicc = '', $secmobile = '', $secmobilestatus = 0) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::insert($data, $return_insert_id, $replace, $silent);
		} else {
			if($return_insert_id === false || $replace === false || $silent === false || $null1 === null || $null2 === null || $null3 === null) {
				throw new Exception("Invalid Use C:t('common_member')->insert Function.");
			}
			return $this->insert_user($data, $return_insert_id, $replace, $silent, $null1, $null2, $null3, $null4, $null5, $secmobicc, $secmobile, $secmobilestatus);
		}
	}

	public function insert_user($uid, $username, $password, $email, $ip, $groupid, $extdata, $adminid = 0, $port = 0, $secmobicc = '', $secmobile = '', $secmobilestatus = 0) {
		if(($uid = dintval($uid))) {
			$credits = $extdata['credits'] ?? [];
			$profile = $extdata['profile'] ?? [];
			$profile['uid'] = $uid;
			$base = [
				'uid' => $uid,
				'loginname' => (string)$username,
				'username' => (string)$username,
				'password' => (string)$password,
				'email' => (string)$email,
				'secmobicc' => (string)$secmobicc,
				'secmobile' => (string)$secmobile,
				'secmobilestatus' => $secmobilestatus,
				'adminid' => intval($adminid),
				'groupid' => intval($groupid),
				'regdate' => TIMESTAMP,
				'emailstatus' => intval($extdata['emailstatus']),
				'credits' => dintval($credits[0]),
				'timeoffset' => 9999
			];
			$status = [
				'uid' => $uid,
				'regip' => (string)$ip,
				'lastip' => (string)$ip,
				'port' => (string)$port,
				'regport' => (string)$port,
				'lastvisit' => TIMESTAMP,
				'lastactivity' => TIMESTAMP,
				'lastpost' => 0,
				'lastsendmail' => 0
			];
			$count = [
				'uid' => $uid,
				'extcredits1' => dintval($credits[1]),
				'extcredits2' => dintval($credits[2]),
				'extcredits3' => dintval($credits[3]),
				'extcredits4' => dintval($credits[4]),
				'extcredits5' => dintval($credits[5]),
				'extcredits6' => dintval($credits[6]),
				'extcredits7' => dintval($credits[7]),
				'extcredits8' => dintval($credits[8])
			];
			$ext = ['uid' => $uid];
			parent::insert($base, false, true);
			table_common_member_status::t()->insert($status, false, true);
			table_common_member_count::t()->insert($count, false, true);
			table_common_member_profile::t()->insert($profile, false, true);
			table_common_member_field_forum::t()->insert($ext, false, true);
			table_common_member_field_home::t()->insert($ext, false, true);
		}
	}

	public function delete($val, $unbuffered = false, $fetch_archive = 0) {
		$ret = false;
		if(($val = dintval($val, true))) {
			$ret = parent::delete($val, $unbuffered, $fetch_archive);
			if($this->_allowmem) {
				$data = ($data = memory('get', 'deleteuids')) === false ? [] : $data;
				foreach((array)$val as $uid) {
					$data[$uid] = $uid;
				}
				memory('set', 'deleteuids', $data, 86400 * 2);
			}
		}
		return $ret;
	}

	public function count_zombie() {
		$dateline = TIMESTAMP - 31536000;
		return DB::result_first('SELECT count(*) FROM %t mc, %t ms WHERE mc.posts<5 AND ms.lastvisit<%d AND ms.uid=mc.uid', ['common_member_count', 'common_member_status', $dateline]);
	}

	public function split($splitnum, $iscron = false) {
		loadcache('membersplitdata');
		@set_time_limit(0);
		discuz_database_safecheck::setconfigstatus(0);
		$dateline = TIMESTAMP - 31536000;
		$temptablename = DB::table('common_member_temp___');
		if(!DB::fetch_first("SHOW TABLES LIKE '$temptablename'")) {
			$engine = strtolower(getglobal('config/db/common/engine')) !== 'innodb' ? 'MyISAM' : 'InnoDB';
			DB::query("CREATE TABLE $temptablename (`uid` int(10) NOT NULL DEFAULT 0,PRIMARY KEY (`uid`)) ENGINE=".$engine.';');
		}
		$splitnum = max(0, intval($splitnum));
		if(!DB::result_first('SELECT COUNT(*) FROM '.$temptablename)) {
			DB::query('INSERT INTO '.$temptablename.' (`uid`) SELECT ms.uid AS uid FROM %t mc, %t ms WHERE mc.posts<5 AND ms.lastvisit<%d AND mc.uid=ms.uid ORDER BY ms.uid DESC LIMIT %d', ['common_member_count', 'common_member_status', $dateline, $splitnum]);
		}

		if(DB::result_first('SELECT COUNT(*) FROM '.$temptablename) > 1) {


			if(!$iscron && getglobal('setting/memberspliting') === null) {
				$this->switch_keys('disable');
			}
			$uidlist = DB::fetch_all('SELECT uid FROM '.$temptablename.' ORDER BY uid DESC', null, 'uid');
			unset($uidlist[key($uidlist)]);
			$uids = dimplode(array_keys($uidlist));
			$movesql = 'REPLACE INTO %t SELECT * FROM %t WHERE uid IN ('.$uids.')';
			$deletesql = 'DELETE FROM %t WHERE uid IN ('.$uids.')';
			if(DB::query($movesql, ['common_member_archive', 'common_member'], false, true)) {
				DB::query($deletesql, ['common_member'], false, true);
			}
			if(DB::query($movesql, ['common_member_profile_archive', 'common_member_profile'], false, true)) {
				DB::query($deletesql, ['common_member_profile'], false, true);
			}
			if(DB::query($movesql, ['common_member_field_forum_archive', 'common_member_field_forum'], false, true)) {
				DB::query($deletesql, ['common_member_field_forum'], false, true);
			}
			if(DB::query($movesql, ['common_member_field_home_archive', 'common_member_field_home'], false, true)) {
				DB::query($deletesql, ['common_member_field_home'], false, true);
			}
			if(DB::query($movesql, ['common_member_status_archive', 'common_member_status'], false, true)) {
				DB::query($deletesql, ['common_member_status'], false, true);
			}
			if(DB::query($movesql, ['common_member_count_archive', 'common_member_count'], false, true)) {
				DB::query($deletesql, ['common_member_count'], false, true);
			}

			DB::query('DROP TABLE '.$temptablename);
			$membersplitdata = getglobal('cache/membersplitdata');
			$zombiecount = $membersplitdata['zombiecount'] - $splitnum;
			if($zombiecount < 0) {
				$zombiecount = 0;
			}
			savecache('membersplitdata', ['membercount' => $membersplitdata['membercount'], 'zombiecount' => $zombiecount, 'dateline' => TIMESTAMP]);
			table_common_setting::t()->delete('memberspliting');
			return true;
		} else {
			DB::query('DROP TABLE '.$temptablename);
			if(!$iscron) {
				$this->switch_keys('enable');
				table_common_member_profile::t()->optimize();
				table_common_member_field_forum::t()->optimize();
				table_common_member_field_home::t()->optimize();
			}
			return false;
		}
	}

	public function switch_keys($type) {
		if($type === 'disable') {
			$type = 'DISABLE';
			table_common_setting::t()->update_batch(['memberspliting' => 1, 'membersplit' => 1]);
		} else {
			$type = 'ENABLE';
			table_common_setting::t()->delete('memberspliting');
		}

		require_once libfile('function/cache');
		updatecache('setting');
	}

	public function count_by_credits($credits) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE credits>%d', [$this->_table, $credits]);
	}

	public function fetch_all_for_spacecp_search($wherearr, $fromarr, $start = 0, $limit = 100) {
		if(!$start && !$limit) {
			$start = 100;
		}
		if(!$wherearr) {
			$wherearr[] = '1';
		}
		if(!$fromarr) {
			$fromarr[] = DB::table($this->_table);
		}
		return DB::fetch_all('SELECT s.* FROM '.implode(',', $fromarr).' WHERE '.implode(' AND ', $wherearr).DB::limit($start, $limit));
	}

	public function fetch_all_girls_for_ranklist($offset = 0, $limit = 20, $orderby = 'ORDER BY s.unitprice DESC, s.credit DESC') {
		$members = [];
		$query = DB::query('SELECT m.uid, m.username, mc.*, mp.gender
			FROM '.DB::table('common_member').' m
			LEFT JOIN '.DB::table('home_show').' s ON s.uid=m.uid
			LEFT JOIN '.DB::table('common_member_profile').' mp ON mp.uid=m.uid
			LEFT JOIN '.DB::table('common_member_count')." mc ON mc.uid=m.uid
			WHERE mp.gender='2'
			ORDER BY $orderby
			LIMIT $offset, $limit");
		while($member = DB::fetch($query)) {
			$member['avatar'] = avatar($member['uid'], 'small');
			$members[] = $member;
		}
		return $members;
	}


	public function fetch_all_order_by_credit_for_ranklist($num, $orderby) {
		$data = [];
		if(!($num = intval($num))) {
			return $data;
		}
		if($orderby === 'all') {
			$sql = 'SELECT m.uid,m.username,m.groupid,m.credits,field.spacenote FROM '.DB::table('common_member').' m
				LEFT JOIN '.DB::table('common_member_field_home')." field ON field.uid=m.uid
				ORDER BY m.credits DESC LIMIT 0, $num";
		} else {
			$orderby = intval($orderby);
			$orderby = in_array($orderby, [1, 2, 3, 4, 5, 6, 7, 8]) ? $orderby : 1;
			$sql = "SELECT m.uid,m.username,m.groupid, mc.extcredits$orderby AS extcredits
				FROM ".DB::table('common_member').' m
				LEFT JOIN '.DB::table('common_member_count')." mc ON mc.uid=m.uid WHERE mc.extcredits$orderby>0
				ORDER BY extcredits$orderby DESC LIMIT 0, $num";
		}

		$query = DB::query($sql);
		while($result = DB::fetch($query)) {
			$data[] = $result;
		}

		return $data;

	}

	public function fetch_all_order_by_friendnum_for_ranklist($num) {

		$num = intval($num);
		$num = $num ? $num : 20;
		$data = $users = $oldorder = [];
		$query = DB::query('SELECT uid, friends FROM '.DB::table('common_member_count').' WHERE friends>0 ORDER BY friends DESC LIMIT '.$num);
		while($user = DB::fetch($query)) {
			$users[$user['uid']] = $user;
			$oldorder[] = $user['uid'];
		}
		$uids = array_keys($users);
		if($uids) {
			$query = DB::query('SELECT m.uid, m.username, m.groupid, field.spacenote
				FROM '.DB::table('common_member').' m
				LEFT JOIN '.DB::table('common_member_field_home').' field ON m.uid=field.uid
				WHERE m.uid IN ('.dimplode($uids).')');
			while($value = DB::fetch($query)) {
				$users[$value['uid']] = array_merge($users[$value['uid']], $value);
			}

			foreach($oldorder as $uid) {
				$data[] = $users[$uid];
			}

		}
		return $data;

	}

	public function max_uid() {
		return DB::result_first('SELECT MAX(uid) FROM %t', [$this->_table]);
	}

	public function range_by_uid($from, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid >= %d ORDER BY uid LIMIT %d', [$this->_table, $from, $limit], $this->_pk);
	}

	public function update_groupid_by_groupid($source, $target) {
		return DB::query('UPDATE %t SET groupid=%d WHERE adminid <= 0 AND groupid=%d', [$this->_table, $target, $source]);
	}

	public function fetch_all_logoff_expiry($timestamp) {
		$timestamp = intval($timestamp);
		$logoffs = DB::fetch_all('SELECT uid FROM '.DB::table($this->_table).' WHERE freeze=-2', [], 'uid');
		return DB::fetch_all('SELECT uid FROM '.DB::table('common_member_status').' WHERE uid IN ('.dimplode(array_keys($logoffs)).") AND lastactivity<'$timestamp'", [], 'uid');
	}

	public function fetch_all_protect_member() {
		global $_G;

		$uids = DB::fetch_all('SELECT uid FROM %t WHERE adminid=1 OR groupid=1', [$this->_table]);
		$return = array_column($uids, 'uid');
		if($_G['uid']) {
			$return[] = $_G['uid'];
		}

		return $return;
	}

}

