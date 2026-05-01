<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('diytemplatename');
$targettplname = $_GET['targettplname'];
$tpldirectory = $_GET['tpldirectory'];
$diydata = table_common_diy_data::t()->fetch_diy($targettplname, $tpldirectory);
if(empty($diydata)) {
	cpmsg_error('diytemplate_targettplname_error', dreferer());
}
if(!submitcheck('permsubmit')) {
	shownav('portal', 'diytemplate', 'diytemplate_perm');
	if(!empty($_GET['from']) && $_GET['from'] == 'portalcategory') {
		$menuroot = ['portalcategory', 'portalcategory'];
	} else {
		$menuroot = ['diytemplate', 'diytemplate'];
	}
	showchildmenu([$menuroot, [$diydata['name'] ?: $_G['cache']['diytemplatename'][$diydata['targettplname']].' ', '']], cplang('diytemplate_perm_edit'));
	showtips('diytemplate_perm_tips');
	showformheader("diytemplate&operation=perm&targettplname=$targettplname&tpldirectory=$tpldirectory");
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'username',
		'<input class="checkbox" type="checkbox" name="chkallmanage" onclick="checkAll(\'prefix\', this.form, \'allowmanage\', \'chkallmanage\')" id="chkallmanage" /><label for="chkallmanage">'.cplang('block_perm_manage').'</label>',
		'<input class="checkbox" type="checkbox" name="chkallrecommend" onclick="checkAll(\'prefix\', this.form, \'allowrecommend\', \'chkallrecommend\')" id="chkallrecommend" /><label for="chkallrecommend">'.cplang('block_perm_recommend').'</label>',
		'<input class="checkbox" type="checkbox" name="chkallneedverify" onclick="checkAll(\'prefix\', this.form, \'needverify\', \'chkallneedverify\')" id="chkallneedverify" /><label for="chkallneedverify">'.cplang('block_perm_needverify').'</label>',
		'block_perm_inherited'
	]);

	$allpermission = table_common_template_permission::t()->fetch_all_by_targettplname($targettplname);
	$allusername = table_common_member::t()->fetch_all_username_by_uid(array_keys($allpermission));
	$line = '&minus;';
	foreach($allpermission as $uid => $value) {
		if(!empty($value['inheritedtplname'])) {
			showtablerow('', ['class="td25"'], [
				'',
				"$allusername[$uid]",
				$value['allowmanage'] ? '&radic;' : $line,
				$value['allowrecommend'] ? '&radic;' : $line,
				$value['needverify'] ? '&radic;' : $line,
				'<a href="'.ADMINSCRIPT.'?action=diytemplate&operation=perm&targettplname='.$value['inheritedtplname'].'">'.$_G['cache']['diytemplatename'][$value['inheritedtplname']].'</a>',
			]);
		} else {
			showtablerow('', ['class="td25"'], [
				"<input type=\"checkbox\" class=\"checkbox\" name=\"delete[{$value['uid']}]\" value=\"{$value['uid']}\" />
					<input type=\"hidden\" name=\"perm[{$value['uid']}][allowmanage]\" value=\"{$value['allowmanage']}\" />
					<input type=\"hidden\" name=\"perm[{$value['uid']}][allowrecommend]\" value=\"{$value['allowrecommend']}\" />
					<input type=\"hidden\" name=\"perm[{$value['uid']}][needverify]\" value=\"{$value['needverify']}\" />",
				"$allusername[$uid]",
				"<input type=\"checkbox\" class=\"checkbox\" name=\"allowmanage[{$value['uid']}]\" value=\"1\" ".($value['allowmanage'] ? 'checked' : '').' />',
				"<input type=\"checkbox\" class=\"checkbox\" name=\"allowrecommend[{$value['uid']}]\" value=\"1\" ".($value['allowrecommend'] ? 'checked' : '').' />',
				"<input type=\"checkbox\" class=\"checkbox\" name=\"needverify[{$value['uid']}]\" value=\"1\" ".($value['needverify'] ? 'checked' : '').' />',
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

	$users = [];
	if(!empty($_GET['newuser'])) {
		$uid = table_common_member::t()->fetch_uid_by_username($_GET['newuser']);
		if($uid) {
			$user = [];
			$user['uid'] = $uid;
			$user['allowmanage'] = $_GET['newallowmanage'] ? 1 : 0;
			$user['allowrecommend'] = $_GET['newallowrecommend'] ? 1 : 0;
			$user['needverify'] = $_GET['newneedverify'] ? 1 : 0;
			$users[] = $user;
		} else {
			cpmsg_error($_GET['newuser'].cplang('block_has_no_allowauthorizedblock'), dreferer());
		}
	}
	if(is_array($_GET['perm'])) {
		foreach($_GET['perm'] as $uid => $value) {
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
	if(!empty($users) || $_GET['delete']) {
		require_once libfile('class/blockpermission');
		$tplpermsission = &template_permission::instance();
		if($_GET['delete']) {
			$tplpermsission->delete_users($targettplname, $_GET['delete']);
		}

		if(!empty($users)) {
			$tplpermsission->add_users($targettplname, $users);
		}
	}
	cpmsg('diytemplate_perm_update_succeed', "action=diytemplate&operation=perm&targettplname=$targettplname&tpldirectory=$tpldirectory", 'succeed');
}
	