<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function getstr($string, $length = 0, $in_slashes = 0, $out_slashes = 0, $bbcode = 0, $html = 0) {
	global $_G;

	$string = trim($string);
	$sppos = strpos($string, chr(0).chr(0).chr(0));
	if($sppos !== false) {
		$string = substr($string, 0, $sppos);
	}
	if($in_slashes) {
		$string = dstripslashes($string);
	}
	$string = preg_replace('/\[hide=?\d*\](.*?)\[\/hide\]/is', '', $string);
	if($html < 0) {
		$string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
	} elseif($html == 0) {
		$string = dhtmlspecialchars($string);
	}

	if($length) {
		$string = cutstr($string, $length);
	}

	if($bbcode) {
		require_once DISCUZ_ROOT.'./source/class/class_bbcode.php';
		$bb = &bbcode::instance();
		$string = $bb->bbcode2html($string, $bbcode);
	}
	if($out_slashes) {
		$string = daddslashes($string);
	}
	return trim($string);
}

function obclean() {
	ob_end_clean();
	if(getglobal('config/output/gzip') && function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}
}

function dreaddir($dir, $extarr = []) {
	$dirs = [];
	if($dh = opendir($dir)) {
		while(($file = readdir($dh)) !== false) {
			if(!empty($extarr) && is_array($extarr)) {
				if(in_array(strtolower(fileext($file)), $extarr)) {
					$dirs[] = $file;
				}
			} else if($file != '.' && $file != '..') {
				$dirs[] = $file;
			}
		}
		closedir($dh);
	}
	return $dirs;
}

function url_implode($gets) {
	$arr = [];
	foreach($gets as $key => $value) {
		if($value) {
			$arr[] = $key.'='.urlencode($value);
		}
	}
	return implode('&', $arr);
}

function ckstart($start, $perpage) {
	global $_G;

	$_G['setting']['maxpage'] = $_G['setting']['maxpage'] ? $_G['setting']['maxpage'] : 100;
	$maxstart = $perpage * intval($_G['setting']['maxpage']);
	if($start < 0 || ($maxstart > 0 && $start >= $maxstart)) {
		showmessage('length_is_not_within_the_scope_of');
	}
}

function getspace($uid) {
	return getuserbyuid($uid);
}

function ckprivacy($key, $privace_type) {
	global $_G, $space;

	$var = "home_ckprivacy_{$key}_{$privace_type}";
	if(isset($_G[$var])) {
		return $_G[$var];
	}
	space_merge($space, 'field_home');
	$result = false;
	if($_G['adminid'] == 1) {
		$result = true;
	} else {
		if($privace_type == 'feed') {
			if(!empty($space['privacy'][$privace_type][$key])) {
				$result = true;
			}
		} elseif($space['self']) {
			$result = true;
		} else {
			if(empty($space['privacy'][$privace_type][$key])) {
				$result = true;
			} elseif($space['privacy'][$privace_type][$key] == 1) {
				include_once libfile('function/friend');
				if(friend_check($space['uid'])) {
					$result = true;
				}
			} elseif($space['privacy'][$privace_type][$key] == 3) {
				$result = !in_array($_G['groupid'], [4, 5, 6, 7]);
			}
		}
	}
	$_G[$var] = $result;
	return $result;
}

function app_ckprivacy($privacy) {
	global $_G, $space;

	$var = "home_app_ckprivacy_{$privacy}";
	if(isset($_G[$var])) {
		return $_G[$var];
	}
	$result = false;
	switch($privacy) {
		case 1:
			include_once libfile('function/friend');
			if(friend_check($space['uid'])) {
				$result = true;
			}
			break;
		case 4:
		case 5:
		case 2:
			break;
		case 3:
			if($space['self']) {
				$result = true;
			}
			break;
		default:
			$result = true;
			break;
	}
	$_G[$var] = $result;
	return $result;
}

function formatsize($size) {
	$prec = 3;
	$size = round(abs($size));
	$units = [0 => ' B ', 1 => ' KB', 2 => ' MB', 3 => ' GB', 4 => ' TB'];
	if($size == 0) return str_repeat(' ', $prec)."0$units[0]";
	$unit = min(4, floor(log($size) / log(2) / 10));
	$size = $size * pow(2, -10 * $unit);
	$digi = $prec - 1 - floor(log($size) / log(10));
	$size = round($size * pow(10, $digi)) * pow(10, -$digi);
	return $size.$units[$unit];
}

function ckfriend($touid, $friend, $target_ids = '') {
	global $_G;

	if(empty($_G['uid'])) return !$friend;
	if($touid == $_G['uid'] || $_G['adminid'] == 1) return true;

	$var = 'home_ckfriend_'.md5($touid.'_'.$friend.'_'.$target_ids);
	if(isset($_G[$var])) return $_G[$var];

	$_G[$var] = false;
	switch($friend) {
		case 4:
		case 0:
			$_G[$var] = true;
			break;
		case 1:
			include_once libfile('function/friend');
			if(friend_check($touid)) {
				$_G[$var] = true;
			}
			break;
		case 2:
			if($target_ids) {
				$target_ids = explode(',', $target_ids);
				if(in_array($_G['uid'], $target_ids)) $_G[$var] = true;
			}
			break;
		default:
			break;
	}
	return $_G[$var];
}

function ckfollow($followuid) {
	global $_G;

	if(empty($_G['uid'])) return false;

	$var = 'home_follow_'.$_G['uid'].'_'.$followuid;
	if(isset($_G[$var])) return $_G[$var];

	$_G[$var] = false;
	$follow = table_home_follow::t()->fetch_status_by_uid_followuid($_G['uid'], $followuid);
	if(isset($follow[$_G['uid']])) {
		$_G[$var] = true;
	}
	return $_G[$var];
}

function sub_url($url, $length) {
	if(strlen($url) > $length) {
		$url = str_replace(['%3A', '%2F'], [':', '/'], rawurlencode($url));
		$url = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, -intval($length * 0.3));
	}
	return $url;
}

function space_domain($space) {
	global $_G;

	if($_G['setting']['allowspacedomain'] && $_G['setting']['domain']['root']['home']) {
		space_merge($space, 'field_home');
		if($space['domain']) {
			$space['domainurl'] = $_G['scheme'].'://'.$space['domain'].'.'.$_G['setting']['domain']['root']['home'];
		}
	}
	if(!empty($_G['setting']['domain']['app']['home'])) {
		$space['domainurl'] = $_G['scheme'].'://'.$_G['setting']['domain']['app']['home'].'/?'.$space['uid'];
	} elseif(empty($space['domainurl'])) {
		$space['domainurl'] = $_G['siteurl'].'?'.$space['uid'];
	}
	return $space['domainurl'];
}

function g_name($groupid) {
	global $_G;
	echo $_G['cache']['usergroups'][$groupid]['grouptitle'];
}

function g_color($groupid) {
	global $_G;
	if(empty($_G['cache']['usergroups'][$groupid]['color'])) {
		echo '';
	} else {
		echo ' style="color:'.$_G['cache']['usergroups'][$groupid]['color'].';"';
	}
}

function mob_perpage($perpage) {
	global $_G;

	$newperpage = isset($_GET['perpage']) ? intval($_GET['perpage']) : 0;
	if($_G['mobile'] && $newperpage > 0 && $newperpage < 500) {
		$perpage = $newperpage;
	}
	return $perpage;
}

function ckicon_uid($feed) {
	global $_G, $space;

	space_merge($space, 'field_home');
	$filter_icon = empty($space['privacy']['filter_icon']) ? [] : array_keys($space['privacy']['filter_icon']);
	if($filter_icon && (in_array($feed['icon'].'|0', $filter_icon) || in_array($feed['icon'].'|'.$feed['uid'], $filter_icon))) {
		return false;
	}
	return true;
}

function sarray_rand($arr, $num = 1) {
	$r_values = [];
	if($arr && count($arr) > $num) {
		if($num > 1) {
			$r_keys = array_rand($arr, $num);
			foreach($r_keys as $key) {
				$r_values[$key] = $arr[$key];
			}
		} else {
			$r_key = array_rand($arr, 1);
			$r_values[$r_key] = $arr[$r_key];
		}
	} else {
		$r_values = $arr;
	}
	return $r_values;
}

function getsiteurl() {
	global $_G;
	return $_G['siteurl'];
}

function pic_get($filepath, $type, $thumb, $remote, $return_thumb = 1, $hastype = '') {
	global $_G;

	$url = $filepath;
	if($return_thumb && $thumb) $url = getimgthumbname($url);
	if($remote > 1 && $type == 'album') {
		$remote -= 2;
		$type = 'forum';
	}
	$type = $hastype ? '' : $type.'/';
	return ($remote ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).$type.$url;
}

function pic_cover_get($pic, $picflag) {
	global $_G;

	$type = 'album';
	if($picflag > 2) {
		$picflag = $picflag - 2;
		$type = 'forum';
	}
	if($picflag == 1) {
		$url = $_G['setting']['attachurl'].$type.'/'.$pic;
	} elseif($picflag == 2) {
		$url = $_G['setting']['ftp']['attachurl'].$type.'/'.$pic;
	} else {
		$url = $pic;
	}
	return $url;
}

function pic_delete($pic, $type, $thumb, $remote) {
	global $_G;

	if($remote > 1 && $type == 'album') {
		$remote -= 2;
		$type = 'forum';
		return true;
	}

	if($remote) {
		ftpcmd('delete', $type.'/'.$pic);
		if($thumb) {
			ftpcmd('delete', $type.'/'.getimgthumbname($pic));
		}
		ftpcmd('close');
	} else {
		@unlink($_G['setting']['attachdir'].'/'.$type.'/'.$pic);
		if($thumb) {
			@unlink($_G['setting']['attachdir'].'/'.$type.'/'.getimgthumbname($pic));
		}
	}
	return true;
}

function pic_upload($FILES, $type = 'album', $thumb_width = 0, $thumb_height = 0, $thumb_type = 2) {
	$upload = new discuz_upload();

	$result = ['pic' => '', 'thumb' => 0, 'remote' => 0];

	$upload->init($FILES, $type);
	if($upload->error()) {
		return [];
	}

	$upload->save();
	if($upload->error()) {
		return [];
	}

	$result['pic'] = $upload->attach['attachment'];

	if($thumb_width && $thumb_height) {
		require_once libfile('class/image');
		$image = new image();
		if($image->Thumb($upload->attach['target'], '', $thumb_width, $thumb_height, $thumb_type)) {
			$result['thumb'] = 1;
		}
	}

	if(ftpperm($upload->attach['ext'], $upload->attach['size'])) {
		if(ftpcmd('upload', $type.'/'.$upload->attach['attachment'])) {
			if($result['thumb']) {
				ftpcmd('upload', $type.'/'.getimgthumbname($upload->attach['attachment']));
			}
			ftpcmd('close');
			@unlink($upload->attach['target']);
			@unlink(getglobal('setting/attachdir').$type.'/'.getimgthumbname($upload->attach['target']));
			$result['remote'] = 1;
		} else {
			if(getglobal('setting/ftp/mirror')) {
				@unlink($upload->attach['target']);
				@unlink(getglobal('setting/attachdir').$type.'/'.getimgthumbname($upload->attach['target']));
				return [];
			}
		}
	}

	return $result;
}

function member_count_update($uid, $counts) {
	global $_G;

	$setsqls = [];
	foreach($counts as $key => $value) {
		if($key == 'credit') {
			if($_G['setting']['creditstransextra'][6]) {
				$key = 'extcredits'.intval($_G['setting']['creditstransextra'][6]);
			} elseif($_G['setting']['creditstrans']) {
				$key = 'extcredits'.intval($_G['setting']['creditstrans']);
			} else {
				continue;
			}
		}
		$setsqls[$key] = $value;
	}
	if($setsqls) {
		updatemembercount($uid, $setsqls);
	}
}


function getdefaultdoing() {
	global $_G;

	$result = [];
	$key = 0;

	if(($result = table_common_setting::t()->fetch_setting('defaultdoing'))) {
		$_G['setting']['defaultdoing'] = explode("\r\n", $result);
		$key = rand(0, count($_G['setting']['defaultdoing']) - 1);
	} else {
		$_G['setting']['defaultdoing'] = [lang('space', 'doing_you_can')];
	}
	return $_G['setting']['defaultdoing'][$key];
}

function getuserdiydata($space) {
	global $_G;
	if(empty($_G['blockposition'])) {
		$userdiy = getuserdefaultdiy();
		if(!empty($space['blockposition'])) {
			$blockdata = dunserialize($space['blockposition']);
			foreach((array)$blockdata as $key => $value) {
				if($key == 'parameters') {
					foreach((array)$value as $k => $v) {
						if(!empty($v)) $userdiy[$key][$k] = $v;
					}
				} else {
					if(!empty($value)) $userdiy[$key] = $value;
				}
			}
		}
		$_G['blockposition'] = $userdiy;
	}
	return $_G['blockposition'];
}


function getuserdefaultdiy() {
	$defaultdiy = [
		'currentlayout' => '1:2:1',
		'block' => [
			'frame`frame1' => [
				'attr' => ['name' => 'frame1'],
				'column`frame1_left' => [
					'block`profile' => ['attr' => ['name' => 'profile']],
					'block`statistic' => ['attr' => ['name' => 'statistic']],
					'block`album' => ['attr' => ['name' => 'album']],
					'block`doing' => ['attr' => ['name' => 'doing']]
				],
				'column`frame1_center' => [
					'block`personalinfo' => ['attr' => ['name' => 'personalinfo']],
					'block`feed' => ['attr' => ['name' => 'feed']],
					'block`share' => ['attr' => ['name' => 'share']],
					'block`blog' => ['attr' => ['name' => 'blog']],
					'block`thread' => ['attr' => ['name' => 'thread']],
					'block`wall' => ['attr' => ['name' => 'wall']]
				],
				'column`frame1_right' => [
					'block`friend' => ['attr' => ['name' => 'friend']],
					'block`visitor' => ['attr' => ['name' => 'visitor']],
					'block`group' => ['attr' => ['name' => 'group']]
				]
			]
		],
		'parameters' => [
			'blog' => ['showmessage' => 150, 'shownum' => 6],
			'doing' => ['shownum' => 15],
			'album' => ['shownum' => 8],
			'thread' => ['shownum' => 10],
			'share' => ['shownum' => 10],
			'friend' => ['shownum' => 18],
			'group' => ['shownum' => 12],
			'visitor' => ['shownum' => 18],
			'wall' => ['shownum' => 16],
			'feed' => ['shownum' => 16],
		],
		'nv' => [
			'nvhidden' => 0,
			'items' => [],
			'banitems' => [],
		],
	];
	return $defaultdiy;
}

function getonlinemember($uids) {
	global $_G;
	if($uids && is_array($uids) && empty($_G['ols'])) {
		$_G['ols'] = [];
		foreach(C::app()->session->fetch_all_by_uid($uids) as $value) {
			if(!$value['invisible']) {
				$_G['ols'][$value['uid']] = $value['lastactivity'];
			}
		}
	}
}

function getfollowfeed($uid, $viewtype, $archiver = false, $start = 0, $perpage = 0) {
	global $_G;

	$list = [];
	if(isset($_G['follwusers'][$uid])) {
		$list['user'] = $_G['follwusers'][$uid];
	} else {
		if($viewtype == 'follow') {
			$list['user'] = table_home_follow::t()->fetch_all_following_by_uid($uid);
			$list['user'][$uid] = ['uid' => $uid];
		} elseif($viewtype == 'special') {
			$list['user'] = table_home_follow::t()->fetch_all_following_by_uid($uid, 1);
		}
		if(!empty($list['user'])) {
			$_G['follwusers'][$uid] = $list['user'];
		}
	}
	$uids = in_array($viewtype, ['other', 'self']) ? $uid : array_keys($list['user']);
	if(!empty($uids) || in_array($viewtype, ['other', 'self'])) {
		$list['feed'] = table_home_follow_feed::t()->fetch_all_by_uid($uids, $archiver, $start, $perpage);
		if($list['feed']) {
			$list['content'] = table_forum_threadpreview::t()->fetch_all(table_home_follow_feed::t()->get_tids());
			if(!$_G['group']['allowgetattach'] || !$_G['group']['allowgetimage']) {
				foreach($list['content'] as $key => $feed) {
					if(!$_G['group']['allowgetimage']) {
						$list['content'][$key]['content'] = preg_replace("/[ \t]*\<li\>\<img id=\"aimg_(.+?)\".*?\>[ \t]*\<\/li\>/is", '', $feed['content']);
					}
					if(!$_G['group']['allowgetattach']) {
						$list['content'][$key]['content'] = preg_replace("/[ \t]*\<li\>\<a href=\"(.+?)\" id=\"attach_(.+?)\".*?\>.*?\<\/a\>[ \t]*\<\/li\>/is", '', $feed['content']);
					}
				}
			}
			$list['threads'] = table_forum_thread::t()->fetch_all_by_tid(table_home_follow_feed::t()->get_tids());
			if(!empty($list['threads']) && is_array($list['threads'])) {
				foreach($list['threads'] as $key => $thread) {
					if(empty($_G['setting']['followforumid']) || $thread['fid'] != $_G['setting']['followforumid']) {
						if(!empty($list['content'][$key]['content'])) {
							$list['content'][$key]['content'] = preg_replace('#onclick="changefeed\([^"]+\)"\s*style="cursor:\s*pointer;"#is', '', $list['content'][$key]['content']);
						}
					}
				}
			}
		}
	}
	return $list;
}

function getthread() {
	$threads = [];
	foreach(table_home_follow_feed::t()->get_ids() as $idtype => $ids) {
		if($idtype == 'thread') {
			$threads = table_forum_thread::t()->fetch_all_by_tid($ids);
		}
	}
	return $threads;
}

function show_view() {
	global $_G, $space;

	if(!$space['self'] && $_G['uid']) {
		$visitor = table_home_visitor::t()->fetch_by_uid_vuid($space['uid'], $_G['uid']);
		$is_anonymous = empty($_G['cookie']['anonymous_visit_'.$_G['uid'].'_'.$space['uid']]) ? 0 : 1;
		if(empty($visitor['dateline'])) {
			$setarr = [
				'uid' => $space['uid'],
				'vuid' => $_G['uid'],
				'vusername' => $is_anonymous ? '' : $_G['username'],
				'dateline' => $_G['timestamp']
			];
			table_home_visitor::t()->insert($setarr, false, true);
			show_credit();
		} else {
			if($_G['timestamp'] - $visitor['dateline'] >= 300) {
				table_home_visitor::t()->update_by_uid_vuid($space['uid'], $_G['uid'], ['dateline' => $_G['timestamp'], 'vusername' => $is_anonymous ? '' : $_G['username']]);
			}
			if($_G['timestamp'] - $visitor['dateline'] >= 3600) {
				show_credit();
			}
		}
		updatecreditbyaction('visit', 0, [], $space['uid']);
	}
}

function show_credit() {
	global $_G, $space;

	$showinfo = table_home_show::t()->fetch($space['uid']);
	if($showinfo['credit'] > 0) {
		$showinfo['unitprice'] = intval($showinfo['unitprice']);
		if($showinfo['credit'] <= $showinfo['unitprice']) {
			notification_add($space['uid'], 'show', 'show_out');
			table_home_show::t()->delete($space['uid']);
		} else {
			table_home_show::t()->update_credit_by_uid($space['uid'], -$showinfo['unitprice']);
		}
	}
}

function tousername(&$list) {
	$loginnames = [];
	foreach($list as $row) {
		if(!empty($row['firstauthor'])) {
			$loginnames[$row['firstauthor']] = $row['firstauthor'];
		}
		if(!empty($row['author'])) {
			$loginnames[$row['author']] = $row['author'];
		}
		if(!empty($row['lastauthor'])) {
			$loginnames[$row['lastauthor']] = $row['lastauthor'];
		}
		if(!empty($row['msgfrom'])) {
			$loginnames[$row['msgfrom']] = $row['msgfrom'];
		}
		if(!empty($row['tousername'])) {
			$loginnames[$row['tousername']] = $row['tousername'];
		}
	}
	$users = table_common_member::t()->fetch_all_by_loginname($loginnames);
	foreach($list as $k => $row) {
		if(!empty($row['firstauthor'])) {
			$list[$k]['firstauthor'] = $users[$row['firstauthor']]['username'];
		}
		if(!empty($row['author'])) {
			$list[$k]['author'] = $users[$row['author']]['username'];
		}
		if(!empty($row['lastauthor'])) {
			$list[$k]['lastauthor'] = $users[$row['lastauthor']]['username'];
		}
		if(!empty($row['msgfrom'])) {
			$list[$k]['msgfrom'] = $users[$row['msgfrom']]['username'];
		}
		if(!empty($row['tousername'])) {
			$list[$k]['tousername'] = $users[$row['tousername']]['username'];
		}
	}
}

