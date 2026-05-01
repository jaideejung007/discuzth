<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$bid = intval($_GET['bid']);
if(!submitcheck('permsubmit')) {
	loadcache('diytemplatename');
	$block = table_common_block::t()->fetch($bid);
	shownav('portal', 'block', 'block_perm');
	showchildmenu([['block', 'block'], [$block['name'] ? $block['name'].' ' : cplang('block_name_null'), '']], cplang('block_perm_edit'));
	showtips('block_perm_tips');
	showformheader("block&operation=perm&bid=$bid");

	$inheritance_checked = !$block['notinherited'] ? 'checked' : '';
	showtableheader('<label><input class="checkbox" type="checkbox" name="inheritance" value="1" '.$inheritance_checked.'/>'.cplang('block_perm_inheritance').'</label>', 'fixpadding');

	showsubtitle(['', 'username',
		'<input class="checkbox" type="checkbox" name="chkallmanage" onclick="checkAll(\'prefix\', this.form, \'allowmanage\', \'chkallmanage\')" id="chkallmanage" /><label for="chkallmanage">'.cplang('block_perm_manage').'</label>',
		'<input class="checkbox" type="checkbox" name="chkallrecommend" onclick="checkAll(\'prefix\', this.form, \'allowrecommend\', \'chkallrecommend\')" id="chkallrecommend" /><label for="chkallrecommend">'.cplang('block_perm_recommend').'</label>',
		'<input class="checkbox" type="checkbox" name="chkallneedverify" onclick="checkAll(\'prefix\', this.form, \'needverify\', \'chkallneedverify\')" id="chkallneedverify" /><label for="chkallneedverify">'.cplang('block_perm_needverify').'</label>',
		'block_perm_inherited'
	]);

	$block_per = table_common_block_permission::t()->fetch_all_by_bid($bid);
	$members = table_common_member::t()->fetch_all(array_keys($block_per));
	$line = '&minus;';
	foreach($block_per as $uid => $value) {
		if(!empty($value['inheritedtplname'])) {
			showtablerow('', ['class="td25"'], [
				'',
				"{$members[$uid]['username']}",
				$value['allowmanage'] ? '&radic;' : $line,
				$value['allowrecommend'] ? '&radic;' : $line,
				$value['needverify'] ? '&radic;' : $line,
				'<a href="'.ADMINSCRIPT.'?action=diytemplate&operation=perm&targettplname='.$value['inheritedtplname'].'">'.$_G['cache']['diytemplatename'][$value['inheritedtplname']].'</a>',
			]);
		} else {
			showtablerow('', ['class="td25"'], [
				"<input type=\"checkbox\" class=\"checkbox\" name=\"delete[$uid]\" value=\"$uid\" />
					<input type=\"hidden\" name=\"perm[$uid][allowmanage]\" value=\"{$value['allowmanage']}\" />
					<input type=\"hidden\" name=\"perm[$uid][allowrecommend]\" value=\"{$value['allowrecommend']}\" />
					<input type=\"hidden\" name=\"perm[$uid][needverify]\" value=\"{$value['needverify']}\" />",
				"{$members[$uid]['username']}",
				"<input type=\"checkbox\" class=\"checkbox\" name=\"allowmanage[$uid]\" value=\"1\" ".($value['allowmanage'] ? 'checked' : '').' />',
				"<input type=\"checkbox\" class=\"checkbox\" name=\"allowrecommend[$uid]\" value=\"1\" ".($value['allowrecommend'] ? 'checked' : '').' />',
				"<input type=\"checkbox\" class=\"checkbox\" name=\"needverify[$uid]\" value=\"1\" ".($value['needverify'] ? 'checked' : '').' />',
				$line,
			]);
		}
	}
	showtablerow('', ['class="td25"'], [
		cplang('add_new'),
		'<input type="text" class="txt" name="newuser" value="" size="20" />',
		'<input type="checkbox" class="checkbox" name="newallowmanage" value="1" />',
		'<input type="checkbox" class="checkbox" name="newallowrecommend" value="1" />',
		'<input type="checkbox" class="checkbox" name="newneedverify" value="1" />',
		'',
	]);

	showsubmit('permsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
} else {

	if(!($block = table_common_block::t()->fetch($bid))) {
		cpmsg('block_not_exists');
	}

	$users = [];
	if(is_array($_GET['perm'])) {
		foreach($_GET['perm'] as $uid => $value) {
			$user = [];
			if(empty($_GET['delete']) || !in_array($uid, $_GET['delete'])) {
				$user = [];
				$user['allowmanage'] = $_GET['allowmanage'][$uid] ? 1 : 0;
				$user['allowrecommend'] = $_GET['allowrecommend'][$uid] ? 1 : 0;
				$user['needverify'] = $_GET['needverify'][$uid] ? 1 : 0;
				if($value['allowmanage'] != $user['allowmanage'] || $value['allowrecommend'] != $user['allowrecommend'] || $value['needverify'] != $user['needverify']) {
					$user['uid'] = intval($uid);
					$users[] = $user;
				}
			}
		}
	}
	if(!empty($_GET['newuser'])) {
		$uid = table_common_member::t()->fetch_uid_by_username($_GET['newuser']);
		if($uid) {
			$user['uid'] = $uid;
			$user['allowmanage'] = $_GET['newallowmanage'] ? 1 : 0;
			$user['allowrecommend'] = $_GET['newallowrecommend'] ? 1 : 0;
			$user['needverify'] = $_GET['newneedverify'] ? 1 : 0;
			$users[$user['uid']] = $user;
		} else {
			cpmsg_error($_GET['newuser'].cplang('block_has_no_allowauthorizedblock'));
		}
	}

	require_once libfile('class/blockpermission');
	$blockpermsission = &block_permission::instance();
	if(!empty($users)) {
		$blockpermsission->add_users_perm($bid, $users);
	}

	if(!empty($_GET['delete'])) {
		$blockpermsission->delete_users_perm($bid, $_GET['delete']);
	}

	$notinherited = !$_POST['inheritance'] ? '1' : '0';
	if($notinherited != $block['notinherited']) {
		if($notinherited) {
			$blockpermsission->delete_inherited_perm_by_bid($bid);
		} else {
			$blockpermsission->remake_inherited_perm($bid);
		}
		table_common_block::t()->update($bid, ['notinherited' => $notinherited]);
	}

	cpmsg('block_perm_update_succeed', "action=block&operation=perm&bid=$bid", 'succeed');
}
	