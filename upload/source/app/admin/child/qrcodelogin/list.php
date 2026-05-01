<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$isfounder = isfounder();

$do = isset($_GET['do']) ? trim($_GET['do']) : 'index';

if($do == 'index') {
	if($isfounder) {
		showtips('qrcodelogin_tips');
	} else {
		showtips('qrcodelogin_tips2');
	}

	$data = requestLoginApi('index');
	if($data['errCode'] == -2) {
		if(!$isfounder) {
			cpmsg('qrcodelogin_closed');
		}
		showformheader('qrcodelogin&operation=list&do=submit');
		showtableheader('qrcodelogin_create');
		showsetting('qrcodelogin_pwd', 'pwd', '', 'password');
		showsetting('qrcodelogin_pwd2', 'repwd', '', 'password');
		showsubmit('registersubmit');
		showtablefooter();
		showformfooter();
	} elseif($data['errCode'] != 0) {
		cpmsg(sprintf(cplang('qrcodelogin_api_error'), $data['errCode']));
	} else {
		showformheader('qrcodelogin&operation=list&do=submit');
		showtableheader();
		$adminUids = [];
		foreach($data['data']['list'] as $row) {
			$adminUids[$row['adminUid']] = $row['adminUid'];
		}
		$haveBind = false;
		$members = table_common_member::t()->fetch_all_username_by_uid($adminUids);
		$style = ['class="td25"', 'class="td24"', 'class="td24"', 'class="td24"', 'class="td24"'];
		showsubtitle(['del', 'qrcodelogin_user', 'qrcodelogin_bindaccount', 'qrcodelogin_lastlogin', 'qrcodelogin_lastip', $isfounder ? 'qrcodelogin_status' : ''], 'header', $style);
		foreach($data['data']['list'] as $row) {
			showtablerow('style="height:20px"', $style, [
				$isfounder || $row['adminUid'] == $_G['uid'] ? "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$row['openid']}\">" : '',
				$members[$row['adminUid']].'('.$row['adminUid'].')',
				base64_decode($row['nickname']),
				$row['lastLogin'] ? dgmdate($row['lastLogin']) : '',
				$row['lastIp'],
				$isfounder ?
					"<label><input class=\"radio\" type=\"radio\" name=\"status[{$row['openid']}]\" value=\"1\"".($row['status'] == 1 ? ' checked' : '').">".cplang('qrcodelogin_status_1')."</label>".
					"<label><input class=\"radio\" type=\"radio\" name=\"status[{$row['openid']}]\" value=\"0\"".(!$row['status'] ? ' checked' : '').">".cplang('qrcodelogin_status_0')."</label>".
					"<label><input class=\"radio\" type=\"radio\" name=\"status[{$row['openid']}]\" value=\"-1\"".($row['status'] == -1 ? ' checked' : '').">".cplang('qrcodelogin_status_-1')."</label>" : '',
			]);
			if($row['adminUid'] == $_G['uid']) {
				$haveBind = true;
			}
		}
		$s = !$haveBind ? '<input type="submit" class="btn" name="add" value="'.cplang('qrcodelogin_bind').'" />' : '';
		showsubmit($data['data']['list'] ? 'submit' : '', 'submit', '', $s);
		showtablefooter();
		showformfooter();
	}
} elseif(submitcheck('registersubmit') && $isfounder) {
	$pwd = $_GET['pwd'];
	$repwd = $_GET['repwd'];
	if(!$pwd || !$repwd || $pwd != $repwd) {
		cpmsg('qrcodelogin_pwd_error');
	}
	$data = requestLoginApi('register', [
		'sitename' => $_G['setting']['sitename'],
		'bindpwd' => md5(sha1($pwd)),
		'authmd5' => md5($_G['config']['security']['authkey']),
	]);
	if($data['errCode'] != 0) {
		cpmsg(sprintf(cplang('qrcodelogin_api_error'), $data['errCode']));
	}
	cpmsg('qrcodelogin_create_succeed', 'action=qrcodelogin&operation=list', 'succeed');
} elseif(submitcheck('add')) {
	showformheader('qrcodelogin&operation=list&do=submit');
	showtableheader();
	showsetting('qrcodelogin_wechat_scan', 'pwd', '', '<img src="https://api.witframe.com/discuzlogin/bind" />');
	showsetting('qrcodelogin_bindcode', 'code', '', 'text');
	showsetting('qrcodelogin_pwd', 'pwd', '', 'password');
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();
} elseif(submitcheck('addsubmit')) {
	$data = requestLoginApi('bindcheck', [
		'code' => $_GET['code']
	]);
	if(empty($data['data']['user'])) {
		cpmsg('qrcodelogin_code_error');
	}
	$member = table_common_member::t()->fetch($_G['uid']);
	$data = requestLoginApi('add', [
		'bindpwd' => md5(sha1($_GET['pwd'])),
		'pwdmd5' => md5($member['password']),
		'adminUid' => $_G['uid'],
		'openid' => $data['data']['user']['openid'],
		'nickname' => $data['data']['user']['nickname'],
	]);
	if($data['errCode'] == -4) {
		cpmsg('qrcodelogin_bindpwd_error');
	}
	if($data['errCode'] != 0) {
		cpmsg(sprintf(cplang('qrcodelogin_api_error'), $data['errCode']));
	}
	cpmsg('qrcodelogin_bind_succeed', 'action=qrcodelogin&operation=list', 'succeed');
} elseif(submitcheck('submit')) {
	$data = requestLoginApi('index');

	foreach($_GET['delete'] as $openid) {
		$data = requestLoginApi('delete', ['openid' => $openid]);
		if($data['errCode'] != 0) {
			cpmsg(sprintf(cplang('qrcodelogin_api_error'), $data['errCode']));
		}
	}

	if($isfounder) {
		$notify = 0;
		foreach($data['data']['list'] as $row) {
			if($row['status'] != $_GET['status'][$row['openid']]) {
				$data = requestLoginApi('edit', [
						'openid' => $row['openid'],
						'status' => $_GET['status'][$row['openid']]]
				);
				if($data['errCode'] != 0) {
					cpmsg(sprintf(cplang('qrcodelogin_api_error'), $data['errCode']));
				}
			}
			if($_GET['status'][$row['openid']] == 1) {
				$notify = 1;
			}
		}

		if($_G['setting']['admin_qrlogin_notify'] != $notify) {
			$settings = ['admin_qrlogin_notify' => $notify];
			table_common_setting::t()->update_batch($settings);
			updatecache('setting');
		}
	}

	cpmsg('qrcodelogin_update_succeed', 'action=qrcodelogin&operation=list', 'succeed');
}