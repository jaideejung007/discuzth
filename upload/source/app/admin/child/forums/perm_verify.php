<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$s = '';
$exists = false;
foreach($_G['setting']['verify'] as $vid => $verify) {
	if(!$verify['available']) {
		continue;
	}
	$colums = [
		'<input class="checkbox" title="'.cplang('select_all').'" type="checkbox" name="chkallverify'.$vid.'" onclick="checkAll(\'value\', this.form, \'verify'.$vid.'\', \'chkallverify'.$vid.'\')" id="chkallverify_'.$vid.'" />',
		'<label for="chkallverify_'.$vid.'"> '.$verify['title'].'</label>', 'v'.$vid];
	foreach($perms as $perm) {
		$checked = str_contains($forum[$perm], "\tv$vid\t") ? 'checked="checked"' : NULL;
		$checked && $exists = true;
		$colums[] = '<input class="checkbox" type="checkbox" name="'.$perm.'[]" value="v'.$vid.'" chkvalue="verify'.$vid.'" '.$checked.'>';
	}
	$s .= showtablerow('', ['', '', 'class="lightfont"'], $colums, true);
}
if($s) {
	$tg = '<a href="javascript:;" onclick="toggle_group(\'gverify\', this)">['.($exists ? '-' : '+').']</a>';
	showtablerow('', ['', '', 'class="lightfont" colspan="'.$permcolspan.'"'], [$tg, '<b>'.$lang['forums_edit_perm_verify'].'</b>', 'verify']);
	showtagheader('tbody', 'gverify', $exists);
	echo $s;
	$permtits = [
		'<input class="checkbox" title="'.cplang('select_all').'" type="checkbox" name="chkallmverify" onclick="checkAll(\'value\', this.form, \'mverify\', \'chkallmverify\')" id="chkallmverify" />',
		'<label for="chkallmverify"> <b><i>'.cplang('forums_edit_perm_mustall').'</i></b></label>', ''];
	foreach($perms as $perm) {
		$checked = preg_match("/(^|\t)_v\[(.+?)\]/", $forum[$perm]) ? 'checked="checked"' : NULL;
		$permtits[] = '<input class="checkbox" type="checkbox" name="mverify['.$perm.']" value="'.$perm.'" chkvalue="mverify" '.$checked.'>';
	}
	showtablerow('', '', $permtits);
	showtagfooter('tbody');
}