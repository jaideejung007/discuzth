<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('RUN_MODE') || RUN_MODE != 'tool') {
	show_msg('method_undefined', $method, 0);
}

$db = new dbstuff;
$db->connect($_config['db'][1]['dbhost'], $_config['db'][1]['dbuser'], $_config['db'][1]['dbpw'], $_config['db'][1]['dbname'], DBCHARSET);
$db->fetch_first("SELECT * FROM {$_config['db'][1]['tablepre']}common_member WHERE uid = 1", $member);

if(empty($_POST['password']) || empty($_POST['password2'])) {
	$founders = @explode(',', $_config['admincp']['founder']);
	if(!in_array(1, $founders)) {
		show_msg('tool_resetpw_uid1', '', 0);
	}

	show_header();
	echo '</div><div class="main">';
	show_setting('start');
	show_setting('hidden', 'method', 'resetpw');
	echo '<div class="desc">'.lang('tool_resetpw_founder').'</div>';
	echo '<div class="box">';
	show_setting('', '', '<label class="tbopt">'.lang('tool_resetpw_loginname').':</label>'.$member['loginname'], '');
	show_setting('tool_resetpw_password', 'password', '', 'password');
	show_setting('tool_resetpw_password2', 'password2', '', 'password');
	echo '</div>';
	echo '<div class="btnbox">';
	show_setting('', 'submitname', 'new_step', 'oldbtn|submit');
	echo '</div>';
	show_setting('end');
	show_footer();
} else {
	if($_POST['password'] != $_POST['password2']) {
		show_msg('tool_resetpw_password_error', '', 0);
	}

	require_once ROOT_PATH.'./source/class/uc/client.php';

	$ucresult = uc_user_edit(addslashes($member['loginname']), $_POST['password'], $_POST['password'], '', 1, 0);
	if($ucresult < 0) {
		show_msg('ucenter_error', $ucresult, 0);
	}

	$db->query("UPDATE {$_config['db'][1]['tablepre']}common_member SET password = '".md5(random(10))."' WHERE uid = 1");

	show_header();
	echo '</div><div class="main">';
	echo '<div class="box">';
	show_tips('tool_resetpw_success');
	echo '</div>
		<div class="btnbox">
			<em>'.lang('tool_tips').'</em>
			<div class="inputbox">
			<input type="button" name="oldbtn" value="'.lang('old_step').'" class="btn oldbtn" onclick="location.href=\'?\'">
			<input type="button" value="'.lang('done').'" class="btn" onclick="location.href=\'?method=done\'">
	      	</div></div>';
	show_footer();
}