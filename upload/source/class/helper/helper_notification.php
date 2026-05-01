<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_notification {


	public static function notification_add($touid, $type, $note, $notevars = [], $system = 0, $category = -1) {
		global $_G;

		if(!($tospace = getuserbyuid($touid))) {
			return false;
		}

		$uid = $_G['uid'];
		$username = $_G['member']['username'];
		if(isset($notevars['from_uid']) && $notevars['from_uid'] != $uid) {
			$uid = $notevars['from_uid'];
			$member = table_common_member::t()->fetch($uid);
			if(!$member) {
				return false;
			}
			$username = $member['username'];
		}
		space_merge($tospace, 'field_home');
		$filter = empty($tospace['privacy']['filter_note']) ? [] : array_keys($tospace['privacy']['filter_note']);

		if($filter && (in_array($type.'|0', $filter) || in_array($type.'|'.$uid, $filter))) {
			return false;
		}
		if($category == -1) {
			$category = 0;
			$categoryname = '';
			if($type == 'follow' || $type == 'follower') {
				switch($type) {
					case 'follow' :
						$category = 5;
						break;
					case 'follower' :
						$category = 6;
						break;
				}
				$categoryname = $type;
			} else {
				foreach($_G['notice_structure'] as $key => $val) {
					if(in_array($type, $val)) {
						$category = match ($key) {
							'mypost' => 1,
							'interactive' => 2,
							'system' => 3,
							'manage' => 4,
							default => 0,
						};
						$categoryname = $key;
						break;
					}
				}
			}
		} else {
			$categoryname = match ($category) {
				1 => 'mypost',
				2 => 'interactive',
				3 => 'system',
				4 => 'manage',
				5 => 'follow',
				6 => 'follower',
				default => 'app',
			};
		}
		if($category == 0) {
			$categoryname = 'app';
		} elseif($category == 1 || $category == 2) {
			$categoryname = $type;
		}
		$notevars['actor'] = "<a href=\"home.php?mod=space&uid={$uid}\">{$username}</a>";

		$vars = explode(':', $note);
		if(count($vars) == 2) {
			$notestring = lang('plugin/'.$vars[0], $vars[1], $notevars);
		} else {
			$notestring = lang('notification', $note, $notevars);
		}

		$oldnote = [];
		if($notevars['from_id'] && $notevars['from_idtype']) {
			$oldnote = table_home_notification::t()->fetch_by_fromid_uid($notevars['from_id'], $notevars['from_idtype'], $touid);
		}
		if(empty($oldnote['from_num'])) $oldnote['from_num'] = 0;
		$notevars['from_num'] = $notevars['from_num'] ? $notevars['from_num'] : 1;
		$setarr = [
			'uid' => $touid,
			'type' => $type,
			'new' => 1,
			'authorid' => $uid,
			'author' => $_G['username'],
			'note' => $notestring,
			'dateline' => $_G['timestamp'],
			'from_id' => $notevars['from_id'] ?? 0,
			'from_idtype' => $notevars['from_idtype'] ?? '',
			'from_num' => ($oldnote['from_num'] + $notevars['from_num']),
			'category' => $category
		];
		if($system) {
			$setarr['authorid'] = 0;
			$setarr['author'] = '';
		}
		$pkId = 0;
		if($oldnote['id']) {
			table_home_notification::t()->update($oldnote['id'], $setarr);
			$pkId = $oldnote['id'];
		} else {
			$oldnote['new'] = 0;
			$pkId = table_home_notification::t()->insert($setarr, true);
		}
		$banType = ['task'];

		if(empty($oldnote['new'])) {
			table_common_member::t()->increase($touid, ['newprompt' => 1]);
			$newprompt = table_common_member_newprompt::t()->fetch($touid);
			if($newprompt) {
				$newprompt['data'] = dunserialize($newprompt['data']);
				if(!empty($newprompt['data'][$categoryname])) {
					$newprompt['data'][$categoryname] = intval($newprompt['data'][$categoryname]) + 1;
				} else {
					$newprompt['data'][$categoryname] = 1;
				}
				table_common_member_newprompt::t()->update($touid, ['data' => serialize($newprompt['data'])]);
			} else {
				table_common_member_newprompt::t()->insert_newprompt($touid, [$categoryname => 1]);
			}
			require_once libfile('function/mail');
			$mail_subject = lang('notification', 'mail_to_user');
			sendmail_touser($touid, $mail_subject, $notestring, $type);
		}

		if(!$system && $uid && $touid != $uid) {
			table_home_friend::t()->update_num_by_uid_fuid(1, $uid, $touid);
		}

		account_base::call('notificationAdd', [$touid, $note, $notestring]);
	}

	public static function manage_addnotify($type, $from_num = 0, $langvar = []) {
		global $_G;
		$notifyusers = dunserialize($_G['setting']['notifyusers']);
		$notifytypes = explode(',', $_G['setting']['adminnotifytypes']);
		$notifytypes = array_flip($notifytypes);
		$notearr = ['from_id' => 1, 'from_idtype' => $type, 'from_num' => $from_num];
		if($langvar) {
			$langkey = $langvar['langkey'];
			$notearr = array_merge($notearr, $langvar);
		} else {
			$langkey = 'manage_'.$type;
		}
		foreach($notifyusers as $uid => $user) {
			if($user['types'][$notifytypes[$type]]) {
				helper_notification::notification_add($uid, $type, $langkey, $notearr, 1, 4);
			}
		}
	}

	public static function get_categorynum($newprompt_data) {
		global $_G;
		$categorynum = [];
		if(empty($newprompt_data) || !is_array($newprompt_data)) {
			return [];
		}
		foreach($newprompt_data as $key => $val) {
			if(in_array($key, ['follow', 'follower'])) {
				continue;
			}
			if(in_array($key, $_G['notice_structure']['mypost'])) {
				$categorynum['mypost'] += $val;
			} elseif(in_array($key, $_G['notice_structure']['interactive'])) {
				$categorynum['interactive'] += $val;
			} else {
				$categorynum[$key] = $val;
			}
		}
		return $categorynum;
	}

	public static function update_newprompt($uid, $type) {
		global $_G;
		if($_G['member']['newprompt_num']) {
			$tmpprompt = $_G['member']['newprompt_num'];
			$num = 0;
			$updateprompt = 0;
			if(!$type) {
				$tmpprompt = [];
			} elseif(!empty($tmpprompt[$type])) {
				unset($tmpprompt[$type]);
				$updateprompt = true;
			}
			foreach($tmpprompt as $key => $val) {
				$num += $val;
			}
			if($num) {
				if($updateprompt) {
					table_common_member_newprompt::t()->update($uid, ['data' => serialize($tmpprompt)]);
					table_common_member::t()->update($uid, ['newprompt' => $num]);
				}
			} else {
				table_common_member_newprompt::t()->delete($_G['uid']);
				table_common_member::t()->update($_G['uid'], ['newprompt' => 0]);
			}
		}
	}
}

