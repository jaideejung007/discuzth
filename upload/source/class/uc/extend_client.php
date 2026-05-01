<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

(defined('IN_UC') || defined('IN_API')) or exit('Access denied');

if(!defined('API_RETURN_SUCCEED')) {
	define('API_RETURN_SUCCEED', '1');
	define('API_RETURN_FAILED', '-1');
	define('API_RETURN_FORBIDDEN', '-2');
}

class uc_note_handler {

	public static function deleteuser($get, $post) {
		global $_G;
		$uids = str_replace("'", '', stripslashes($get['ids']));
		$ids = [];
		$ids = array_keys(table_common_member::t()->fetch_all($uids));
		require_once DISCUZ_ROOT.'./source/function/function_delete.php';
		$ids && deletemember($ids);

		return API_RETURN_SUCCEED;
	}

	public static function renameuser($get, $post) {
		global $_G;
		$len = strlen($get['newusername']);
		if($len > 22 || $len < 3 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&\(\)']/is", $get['newusername'])) {
			return API_RETURN_FAILED;
		}

		$tables = [
			'common_block' => ['id' => 'uid', 'name' => 'username'],
			'common_invite' => ['id' => 'fuid', 'name' => 'fusername'],
			'common_member_verify_info' => ['id' => 'uid', 'name' => 'username'],
			'common_mytask' => ['id' => 'uid', 'name' => 'username'],
			'common_report' => ['id' => 'uid', 'name' => 'username'],

			'forum_thread' => ['id' => 'authorid', 'name' => 'author'],
			'forum_activityapply' => ['id' => 'uid', 'name' => 'username'],
			'forum_groupuser' => ['id' => 'uid', 'name' => 'username'],
			'forum_pollvoter' => ['id' => 'uid', 'name' => 'username'],
			'forum_post' => ['id' => 'authorid', 'name' => 'author'],
			'forum_postcomment' => ['id' => 'authorid', 'name' => 'author'],
			'forum_ratelog' => ['id' => 'uid', 'name' => 'username'],

			'home_album' => ['id' => 'uid', 'name' => 'username'],
			'home_blog' => ['id' => 'uid', 'name' => 'username'],
			'home_clickuser' => ['id' => 'uid', 'name' => 'username'],
			'home_docomment' => ['id' => 'uid', 'name' => 'username'],
			'home_doing' => ['id' => 'uid', 'name' => 'username'],
			'home_feed' => ['id' => 'uid', 'name' => 'username'],
			'home_friend' => ['id' => 'fuid', 'name' => 'fusername'],
			'home_friend_request' => ['id' => 'fuid', 'name' => 'fusername'],
			'home_notification' => ['id' => 'authorid', 'name' => 'author'],
			'home_pic' => ['id' => 'uid', 'name' => 'username'],
			'home_poke' => ['id' => 'fromuid', 'name' => 'fromusername'],
			'home_share' => ['id' => 'uid', 'name' => 'username'],
			'home_show' => ['id' => 'uid', 'name' => 'username'],
			'home_specialuser' => ['id' => 'uid', 'name' => 'username'],
			'home_visitor' => ['id' => 'vuid', 'name' => 'vusername'],

			'portal_article_title' => ['id' => 'uid', 'name' => 'username'],
			'portal_comment' => ['id' => 'uid', 'name' => 'username'],
			'portal_topic' => ['id' => 'uid', 'name' => 'username'],
			'portal_topic_pic' => ['id' => 'uid', 'name' => 'username'],
		];

		if(!table_common_member::t()->update($get['uid'], ['username' => $get['newusername']]) && isset($_G['setting']['membersplit'])) {
			table_common_member_archive::t()->update($get['uid'], ['username' => $get['newusername']]);
		}

		loadcache('posttableids');
		if($_G['cache']['posttableids']) {
			$posttableids = is_array($_G['cache']['posttableids']) ? $_G['cache']['posttableids'] : [0];
			foreach($posttableids as $tableid) {
				$tables[getposttable($tableid)] = ['id' => 'authorid', 'name' => 'author'];
			}
		}

		foreach($tables as $table => $conf) {
			DB::query('UPDATE '.DB::table($table)." SET `{$conf['name']}`='{$get['newusername']}' WHERE `{$conf['id']}`='{$get['uid']}'");
		}
		return API_RETURN_SUCCEED;
	}

	public static function updatepw($get, $post) {
		global $_G;
		$username = $get['username'];
		$newpw = md5(time().rand(100000, 999999));
		$uid = 0;
		if(($uid = table_common_member::t()->fetch_uid_by_username($username))) {
			$ext = '';
		} elseif(($uid = table_common_member_archive::t()->fetch_uid_by_username($username))) {
			$ext = '_archive';
		}
		if($uid) {
			C::t('common_member'.$ext)->update($uid, ['password' => $newpw]);
		}

		return API_RETURN_SUCCEED;
	}

	public static function checkavatar($get, $post) {
		global $_G;
		$uid = $get['uid'];
		$size = $get['size'];
		$type = $get['type'];
		$size = in_array($size, ['big', 'middle', 'small']) ? $size : 'middle';
		$uid = abs(intval($uid));
		$uid = sprintf('%09d', $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$typeadd = $type == 'real' ? '_real' : '';
		if(!UC_AVTPATH) {
			$avtpath = './data/avatar/';
		} else {
			$avtpath = str_replace('..', '', UC_AVTPATH);
		}
		$avatarfile = realpath(DISCUZ_ROOT.$avtpath).'/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
		if(file_exists($avatarfile)) {
			return API_RETURN_SUCCEED;
		} else {
			return API_RETURN_FAILED;
		}
	}

	public static function loadavatarpath() {
		global $_G;
		if(!defined('UC_DELAVTDIR')) {
			define('UC_DELAVTDIR', DISCUZ_ROOT.$_G['setting']['avatarpath'].'/');
		}
	}
}