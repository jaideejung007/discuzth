<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$return = isset($_GET['return']) && $_GET['return'] ? 'admin' : '';

list($pluginsetting, $pluginvalue) = get_pluginsetting('groups');

list($stylesetting, $stylevalue) = get_stylesetting('groups');

$multiset = 0;
$gids = [];
if(empty($_GET['multi'])) {
	if($_GET['id']) {
		$gids[0] = $_GET['id'];
	}
} else {
	$multiset = 1;
	if(is_array($_GET['multi'])) {
		$gids = &$_GET['multi'];
	} else {
		$_GET['multi'] = explode(',', $_GET['multi']);
		$gids = &$_GET['multi'];
	}
}
if(!empty($_GET['multi']) && is_array($_GET['multi']) && count($_GET['multi']) == 1) {
	if($_GET['multi'][0]) {
		$gids[0] = $_GET['multi'][0];
	}
	$multiset = 0;
}


if(!count($gids)) {
	$grouplist = "<select name=\"id\" style=\"width:150px\">\n";
	$conditions = !empty($_GET['anchor']) && $_GET['anchor'] == 'system' ? 'special' : '';
	foreach(table_common_usergroup::t()->fetch_all_by_type($conditions) as $group) {
		$grouplist .= "<option value=\"{$group['groupid']}\">{$group['grouptitle']}</option>\n";
	}
	$grouplist .= '</select>';
	cpmsg('usergroups_edit_nonexistence', 'action=usergroups&operation=edit'.(!empty($_GET['highlight']) ? "&highlight={$_GET['highlight']}" : '').(!empty($_GET['highlight']) ? "&anchor={$_GET['anchor']}" : ''), 'form', [], $grouplist);
}

$group_data = table_common_usergroup::t()->fetch_all_usergroup($gids);
$groupfield_data = table_common_usergroup_field::t()->fetch_all($gids);
if(!$group_data) {
	cpmsg('usergroups_nonexistence', '', 'error');
} else {
	foreach($group_data as $curgid => $group) {
		$group = array_merge($group, (array)$groupfield_data[$curgid]);
		if(isset($pluginvalue[$group['groupid']])) {
			$group['plugin'] = $pluginvalue[$group['groupid']];
		}
		if(isset($stylevalue[$group['groupid']])) {
			$group['style'] = $stylevalue[$group['groupid']];
		}
		$group['fields'] = !empty($group['fields']) ? json_decode($group['fields'], true) : [];
		$mgroup[] = $group;
	}
}

$allowthreadplugin = $_G['setting']['threadplugins'] ? table_common_setting::t()->fetch_setting('allowthreadplugin', true) : [];
if(!submitcheck('detailsubmit') && !submitcheck('multijssubmit')) {

	$grouplist = $groupcount = [];
	foreach(table_common_usergroup::t()->range_orderby_credit() as $ggroup) {
		$checked = $_GET['id'] == $ggroup['groupid'] || (is_array($_GET['multi']) && in_array($ggroup['groupid'], $_GET['multi']));
		$ggroup['type'] = $ggroup['type'] == 'special' && $ggroup['radminid'] ? 'specialadmin' : $ggroup['type'];
		$groupcount[$ggroup['type']]++;
		$grouplist[$ggroup['type']] .= '<input class="left checkbox ck" chkvalue="'.$ggroup['type'].'" name="multi[]" value="'.$ggroup['groupid'].'" type="checkbox" '.($checked ? 'checked="checked" ' : '').'/>'.
			'<a href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=usergroups&operation=edit&switch=yes&id='.$ggroup['groupid'].'&anchor=\'+currentAnchor+\'&scrolltop=\'+scrollTopBody()"'.($checked ? ' class="current"' : '').'>'.$ggroup['grouptitle'].'</a>';
		if(!($groupcount[$ggroup['type']] % 3)) {
			$grouplist[$ggroup['type']] .= '<br style="clear:both" />';
		}
	}
	$gselect = '<span id="ugselect" class="right popupmenu_dropmenu" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'34\'});$(\'ugselect_menu\').style.top=(parseInt($(\'ugselect_menu\').style.top)-scrollTopBody())+\'px\';$(\'ugselect_menu\').style.left=(parseInt($(\'ugselect_menu\').style.left)-document.documentElement.scrollLeft-20)+\'px\'">'.$lang['usergroups_switch'].'<em>&nbsp;&nbsp;</em></span>'.
		'<div id="ugselect_menu" class="popupmenu_popup" style="display:none">'.
		'<em class="cl"><span class="right"><input name="checkall_member" onclick="checkAll(\'value\', this.form, \'member\', \'checkall_member\')" type="checkbox" class="vmiddle checkbox" /></span>'.$lang['usergroups_member'].'</em>'.$grouplist['member'].'<br />'.
		($grouplist['special'] ? '<em class="cl"><span class="right"><input name="checkall_special" onclick="checkAll(\'value\', this.form, \'special\', \'checkall_special\')" type="checkbox" class="vmiddle checkbox" /></span>'.$lang['usergroups_special'].'</em>'.$grouplist['special'].'<br />' : '').
		($grouplist['specialadmin'] ? '<em class="cl"><span class="right"><input name="checkall_specialadmin" onclick="checkAll(\'value\', this.form, \'specialadmin\', \'checkall_specialadmin\')" type="checkbox" class="vmiddle checkbox" /></span>'.$lang['usergroups_specialadmin'].'</em>'.$grouplist['specialadmin'].'<br />' : '').
		'<em class="cl"><span class="right"><input name="checkall_system" onclick="checkAll(\'value\', this.form, \'system\', \'checkall_system\')" type="checkbox" class="vmiddle checkbox" /></span>'.$lang['usergroups_system'].'</em>'.$grouplist['system'].
		'<br style="clear:both" /><div class="cl"><input type="button" class="btn right" onclick="multiselect(\'menuform\')" value="'.cplang('usergroups_multiedit').'" /></div>'.
		'</div>';
	$anchor = in_array($_GET['anchor'], ['basic', 'system', 'special', 'post', 'attach', 'magic', 'invite', 'pm', 'credit', 'home', 'group', 'portal', 'plugin', 'style']) ? $_GET['anchor'] : 'basic';
	showformheader('', '', 'menuform', 'get');
	showhiddenfields(['action' => 'usergroups', 'operation' => 'edit']);
	showchildmenu([['nav_usergroups', 'usergroups']], (count($mgroup) == 1 ? $mgroup[0]['grouptitle'].'(groupid:'.$mgroup[0]['groupid'].')' : cplang('multiedit')), [
		['usergroups_edit_basic', 'basic', $anchor == 'basic'],
		count($mgroup) == 1 && $mgroup[0]['type'] == 'special' && $mgroup[0]['radminid'] < 1 ? ['usergroups_edit_system', 'system', $anchor == 'system'] : [],
		[['menu' => 'usergroups_edit_forum', 'submenu' => [
			['usergroups_edit_post', 'post', $anchor == 'post'],
			['usergroups_edit_attach', 'attach', $anchor == 'attach'],
			['usergroups_edit_special', 'special', $anchor == 'special']
		]]],
		['usergroups_edit_group', 'group', $anchor == 'group'],
		['usergroups_edit_portal', 'portal', $anchor == 'portal'],
		['usergroups_edit_home', 'home', $anchor == 'home'],
		['usergroups_edit_credit', 'credit', $anchor == 'credit'],
		['usergroups_edit_magic', 'magic', $anchor == 'magic'],
		['usergroups_edit_invite', 'invite', $anchor == 'invite'],
		$pluginsetting || $stylesetting ? [['menu' => 'usergroups_edit_other', 'submenu' => [
			!$pluginsetting ? [] : ['usergroups_edit_plugin', 'plugin', $anchor == 'plugin'],
			!$stylesetting ? [] : ['usergroups_edit_style', 'style', $anchor == 'style'],
		]]] : [],
	], $gselect, true);
	showformfooter();

	if(count($mgroup) == 1 && $mgroup[0]['type'] == 'special' && $mgroup[0]['radminid'] < 1) {
		showtips('usergroups_edit_system_tips', 'system_tips', $anchor == 'system');
	}
	if($multiset) {
		showtips('setting_multi_tips');
	}

	showtips('usergroups_edit_magic_tips', 'magic_tips', $anchor == 'magic');
	showtips('usergroups_edit_invite_tips', 'invite_tips', $anchor == 'invite');
	if($_GET['id'] == 7) {
		showtips('usergroups_edit_system_guest_portal_tips', 'portal_tips', $anchor == 'portal');
		showtips('usergroups_edit_system_guest_home_tips', 'home_tips', $anchor == 'home');
	}

	if($multiset) {
		$_G['showsetting_multi'] = 0;
		$_G['showsetting_multicount'] = count($mgroup);
		foreach($mgroup as $group) {
			$_G['showtableheader_multi'][] = '<a href="javascript:;" onclick="location.href=\''.ADMINSCRIPT.'?action=usergroups&operation=edit&id='.$group['groupid'].'&anchor=\'+$(\'cpform\').anchor.value;return false">'.$group['grouptitle'].'(groupid:'.$group['groupid'].')</a>';
		}
	}

	showformheader("usergroups&operation=edit&id={$_GET['id']}&return=$return", 'enctype');
	$mgids = [];
	foreach($mgroup as $group) {
		$_GET['id'] = $gid = $group['groupid'];
		$mgids[] = $gid;

		if(!$multiset && $group['type'] == 'special' && $group['radminid'] < 1) {
			/*search={"nav_usergroups":"action=usergroups","usergroups_edit_basic":"action=usergroups&operation=edit&anchor=system"}*/
			showtagheader('div', 'system', $anchor == 'system');
			showtableheader();
			if($group['system'] == 'private') {
				$system = ['public' => 0, 'dailyprice' => 0, 'minspan' => 0];
			} else {
				$system = ['public' => 1, 'dailyprice' => 0, 'minspan' => 0];
				list($system['dailyprice'], $system['minspan']) = explode("\t", $group['system']);
			}
			showsetting('usergroups_up_groupid_main', ['upgroupid[main]', [
				[1, cplang('yes'), ['upgroupidSub' => 'none', 'upgroupidMain' => '']],
				[0, cplang('no'), ['upgroupidSub' => '', 'upgroupidMain' => 'none']],
			], 1], $group['upgroupid'] == $group['groupid'], 'mradio');
			showtagheader('tbody', 'upgroupidMain', $group['upgroupid'] == $group['groupid'], 'sub');
			$v = !empty($_G['setting']['upgroup_name'][$group['groupid']]) ? $_G['setting']['upgroup_name'][$group['groupid']] : '';
			showsetting('usergroups_up_groupid_name', 'upgroup_name', $v, 'text');

			$extcreditsbtn = '';
			for($i = 1; $i <= 8; $i++) {
				$extcredittitle = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : cplang('setting_credits_formula_extcredits').$i;
				$resultstr .= 'result = result.replace(/extcredits'.$i.'/g, \'<u>'.str_replace("'", "\'", $extcredittitle).'</u>\');'."\r\n";
				$extcreditsbtn .= '<a href="###" onclick="creditinsertunit(\'extcredits'.$i.'\')">'.$extcredittitle.'</a> &nbsp;';
			}
			$formulareplace = '\'<u>'.cplang('setting_credits_formula_digestposts').'</u>\',\'<u>'.cplang('setting_credits_formula_posts').'</u>\'';
			?>
			<script type="text/JavaScript">
				function isUndefined(variable) {
					return typeof variable == 'undefined' ? true : false;
				}

				function creditinsertunit(text, textend) {
					insertunit($('creditsformula'), text, textend);
					formulaexp();
				}

				var formulafind = new Array('digestposts', 'posts');
				var formulareplace = new Array(<?php echo $formulareplace?>);

				function formulaexp() {
					var result = $('creditsformula').value;
					<?php
					echo $resultstr;
					echo 'result = result.replace(/digestposts/g, \'<u>'.$lang['setting_credits_formula_digestposts'].'</u>\');';
					echo 'result = result.replace(/posts/g, \'<u>'.$lang['setting_credits_formula_posts'].'</u>\');';
					echo 'result = result.replace(/threads/g, \'<u>'.$lang['setting_credits_formula_threads'].'</u>\');';
					echo 'result = result.replace(/oltime/g, \'<u>'.$lang['setting_credits_formula_oltime'].'</u>\');';
					echo 'result = result.replace(/friends/g, \'<u>'.$lang['setting_credits_formula_friends'].'</u>\');';
					echo 'result = result.replace(/doings/g, \'<u>'.$lang['setting_credits_formula_doings'].'</u>\');';
					echo 'result = result.replace(/blogs/g, \'<u>'.$lang['setting_credits_formula_blogs'].'</u>\');';
					echo 'result = result.replace(/albums/g, \'<u>'.$lang['setting_credits_formula_albums'].'</u>\');';
					echo 'result = result.replace(/sharings/g, \'<u>'.$lang['setting_credits_formula_sharings'].'</u>\');';
					?>
					$('formulapermexp').innerHTML = result;
				}

			</script>

			<?php
			print <<<EOF
			<tr>
				<td class="td27" colspan="2">{$lang['usergroups_up_creditsformula']}:</td>
			</tr>
			<tr>
				<td colspan="2" class="rowform">
					<div class="extcredits">
						$extcreditsbtn
						<a href="###" onclick="creditinsertunit(' posts ')">{$lang['setting_credits_formula_posts']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' threads ')">{$lang['setting_credits_formula_threads']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' digestposts ')">{$lang['setting_credits_formula_digestposts']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' oltime ')">{$lang['setting_credits_formula_oltime']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' friends ')">{$lang['setting_credits_formula_friends']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' doings ')">{$lang['setting_credits_formula_doings']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' blogs ')">{$lang['setting_credits_formula_blogs']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' albums ')">{$lang['setting_credits_formula_albums']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' sharings ')">{$lang['setting_credits_formula_sharings']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' + ')">&nbsp;+&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' - ')">&nbsp;-&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' * ')">&nbsp;*&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' / ')">&nbsp;/&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' (', ') ')">&nbsp;(&nbsp;)&nbsp;</a>&nbsp;
					</div>
					<div id="formulapermexp" class="margintop marginbot diffcolor2">$formulapermexp</div>
					<textarea name="creditsformulanew" id="creditsformula" class="marginbot" style="width:80%" rows="3" onkeyup="formulaexp()" onkeydown="textareakey(this, event)">{$group['creditsformula']}</textarea>
					<script type="text/JavaScript">formulaexp()</script>
					<br /><span class="smalltxt">{$lang['usergroups_up_creditsformula_comment']}</span>
				</td>
			</tr>
EOF;

			showtagfooter('tbody');
			showtagheader('tbody', 'upgroupidSub', $group['upgroupid'] != $group['groupid'], 'sub');
			$gselect = [[0, cplang('none')]];
			foreach(table_common_usergroup::t()->range_orderby_creditshigher() as $v) {
				if($v['upgroupid'] == $v['groupid'] && $v['groupid'] != $group['groupid']) {
					$gselect[] = [$v['groupid'], $_G['setting']['upgroup_name'][$v['groupid']]];
				}
			}
			showsetting('usergroups_up_groupid_sub', ['upgroupid[sub]', $gselect], $group['upgroupid'], 'select');
			showtagfooter('tbody');

			showsetting('usergroups_edit_system_public', 'system_publicnew', $system['public'], 'radio', 0, 1);
			showsetting('usergroups_edit_system_dailyprice', 'system_dailypricenew', $system['dailyprice'], 'text');
			showsetting('usergroups_edit_system_minspan', 'system_minspannew', $system['minspan'], 'text');
			showtablefooter();
			showtagfooter('div');
			/*search*/
		}

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_basic":"action=usergroups&operation=edit&anchor=basic"}*/
		showmultititle();
		showtagheader('div', 'basic', $anchor == 'basic');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_basic');
		showsetting('usergroups_edit_basic_title', 'grouptitlenew', $group['grouptitle'], 'text');
		$group['exempt'] = strrev(sprintf('%0'.strlen($group['exempt']).'b', $group['exempt']));
		if(!$multiset) {
			if($group['icon']) {
				$valueparse = parse_url($group['icon']);
				if(isset($valueparse['host'])) {
					$groupicon = $group['icon'];
				} else {
					$groupicon = $_G['setting']['attachurl'].'common/'.$group['icon'].'?'.random(6);
				}
				$groupiconhtml = '<label><input type="checkbox" class="checkbox" name="deleteicon['.$group['groupid'].']" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$groupicon.'" />';
			}
			showsetting('usergroups_icon', 'iconnew', $group['icon'], 'filetext', '', 0, $groupiconhtml);
		}


		$group['allowvisit'] = $group['groupid'] == 1 ? 2 : $group['allowvisit'];

		showsetting('usergroups_edit_basic_visit', ['allowvisitnew', [
			[0, cplang('usergroups_edit_basic_visit_none')],
			[1, cplang('usergroups_edit_basic_visit_normal')],
			[2, cplang('usergroups_edit_basic_visit_super')],
		]], $group['allowvisit'], 'mradio');

		showsetting('usergroups_edit_basic_read_access', 'readaccessnew', $group['readaccess'], 'text');
		showsetting('usergroups_edit_basic_max_friend_number', 'maxfriendnumnew', $group['maxfriendnum'], 'text');
		showsetting('usergroups_edit_basic_domain_length', 'domainlengthnew', $group['domainlength'], 'text');
		showsetting('usergroups_edit_basic_invisible', 'allowinvisiblenew', $group['allowinvisible'], 'radio');
		showsetting('usergroups_edit_basic_allowtransfer', 'allowtransfernew', $group['allowtransfer'], 'radio');
		showsetting('usergroups_edit_basic_allowfollow', 'allowfollownew', $group['allowfollow'], 'radio');
		showsetting('usergroups_edit_basic_allowsendpm', 'allowsendpmnew', $group['allowsendpm'], 'radio', 0, 1);
		showsetting('usergroups_edit_pm_sendpmmaxnum', 'allowsendpmmaxnumnew', $group['allowsendpmmaxnum'], 'text');
		showsetting('usergroups_edit_pm_sendallpm', 'allowsendallpmnew', $group['allowsendallpm'], 'radio');
		showtagfooter('tbody');
		showsetting('usergroups_edit_post_html', 'allowhtmlnew', $group['allowhtml'], 'radio');
		showsetting('usergroups_edit_post_url', ['allowposturlnew', [
			[0, $lang['usergroups_edit_post_url_banned']],
			[1, $lang['usergroups_edit_post_url_mod']],
			[2, $lang['usergroups_edit_post_url_unhandle']],
			[3, $lang['usergroups_edit_post_url_enable']]
		]], $group['allowposturl'], 'mradio');
		showsetting('usergroups_edit_basic_allow_statdata', 'allowstatdatanew', $group['allowstatdata'], 'radio');
		showsetting('usergroups_edit_basic_allowavatarupload', 'allowavataruploadnew', $group['allowavatarupload'], 'radio');
		showsetting('usergroups_edit_basic_allowviewprofile', 'allowviewprofilenew', $group['allowviewprofile'], 'radio');
		showsetting('usergroups_edit_basic_search_post', 'allowfulltextnew', $group['allowsearch'] & 32, 'radio');
		$group['allowsearch'] = $group['allowsearch'] > 128 ? $group['allowsearch'] - 128 : $group['allowsearch'];
		showsetting('usergroups_edit_basic_search', ['allowsearchnew', [
			cplang('setting_search_status_portal'),
			cplang('setting_search_status_forum'),
			cplang('setting_search_status_blog'),
			cplang('setting_search_status_album'),
			cplang('setting_search_status_group'),
			false,
			cplang('setting_search_status_collection')
		]], $group['allowsearch'], 'binmcheckbox');
		showsetting('usergroups_edit_basic_reasonpm', ['reasonpmnew', [
			[0, $lang['usergroups_edit_basic_reasonpm_none']],
			[1, $lang['usergroups_edit_basic_reasonpm_reason']],
			[2, $lang['usergroups_edit_basic_reasonpm_pm']],
			[3, $lang['usergroups_edit_basic_reasonpm_both']]
		]], $group['reasonpm'], 'mradio');
		showsetting('usergroups_edit_basic_cstatus', 'allowcstatusnew', $group['allowcstatus'], 'radio');
		showsetting('usergroups_edit_basic_disable_periodctrl', 'disableperiodctrlnew', $group['disableperiodctrl'], 'radio');
		showsetting('usergroups_edit_basic_hour_threads', 'maxthreadsperhournew', intval($group['maxthreadsperhour']), 'text');
		showsetting('usergroups_edit_basic_hour_posts', 'maxpostsperhournew', intval($group['maxpostsperhour']), 'text');
		showsetting('usergroups_edit_basic_seccode', 'seccodenew', $group['seccode'], 'radio', $group['groupid'] == 7);
		showsetting('usergroups_edit_basic_forcesecques', 'forcesecquesnew', $group['forcesecques'], 'radio');
		if(!in_array($gid, [7, 8])) {
			showsetting('usergroups_edit_basic_forcelogin', ['forceloginnew', [
				[0, $lang['usergroups_edit_basic_forcelogin_none']],
				[2, $lang['usergroups_edit_basic_forcelogin_mail']],
			]], $group['forcelogin'], 'mradio');
		}
		showsetting('usergroups_edit_basic_disable_postctrl', 'disablepostctrlnew', $group['disablepostctrl'], 'radio');
		showsetting('usergroups_edit_basic_ignore_censor', 'ignorecensornew', $group['ignorecensor'], 'radio');
		showsetting('usergroups_edit_basic_allowcreatecollection', 'allowcreatecollectionnew', intval($group['allowcreatecollection']), 'text');
		showsetting('usergroups_edit_basic_allowfollowcollection', 'allowfollowcollectionnew', intval($group['allowfollowcollection']), 'text');
		showsetting('usergroups_edit_basic_close_ad', 'closeadnew', $group['closead'], 'radio');
		showsetting('usergroups_edit_post_tag', 'allowposttagnew', $group['allowposttag'], 'radio');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_special":"action=usergroups&operation=edit&anchor=special"}*/
		showtagheader('div', 'special', $anchor == 'special');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_special');
		showsetting('usergroups_edit_special_activity', 'allowpostactivitynew', $group['allowpostactivity'], 'radio');
		showsetting('usergroups_edit_special_poll', 'allowpostpollnew', $group['allowpostpoll'], 'radio');
		showsetting('usergroups_edit_special_vote', 'allowvotenew', $group['allowvote'], 'radio');
		showsetting('usergroups_edit_special_reward', 'allowpostrewardnew', $group['allowpostreward'], 'radio');
		showsetting('usergroups_edit_special_reward_min', 'minrewardpricenew', $group['minrewardprice'], 'text');
		showsetting('usergroups_edit_special_reward_max', 'maxrewardpricenew', $group['maxrewardprice'], 'text');
		showsetting('usergroups_edit_special_trade', 'allowposttradenew', $group['allowposttrade'], 'radio');
		showsetting('usergroups_edit_special_trade_min', 'mintradepricenew', $group['mintradeprice'], 'text');
		showsetting('usergroups_edit_special_trade_max', 'maxtradepricenew', $group['maxtradeprice'], 'text');
		showsetting('usergroups_edit_special_trade_stick', 'tradesticknew', $group['tradestick'], 'text');
		showsetting('usergroups_edit_special_debate', 'allowpostdebatenew', $group['allowpostdebate'], 'radio');
		showsetting('usergroups_edit_special_rushreply', 'allowpostrushreplynew', $group['allowpostrushreply'], 'radio');
		$threadpluginselect = [];
		if(is_array($_G['setting']['threadplugins'])) foreach($_G['setting']['threadplugins'] as $tpid => $data) {
			$threadpluginselect[] = [$tpid, $data['name']];
		}
		if($threadpluginselect) {
			showsetting('usergroups_edit_special_allowthreadplugin', ['allowthreadpluginnew', $threadpluginselect], $allowthreadplugin[$_GET['id']], 'mcheckbox');
		}
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_post":"action=usergroups&operation=edit&anchor=post"}*/
		showtagheader('div', 'post', $anchor == 'post');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_post');
		showsetting('usergroups_edit_post_new', 'allowpostnew', $group['allowpost'], 'radio');
		showsetting('usergroups_edit_post_reply', 'allowreplynew', $group['allowreply'], 'radio');
		showsetting('usergroups_edit_post_direct', ['allowdirectpostnew', [
			[0, $lang['usergroups_edit_post_direct_none']],
			[1, $lang['usergroups_edit_post_direct_reply']],
			[2, $lang['usergroups_edit_post_direct_thread']],
			[3, $lang['usergroups_edit_post_direct_all']]
		]], $group['allowdirectpost'], 'mradio');
		showsetting('usergroups_edit_post_allow_down_remote_img', 'allowdownremoteimgnew', $group['allowdownremoteimg'], 'radio');
		showsetting('usergroups_edit_post_anonymous', 'allowanonymousnew', $group['allowanonymous'], 'radio');
		showsetting('usergroups_edit_post_set_read_perm', 'allowsetreadpermnew', $group['allowsetreadperm'], 'radio');
		showsetting('usergroups_edit_post_maxprice', 'maxpricenew', $group['maxprice'], 'text');
		showsetting('usergroups_edit_post_hide_code', 'allowhidecodenew', $group['allowhidecode'], 'radio');
		showsetting('usergroups_edit_post_mediacode', 'allowmediacodenew', $group['allowmediacode'], 'radio');
		showsetting('usergroups_edit_post_begincode', 'allowbegincodenew', $group['allowbegincode'], 'radio');
		showsetting('usergroups_edit_post_sig_bbcode', 'allowsigbbcodenew', $group['allowsigbbcode'], 'radio');
		showsetting('usergroups_edit_post_sig_img_code', 'allowsigimgcodenew', $group['allowsigimgcode'], 'radio');
		showsetting('usergroups_edit_post_max_sig_size', 'maxsigsizenew', $group['maxsigsize'], 'text');
		if($group['groupid'] != 7) {
			showsetting('usergroups_edit_post_recommend', 'allowrecommendnew', $group['allowrecommend'], 'text');
		}
		showsetting('usergroups_edit_post_edit_time_limit', 'edittimelimitnew', intval($group['edittimelimit']), 'text');
		showsetting('usergroups_edit_post_allowreplycredit', 'allowreplycreditnew', $group['allowreplycredit'], 'radio');
		showsetting('usergroups_edit_post_allowcommentpost', ['allowcommentpostnew', [
			$lang['usergroups_edit_post_allowcommentpost_firstpost'],
			$lang['usergroups_edit_post_allowcommentpost_reply'],
		]], $group['allowcommentpost'], 'binmcheckbox', (!is_array($_G['setting']['allowpostcomment']) || !in_array(1, $_G['setting']['allowpostcomment'])));
		showsetting('usergroups_edit_post_allowcommentreply', 'allowcommentreplynew', $group['allowcommentreply'], 'radio', (!is_array($_G['setting']['allowpostcomment']) || !in_array(2, $_G['setting']['allowpostcomment'])));
		showsetting('usergroups_edit_post_allowcommentitem', 'allowcommentitemnew', $group['allowcommentitem'], 'radio', (!is_array($_G['setting']['allowpostcomment']) || !in_array(1, $_G['setting']['allowpostcomment'])));
		showsetting('usergroups_edit_post_allowat', 'allowatnew', $group['allowat'], 'text');
		showsetting('usergroups_edit_post_allowsave', 'allowsavenew', $group['allowsave'], 'radio');
		showsetting('usergroups_edit_post_allowsavereply', 'allowsavereplynew', $group['allowsavereply'], 'radio');
		showsetting('usergroups_edit_post_allowsavenum', 'allowsavenumnew', $group['allowsavenum'], 'text');
		showsetting('usergroups_edit_post_allowsetpublishdate', 'allowsetpublishdatenew', $group['allowsetpublishdate'], 'radio');
		showsetting('usergroups_edit_post_allowcommentcollection', 'allowcommentcollectionnew', $group['allowcommentcollection'], 'radio');
		showsetting('usergroups_edit_post_allowimgcontent', 'allowimgcontentnew', $group['allowimgcontent'], 'radio');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		$group['maxattachsize'] = intval($group['maxattachsize'] / 1024);
		$group['maxsizeperday'] = intval($group['maxsizeperday'] / 1024);
		$group['maximagesize'] = intval($group['maximagesize'] / 1024);
		$group['maxspacesize'] = intval($group['maxspacesize'] / 1024);

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_attach":"action=usergroups&operation=edit&anchor=attach"}*/
		showtagheader('div', 'attach', $anchor == 'attach');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_attach');
		showsetting('usergroups_edit_attach_get', 'allowgetattachnew', $group['allowgetattach'], 'radio');
		showsetting('usergroups_edit_attach_getimage', 'allowgetimagenew', $group['allowgetimage'], 'radio');
		showsetting('usergroups_edit_attach_post', 'allowpostattachnew', $group['allowpostattach'], 'radio');
		showsetting('usergroups_edit_attach_set_perm', 'allowsetattachpermnew', $group['allowsetattachperm'], 'radio');
		showsetting('usergroups_edit_image_post', 'allowpostimagenew', $group['allowpostimage'], 'radio');
		showcomponent('usergroups_edit_attach_max_size', 'maxattachsizenew', $group['maxattachsize'], 'component_size');
		showcomponent('usergroups_edit_attach_max_size_per_day', 'maxsizeperdaynew', $group['maxsizeperday'], 'component_size');
		showsetting('usergroups_edit_attach_max_number_per_day', 'maxattachnumnew', $group['maxattachnum'], 'text');
		showsetting('usergroups_edit_attach_ext', 'attachextensionsnew', $group['attachextensions'], 'text');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_magic":"action=usergroups&operation=edit&anchor=magic"}*/
		showtagheader('div', 'magic', $anchor == 'magic');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_magic');
		showsetting('usergroups_edit_magic_permission', ['allowmagicsnew', [
			[0, $lang['usergroups_edit_magic_unallowed']],
			[1, $lang['usergroups_edit_magic_allow']],
			[2, $lang['usergroups_edit_magic_allow_and_pass']]
		]], $group['allowmagics'], 'mradio');
		showsetting('usergroups_edit_magic_discount', 'magicsdiscountnew', $group['magicsdiscount'], 'text');
		showsetting('usergroups_edit_magic_max', 'maxmagicsweightnew', $group['maxmagicsweight'], 'text');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_invite":"action=usergroups&operation=edit&anchor=invite"}*/
		showtagheader('div', 'invite', $anchor == 'invite');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_invite');
		showsetting('usergroups_edit_invite_permission', 'allowinvitenew', $group['allowinvite'], 'radio');
		showsetting('usergroups_edit_invite_send_permission', 'allowmailinvitenew', $group['allowmailinvite'], 'radio');
		showsetting('usergroups_edit_invite_price', 'invitepricenew', $group['inviteprice'], 'text', norelatedlink: true);
		showsetting('usergroups_edit_invite_buynum', 'maxinvitenumnew', $group['maxinvitenum'], 'text', norelatedlink: true);
		showsetting('usergroups_edit_invite_maxinviteday', 'maxinvitedaynew', $group['maxinviteday'], 'text', norelatedlink: true);
		showtablefooter();
		showtagfooter('div');
		/*search*/

		$raterangearray = [];
		foreach(explode("\n", $group['raterange']) as $range) {
			$range = explode("\t", $range);
			$raterangearray[$range[0]] = ['isself' => $range[1], 'min' => $range[2], 'max' => $range[3], 'mrpd' => $range[4]];
		}

		if($multiset) {
			showtagheader('div', 'credit', $anchor == 'credit');
			showtableheader('', 'nobottom');
			showtitle('usergroups_edit_credit');
			showsetting('usergroups_edit_credit_exempt_sendpm', 'exemptnew[0]', $group['exempt'][0], 'radio');
			showsetting('usergroups_edit_credit_exempt_search', 'exemptnew[1]', $group['exempt'][1], 'radio');
			$exempttype = $group['radminid'] ? ($group['radminid'] == 3 ? 1 : 2) : 3;
			showsetting(($group['radminid'] ? $lang['usergroups_edit_credit_exempt_outperm'] : '').$lang['usergroups_edit_credit_exempt_getattch'], 'exemptnew[2]', $group['exempt'][2], 'radio', $exempttype == 2 ? 'readonly' : 0, '', '', '', 'm_getattch');
			showsetting($lang['usergroups_edit_credit_exempt_inperm'].$lang['usergroups_edit_credit_exempt_getattch'], 'exemptnew[5]', $group['exempt'][5], 'radio', $exempttype == 1 ? 0 : 'readonly');
			showsetting(($group['radminid'] ? $lang['usergroups_edit_credit_exempt_outperm'] : '').$lang['usergroups_edit_credit_exempt_attachpay'], 'exemptnew[3]', $group['exempt'][3], 'radio', $exempttype == 2 ? 'readonly' : 0, '', '', '', 'm_attachpay');
			showsetting($lang['usergroups_edit_credit_exempt_inperm'].$lang['usergroups_edit_credit_exempt_attachpay'], 'exemptnew[6]', $group['exempt'][6], 'radio', $exempttype == 1 ? 0 : 'readonly');
			showsetting(($group['radminid'] ? $lang['usergroups_edit_credit_exempt_outperm'] : '').$lang['usergroups_edit_credit_exempt_threadpay'], 'exemptnew[4]', $group['exempt'][4], 'radio', $exempttype == 2 ? 'readonly' : 0, '', '', '', 'm_threadpay');
			showsetting($lang['usergroups_edit_credit_exempt_inperm'].$lang['usergroups_edit_credit_exempt_threadpay'], 'exemptnew[7]', $group['exempt'][7], 'radio', $exempttype == 1 ? 0 : 'readonly');

			showtitle('usergroups_edit_credit_allowrate', '', 0);
			for($i = 1; $i <= 8; $i++) {
				if(isset($_G['setting']['extcredits'][$i])) {
					showsetting($_G['setting']['extcredits'][$i]['title'], 'raterangenew['.$i.'][allowrate]', $raterangearray[$i], 'radio');
					showsetting($_G['setting']['extcredits'][$i]['title'].' '.$lang['usergroups_edit_credit_rate_isself'], 'raterangenew['.$i.'][isself]', $raterangearray[$i]['isself'], 'radio');
					showsetting($_G['setting']['extcredits'][$i]['title'].' '.$lang['usergroups_edit_credit_rate_min'], 'raterangenew['.$i.'][min]', $raterangearray[$i]['min'], 'text');
					showsetting($_G['setting']['extcredits'][$i]['title'].' '.$lang['usergroups_edit_credit_rate_max'], 'raterangenew['.$i.'][max]', $raterangearray[$i]['max'], 'text');
					showsetting($_G['setting']['extcredits'][$i]['title'].' '.$lang['usergroups_edit_credit_rate_mrpd'], 'raterangenew['.$i.'][mrpd]', $raterangearray[$i]['mrpd'], 'text');
				}
			}
			showtablefooter();
			showtagfooter('div');
		} else {
			/*search={"nav_usergroups":"action=usergroups","usergroups_edit_credit":"action=usergroups&operation=edit&anchor=credit"}*/
			showtagheader('div', 'credit', $anchor == 'credit');
			showtableheader('', 'nobottom');
			showtitle('usergroups_edit_credit');
			showsetting('usergroups_edit_credit_exempt_sendpm', 'exemptnew[0]', $group['exempt'][0], 'radio');
			showsetting('usergroups_edit_credit_exempt_search', 'exemptnew[1]', $group['exempt'][1], 'radio');
			if($group['radminid']) {
				if($group['radminid'] == 3) {
					showsetting($lang['usergroups_edit_credit_exempt_outperm'].$lang['usergroups_edit_credit_exempt_getattch'], 'exemptnew[2]', $group['exempt'][2], 'radio');
					showsetting($lang['usergroups_edit_credit_exempt_inperm'].$lang['usergroups_edit_credit_exempt_getattch'], 'exemptnew[5]', $group['exempt'][5], 'radio');
					showsetting($lang['usergroups_edit_credit_exempt_outperm'].$lang['usergroups_edit_credit_exempt_attachpay'], 'exemptnew[3]', $group['exempt'][3], 'radio');
					showsetting($lang['usergroups_edit_credit_exempt_inperm'].$lang['usergroups_edit_credit_exempt_attachpay'], 'exemptnew[6]', $group['exempt'][6], 'radio');
					showsetting($lang['usergroups_edit_credit_exempt_outperm'].$lang['usergroups_edit_credit_exempt_threadpay'], 'exemptnew[4]', $group['exempt'][4], 'radio');
					showsetting($lang['usergroups_edit_credit_exempt_inperm'].$lang['usergroups_edit_credit_exempt_threadpay'], 'exemptnew[7]', $group['exempt'][7], 'radio');
				} else {
					echo '<input name="exemptnew[2]" type="hidden" value="1" /><input name="exemptnew[3]" type="hidden" value="1" /><input name="exemptnew[4]" type="hidden" value="1" />'.
						'<input name="exemptnew[5]" type="hidden" value="1" /><input name="exemptnew[6]" type="hidden" value="1" /><input name="exemptnew[7]" type="hidden" value="1" />';
				}
			} else {
				showsetting('usergroups_edit_credit_exempt_getattch', 'exemptnew[2]', $group['exempt'][2], 'radio');
				showsetting('usergroups_edit_credit_exempt_attachpay', 'exemptnew[3]', $group['exempt'][3], 'radio');
				showsetting('usergroups_edit_credit_exempt_threadpay', 'exemptnew[4]', $group['exempt'][4], 'radio');
			}

			echo '<tr><td colspan="2">'.$lang['usergroups_edit_credit_exempt_comment'].'</td></tr>';
			echo '<tr><td colspan="2" class="td27"><a href="'.ADMINSCRIPT.'?action=credits&operation=list&anchor=policytable&groupid='.$group['groupid'].'" target="_blank">'.$lang['usergroups_edit_credit_group_policy'].'</a></td></tr>';

			showtablefooter();
			showtableheader('usergroups_edit_credit_allowrate', 'nobottom');

			$titlecolumn[0] = $lang['name'];
			for($i = 1; $i <= 8; $i++) {
				if(isset($_G['setting']['extcredits'][$i])) {
					$titlecolumn[$i] = $_G['setting']['extcredits'][$i]['title'];
				}
			}
			showsubtitle($titlecolumn);
			$leftcolumn = ['enable', 'usergroups_edit_credit_rate_isself', 'usergroups_edit_credit_rate_min', 'usergroups_edit_credit_rate_max', 'usergroups_edit_credit_rate_mrpd'];
			foreach($leftcolumn as $value) {
				echo '<tr><td>'.$lang[$value].'</td>';
				foreach($titlecolumn as $subkey => $subvalue) {
					if(!$subkey) continue;
					if($value == 'enable') {
						echo '<td><input type="checkbox" class="checkbox" name="raterangenew['.$subkey.'][allowrate]" value="1" '.(empty($raterangearray[$subkey]) ? '' : 'checked').'></td>';
					} elseif($value == 'usergroups_edit_credit_rate_isself') {
						echo '<td><input type="checkbox" class="checkbox" name="raterangenew['.$subkey.'][isself]" value="1" '.(empty($raterangearray[$subkey]['isself']) ? '' : 'checked').'></td>';
					} elseif($value == 'usergroups_edit_credit_rate_min') {
						echo '<td class="td28"><input type="text" class="txt" name="raterangenew['.$subkey.'][min]" size="3" value="'.$raterangearray[$subkey]['min'].'"></td>';
					} elseif($value == 'usergroups_edit_credit_rate_max') {
						echo '<td class="td28"><input type="text" class="txt" name="raterangenew['.$subkey.'][max]" size="3" value="'.$raterangearray[$subkey]['max'].'"></td>';
					} elseif($value == 'usergroups_edit_credit_rate_mrpd') {
						echo '<td class="td28"><input type="text" class="txt" name="raterangenew['.$subkey.'][mrpd]" size="3" value="'.$raterangearray[$subkey]['mrpd'].'"></td>';
					}
				}
				echo '</tr>';
			}
			echo '<tr><td class="lineheight" colspan="9">'.$lang['usergroups_edit_credit_rate_tips'].'</td></tr>';
			showtablefooter();
			showtagfooter('div');
			/*search*/
		}

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_home":"action=usergroups&operation=edit&anchor=home"}*/
		showtagheader('div', 'home', $anchor == 'home');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_home');
		showcomponent('usergroups_edit_attach_max_space_size', 'maxspacesizenew', $group['maxspacesize'], 'component_size', '', 'MB,GB');
		showsetting('usergroups_edit_home_allow_blog', 'allowblognew', $group['allowblog'], 'radio', '', 1);
		showsetting('usergroups_edit_home_allow_blog_mod', 'allowblogmodnew', $group['allowblogmod'], 'radio');
		showtagfooter('tbody');
		showsetting('usergroups_edit_home_allow_doing', 'allowdoingnew', $group['allowdoing'], 'radio', '', 1);
		showsetting('usergroups_edit_home_allow_doing_mod', 'allowdoingmodnew', $group['allowdoingmod'], 'radio');
		showtagfooter('tbody');
		showsetting('usergroups_edit_home_allow_upload', 'allowuploadnew', $group['allowupload'], 'radio', '', 1);
		showsetting('usergroups_edit_home_allow_upload_mod', 'allowuploadmodnew', $group['allowuploadmod'], 'radio');
		showcomponent('usergroups_edit_home_image_max_size', 'maximagesizenew', $group['maximagesize'], 'component_size');
		showtagfooter('tbody');
		showsetting('usergroups_edit_home_allow_share', 'allowsharenew', $group['allowshare'], 'radio', '', 1);
		showsetting('usergroups_edit_home_allow_share_mod', 'allowsharemodnew', $group['allowsharemod'], 'radio');
		showtagfooter('tbody');
		showsetting('usergroups_edit_home_allow_comment', 'allowcommentnew', $group['allowcomment'], 'radio', '', 1);
		showsetting('usergroups_edit_home_allow_comment_mod', 'allowcommentmodnew', $group['allowcommentmod'], 'radio');
		showtagfooter('tbody');
		showsetting('usergroups_edit_home_allow_poke', 'allowpokenew', $group['allowpoke'], 'radio');
		showsetting('usergroups_edit_home_allow_friend', 'allowfriendnew', $group['allowfriend'], 'radio');
		showsetting('usergroups_edit_home_allow_click', 'allowclicknew', $group['allowclick'], 'radio');
		showsetting('usergroups_edit_home_allow_space_diy_html', 'allowspacediyhtmlnew', $group['allowspacediyhtml'], 'radio');
		showsetting('usergroups_edit_home_allow_space_diy_bbcode', 'allowspacediybbcodenew', $group['allowspacediybbcode'], 'radio');
		showsetting('usergroups_edit_home_allow_space_diy_imgcode', 'allowspacediyimgcodenew', $group['allowspacediyimgcode'], 'radio');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_group":"action=usergroups&operation=edit&anchor=group"}*/
		showtagheader('div', 'group', $anchor == 'group');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_group');
		showsetting('usergroups_edit_group_build', 'allowbuildgroupnew', $group['allowbuildgroup'], 'text');
		showsetting('usergroups_edit_group_buildcredits', 'buildgroupcreditsnew', $group['buildgroupcredits'], 'text');
		showsetting('usergroups_edit_post_direct_group', ['allowgroupdirectpostnew', [
			[0, $lang['usergroups_edit_post_direct_none']],
			[1, $lang['usergroups_edit_post_direct_reply']],
			[2, $lang['usergroups_edit_post_direct_thread']],
			[3, $lang['usergroups_edit_post_direct_all']]
		]], $group['allowgroupdirectpost'], 'mradio');
		showsetting('usergroups_edit_post_url_group', ['allowgroupposturlnew', [
			[0, $lang['usergroups_edit_post_url_banned']],
			[1, $lang['usergroups_edit_post_url_mod']],
			[2, $lang['usergroups_edit_post_url_unhandle']],
			[3, $lang['usergroups_edit_post_url_enable']]
		]], $group['allowgroupposturl'], 'mradio');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"nav_usergroups":"action=usergroups","usergroups_edit_portal":"action=usergroups&operation=edit&anchor=portal"}*/
		showtagheader('div', 'portal', $anchor == 'portal');
		showtableheader('', 'nobottom');
		showtitle('usergroups_edit_portal');
		showsetting('usergroups_edit_portal_allow_comment_article', 'allowcommentarticlenew', $group['allowcommentarticle'], 'text');
		showsetting('usergroups_edit_portal_allow_comment_article_mod', 'allowcommentarticlemodnew', $group['allowcommentarticlemod'], 'radio');
		showtagfooter('tbody');
		showsetting('usergroups_edit_portal_allow_post_article', 'allowpostarticlenew', $group['allowpostarticle'], 'radio', '', 1);
		showsetting('usergroups_edit_portal_allow_down_local_img', 'allowdownlocalimgnew', $group['allowdownlocalimg'], 'radio');
		showsetting('usergroups_edit_portal_allow_post_article_moderate', 'allowpostarticlemodnew', $group['allowpostarticlemod'], 'radio');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		if($pluginsetting) {
			showtagheader('div', 'plugin', $anchor == 'plugin');
			showtableheader('', 'nobottom');
			foreach($pluginsetting as $plugind => $setting) {
				showtitle($setting['name']);
				foreach($setting['setting'] as $varid => $var) {
					if(!empty($var['variable']) && str_starts_with($var['variable'], 'fields_')) {
						$variable = str_replace('fields_', '', $var['variable']);
						$varname = 'fieldsnew[plugin]['.$plugind.']['.$variable.']';
						$value = $group['fields']['plugin'][$plugind][$variable] ?? '';
					} else {
						$varname = 'pluginnew['.$varid.']';
						$value = $group['plugin'][$varid];
					}
					if($var['type'] != 'select') {
						showsetting($var['title'], $varname, $value, $var['type'], '', 0, $var['description']);
					} else {
						showsetting($var['title'], [$varname, $var['select']], $value, $var['type'], '', 0, $var['description']);
					}
				}
			}
			showtablefooter();
			showtagfooter('div');
		}

		if($stylesetting) {
			showtagheader('div', 'style', $anchor == 'style');
			showtableheader('', 'nobottom');
			foreach($stylesetting as $setting) {
				showtitle($setting['name']);
				foreach($setting['setting'] as $varid => $var) {
					if($var['type'] != 'select') {
						showsetting($var['title'], 'stylenew['.$varid.']', $group['style'][$varid], $var['type'], '', 0, $var['description']);
					} else {
						showsetting($var['title'], ['stylenew['.$varid.']', $var['select']], $group['style'][$varid], $var['type'], '', 0, $var['description']);
					}
				}
			}
			showtablefooter();
			showtagfooter('div');
		}

		showtableheader();
		showsubmit('detailsubmit', 'submit');
		showtablefooter();
		$_G['showsetting_multi']++;
	}

	if($_G['showsetting_multicount'] > 1) {
		showhiddenfields(['multi' => implode(',', $mgids)]);
		showmulti();
	}
	showformfooter();
} else {

	if(!$multiset) {
		$_GET['multinew'] = [0 => ['single' => 1]];
	}
	$pluginvars = $stylevars = [];
	foreach($_GET['multinew'] as $k => $row) {
		if(empty($row['single'])) {
			foreach($row as $key => $value) {
				$_GET[''.$key] = $value;
			}
			$_GET['id'] = $_GET['multi'][$k];
		}
		$group = $mgroup[$k];

		$systemnew = 'private';
		$upgroupidnew = 0;
		$setcredits = false;
		$creditsformulanew = '';

		if($group['type'] == 'special' && $group['radminid'] > 0) {

			$radminidnew = $group['radminid'];

		} elseif($group['type'] == 'special') {

			if(!checkformulacredits($_GET['creditsformulanew'])) {
				cpmsg('setting_creditsformula_invalid', '', 'error');
			}
			$creditsformulanew = $_GET['creditsformulanew'];

			$radminidnew = '0';
			if(!$multiset && $_GET['system_publicnew']) {
				if($_GET['system_dailypricenew'] > 0) {
					if(!$_G['setting']['creditstrans']) {
						cpmsg('usergroups_edit_creditstrans_disabled', '', 'error', ['frame' => $multiset]);
					} else {
						$system_minspannew = $_GET['system_minspannew'] <= 0 ? 1 : $_GET['system_minspannew'];
						$systemnew = intval($_GET['system_dailypricenew'])."\t".intval($system_minspannew);
					}
				} else {
					$systemnew = "0\t0";
				}
			}
			if(!$multiset && !empty($_GET['upgroupid'])) {
				$upgroup_name = !empty($_G['setting']['upgroup_name']) ? $_G['setting']['upgroup_name'] : [];

				if(!empty($_GET['upgroupid']['main'])) {
					$upgroupidnew = $group['groupid'];
					$setcredits = true;
					$creditshighernew = 0;
					$creditslowernew = $group['creditslower'];
					if(isset($_GET['upgroup_name'])) {
						$upgroup_name[$group['groupid']] = $_GET['upgroup_name'];
					}
				} elseif(!empty($_GET['upgroupid']['sub'])) {
					$upgroupidnew = $_GET['upgroupid']['sub'];
				} else {
					$upgroupidnew = 0;
					$setcredits = true;
					$creditshighernew = 0;
					$creditslowernew = 0;
					unset($upgroup_name[$group['groupid']]);
				}

				$settings = [
					'upgroup_name' => $upgroup_name,
				];
				table_common_setting::t()->update_batch($settings);
				updatecache('setting');
			}
		} else {
			$radminidnew = in_array($group['groupid'], [1, 2, 3]) ? $group['groupid'] : 0;
		}

		if(is_array($_GET['raterangenew'])) {
			foreach($_GET['raterangenew'] as $key => $rate) {
				if($key >= 1 && $key <= 8 && $rate['allowrate']) {
					if(!$rate['mrpd'] || $rate['max'] <= $rate['min'] || $rate['mrpd'] < max(abs($rate['min']), abs($rate['max']))) {
						cpmsg('usergroups_edit_rate_invalid', '', 'error', ['frame' => $multiset]);
					} else {
						$_GET['raterangenew'][$key] = implode("\t", [$key, ($rate['isself'] ? $rate['isself'] : 0), $rate['min'], $rate['max'], $rate['mrpd']]);
					}
				} else {
					unset($_GET['raterangenew'][$key]);
				}
			}
		}

		if($group['groupid'] == 1) {
			$_GET['allowvisitnew'] = 2;
		}

		$raterangenew = $_GET['raterangenew'] ? implode("\n", $_GET['raterangenew']) : '';
		$maxpricenew = $_GET['maxpricenew'] < 0 ? 0 : intval($_GET['maxpricenew']);
		$maxpostsperhournew = $_GET['maxpostsperhournew'] > 255 ? 255 : intval($_GET['maxpostsperhournew']);
		$maxthreadsperhournew = $_GET['maxthreadsperhournew'] > 255 ? 255 : intval($_GET['maxthreadsperhournew']);

		$extensionarray = [];
		foreach(explode(',', $_GET['attachextensionsnew']) as $extension) {
			if($extension = trim($extension)) {
				$extensionarray[] = $extension;
			}
		}
		$attachextensionsnew = implode(', ', $extensionarray);

		if($_GET['maxtradepricenew'] == $_GET['mintradepricenew'] || $_GET['maxtradepricenew'] < 0 || $_GET['mintradepricenew'] <= 0 || ($_GET['maxtradepricenew'] && $_GET['maxtradepricenew'] < $_GET['mintradepricenew'])) {
			cpmsg('trade_fee_error', '', 'error', ['frame' => $multiset]);
		} elseif(($_GET['maxrewardpricenew'] != 0 && $_GET['minrewardpricenew'] >= $_GET['maxrewardpricenew']) || $_GET['minrewardpricenew'] < 1 || $_GET['minrewardpricenew'] < 0 || $_GET['maxrewardpricenew'] < 0) {
			cpmsg('reward_credits_error', '', 'error', ['frame' => $multiset]);
		}

		$exemptnewbin = '';
		for($i = 0; $i < 8; $i++) {
			$exemptnewbin = intval($_GET['exemptnew'][$i]).$exemptnewbin;
		}
		$exemptnew = bindec($exemptnewbin);

		$tradesticknew = $_GET['tradesticknew'] > 0 ? intval($_GET['tradesticknew']) : 0;
		$maxinvitedaynew = $_GET['maxinvitedaynew'] > 0 ? intval($_GET['maxinvitedaynew']) : 10;
		$maxattachsizenew = $_GET['maxattachsizenew'] > 0 ? intval($_GET['maxattachsizenew'] * 1024) : 0;
		$maximagesizenew = $_GET['maximagesizenew'] > 0 ? intval($_GET['maximagesizenew'] * 1024) : 0;
		$maxspacesizenew = $_GET['maxspacesizenew'] > 0 ? intval($_GET['maxspacesizenew'] * 1024) : 0;
		$maxsizeperdaynew = $_GET['maxsizeperdaynew'] > 0 ? intval($_GET['maxsizeperdaynew'] * 1024) : 0;
		$maxattachnumnew = $_GET['maxattachnumnew'] > 0 ? intval($_GET['maxattachnumnew']) : 0;
		$allowrecommendnew = $_GET['allowrecommendnew'] > 0 ? intval($_GET['allowrecommendnew']) : 0;
		$dataarr = [
			'grouptitle' => $_GET['grouptitlenew'],
			'radminid' => $radminidnew,
			'allowvisit' => $_GET['allowvisitnew'],
			'allowfollow' => $_GET['allowfollownew'],
			'allowsendpm' => $_GET['allowsendpmnew'],
			'maxinvitenum' => $_GET['maxinvitenumnew'],
			'maxinviteday' => $maxinvitedaynew,
			'allowinvite' => $_GET['allowinvitenew'],
			'allowmailinvite' => $_GET['allowmailinvitenew'],
			'inviteprice' => $_GET['invitepricenew'],
		];
		if(!empty($setcredits)) {
			$dataarr['creditshigher'] = $creditshighernew;
			$dataarr['creditslower'] = $creditslowernew;
		}
		if(!$multiset) {
			$dataarr['upgroupid'] = $upgroupidnew;
			$dataarr['creditsformula'] = $creditsformulanew;
			$dataarr['system'] = $systemnew;
			if($_FILES['iconnew']) {
				$data = ['extid' => "{$_GET['id']}"];
				$iconnew = upload_icon_banner($data, $_FILES['iconnew'], 'usergroup_icon');
			} else {
				$iconnew = $_GET['iconnew'];
			}
			if($iconnew) {
				$dataarr['icon'] = $iconnew;
			}
			if($_GET['deleteicon']) {
				$valueparse = parse_url($group['icon']);
				if(!isset($valueparse['host'])) {
					$group['icon'] = str_replace(['..', '//'], ['', '/'], $group['icon']);
					@unlink($_G['setting']['attachurl'].'common/'.$group['icon']);
					ftpcmd('delete', 'common/'.$group['icon']);
				}
				$dataarr['icon'] = '';
			}
		}
		table_common_usergroup::t()->update_usergroup($_GET['id'], $dataarr);

		if($pluginsetting) {
			foreach($_GET['pluginnew'] as $pluginvarid => $value) {
				$pluginvars[$pluginvarid][$_GET['id']] = $value;
			}
		}

		if($stylesetting) {
			foreach($_GET['stylenew'] as $stylevarid => $value) {
				$stylevars[$stylevarid][$_GET['id']] = $value;
			}
		}

		table_forum_onlinelist::t()->update_by_groupid($_GET['id'], ['title' => $_GET['grouptitlenew']]);

		$dataarr = [
			'readaccess' => $_GET['readaccessnew'],
			'allowpost' => $_GET['allowpostnew'],
			'allowreply' => $_GET['allowreplynew'],
			'allowpostpoll' => $_GET['allowpostpollnew'],
			'allowpostreward' => $_GET['allowpostrewardnew'],
			'allowposttrade' => $_GET['allowposttradenew'],
			'allowpostactivity' => $_GET['allowpostactivitynew'],
			'allowdirectpost' => $_GET['allowdirectpostnew'],
			'allowgetattach' => $_GET['allowgetattachnew'],
			'allowgetimage' => $_GET['allowgetimagenew'],
			'allowpostattach' => $_GET['allowpostattachnew'],
			'allowvote' => $_GET['allowvotenew'],
			'allowsearch' => bindec(intval($_GET['allowsearchnew'][7]).intval($_GET['allowfulltextnew']).intval($_GET['allowsearchnew'][5]).intval($_GET['allowsearchnew'][4]).intval($_GET['allowsearchnew'][3]).intval($_GET['allowsearchnew'][2]).intval($_GET['allowsearchnew'][1])),
			'allowcstatus' => $_GET['allowcstatusnew'],
			'allowinvisible' => $_GET['allowinvisiblenew'],
			'allowtransfer' => $_GET['allowtransfernew'],
			'allowsetreadperm' => $_GET['allowsetreadpermnew'],
			'allowsetattachperm' => $_GET['allowsetattachpermnew'],
			'allowpostimage' => $_GET['allowpostimagenew'],
			'allowposttag' => $_GET['allowposttagnew'],
			'allowhidecode' => $_GET['allowhidecodenew'],
			'allowmediacode' => $_GET['allowmediacodenew'],
			'allowbegincode' => $_GET['allowbegincodenew'],
			'allowhtml' => $_GET['allowhtmlnew'],
			'allowanonymous' => $_GET['allowanonymousnew'],
			'allowsigbbcode' => $_GET['allowsigbbcodenew'],
			'allowsigimgcode' => $_GET['allowsigimgcodenew'],
			'allowmagics' => $_GET['allowmagicsnew'],
			'disableperiodctrl' => $_GET['disableperiodctrlnew'],
			'reasonpm' => $_GET['reasonpmnew'],
			'maxprice' => $maxpricenew,
			'maxsigsize' => $_GET['maxsigsizenew'],
			'maxspacesize' => $maxspacesizenew,
			'maxattachsize' => $maxattachsizenew,
			'maximagesize' => $maximagesizenew,
			'maxsizeperday' => $maxsizeperdaynew,
			'maxpostsperhour' => $maxpostsperhournew,
			'maxthreadsperhour' => $maxthreadsperhournew,
			'attachextensions' => $attachextensionsnew,
			'mintradeprice' => $_GET['mintradepricenew'],
			'maxtradeprice' => $_GET['maxtradepricenew'],
			'minrewardprice' => $_GET['minrewardpricenew'],
			'maxrewardprice' => $_GET['maxrewardpricenew'],
			'magicsdiscount' => $_GET['magicsdiscountnew'] >= 0 && $_GET['magicsdiscountnew'] < 10 ? $_GET['magicsdiscountnew'] : 0,
			'maxmagicsweight' => $_GET['maxmagicsweightnew'] >= 0 && $_GET['maxmagicsweightnew'] <= 60000 ? $_GET['maxmagicsweightnew'] : 1,
			'allowpostdebate' => $_GET['allowpostdebatenew'],
			'tradestick' => $tradesticknew,
			'maxattachnum' => $maxattachnumnew,
			'allowposturl' => $_GET['allowposturlnew'],
			'allowrecommend' => $allowrecommendnew,
			'allowpostrushreply' => $_GET['allowpostrushreplynew'],
			'maxfriendnum' => $_GET['maxfriendnumnew'],
			'seccode' => $_GET['seccodenew'],
			'forcesecques' => $_GET['forcesecquesnew'],
			'forcelogin' => $_GET['forceloginnew'],
			'domainlength' => $_GET['domainlengthnew'],
			'disablepostctrl' => $_GET['disablepostctrlnew'],
			'allowblog' => $_GET['allowblognew'],
			'allowdoing' => $_GET['allowdoingnew'],
			'allowupload' => $_GET['allowuploadnew'],
			'allowshare' => $_GET['allowsharenew'],
			'allowblogmod' => $_GET['allowblogmodnew'],
			'allowdoingmod' => $_GET['allowdoingmodnew'],
			'allowuploadmod' => $_GET['allowuploadmodnew'],
			'allowsharemod' => $_GET['allowsharemodnew'],
			'allowpoke' => $_GET['allowpokenew'],
			'allowfriend' => $_GET['allowfriendnew'],
			'allowclick' => $_GET['allowclicknew'],
			'allowcomment' => $_GET['allowcommentnew'],
			'allowcommentmod' => $_GET['allowcommentmodnew'],
			'allowcommentarticle' => intval($_GET['allowcommentarticlenew']),
			'allowcommentarticlemod' => $_GET['allowcommentarticlemodnew'],
			'allowcommentpost' => bindec(intval($_GET['allowcommentpostnew'][2]).intval($_GET['allowcommentpostnew'][1])),
			'allowspacediyhtml' => $_GET['allowspacediyhtmlnew'],
			'allowspacediybbcode' => $_GET['allowspacediybbcodenew'],
			'allowspacediyimgcode' => $_GET['allowspacediyimgcodenew'],
			'allowstatdata' => $_GET['allowstatdatanew'],
			'allowavatarupload' => $_GET['allowavataruploadnew'],
			'allowviewprofile' => $_GET['allowviewprofilenew'],
			'allowpostarticle' => $_GET['allowpostarticlenew'],
			'allowpostarticlemod' => $_GET['allowpostarticlemodnew'],
			'allowbuildgroup' => $_GET['allowbuildgroupnew'],
			'buildgroupcredits' => $_GET['buildgroupcreditsnew'],
			'allowgroupdirectpost' => intval($_GET['allowgroupdirectpostnew']),
			'allowgroupposturl' => intval($_GET['allowgroupposturlnew']),
			'edittimelimit' => intval($_GET['edittimelimitnew']),
			'allowcommentreply' => intval($_GET['allowcommentreplynew']),
			'allowdownlocalimg' => intval($_GET['allowdownlocalimgnew']),
			'allowdownremoteimg' => intval($_GET['allowdownremoteimgnew']),
			'allowcommentitem' => intval($_GET['allowcommentitemnew']),
			'allowat' => intval($_GET['allowatnew']),
			'allowsave' => intval($_GET['allowsavenew']),
			'allowsavereply' => intval($_GET['allowsavereplynew']),
			'allowsavenum' => intval($_GET['allowsavenumnew']),
			'allowreplycredit' => intval($_GET['allowreplycreditnew']),
			'allowsetpublishdate' => intval($_GET['allowsetpublishdatenew']),
			'allowcommentcollection' => intval($_GET['allowcommentcollectionnew']),
			'allowimgcontent' => intval($_GET['allowimgcontentnew']),
			'allowcreatecollection' => intval($_GET['allowcreatecollectionnew']),
			'allowfollowcollection' => intval($_GET['allowfollowcollectionnew']),
			'exempt' => $exemptnew,
			'raterange' => $raterangenew,
			'ignorecensor' => intval($_GET['ignorecensornew']),
			'allowsendallpm' => intval($_GET['allowsendallpmnew']),
			'allowsendpmmaxnum' => intval($_GET['allowsendpmmaxnumnew']),
			'closead' => intval($_GET['closeadnew']),
			'fields' => !empty($_GET['fieldsnew']) ? json_encode($_GET['fieldsnew']) : '{}',
		];
		table_common_usergroup_field::t()->update($_GET['id'], $dataarr);

		if($_G['setting']['threadplugins']) {
			$allowthreadplugin = table_common_setting::t()->fetch_setting('allowthreadplugin', true);
			$allowthreadplugin[$_GET['id']] = $_GET['allowthreadpluginnew'];
			table_common_setting::t()->update_setting('allowthreadplugin', $allowthreadplugin);
		}
		if(empty($row['single'])) {
			foreach($row as $key => $value) {
				unset($_GET[''.$key]);
			}
		}
	}

	if($pluginvars) {
		set_pluginsetting($pluginvars);
	}

	if($stylevars) {
		set_stylesetting($stylevars);
	}

	updatecache(['setting', 'usergroups', 'onlinelist', 'groupreadaccess']);

	cpmsg('usergroups_edit_succeed',
		'action=usergroups&operation=edit&'.($multiset ? 'multi='.implode(',', $_GET['multi']) : 'id='.$_GET['id']).'&anchor='.$_GET['anchor'],
		'succeed', ['frame' => $multiset]);
}
	