<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_GET['op'] = empty($_GET['op']) ? '' : trim($_GET['op']);

if(empty($_G['setting']['sendmailday'])) {
	showmessage('no_privilege_sendmailday');
}

if(submitcheck('setsendemailsubmit')) {
	$_GET['sendmail'] = serialize($_GET['sendmail']);
	table_common_member_field_home::t()->update($_G['uid'], ['acceptemail' => $_GET['sendmail']]);
	showmessage('do_success', 'home.php?mod=spacecp&ac=sendmail');
}


if(empty($space['email']) || !isemail($space['email'])) {
	showmessage('email_input');
}

$sendmail = [];
if($space['acceptemail'] && is_array($space['acceptemail'])) {
	foreach($space['acceptemail'] as $mkey => $mailset) {
		if($mkey != 'frequency') {
			$sendmail[$mkey] = empty($space['acceptemail'][$mkey]) ? '' : ' checked';
		} else {
			$sendmail[$mkey] = [$space['acceptemail']['frequency'] => 'selected'];
		}
	}
}

include_once template('home/spacecp_sendmail');

