<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$do = !in_array(getgpc('do'), ['group', 'member', 'gperm', 'notifyusers']) ? 'member' : getgpc('do');
shownav('founder', 'menu_founder_perm');

if($do == 'group') {
	$id = intval(getgpc('id'));

	if(!$id) {
		foreach(table_common_admincp_group::t()->range() as $group) {
			$groups[$group['cpgroupid']] = $group['cpgroupname'];
		}
		if(!submitcheck('submit')) {
			showsubmenu('menu_founder_perm', [
				['nav_founder_perm_member', 'founder&operation=perm&do=member', 0],
				['nav_founder_perm_group', 'founder&operation=perm&do=group', 1],
				['nav_founder_perm_notifyusers', 'founder&operation=perm&do=notifyusers', 0],
			]);
			showformheader('founder&operation=perm&do=group');
			showtableheader();
			showsubtitle(['', 'founder_cpgroupname', '']);
			foreach($groups as $id => $group) {
				showtablerow('style="height:20px"', ['class="td25"', 'class="td24"'], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$id\">",
					"<input type=\"text\" class=\"txtnobd\" onblur=\"this.className='txtnobd'\" onfocus=\"this.className='txt'\" size=\"15\" name=\"name[$id]\" value=\"$group\">",
					'<a href="'.ADMINSCRIPT.'?action=founder&operation=perm&do=group&id='.$id.'">'.cplang('edit').'</a>'
				]);
			}
			showtablerow('style="height:20px"', [], [cplang('add_new'), '<input class="txt" type="text" name="newcpgroupname" value="" />', '']);
			showsubmit('submit', 'submit', 'del');
			showtablefooter();
			showformfooter();
		} else {
			if(!empty($_GET['newcpgroupname'])) {
				if(table_common_admincp_group::t()->fetch_by_cpgroupname($_GET['newcpgroupname'])) {
					cpmsg('founder_perm_group_name_duplicate', '', 'error', ['name' => $_GET['newcpgroupname']]);
				}
				table_common_admincp_group::t()->insert(['cpgroupname' => strip_tags($_GET['newcpgroupname'])]);
			}
			if(!empty($_GET['delete'])) {
				table_common_admincp_perm::t()->delete_by_cpgroupid_perm($_GET['delete']);
				table_common_admincp_member::t()->update_cpgroupid_by_cpgroupid($_GET['delete'], ['cpgroupid' => 0]);
				table_common_admincp_group::t()->delete($_GET['delete']);
			}
			if(!empty($_GET['name'])) {
				foreach($_GET['name'] as $id => $name) {
					if($groups[$id] != $name) {
						$cpgroupid = ($cpgroup = table_common_admincp_group::t()->fetch_by_cpgroupname($name)) ? $cpgroup['cpgroupid'] : 0;
						if($cpgroupid && $_GET['name'][$cpgroupid] == $groups[$cpgroupid]) {
							cpmsg('founder_perm_group_name_duplicate', '', 'error', ['name' => $name]);
						}
						table_common_admincp_group::t()->update($id, ['cpgroupname' => $name]);
					}
				}
			}
			cpmsg('founder_perm_group_update_succeed', 'action=founder&operation=perm&do=group', 'succeed');
		}
	} else {
		if(!submitcheck('submit')) {

			showpermstyle();
			$perms = [];
			foreach(table_common_admincp_perm::t()->fetch_all_by_cpgroupid($id) as $perm) {
				$perms[] = $perm['perm'];
			}

			$cpgroupname = ($cpgroup = table_common_admincp_group::t()->fetch($id)) ? $cpgroup['cpgroupname'] : '';
			$data = getactionarray();
			$grouplist = '';
			foreach(table_common_admincp_group::t()->range() as $ggroup) {
				$grouplist .= '<a href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=founder&operation=perm&do=group&switch=yes&id='.$ggroup['cpgroupid'].'&scrolltop=\'+document.documentElement.scrollTop"'.($_GET['id'] == $ggroup['cpgroupid'] ? ' class="current"' : '').'>'.$ggroup['cpgroupname'].'</a>';
			}
			$grouplist = '<span id="cpgselect" class="right popupmenu_dropmenu" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'34\'});$(\'cpgselect_menu\').style.top=(parseInt($(\'cpgselect_menu\').style.top)-document.documentElement.scrollTop)+\'px\'">'.cplang('founder_group_switch').'<em>&nbsp;&nbsp;</em></span>'.
				'<div id="cpgselect_menu" class="popupmenu_popup" style="display:none">'.$grouplist.'</div>';

			showchildmenu([['menu_founder_perm', 'founder&operation=perm'], ['nav_founder_perm_group', 'founder&operation=perm&do=group']], $cpgroupname, [], $grouplist);

			showformheader('founder&operation=perm&do=group&id='.$id);
			showboxheader('', '', '', 1);

			foreach($data['cats'] as $platform => $pv) {
				$row_platform = '<div id="platform_'.$platform.'" class="marginbot"><h1 class="boxheader"><strong>'.$data['names'][$platform].'</strong><a id="a_platform_'.$platform.'_body" class="marginleft" href="javascript:;" onclick="toggle_group(\'platform_'.$platform.'_body\')">[-]</a></h1><div id="platform_'.$platform.'_body" class="boxbody">';
				echo $row_platform;
				foreach($pv as $topkey) {
					if(!$data['actions'][$platform][$topkey]) {
						continue;
					}
					$checkedall = true;
					$row = '<div class="boxbody drow clp" id="perms_'.$topkey.'">';
					foreach($data['actions'][$platform][$topkey] as $k => $item) {
						if(!$item) {
							continue;
						}
						$checked = is_array($perms) && in_array($item[1], $perms);
						if(!$checked) {
							$checkedall = false;
						}
						$row .= $item[1] ? '<div class="item'.($checked ? ' checked' : '').'"><a class="right" title="'.cplang('config').'" href="'.ADMINSCRIPT.'?frames=yes&action=founder&operation=perm&do=gperm&gplatform='.$platform.'&gset='.$topkey.'_'.$k.'" target="_blank">&nbsp;</a><label><input name="permnew[]" value="'.$item[1].'" class="checkbox" type="checkbox" '.($checked ? 'checked="checked" ' : '').' onclick="checkclk(this)" />'.cplang($item[0]).'</label></div>' : '';
					}
					$row .= '</div>';
					if($topkey != 'setting') {
						$_lang = cplang('header_'.$topkey);
						if(str_starts_with($_lang, 'header_')) {
							$_lang = substr($_lang, 7);
						}
						showboxtitle('<label><input class="checkbox" type="checkbox" onclick="permcheckall(this, \'perms_'.$topkey.'\')" '.($checkedall ? 'checked="checked" ' : '').'/> '.$_lang.'</label>');
					} else {
						showboxtitle('founder_perm_setting');
					}
					echo $row;
				}
				$row_platform = '</div></div>';
				echo $row_platform;
			}

			showsubmit('submit');
			showboxfooter(1);
			showformfooter();
			if(!empty($_GET['switch'])) {
				echo '<script type="text/javascript">showMenu({\'ctrlid\':\'cpgselect\',\'pos\':\'34\'});</script>';
			}

		} else {
			table_common_admincp_perm::t()->delete_by_cpgroupid_perm($id);
			if($_GET['permnew']) {
				foreach($_GET['permnew'] as $perm) {
					table_common_admincp_perm::t()->insert(['cpgroupid' => $id, 'perm' => $perm]);
				}
			}

			cpmsg('founder_perm_groupperm_update_succeed', 'action=founder&operation=perm&do=group', 'succeed');
		}
	}

} elseif($do == 'member') {

	$founders = $_G['config']['admincp']['founder'] !== '' ? explode(',', str_replace(' ', '', addslashes($_G['config']['admincp']['founder']))) : [];
	if($founders) {
		$founderexists = true;
		$fuid = $fuser = [];
		foreach($founders as $founder) {
			if(is_numeric($founder)) {
				$fuid[] = $founder;
			} else {
				$fuser[] = $founder;
			}
		}
		$founders = [];
		if($fuid) {
			$founders = $founders + table_common_member::t()->fetch_all($fuid, false, 0);
		}
		if($fuser) {
			$founders = $founders + table_common_member::t()->fetch_all_by_username($fuser);
		}
	} else {
		$founderexists = false;
		$founders = table_common_member::t()->fetch_all_by_adminid(1);
	}
	$id = empty($_GET['id']) ? 0 : $_GET['id'];

	if(!$id) {
		if(!submitcheck('submit')) {
			showsubmenu('menu_founder_perm', [
				['nav_founder_perm_member', 'founder&operation=perm&do=member', 1],
				['nav_founder_perm_group', 'founder&operation=perm&do=group', 0],
				['nav_founder_perm_notifyusers', 'founder&operation=perm&do=notifyusers', 0],
			]);
			$groupselect = '<select name="newcpgroupid"><option value="0">'.cplang('founder_master').'</option>';
			$groups = [];
			foreach(table_common_admincp_group::t()->range() as $group) {
				$groupselect .= '<option value="'.$group['cpgroupid'].'">'.$group['cpgroupname'].'</option>';
				$groups[$group['cpgroupid']] = $group['cpgroupname'];
			}
			$groupselect .= '</select>';
			$members = $adminmembers = [];
			$adminmembers = table_common_admincp_member::t()->range();
			foreach($adminmembers as $adminmember) {
				$adminmembers[$adminmember['uid']] = $adminmember;
			}
			foreach($founders as $uid => $founder) {
				$members[$uid] = ['uid' => $uid, 'username' => $founder['username'], 'cpgroupname' => cplang('founder_admin')];
			}
			if($adminmembers) {
				foreach(table_common_member::t()->fetch_all(array_keys($adminmembers), false, 0) as $member) {
					if(isset($members[$member['uid']])) {
						table_common_admincp_member::t()->delete($member['uid']);
						continue;
					}
					$member['cpgroupname'] = !empty($adminmembers[$member['uid']]['cpgroupid']) ? $groups[$adminmembers[$member['uid']]['cpgroupid']] : cplang('founder_master');
					if(!$founderexists && in_array($member['uid'], array_keys($founders))) {
						$member['cpgroupname'] = cplang('founder_admin');
					}
					$members[$member['uid']] = $member;
				}
			}
			/*search={"menu_founder_perm":"action=founder"}*/
			if(!$founderexists) {
				showtips(cplang('home_security_nofounder').cplang('home_security_founder'));
			} else {
				showtips('home_security_founder');
			}
			/*search*/
			showformheader('founder&operation=perm&do=member');
			showtableheader();
			showsubtitle(['', 'founder_username', 'founder_usergname', '']);
			foreach($members as $id => $member) {
				$isfounder = array_key_exists($id, $founders);
				showtablerow('style="height:20px"', ['class="td25"', 'class="td24"', 'class="td24"'], [
					!$isfounder || isset($adminmembers[$member['uid']]['cpgroupid']) ? "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$id]\">" : '',
					"<a href=\"home.php?mod=space&uid={$member['uid']}\" target=\"_blank\">{$member['username']}</a>",
					$member['cpgroupname'],
					!$isfounder && $adminmembers[$member['uid']]['cpgroupid'] ? '<a href="'.ADMINSCRIPT.'?action=founder&operation=perm&do=member&id='.$id.'">'.cplang('edit').'</a>' : ''
				]);
			}
			showtablerow('style="height:20px"', ['class="td25"', 'class="td24"', 'class="td24"'], [cplang('add_new'), '<input class="txt" type="text" name="newcpusername" value="" />', $groupselect, '']);
			showsubmit('submit', 'submit', 'del');
			showtablefooter();
			showformfooter();
		} else {
			if(!empty($_GET['newcpusername'])) {
				$newcpuid = table_common_member::t()->fetch_uid_by_username($_GET['newcpusername']);
				if(!$newcpuid) {
					$newcpuid = table_common_member::t()->fetch_uid_by_loginname($_GET['newcpusername']);
				}
				if(!$newcpuid) {
					cpmsg('founder_perm_member_noexists', '', 'error', ['name' => $_GET['newcpusername']]);
				}
				if(table_common_admincp_member::t()->count_by_uid($newcpuid) || array_key_exists($newcpuid, $founders)) {
					cpmsg('founder_perm_member_duplicate', '', 'error', ['name' => $_GET['newcpusername']]);
				}
				table_common_admincp_member::t()->insert(['uid' => $newcpuid, 'cpgroupid' => $_GET['newcpgroupid']]);
			}
			if(!empty($_GET['delete'])) {
				table_common_admincp_member::t()->delete($_GET['delete']);
			}
			updatecache('founder');
			cpmsg('founder_perm_member_update_succeed', 'action=founder&operation=perm&do=member', 'succeed');
		}
	} else {
		if(!submitcheck('submit')) {
			$member = table_common_admincp_member::t()->fetch($id);
			if(!$member) {
				cpmsg('founder_perm_member_noexists', '', 'error');
			}
			$user = getuserbyuid($id);
			$username = $user['username'];
			$cpgroupid = empty($_GET['cpgroupid']) ? $member['cpgroupid'] : $_GET['cpgroupid'];
			$member['customperm'] = empty($_GET['cpgroupid']) || $_GET['cpgroupid'] == $member['cpgroupid'] ? dunserialize($member['customperm']) : [];
			$perms = [];
			foreach(table_common_admincp_perm::t()->fetch_all_by_cpgroupid($cpgroupid) as $perm) {
				$perms[] = $perm['perm'];
			}
			$data = getactionarray();

			$groupselect = '<select name="cpgroupidnew" onchange="location.href=\''.ADMINSCRIPT.'?action=founder&operation=perm&do=member&id='.$id.'&cpgroupid=\' + this.value">';
			foreach(table_common_admincp_group::t()->range() as $group) {
				$groupselect .= '<option value="'.$group['cpgroupid'].'"'.($group['cpgroupid'] == $cpgroupid ? ' selected="selected"' : '').'>'.$group['cpgroupname'].'</option>';
			}
			$groupselect .= '</select>';

			showpermstyle();
			showsubmenu('menu_founder_memberperm', [], '', ['username' => $username]);

			showformheader('founder&operation=perm&do=member&id='.$id);
			showtableheader();
			showsetting('founder_usergname', '', '', $groupselect);
			showtablefooter();


			showboxheader('', '', '', 1);

			foreach($data['cats'] as $platform => $pv) {
				$row_platform = '<div id="platform_'.$platform.'" class="marginbot"><h1 class="boxheader"><strong>'.$data['names'][$platform].'</strong><a id="a_platform_'.$platform.'_body" class="marginleft" href="javascript:;" onclick="toggle_group(\'platform_'.$platform.'_body\')">[-]</a></h1><div id="platform_'.$platform.'_body" class="boxbody">';
				echo $row_platform;
				foreach($pv as $topkey) {
					if(!$data['actions'][$platform][$topkey]) {
						continue;
					}
					$checkedall = true;
					$row = '<div class="boxbody drow clp" id="perms_'.$topkey.'">';
					foreach($data['actions'][$platform][$topkey] as $item) {
						if(!$item) {
							continue;
						}
						$checked = is_array($perms) && in_array($item[1], $perms);
						$customchecked = is_array($member['customperm']) && in_array($item[1], $member['customperm']);
						$extra = $checked ? ($customchecked ? '' : 'checked="checked" ').' onclick="checkclk(this)"' : 'disabled="disabled" ';
						if(!$checked || $customchecked) {
							$checkedall = false;
						}
						$row .= '<div class="item'.($checked && !$customchecked ? ' checked' : '').'"><label><input name="permnew[]" value="'.$item[1].'" class="checkbox" type="checkbox" '.$extra.'/>'.cplang($item[0]).'</label></div>';
					}
					$row .= '</div>';
					if($topkey != 'setting') {
						$_lang = cplang('header_'.$topkey);
						if(str_starts_with($_lang, 'header_')) {
							$_lang = substr($_lang, 7);
						}
						showboxtitle('<label><input class="checkbox" type="checkbox" onclick="permcheckall(this, \'perms_'.$topkey.'\')" '.($checkedall ? 'checked="checked" ' : '').'/> '.$_lang.'</label>');
					} else {
						showboxtitle('founder_perm_setting');
					}
					echo $row;
				}
				$row_platform = '</div></div>';
				echo $row_platform;
			}

			showsubmit('submit');
			showboxfooter(1);
			showformfooter();
		} else {
			$_permnew = !empty($_GET['permnew']) ? $_GET['permnew'] : [];
			$cpgroupidnew = $_GET['cpgroupidnew'];
			$dbperms = table_common_admincp_perm::t()->fetch_all_by_cpgroupid($cpgroupidnew);
			$perms = [];
			foreach($dbperms as $dbperm) {
				$perms[] = $dbperm['perm'];
			}
			$customperm = serialize(array_diff($perms, $_permnew));
			table_common_admincp_member::t()->update($id, ['cpgroupid' => $cpgroupidnew, 'customperm' => $customperm]);
			cpmsg('founder_perm_member_update_succeed', 'action=founder&operation=perm&do=member', 'succeed');
		}
	}

} elseif($do == 'gperm' && !empty($_GET['gset'])) {

	$gset = $_GET['gset'];
	[$topkey, $k] = explode('_', $gset);
	$data = getactionarray();
	$gset = $data['actions'][$_GET['gplatform']][$topkey][$k];
	if(!$gset) {
		cpmsg('undefined_action', '', 'error');
	}
	if(!submitcheck('submit')) {
		$allperms = table_common_admincp_perm::t()->fetch_all_by_perm($gset[1]);
		$groups = table_common_admincp_group::t()->range();
		showsubmenu('menu_founder_permgrouplist', [[]], '', ['perm' => cplang($gset[0])]);

		showformheader('founder&operation=perm&do=gperm&gplatform='.$_GET['gplatform'].'&gset='.$_GET['gset']);
		showtableheader();
		showsubtitle(['', 'founder_usergname']);
		foreach($groups as $id => $group) {
			showtablerow('style="height:20px"', ['class="td25"', ''], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"permnew[]\" ".($allperms[$group['cpgroupid']]['perm'] ? 'checked="checked"' : '')." value=\"$id\">",
				$group['cpgroupname']
			]);
		}
		showsubmit('submit');
		showtablefooter();
		showformfooter();
	} else {
		foreach(table_common_admincp_group::t()->range() as $group) {
			if(in_array($group['cpgroupid'], $_GET['permnew'])) {
				table_common_admincp_perm::t()->insert(['cpgroupid' => $group['cpgroupid'], 'perm' => $gset[1]], false, true);
			} else {
				table_common_admincp_perm::t()->delete_by_cpgroupid_perm($group['cpgroupid'], $gset[1]);
			}
		}
		cpmsg('founder_perm_gperm_update_succeed', 'action=founder&operation=perm', 'succeed');
	}

} elseif($do == 'notifyusers') {
	$notifyusers = dunserialize($_G['setting']['notifyusers']);
	$notifytypes = explode(',', $_G['setting']['adminnotifytypes']);
	if(!submitcheck('submit')) {
		showpermstyle();
		showsubmenu('menu_founder_perm', [
			['nav_founder_perm_member', 'founder&operation=perm&do=member', 0],
			['nav_founder_perm_group', 'founder&operation=perm&do=group', 0],
			['nav_founder_perm_notifyusers', 'founder&operation=perm&do=notifyusers', 1],
		]);
		showtips('founder_notifyusers_tips');
		showformheader('founder&operation=perm&do=notifyusers');
		showtableheader();
		showsubtitle(['', 'username', '', 'founder_notifyusers_types']);
		foreach($notifyusers as $uid => $user) {
			$types = '';
			foreach($notifytypes as $key => $typename) {
				$checked = $user['types'][$key] ? ' checked' : '';
				if(str_starts_with($typename, 'verify_')) {
					$i = substr($typename, -1, 1);
					if($_G['setting']['verify'][$i]['available']) {
						$tname = $_G['setting']['verify'][$i]['title'];
					} else {
						continue;
					}
				} else {
					$tname = cplang('founder_notidyusers_'.$typename);
				}
				$types .= "<div class=\"item$checked\"><label class=\"txt\"><input class=\"checkbox\" onclick=\"checkclk(this)\" type=\"checkbox\" name=\"notifytypes_{$uid}[{$typename}]\" value=\"1\"$checked>".$tname.'</label></div>';
			}
			showtablerow('style="height:20px"', ['class="td25"', 'class="td24"', 'class="td25"', 'class="vtop"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$uid\">",
				"<input type=\"hidden\" class=\"txtnobd\" name=\"name[$uid]\" value=\"{$user['username']}\">{$user['username']}",
				'<input name="chkall_'.$uid.'" id="chkall_'.$uid.'" type="checkbox" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'notifytypes_'.$uid.'\', \'chkall_'.$uid.'\', 1)" />'.cplang('select_all'),
				$types
			]);
		}
		showtablerow('style="height:20px"', ['', 'colspan="3"'], [cplang('add_new'), '<input class="txt" type="text" name="newusername" value="" />']);
		showsubmit('submit', 'submit', 'del');
		showtablefooter();
		showformfooter();
	} else {
		$newnotifyusers = [];
		if($_GET['name']) {
			foreach($_GET['name'] as $uid => $username) {
				if($_GET['delete'] && in_array($uid, $_GET['delete'])) {
					continue;
				}
				$types = '';
				foreach($notifytypes as $typename) {
					$types .= intval($_GET['notifytypes_'.$uid][$typename]);
				}
				$newnotifyusers[$uid] = ['username' => $username, 'types' => $types];
			}
		}
		if($_GET['newusername']) {
			$newusername = addslashes($_GET['newusername']);
			$newuid = table_common_member::t()->fetch_uid_by_username($newusername);
			if($newuid) {
				$newnotifyusers[$newuid] = ['username' => $newusername, 'types' => ''];
			}
		}
		table_common_setting::t()->update_setting('notifyusers', $newnotifyusers);
		updatecache('setting');
		cpmsg('founder_perm_notifyusers_succeed', 'action=founder&operation=perm&do=notifyusers', 'succeed');
	}
}
	