<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('profilesetting');
$vid = intval($_GET['do']);
$anchor = in_array($_GET['anchor'], ['authstr', 'refusal', 'pass', 'add']) ? $_GET['anchor'] : 'authstr';
$current = [$anchor => 1];
if($anchor == 'add') {
	if(!submitcheck('addverifysubmit') || $vid < 0 || $vid > 6) {
		$navmenu[0] = ['members_verify_nav_authstr', 'verify&operation=verify&anchor=authstr&do='.$vid, 0];
		$navmenu[1] = ['members_verify_nav_refusal', 'verify&operation=verify&anchor=refusal&do='.$vid, 0];
		$navmenu[2] = ['members_verify_nav_pass', 'verify&operation=verify&anchor=pass&do='.$vid, 0];
		$navmenu[3] = ['members_verify_nav_add', 'verify&operation=verify&anchor=add&do='.$vid, 1];
		$vid ? shownav('user', 'nav_members_verify', $_G['setting']['verify'][$vid]['title']) : shownav('user', $_G['setting']['verify'][$vid]['title']);
		showsubmenu($lang['members_verify_add'].'-'.$_G['setting']['verify'][$vid]['title'], $navmenu);
		showformheader("verify&operation=verify&anchor=add&do=$vid", 'enctype');
		showtableheader();
		showsetting('members_verify_userlist', 'users', $member['users'], 'textarea');
		showsubmit('addverifysubmit');
		showtablefooter();
		showformfooter();
	} else {
		$userlist = explode("\r\n", $_GET['users']);
		$insert = [];
		$haveuser = false;
		$members = table_common_member::t()->fetch_all_by_username($userlist);
		$vuids = [];
		foreach($members as $value) {
			$vuids[$value['uid']] = $value['uid'];
		}
		$verifyusers = table_common_member_verify::t()->fetch_all($vuids);
		foreach($members as $member) {
			if(isset($verifyusers[$member['uid']])) {
				table_common_member_verify::t()->update($member['uid'], ["verify$vid" => 1]);
			} else {
				table_common_member_verify::t()->insert(['uid' => $member['uid'], "verify$vid" => 1]);
			}
			helper_forumperm::clear_cache($member['uid']);
			$haveuser = true;
		}
		if($haveuser) {
			cpmsg('members_verify_add_user_succeed', 'action=verify&operation=verify&do='.$vid.'&anchor=pass', 'succeed');
		} else {
			cpmsg_error('members_verify_add_user_failure', 'action=verify&operation=add&vid='.$vid);
		}
	}

} else {
	if($anchor != 'pass') {
		$_GET['verifytype'] = $vid;
	} else {
		$_GET['verify'.$vid] = 1;
		$_GET['orderby'] = 'uid';
	}
	require_once libfile('function/profile');
	if(!submitcheck('verifysubmit', true)) {

		$menutitle = $vid ? $_G['setting']['verify'][$vid]['title'] : $lang['members_verify_profile'];
		$navmenu[0] = ['members_verify_nav_authstr', 'verify&operation=verify&anchor=authstr&do='.$vid, $current['authstr']];
		$navmenu[1] = ['members_verify_nav_refusal', 'verify&operation=verify&anchor=refusal&do='.$vid, $current['refusal']];
		if($vid) {
			$navmenu[2] = ['members_verify_nav_pass', 'verify&operation=verify&anchor=pass&do='.$vid, $current['pass']];
			$navmenu[3] = ['members_verify_nav_add', 'verify&operation=verify&anchor=add&do='.$vid, $current['add']];
		}
		$vid ? shownav('user', 'nav_members_verify', $menutitle) : shownav('user', $menutitle);
		showsubmenu($lang['members_verify_verify'].($vid ? '-'.$menutitle : ''), $navmenu);


		$searchlang = [];
		$keys = ['search', 'likesupport', 'resultsort', 'defaultsort', 'orderdesc', 'orderasc', 'perpage_10', 'perpage_20', 'perpage_50', 'perpage_100',
			'members_verify_dateline', 'members_verify_uid', 'members_verify_username', 'members_verify_fieldid'];
		foreach($keys as $key) {
			$searchlang[$key] = cplang($key);
		}

		$orderby = $_GET['orderby'] ?? '';
		$orderby = [$orderby => ' selected'];
		$datehtml = $orderbyhtml = '';
		if($anchor != 'pass') {
			$datehtml = "<tr><td>{$searchlang['members_verify_dateline']}</td><td colspan=\"3\">
					<input type=\"text\" name=\"dateline1\" value=\"{$_GET['dateline1']}\" size=\"10\" onclick=\"showcalendar(event, this)\"> ~
					<input type=\"text\" name=\"dateline2\" value=\"{$_GET['dateline2']}\" size=\"10\" onclick=\"showcalendar(event, this)\"> (YYYY-MM-DD)
					</td></tr>";
			$orderbyhtml = "<select name=\"orderby\"><option value=\"dateline\"{$orderby['dateline']}>{$searchlang['members_verify_dateline']}</option>	</select>";
		} else {
			$orderbyhtml = "<select name=\"orderby\"><option value=\"uid\"{$orderby['dateline']}>{$searchlang['members_verify_uid']}</option>	</select>";
		}


		$ordersc = $_GET['ordersc'] ?? '';
		$perpages = $_GET['perpage'] ?? '';
		$ordersc = [$ordersc => ' selected'];
		$perpages = [$perpages => ' selected'];
		$adminscript = ADMINSCRIPT;
		$staticurl = STATICURL;
		$expertsearch = $vid ? '&nbsp;<a href="'.ADMINSCRIPT.'?action=members&operation=search&more=1&vid='.$vid.'" target="_top">'.cplang('search_higher').'</a>' : '';
		echo <<<EOF
			<form method="post" autocomplete="off" action="$adminscript">
				<div class="dbox"><div class="boxbody">
					<table cellspacing="3" cellpadding="3" class="tb tb2">
					<tr>
						<td>{$searchlang['members_verify_username']}* </td><td><input type="text" name="username" value="{$_GET['username']}"></td>
						<td>{$searchlang['members_verify_uid']}</td><td><input type="text" name="uid" value="{$_GET['uid']}"> *{$searchlang['likesupport']}</td>

					</tr>
					$datehtml
					<tr>
						<td>{$searchlang['resultsort']}</td>
						<td colspan="3">
							$orderbyhtml
							<select name="ordersc">
							<option value="desc"{$ordersc['desc']}>{$searchlang['orderdesc']}</option>
							<option value="asc"{$ordersc['asc']}>{$searchlang['orderasc']}</option>
							</select>
							<select name="perpage">
							<option value="10"{$perpages[10]}>{$searchlang['perpage_10']}</option>
							<option value="20"{$perpages[20]}>{$searchlang['perpage_20']}</option>
							<option value="50"{$perpages[50]}>{$searchlang['perpage_50']}</option>
							<option value="100"{$perpages[100]}>{$searchlang['perpage_100']}</option>
							</select>
							<input type="hidden" name="action" value="verify">
							<input type="hidden" name="operation" value="verify">
							<input type="hidden" name="do" value="$vid">
							<input type="hidden" name="anchor" value="$anchor">
							<input type="submit" name="searchsubmit" value="{$searchlang['search']}" class="btn">$expertsearch
						</td>
					</tr>
					</table>
				</div></div>
			</form>
			<iframe id="frame_profile" name="frame_profile" style="display: none"></iframe>
			<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
			<script type="text/javascript">
				function showreason(vid, flag) {
					var reasonobj = $('reason_'+vid);
					if(reasonobj) {
						reasonobj.style.display = flag ? '' : 'none';
					}
					if(!flag && $('verifyitem_' + vid) != null) {
						var checkboxs = $('verifyitem_' + vid).getElementsByTagName('input');
						for(var i in checkboxs) {
							if(checkboxs[i].type == 'checkbox') {
								checkboxs[i].checked = '';
							}
						}
					}
				}
				function mod_setbg(vid, value) {
					$('mod_' + vid + '_row').className = 'mod_' + value;
				}
				function mod_setbg_all(value) {
					checkAll('option', $('cpform'), value);
					var trs = $('cpform').getElementsByTagName('TR');
					for(var i in trs) {
						if(trs[i].id && trs[i].id.substr(0, 4) == 'mod_') {
							trs[i].className = 'mod_' + value;
							showreason(trs[i].getAttribute('verifyid'), value == 'refusal' ? 1 : 0);
						}
					}
				}
				function mod_cancel_all() {
					var inputs = $('cpform').getElementsByTagName('input');
					for(var i in inputs) {
						if(inputs[i].type == 'radio') {
							inputs[i].checked = '';
						}
					}
					var trs = $('cpform').getElementsByTagName('TR');
					for(var i in trs) {
						if(trs[i].id && trs[i].id.match(/^mod_(\d+)_row$/)) {
							trs[i].className = "mod_cancel";
							showreason(trs[i].getAttribute('verifyid'), 0)
						}
					}
				}
				function singleverify(vid) {
					var formobj = $('cpform');
					var oldaction = formobj.action;
					formobj.action = oldaction+'&frame=no&singleverify='+vid;
					formobj.target = "frame_profile";
					formobj.submit();
					formobj.action = oldaction;
					formobj.target = "";
				}

			</script>
EOF;

		$mpurl = ADMINSCRIPT.'?action=verify&operation=verify&anchor='.$anchor.'&do='.$vid;

		if($anchor == 'refusal') {
			$_GET['flag'] = -1;
		} elseif($anchor == 'authstr') {
			$_GET['flag'] = 0;
		}
		$intkeys = ['uid', 'verifytype', 'flag', 'verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6'];
		$strkeys = [];
		$randkeys = [];
		$likekeys = ['username'];
		$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys, 'v.');
		foreach($likekeys as $k) {
			$_GET[$k] = dhtmlspecialchars($_GET[$k]);
		}
		$mpurl .= '&'.implode('&', $results['urls']);
		$wherearr = $results['wherearr'];
		if($_GET['dateline1']) {
			$wherearr[] = "v.dateline >= '".strtotime($_GET['dateline1'])."'";
			$mpurl .= '&dateline1='.$_GET['dateline1'];
		}
		if($_GET['dateline2']) {
			$wherearr[] = "v.dateline <= '".strtotime($_GET['dateline2'])."'";
			$mpurl .= '&dateline2='.$_GET['dateline2'];
		}

		$wheresql = empty($wherearr) ? '1' : implode(' AND ', $wherearr);

		$orders = getorders(['dateline', 'uid'], 'dateline', 'v.');
		$ordersql = $orders['sql'];
		if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
		$orderby = [$_GET['orderby'] => ' selected'];
		$ordersc = [$_GET['ordersc'] => ' selected'];

		$orders = in_array($_G['orderby'], ['dateline', 'uid']) ? $_G['orderby'] : 'dateline';
		$ordersc = in_array(strtolower($_GET['ordersc']), ['asc', 'desc']) ? $_GET['ordersc'] : 'desc';

		$perpage = empty($_GET['perpage']) ? 0 : intval($_GET['perpage']);
		if(!in_array($perpage, [10, 20, 50, 100])) $perpage = 10;
		$perpages = [$perpage => ' selected'];
		$mpurl .= '&perpage='.$perpage;

		$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
		if($page < 1) $page = 1;
		$start = ($page - 1) * $perpage;

		$multipage = '';

		showformheader('verify&operation=verify&do='.$vid.'&anchor='.$anchor);
		echo "<script>disallowfloat = '{$_G['setting']['disallowfloat']}';</script><input type=\"hidden\" name=\"verifysubmit\" value=\"trun\" />";
		showtableheader('members_verify_manage', 'fixpadding');

		if($anchor != 'pass') {
			$cssarr = ['width="90"', 'width="120"', 'width="120"', ''];
			$titlearr = [$lang['members_verify_username'], $lang['members_verify_type'], $lang['members_verify_dateline'], $lang['members_verify_info']];
			showtablerow('class="header"', $cssarr, $titlearr);
			$count = table_common_member_verify_info::t()->count_by_search($_GET['uid'], $vid, $_GET['flag'], $_GET['username'], strtotime($_GET['dateline1']), strtotime($_GET['dateline2']));
		} else {
			$cssarr = ['width="80"', 'width="90"', 'width="120"', ''];
			$titlearr = ['', $lang['members_verify_username'], $lang['members_verify_type'], $lang['members_verify_info']];
			showtablerow('class="header"', $cssarr, $titlearr);
			$wheresql = (!empty($_GET['username']) ? str_replace('v.username', 'm.username', $wheresql) : $wheresql).' AND v.uid=m.uid ';
			$count = table_common_member_verify::t()->count_by_search($_GET['uid'], $vid, $_GET['username']);
		}
		if($count) {

			if($anchor != 'pass') {
				$verifyusers = table_common_member_verify_info::t()->fetch_all_search($_GET['uid'], $vid, $_GET['flag'], $_GET['username'], strtotime($_GET['dateline1']), strtotime($_GET['dateline2']), $orders, $start, $perpage, $ordersc);
			} else {
				$verifyusers = table_common_member_verify::t()->fetch_all_search($_GET['uid'], $vid, $_GET['username'], 'v.uid', $start, $perpage, $ordersc);
				$verifyuids = array_keys($verifyusers);
				$profiles = table_common_member_profile::t()->fetch_all($verifyuids, false, 0);
			}

			foreach($verifyusers as $uid => $value) {
				if($anchor == 'pass') {
					$value = array_merge($value, $profiles[$uid]);
				}
				$value['username'] = '<a href="home.php?mod=space&uid='.$value['uid'].'&do=profile" target="_blank">'.avatar($value['uid'], 'small').'<br/>'.$value['username'].'</a>';
				if($anchor != 'pass') {
					$fields = $anchor != 'pass' ? dunserialize($value['field']) : $_G['setting']['verify'][$vid]['field'];
					$verifytype = $value['verifytype'] ? $_G['setting']['verify'][$value['verifytype']]['title'] : $lang['members_verify_profile'];
					$fieldstr = '<table width="96%">';
					$i = 0;
					$fieldstr .= '<tr>'.($anchor == 'authstr' ? '<td width="26">'.$lang['members_verify_refusal'].'</td>' : '').'<td width="100">'.$lang['members_verify_fieldid'].'</td><td>'.$lang['members_verify_newvalue'].'</td></tr><tbody id="verifyitem_'.$value['vid'].'">';
					$i++;
					foreach($fields as $key => $field) {
						if(in_array($key, ['constellation', 'zodiac', 'birthyear', 'birthmonth', 'birthcountry', 'birthprovince', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residedist', 'residecommunity'])) {
							continue;
						}
						if($_G['cache']['profilesetting'][$key]['formtype'] == 'file') {
							if($field) {
								$field = '<a href="'.(getglobal('setting/attachurl').'./profile/'.$field).'" target="_blank"><img src="'.(getglobal('setting/attachurl').'./profile/'.$field).'" class="verifyimg" /></a>';
							} else {
								$field = cplang('members_verify_pic_removed');
							}
						} elseif(in_array($key, ['gender', 'birthday', 'birthcity', 'residecity'])) {
							$field = profile_show($key, $fields);
						}
						$fieldstr .= '<tr>'.($anchor == 'authstr' ? '<td><input type="checkbox" name="refusal['.$value['vid'].']['.$key.']" value="'.$key.'" onclick="$(\'refusal'.$value['vid'].'\').click();" /></td>' : '').'<td>'.$_G['cache']['profilesetting'][$key]['title'].':</td><td>'.$field.'</td></tr>';
						$i++;
					}
					$opstr = '';

					if($anchor == 'authstr') {
						$opstr .= "<label><input class=\"radio\" type=\"radio\" name=\"verify[{$value['vid']}]\" value=\"validate\" onclick=\"mod_setbg({$value['vid']}, 'validate');showreason({$value['vid']}, 0);\">{$lang['validate']}</label>&nbsp;<label><input class=\"radio\" type=\"radio\" name=\"verify[{$value['vid']}]\" value=\"refusal\" id=\"refusal{$value['vid']}\" onclick=\"mod_setbg({$value['vid']}, 'refusal');showreason({$value['vid']}, 1);\">{$lang['members_verify_refusal']}</label>";
					} elseif($anchor == 'refusal') {
						$opstr .= "<label><input class=\"radio\" type=\"radio\" name=\"verify[{$value['vid']}]\" value=\"validate\" onclick=\"mod_setbg({$value['vid']}, 'validate');\">{$lang['validate']}</label>";
					}

					$fieldstr .= "</tbody><tr><td colspan=\"5\">$opstr &nbsp;<span id=\"reason_{$value['vid']}\" style=\"display: none;\">{$lang['moderate_reasonpm']}&nbsp; <input type=\"text\" class=\"txt\" name=\"reason[{$value['vid']}]\" style=\"margin: 0px;\"></span>&nbsp;<input type=\"button\" value=\"{$lang['moderate']}\" name=\"singleverifysubmit\" class=\"btn\" onclick=\"singleverify({$value['vid']});\"></td></tr></table>";

					$valuearr = [$value['username'], $verifytype, dgmdate($value['dateline'], 'dt'), $fieldstr];
					showtablerow("id=\"mod_{$value['vid']}_row\" verifyid=\"{$value['vid']}}\"", $cssarr, $valuearr);
				} else {
					$fields = $_G['setting']['verify'][$vid]['field'];
					$verifytype = $vid ? $_G['setting']['verify'][$vid]['title'] : $lang['members_verify_profile'];

					$fieldstr = '<table width="96%">';
					$fieldstr .= '<tr><td width="100">'.$lang['members_verify_fieldid'].'</td><td>'.$lang['members_verify_newvalue'].'</td></tr>';
					foreach($fields as $key => $field) {
						if(!in_array($key, ['constellation', 'zodiac', 'birthyear', 'birthmonth', 'birthcountry', 'birthprovince', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residedist', 'residecommunity'])) {
							if(in_array($key, ['gender', 'birthday', 'birthcity', 'residecity'])) {
								$value[$field] = profile_show($key, $value);
							}
							if($_G['cache']['profilesetting'][$key]['formtype'] == 'file') {
								if($value[$field]) {
									$value[$field] = '<a href="'.(getglobal('setting/attachurl').'./profile/'.$value[$field]).'" target="_blank"><img src="'.(getglobal('setting/attachurl').'./profile/'.$value[$field]).'" class="verifyimg" /></a>';
								} else {
									$value[$field] = cplang('members_verify_pic_removed');
								}
							}
							$fieldstr .= '<tr><td width="100">'.$_G['cache']['profilesetting'][$key]['title'].':</td><td>'.$value[$field].'</td></tr>';
						}
					}
					$fieldstr .= '</table>';
					$opstr = "<ul class=\"nofloat\"><li><label><input class=\"radio\" type=\"radio\" name=\"verify[{$value['uid']}]\" value=\"export\" onclick=\"mod_setbg({$value['uid']}, 'validate');\">{$lang['export']}</label></li><li><label><input class=\"radio\" type=\"radio\" name=\"verify[{$value['uid']}]\" value=\"refusal\" onclick=\"mod_setbg({$value['uid']}, 'refusal');\">{$lang['members_verify_refusal']}</label></li></ul>";
					$valuearr = [$opstr, $value['username'], $verifytype, $fieldstr];
					showtablerow("id=\"mod_{$value['uid']}_row\"", $cssarr, $valuearr);
				}
			}
			$multipage = multi($count, $perpage, $page, $mpurl);
			if($anchor != 'pass') {
				showsubmit('batchverifysubmit', 'submit', '', '<a href="#all" onclick="mod_setbg_all(\'validate\')">'.cplang('moderate_all_validate').'</a>'.($anchor == 'authstr' ? ' &nbsp;<a href="#all" onclick="mod_setbg_all(\'refusal\')">'.cplang('moderate_refusal_all').'</a>' : '').' &nbsp;<a href="#all" onclick="mod_cancel_all();">'.cplang('moderate_cancel_all').'</a>', $multipage, false);
			} else {
				showsubmit('batchverifysubmit', 'submit', '', '<a href="#all" onclick="mod_setbg_all(\'export\')">'.cplang('moderate_export_all').'</a> &nbsp;<a href="#all" onclick="mod_setbg_all(\'refusal\')">'.cplang('moderate_refusal_all').'</a> &nbsp;<a href="#all" onclick="mod_cancel_all();">'.cplang('moderate_cancel_all').'</a> &nbsp;|&nbsp;<a href="'.ADMINSCRIPT.'?action=verify&operation=verify&do='.$vid.'&anchor=pass&verifysubmit=true">'.cplang('moderate_export_getall').'</a>', $multipage, false);
			}
		} else {
			showtablerow('', 'colspan="'.count($cssarr).'"', '<strong>'.cplang('moderate_nodata').'</strong>');
		}

		showtablefooter();
		showformfooter();

	} else {

		if($anchor == 'pass') {
			$verifyuids = [];
			$note_values = [
				'verify' => $vid ? '<a href="home.php?mod=spacecp&ac=profile&op=verify&vid='.$vid.'" target="_blank">'.$_G['setting']['verify'][$vid]['title'].'</a>' : ''
			];
			foreach($_GET['verify'] as $uid => $type) {
				if($type == 'export') {
					$verifyuids['export'][] = $uid;
				} elseif($type == 'refusal') {
					$verifyuids['refusal'][] = $uid;
					notification_add($uid, 'verify', 'profile_verify_pass_refusal', $note_values, 1);
				}
				helper_forumperm::clear_cache($uid);
			}
			if(is_array($verifyuids['refusal']) && !empty($verifyuids['refusal'])) {
				table_common_member_verify::t()->update($verifyuids['refusal'], ["verify$vid" => '0']);
			}
			if(is_array($verifyuids['export']) && !empty($verifyuids['export']) || empty($verifyuids['refusal'])) {
				$uids = [];
				if(is_array($verifyuids['export']) && !empty($verifyuids['export'])) {
					$uids = $verifyuids['export'];
				}
				$fields = $_G['setting']['verify'][$vid]['field'];
				$fields = array_reverse($fields);
				$fields['username'] = 'username';
				$fields = array_reverse($fields);
				$title = $verifylist = '';
				$showtitle = true;
				$verifyusers = table_common_member_verify::t()->fetch_all_by_vid($vid, 1, $uids);
				$verifyuids = array_keys($verifyusers);
				$members = table_common_member::t()->fetch_all($verifyuids, false, 0);
				$profiles = table_common_member_profile::t()->fetch_all($verifyuids, false, 0);
				foreach($verifyusers as $uid => $value) {
					$value = array_merge($value, $members[$uid], $profiles[$uid]);
					$str = $common = '';
					foreach($fields as $key => $field) {
						if(in_array($key, ['constellation', 'zodiac', 'birthyear', 'birthmonth', 'birthcountry', 'birthprovince', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residedist', 'residecommunity'])) {
							continue;
						}
						if($showtitle) {
							$title .= $common.($key == 'username' ? $lang['username'] : $_G['cache']['profilesetting'][$key]['title']);
						}
						if(in_array($key, ['gender', 'birthday', 'birthcity', 'residecity'])) {
							$value[$field] = profile_show($key, $value);
						}
						$str .= $common.$value[$field];
						$common = "\t";
					}
					$verifylist .= $str."\n";
					$showtitle = false;
				}
				$verifylist = $title."\n".$verifylist;
				$filename = date('Ymd', TIMESTAMP).'.xls';

				define('FOOTERDISABLED', true);
				ob_end_clean();
				header('Content-type:application/vnd.ms-excel');
				header('Content-Encoding: none');
				header('Content-Disposition: attachment; filename='.$filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				if($_G['charset'] != 'gbk') {
					$verifylist = diconv($verifylist, $_G['charset'], 'GBK');
				}
				echo $verifylist;
				exit();
			} else {
				cpmsg('members_verify_succeed', 'action=verify&operation=verify&do='.$vid.'&anchor=pass', 'succeed');
			}
		} else {
			$vids = [];
			$single = intval($_GET['singleverify']);
			$verifyflag = !empty($_GET['verify']);
			if($verifyflag) {
				if($single) {
					$_GET['verify'] = [$single => $_GET['verify'][$single]];
				}
				foreach($_GET['verify'] as $id => $type) {
					$vids[] = $id;
					helper_forumperm::clear_cache($id);
				}

				$verifysetting = $_G['setting']['verify'];
				$verify = $refusal = [];
				foreach(table_common_member_verify_info::t()->fetch_all($vids) as $value) {
					if(in_array($_GET['verify'][$value['vid']], ['refusal', 'validate'])) {
						$fields = dunserialize($value['field']);
						$verifysetting = $_G['setting']['verify'][$value['verifytype']];

						if($_GET['verify'][$value['vid']] == 'refusal') {
							$refusalfields = !empty($_GET['refusal'][$value['vid']]) ? $_GET['refusal'][$value['vid']] : $verifysetting['field'];
							$fieldtitle = $common = '';
							$deleteverifyimg = false;
							foreach($refusalfields as $key => $field) {
								$fieldtitle .= $common.$_G['cache']['profilesetting'][$field]['title'];
								$common = ',';
								if($_G['cache']['profilesetting'][$field]['formtype'] == 'file') {
									$deleteverifyimg = true;
									@unlink(getglobal('setting/attachdir').'./profile/'.$fields[$key]);
									$fields[$field] = '';
								}
							}
							if($deleteverifyimg) {
								table_common_member_verify_info::t()->update($value['vid'], ['field' => serialize($fields)]);
							}
							if($value['verifytype']) {
								$verify['verify']['-1'][] = $value['uid'];
							}
							$verify['flag'][] = $value['vid'];
							$note_values = [
								'verify' => $vid ? '<a href="home.php?mod=spacecp&ac=profile&op=verify&vid='.$vid.'" target="_blank">'.$verifysetting['title'].'</a>' : '',
								'profile' => $fieldtitle,
								'reason' => $_GET['reason'][$value['vid']],
							];
							$note_lang = 'profile_verify_error';
						} else {
							// 用户信息变更记录
							if($_G['setting']['profilehistory']) {
								table_common_member_profile_history::t()->insert(array_merge(table_common_member_profile::t()->fetch(intval($value['uid'])), ['dateline' => time()]));
							}
							table_common_member_profile::t()->update(intval($value['uid']), $fields);
							$verify['delete'][] = $value['vid'];
							if($value['verifytype']) {
								$verify['verify']['1'][] = $value['uid'];
							}
							$note_values = [
								'verify' => $vid ? '<a href="home.php?mod=spacecp&ac=profile&op=verify&vid='.$vid.'" target="_blank">'.$verifysetting['title'].'</a>' : ''
							];
							$note_lang = 'profile_verify_pass';
						}
						notification_add($value['uid'], 'verify', $note_lang, $note_values, 1);
					}
				}
				if($vid && !empty($verify['verify'])) {
					foreach($verify['verify'] as $flag => $uids) {
						$flag = intval($flag);
						table_common_member_verify::t()->update($uids, ["verify$vid" => $flag]);
					}
				}

				if(!empty($verify['delete'])) {
					table_common_member_verify_info::t()->delete($verify['delete']);
				}

				if(!empty($verify['flag'])) {
					table_common_member_verify_info::t()->update($verify['flag'], ['flag' => '-1']);
				}
			}
			if($single && $_GET['frame'] == 'no') {
				echo "<script type=\"text/javascript\">var trObj = parent.$('mod_{$single}_row');trObj.parentNode.removeChild(trObj);</script>";
			} else {
				cpmsg('members_verify_succeed', 'action=verify&operation=verify&do='.$vid.'&anchor='.$_GET['anchor'], 'succeed');
			}
		}
	}
}
	