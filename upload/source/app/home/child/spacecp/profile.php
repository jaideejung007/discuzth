<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$defaultop = '';
$profilegroup = table_common_setting::t()->fetch_setting('profilegroup', true);
foreach($profilegroup as $key => $value) {
	if($value['available']) {
		$defaultop = $key;
		break;
	}
}

$operation = in_array(getgpc('op'), ['base', 'contact', 'edu', 'work', 'info', 'password', 'verify']) ? trim($_GET['op']) : $defaultop;
$space = getuserbyuid($_G['uid']);
space_merge($space, 'field_home');
space_merge($space, 'profile');

list($seccodecheck, $secqaacheck) = seccheck('password');
loadcache('domain');
$domain = &$_G['cache']['domain'];
$spacedomain = isset($rootdomain['home']) && $rootdomain['home'] ? $rootdomain['home'] : [];
$_GET['id'] = getgpc('id') ? preg_replace('/[^A-Za-z0-9_:]/', '', $_GET['id']) : '';
if($operation != 'password') {

	include_once libfile('function/profile');

	loadcache('profilesetting');
	if(empty($_G['cache']['profilesetting'])) {
		require_once libfile('function/cache');
		updatecache('profilesetting');
		loadcache('profilesetting');
	}
}

$allowcstatus = !empty($_G['group']['allowcstatus']);
$verify = table_common_member_verify::t()->fetch($_G['uid']);
if(!empty($verify) && is_array($verify)) {
	foreach($verify as $key => $flag) {
		if(in_array($key, ['verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6', 'verify7']) && $flag == 1) {
			$verifyid = intval(substr($key, -1, 1));
			if($_G['setting']['verify'][$verifyid]['available']) {
				foreach($_G['setting']['verify'][$verifyid]['field'] as $field) {
					$_G['cache']['profilesetting'][$field]['unchangeable'] = 1;
				}
			}
		}
	}
}
$validate = [];
if($_G['setting']['regverify'] == 2 && $_G['groupid'] == 8) {
	$validate = table_common_member_validate::t()->fetch($_G['uid']);
	if(empty($validate) || $validate['status'] != 1) {
		$validate = [];
	}
}

if(submitcheck('profilesubmit')) {

	require_once libfile('function/discuzcode');

	$forum = $setarr = $verifyarr = $errorarr = [];
	$forumfield = ['customstatus', 'sightml'];

	$censor = discuz_censor::instance();

	if($_GET['vid']) {
		$vid = intval($_GET['vid']);
		if(getuserprofile('verify'.$vid) == 1) {
			showmessage('spacecp_profile_message2');
		}
		$verifyconfig = $_G['setting']['verify'][$vid];
		if($verifyconfig['available'] && checkverifyperm($verifyconfig)) {
			$verifyinfo = table_common_member_verify_info::t()->fetch_by_uid_verifytype($_G['uid'], $vid);
			if(!empty($verifyinfo)) {
				$verifyinfo['field'] = dunserialize($verifyinfo['field']);
			}
			foreach($verifyconfig['field'] as $key => $field) {
				if(!isset($verifyinfo['field'][$key])) {
					$verifyinfo['field'][$key] = $key;
				}
			}
		} else {
			$_GET['vid'] = $vid = 0;
			$verifyconfig = [];
		}
	}
	if(isset($_POST['birthcountry'])) {
		$initcity = ['birthcountry', 'birthprovince', 'birthcity', 'birthdist', 'birthcommunity'];
		foreach($initcity as $key) {
			$_GET[''.$key] = $_POST[$key] = !empty($_POST[$key]) ? $_POST[$key] : '';
		}
	}
	if(isset($_POST['residecountry'])) {
		$initcity = ['residecountry', 'resideprovince', 'residecity', 'residedist', 'residecommunity'];
		foreach($initcity as $key) {
			$_GET[''.$key] = $_POST[$key] = !empty($_POST[$key]) ? $_POST[$key] : '';
		}
	}
	foreach($_POST as $key => $value) {
		$field = $_G['cache']['profilesetting'][$key];
		if(in_array($field['formtype'], ['text', 'textarea']) || in_array($key, $forumfield)) {
			$censor->check($value);
			if($censor->modbanned() || $censor->modmoderated()) {
				profile_showerror($key, lang('spacecp', 'profile_censor'));
			}
		}
		if(in_array($key, $forumfield)) {
			if($key == 'sightml') {
				loadcache(['smilies', 'smileytypes']);
				$value = cutstr($value, $_G['group']['maxsigsize'], '');
				foreach($_G['cache']['smilies']['replacearray'] as $skey => $smiley) {
					$_G['cache']['smilies']['replacearray'][$skey] = '[img]'.$_G['siteurl'].'static/image/smiley/'.$_G['cache']['smileytypes'][$_G['cache']['smilies']['typearray'][$skey]]['directory'].'/'.$smiley.'[/img]';
				}
				$value = preg_replace($_G['cache']['smilies']['searcharray'], $_G['cache']['smilies']['replacearray'], trim($value));
				$forum[$key] = discuzcode($value, 1, 0, 0, 0, $_G['group']['allowsigbbcode'], $_G['group']['allowsigimgcode'], 0, 0, 1);
			} elseif($key == 'customstatus' && $allowcstatus) {
				$forum[$key] = dhtmlspecialchars(trim($value));
			}
			continue;
		} elseif($field && !$field['available']) {
			continue;
		} elseif($key == 'timeoffset') {
			if($value >= -12 && $value <= 12 || $value == 9999) {
				table_common_member::t()->update($_G['uid'], ['timeoffset' => intval($value)]);
			}
		} elseif($key == 'site') {
			if(!in_array(strtolower(substr($value, 0, 6)), ['http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://']) && !preg_match('/^static\//', $value) && !preg_match('/^data\//', $value)) {
				$value = 'http://'.$value;
			}
		}
		if($field['formtype'] == 'file') {
			if((!empty($_FILES[$key]) && $_FILES[$key]['error'] == 0) || (!empty($space[$key]) && empty($_GET['deletefile'][$key]))) {
				$value = '1';
			} else {
				$value = '';
			}
		}
		if(empty($field)) {
			continue;
		} elseif(profile_check($key, $value, $space)) {
			$setarr[$key] = dhtmlspecialchars(trim($value));
		} else {
			if($key == 'birthcountry' || $key == 'birthprovince') {
				$key = 'birthcity';
			} elseif($key == 'residecountry' || $key == 'resideprovince' || $key == 'residecommunity' || $key == 'residedist') {
				$key = 'residecity';
			} elseif($key == 'birthyear' || $key == 'birthmonth') {
				$key = 'birthday';
			}
			profile_showerror($key);
		}
		if($field['formtype'] == 'file') {
			unset($setarr[$key]);
		}
		if(isset($setarr[$key]) && $_G['cache']['profilesetting'][$key]['unchangeable'] && $space[$key]) {
			unset($setarr[$key]);
		}
		if($vid && $verifyconfig['available'] && isset($verifyconfig['field'][$key])) {
			if(isset($verifyinfo['field'][$key]) && $setarr[$key] !== $space[$key]) {
				$verifyarr[$key] = $setarr[$key];
			}
			unset($setarr[$key]);
		}
		if(isset($setarr[$key]) && $_G['cache']['profilesetting'][$key]['needverify']) {
			if($setarr[$key] !== $space[$key]) {
				$verifyarr[$key] = $setarr[$key];
			}
			unset($setarr[$key]);
		}
		if(!empty($_G['cache']['profilesetting'][$key]['encrypt'])) {
			$setarr[$key] = authcode_field($_G['cache']['profilesetting'][$key]['encrypt'], $setarr[$key], 'ENCODE');
		}
	}
	if($_GET['deletefile'] && is_array($_GET['deletefile'])) {
		foreach($_GET['deletefile'] as $key => $value) {
			if(isset($_G['cache']['profilesetting'][$key]) && $_G['cache']['profilesetting'][$key]['formtype'] == 'file') {
				$verifyarr[$key] = $setarr[$key] = '';
				if(isprofileimage($space[$key])) {
					@unlink(getglobal('setting/attachdir').'./profile/'.$space[$key]);
				}
				if(isprofileimage($verifyinfo['field'][$key])) {
					@unlink(getglobal('setting/attachdir').'./profile/'.$verifyinfo['field'][$key]);
				}
			}
		}
	}
	if($_FILES) {
		$upload = new discuz_upload();
		foreach($_FILES as $key => $file) {
			if(!isset($_G['cache']['profilesetting'][$key])) {
				continue;
			}
			$field = $_G['cache']['profilesetting'][$key];
			if($field['formtype'] != 'file') {
				continue;
			}
			if((!empty($file) && $file['error'] == 0) || (!empty($space[$key]) && empty($_GET['deletefile'][$key]))) {
				$value = '1';
			} else {
				$value = '';
			}
			if(!profile_check($key, $value, $space)) {
				profile_showerror($key);
			} elseif($field['size'] && $field['size'] * 1024 < $file['size']) {
				profile_showerror($key, lang('spacecp', 'filesize_lessthan').$field['size'].'KB');
			} elseif($_G['cache']['profilesetting'][$key]['unchangeable'] && !empty($space[$key])) {
				profile_showerror($key);
			}
			$upload->init($file, 'profile');
			$attach = $upload->attach;

			if(!$upload->error()) {
				$upload->save();

				if(!$upload->get_image_info($attach['target'])) {
					@unlink($attach['target']);
					continue;
				}
				$setarr[$key] = '';
				if(isprofileimage($space[$key])) {
					@unlink(getglobal('setting/attachdir').'./profile/'.$space[$key]);
				}
				$attach['attachment'] = dhtmlspecialchars(trim($attach['attachment']));
				if($vid && $verifyconfig['available'] && isset($verifyconfig['field'][$key])) {
					if(isset($verifyinfo['field'][$key])) {
						$verifyarr[$key] = $attach['attachment'];
						if(isprofileimage($verifyinfo['field'][$key])) {
							@unlink(getglobal('setting/attachdir').'./profile/'.$verifyinfo['field'][$key]);
						}
					}
					continue;
				}
				if(isset($setarr[$key]) && $_G['cache']['profilesetting'][$key]['needverify']) {
					$verifyarr[$key] = $attach['attachment'];
					if(isprofileimage($verifyinfo['field'][$key])) {
						@unlink(getglobal('setting/attachdir').'./profile/'.$verifyinfo['field'][$key]);
					}
					continue;
				}
				$setarr[$key] = $attach['attachment'];
			}

		}
	}
	if($vid && !empty($verifyinfo['field']) && is_array($verifyinfo['field'])) {
		foreach($verifyinfo['field'] as $key => $fvalue) {
			if(!isset($verifyconfig['field'][$key])) {
				unset($verifyinfo['field'][$key]);
				continue;
			}
			if(empty($verifyarr[$key]) && !isset($verifyarr[$key]) && isset($verifyinfo['field'][$key])) {
				$verifyarr[$key] = !empty($fvalue) && $key != $fvalue ? $fvalue : $space[$key];
			}
		}
	}
	if($forum) {
		if(!$_G['group']['maxsigsize']) {
			$forum['sightml'] = '';
		}
		table_common_member_field_forum::t()->update($_G['uid'], $forum);

	}

	if(isset($_POST['birthmonth']) && ($space['birthmonth'] != $_POST['birthmonth'] || $space['birthday'] != $_POST['birthday'])) {
		$setarr['constellation'] = get_constellation($_POST['birthmonth'], $_POST['birthday']);
	}
	if(isset($_POST['birthyear']) && $space['birthyear'] != $_POST['birthyear']) {
		$setarr['zodiac'] = get_zodiac($_POST['birthyear']);
	}
	if($setarr) {
		
		if($_G['setting']['profilehistory']) {
			table_common_member_profile_history::t()->insert(array_merge(table_common_member_profile::t()->fetch($_G['uid']), ['dateline' => time()]));
		}
		table_common_member_profile::t()->update($_G['uid'], $setarr);
	}

	if($verifyarr) {
		table_common_member_verify_info::t()->delete_by_uid($_G['uid'], $vid);
		$setverify = [
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'verifytype' => $vid,
			'field' => serialize($verifyarr),
			'dateline' => $_G['timestamp']
		];

		table_common_member_verify_info::t()->insert($setverify);
		if(!(table_common_member_verify::t()->count_by_uid($_G['uid']))) {
			table_common_member_verify::t()->insert(['uid' => $_G['uid']]);
		}
		if($_G['setting']['verify'][$vid]['available']) {
			manage_addnotify('verify_'.$vid, 0, ['langkey' => 'manage_verify_field', 'verifyname' => $_G['setting']['verify'][$vid]['title'], 'doid' => $vid]);
		}
	}

	if(isset($_POST['privacy'])) {
		foreach($_POST['privacy'] as $key => $value) {
			if(isset($_G['cache']['profilesetting'][$key])) {
				$space['privacy']['profile'][$key] = intval($value);
			}
		}
		table_common_member_field_home::t()->update($space['uid'], ['privacy' => serialize($space['privacy'])]);
	}

	include_once libfile('function/feed');
	feed_add('profile', 'feed_profile_update_'.$operation, ['hash_data' => 'profile']);
	countprofileprogress();
	$message = $vid ? lang('spacecp', 'profile_verify_verifying', ['verify' => $verifyconfig['title']]) : '';
	profile_showsuccess($message);

} elseif(submitcheck('passwordsubmit', 0, $seccodecheck, $secqaacheck)) {

	$membersql = $memberfieldsql = $authstradd1 = $authstradd2 = $newpasswdadd = '';
	$setarr = [];
	$emailnew = dhtmlspecialchars($_GET['emailnew']);
	$secmobiccnew = $_GET['secmobiccnew'];
	$secmobilenew = $_GET['secmobilenew'];
	$secmobseccode = $_GET['secmobseccodenew'];
	$ignorepassword = 0;

	if(in_array('mobile', $_G['setting']['plugins']['available']) && $wechatuser['isregister']) {
		$_GET['oldpassword'] = '';
		$ignorepassword = 1;
		if(empty($_GET['newpassword'])) {
			showmessage('profile_passwd_empty');
		}
	}

	if($_GET['questionidnew'] === '') {
		$_GET['questionidnew'] = $_GET['answernew'] = '';
	} else {
		$secquesnew = $_GET['questionidnew'] > 0 ? random(8) : '';
	}

	if(!empty($_GET['newpassword']) && $_G['setting']['strongpw']) {
		$strongpw_str = [];
		if(in_array(1, $_G['setting']['strongpw']) && !preg_match('/\d+/', $_GET['newpassword'])) {
			$strongpw_str[] = lang('member/template', 'strongpw_1');
		}
		if(in_array(2, $_G['setting']['strongpw']) && !preg_match('/[a-z]+/', $_GET['newpassword'])) {
			$strongpw_str[] = lang('member/template', 'strongpw_2');
		}
		if(in_array(3, $_G['setting']['strongpw']) && !preg_match('/[A-Z]+/', $_GET['newpassword'])) {
			$strongpw_str[] = lang('member/template', 'strongpw_3');
		}
		if(in_array(4, $_G['setting']['strongpw']) && !preg_match('/[^a-zA-z0-9]+/', $_GET['newpassword'])) {
			$strongpw_str[] = lang('member/template', 'strongpw_4');
		}
		if($strongpw_str) {
			showmessage(lang('member/template', 'password_weak').implode(',', $strongpw_str));
		}
	}
	if(!empty($_GET['newpassword']) && $_GET['newpassword'] != addslashes($_GET['newpassword'])) {
		showmessage('profile_passwd_illegal', '', [], ['return' => true]);
	}
	if(!empty($_GET['newpassword']) && $_GET['newpassword'] != $_GET['newpassword2']) {
		showmessage('profile_passwd_notmatch', '', [], ['return' => true]);
	}

	if($emailnew != $_G['member']['email'] && $_G['setting']['change_email']) {
		showmessage('profile_email_not_change', '', [], ['return' => true]);
	}

	if((strcmp($secmobiccnew, $_G['member']['secmobicc']) != 0 || strcmp($secmobilenew, $_G['member']['secmobile']) != 0) && $_G['setting']['change_secmobile']) {
		showmessage('profile_secmobile_not_change', '', [], ['return' => true]);
	}

	
	if($secmobiccnew === '' && $secmobilenew !== '' && preg_match('#^(\d){1,12}$#', $secmobilenew)) {
		$secmobiccnew = $_G['setting']['smsdefaultcc'];
	}

	
	if($secmobiccnew === '') {
		$secmobiccnew = 0;
	} elseif(!preg_match('#^(\d){1,3}$#', $secmobiccnew)) {
		showmessage('profile_secmobicc_illegal', '', [], ['return' => true]);
	}

	if($secmobilenew === '') {
		$secmobilenew = 0;
	} elseif($secmobilenew !== '' && !preg_match('#^(\d){1,12}$#', $secmobilenew)) {
		showmessage('profile_secmobile_illegal', '', [], ['return' => true]);
	}

	loaducenter();
	if($emailnew != $_G['member']['email']) {
		include_once libfile('function/member');
		checkemail($emailnew);
	}
	$ucresult = uc_user_edit(addslashes($_G['member']['loginname']), $_GET['oldpassword'], $_GET['newpassword'], '', $ignorepassword, $_GET['questionidnew'], $_GET['answernew'], $secmobiccnew, $secmobilenew);
	if($ucresult == -1) {
		showmessage('profile_passwd_wrong', '', [], ['return' => true]);
	} elseif($ucresult == -4) {
		showmessage('profile_email_illegal', '', [], ['return' => true]);
	} elseif($ucresult == -5) {
		showmessage('profile_email_domain_illegal', '', [], ['return' => true]);
	} elseif($ucresult == -6) {
		showmessage('profile_email_duplicate', '', [], ['return' => true]);
	} elseif($ucresult == -9) {
		showmessage('profile_secmobile_duplicate', '', [], ['return' => true]);
	}

	if(!empty($_GET['newpassword']) || $secquesnew) {
		$setarr['password'] = md5(random(10));
	}

	$authstr = false;
	if($emailnew != $_G['member']['email']) {
		if(emailcheck_send($space['uid'], $emailnew)) {
			$authstr = true;
			dsetcookie('newemail', "{$space['uid']}\t$emailnew\t{$_G['timestamp']}", 31536000);
		}
	}
	
	if($_G['setting']['smsstatus'] && (strcmp($secmobiccnew, $_G['member']['secmobicc']) != 0 || strcmp($secmobilenew, $_G['member']['secmobile']) != 0) && empty($secmobseccode)) {
		$length = $_G['setting']['smsdefaultlength'] ? $_G['setting']['smsdefaultlength'] : 4;
		
		
		sms::send($_G['uid'], 0, 1, $secmobiccnew, $secmobilenew, random($length, 1), 0);
	}
	
	$setarr['secmobicc'] = $secmobiccnew == 0 ? '' : $secmobiccnew;
	$setarr['secmobile'] = $secmobilenew == 0 ? '' : $secmobilenew;
	
	if(strcmp($secmobiccnew, $_G['member']['secmobicc']) != 0 || strcmp($secmobilenew, $_G['member']['secmobile']) != 0) {
		$setarr['secmobilestatus'] = sms::verify($_G['uid'], 1, $secmobiccnew, $secmobilenew, $secmobseccode);
	}
	if($setarr) {
		if($_G['member']['freeze'] == 1) {
			$setarr['freeze'] = 0;
		}
		table_common_member::t()->update($_G['uid'], $setarr);
	}
	if($_G['member']['freeze'] == 2 || $_G['member']['freeze'] == -1) {
		$status = table_common_member_validate::t()->fetch($_G['uid']);
		if($status) {
			table_common_member_validate::t()->update($_G['uid'], [
				'submitdate' => TIMESTAMP,
				'submittimes' => $status['submittimes'] + 1,
				'status' => 0,
				'message' => dhtmlspecialchars(addslashes($_POST['freezereson'])),
			]);
		} else {
			table_common_member_validate::t()->insert([
				'uid' => $_G['uid'],
				'submitdate' => TIMESTAMP,
				'moddate' => 0,
				'admin' => '',
				'submittimes' => 1,
				'status' => 0,
				'message' => dhtmlspecialchars(addslashes($_POST['freezereson'])),
				'remark' => '',
			], false, true);
		}
		manage_addnotify('verifyuser');
	}

	
	if(!empty($_GET['newpassword'])) {
		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}

		$reset_password_subject = [
			'tpl' => 'password_reset',
			'var' => [
				'username' => $_G['member']['username'],
				'bbname' => $_G['setting']['bbname'],
				'siteurl' => $_G['setting']['securesiteurl'],
				'datetime' => dgmdate(time(), 'Y-m-d H:i:s'),
				'clientip' => $_G['clientip']
			]
		];
		if(!sendmail("{$_G['member']['username']} <{$_G['member']['email']}>", $reset_password_subject)) {
			runlog('sendmail', "{$_G['member']['email']} sendmail failed.");
		}
	}

	
	if((strcmp($secmobiccnew, $_G['member']['secmobicc']) != 0 || strcmp($secmobilenew, $_G['member']['secmobile']) != 0) && (!$_G['setting']['smsstatus'] || $setarr['secmobilestatus'])) {
		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}

		$reset_secmobile_subject = [
			'tpl' => 'secmobile_reset',
			'var' => [
				'username' => $_G['member']['username'],
				'bbname' => $_G['setting']['bbname'],
				'siteurl' => $_G['setting']['securesiteurl'],
				'datetime' => dgmdate(time(), 'Y-m-d H:i:s'),
				'secmobile' => $_G['member']['secmobicc'].'-'.$_G['member']['secmobile'],
				'clientip' => $_G['clientip']
			]
		];
		if(!sendmail("{$_G['member']['username']} <{$_G['member']['email']}>", $reset_secmobile_subject)) {
			runlog('sendmail', "{$_G['member']['email']} sendmail failed.");
		}
	}

	if($authstr) {
		showmessage('profile_email_verify', 'home.php?mod=spacecp&ac=account');
	} else {
		showmessage('profile_succeed', 'home.php?mod=spacecp&ac=account');
	}
}

if($operation == 'password') {

	dheader('location: home.php?mod=spacecp&ac=account');

	$interval = $_G['setting']['mailinterval'] > 0 ? (int)$_G['setting']['mailinterval'] : 300;
	$resend = getcookie('resendemail');
	$resend = empty($resend) || (TIMESTAMP - $resend) > $interval;
	$newemail = getcookie('newemail');
	$space['newemail'] = !$space['emailstatus'] ? $space['email'] : '';
	if(!empty($newemail)) {
		$mailinfo = explode("\t", $newemail);
		if(is_array($mailinfo) && $mailinfo[0] == $_G['uid'] && isemail($mailinfo[1])) {
			if($space['emailstatus'] && !$space['freeze'] && strcasecmp($mailinfo[1], $space['email']) === 0) {
				dsetcookie('newemail', '', -1);
				$space['newemail'] = '';
			} else {
				$space['newemail'] = $mailinfo[1];
			}
		}
	}

	if(getgpc('resend') && $resend && $_GET['formhash'] == FORMHASH) {
		$toemail = $space['newemail'] ? $space['newemail'] : $space['email'];
		if(emailcheck_send($space['uid'], $toemail)) {
			dsetcookie('newemail', "{$space['uid']}\t$toemail\t{$_G['timestamp']}", 31536000);
			dsetcookie('resendemail', TIMESTAMP);
			showmessage('send_activate_mail_succeed', 'home.php?mod=spacecp&ac=account');
		} else {
			showmessage('send_activate_mail_error', 'home.php?mod=spacecp&ac=account', ['interval' => $interval]);
		}
	} elseif(getgpc('resend')) {
		showmessage('send_activate_mail_error', 'home.php?mod=spacecp&ac=account', ['interval' => $interval]);
	}
	if(!empty($space['newemail'])) {
		$acitvemessage = lang('spacecp', 'email_acitve_message', ['newemail' => $space['newemail'], 'imgdir' => $_G['style']['imgdir'], 'formhash' => FORMHASH]);
	}
	$actives = ['password' => ' class="a"'];
	$navtitle = lang('core', 'title_password_security');
	if($_G['member']['freeze'] == 2 || $_G['member']['freeze'] == -1) {
		$fzvalidate = table_common_member_validate::t()->fetch($space['uid']);
		$space['freezereson'] = $fzvalidate['message'];
		$space['freezemodremark'] = $fzvalidate['remark'];
		$space['freezemoddate'] = dgmdate($fzvalidate['moddate'], 'Y-m-d H:i:s');
		$space['freezemodadmin'] = $fzvalidate['admin'];
		$space['freezemodsubmittimes'] = $fzvalidate['submittimes'];
	}

} else {

	space_merge($space, 'field_home');
	space_merge($space, 'field_forum');

	require_once libfile('function/editor');
	$space['sightml'] = html2bbcode($space['sightml']);

	$vid = getgpc('vid') ? intval($_GET['vid']) : 0;

	$privacy = $space['privacy']['profile'] ? $space['privacy']['profile'] : [];
	$_G['setting']['privacy'] = $_G['setting']['privacy'] ? $_G['setting']['privacy'] : [];
	$_G['setting']['privacy'] = is_array($_G['setting']['privacy']) ? $_G['setting']['privacy'] : dunserialize($_G['setting']['privacy']);
	$_G['setting']['privacy']['profile'] = !empty($_G['setting']['privacy']['profile']) ? $_G['setting']['privacy']['profile'] : [];
	$privacy = array_merge($_G['setting']['privacy']['profile'], $privacy);

	$actives = ['profile' => ' class="a"'];
	$opactives = [$operation => ' class="a"'];
	$allowitems = [];
	if(in_array($operation, ['base', 'contact', 'edu', 'work', 'info'])) {
		$allowitems = $profilegroup[$operation]['field'];
	} elseif($operation == 'verify') {
		if($vid == 0) {
			foreach($_G['setting']['verify'] as $key => $setting) {
				if($setting['available'] && checkverifyperm($setting)) {
					$_GET['vid'] = $vid = $key;
					break;
				}
			}
		}

		if(checkverifyperm($_G['setting']['verify'][$vid])) {
			$actives = ['verify' => ' class="a"'];
			$opactives = [$operation.$vid => ' class="a"'];
			$allowitems = $_G['setting']['verify'][$vid]['field'];
		}
	}
	$showbtn = ($vid && $verify['verify'.$vid] != 1) || empty($vid);
	if(!empty($verify) && is_array($verify)) {
		foreach($verify as $key => $flag) {
			if(in_array($key, ['verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6']) && $flag == 1) {
				$verifyid = intval(substr($key, -1, 1));
				if($_G['setting']['verify'][$verifyid]['available']) {
					foreach($_G['setting']['verify'][$verifyid]['field'] as $field) {
						$_G['cache']['profilesetting'][$field]['unchangeable'] = 1;
					}
				}
			}
		}
	}
	if($vid) {
		if($value = table_common_member_verify_info::t()->fetch_by_uid_verifytype($_G['uid'], $vid)) {
			$field = dunserialize($value['field']);
			foreach($field as $key => $fvalue) {
				$space[$key] = $fvalue;
			}
		}
	}
	$htmls = $settings = [];
	foreach($allowitems as $fieldid) {
		if(!in_array($fieldid, ['sightml', 'customstatus', 'timeoffset'])) {
			$html = profile_setting($fieldid, $space, true);
			if($html) {
				$settings[$fieldid] = $_G['cache']['profilesetting'][$fieldid];
				$htmls[$fieldid] = $html;
			}
		}
	}

}

include template('home/spacecp_profile');

function profile_showerror($key, $extrainfo = '') {
	echo '<script>';
	echo 'parent.show_error("'.$key.'", "'.$extrainfo.'");';
	echo '</script>';
	exit();
}

function profile_showsuccess($message = '') {
	if(!defined('IN_RESTFUL')) {
		echo '<script type="text/javascript">';
		echo "parent.show_success('$message');";
		echo '</script>';
		exit();
	} else {
		showmessage($message);
	}
}

function checkverifyperm($verifyconfig) {
	global $_G;

	return empty($verifyconfig['groupid']) || in_array($_G['groupid'], $verifyconfig['groupid']);
}
