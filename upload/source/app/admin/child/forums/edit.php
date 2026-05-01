<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/forumlist');
require_once libfile('function/domain');
$highlight = getgpc('highlight');
$highlight = !empty($highlight) ? dhtmlspecialchars($highlight, ENT_QUOTES) : '';
$anchor = getgpc('anchor');

list($pluginsetting, $pluginvalue) = get_pluginsetting('forums');

list($stylesetting, $stylevalue) = get_stylesetting('forums');

$multiset = 0;
if(empty($_GET['multi'])) {
	$fids = $fid;
} else {
	$multiset = 1;
	if(is_array($_GET['multi'])) {
		$fids = $_GET['multi'];
	} else {
		$_GET['multi'] = explode(',', $_GET['multi']);
		$fids = &$_GET['multi'];
	}
}
if(!empty($_GET['multi']) && is_array($_GET['multi']) && count($_GET['multi']) == 1) {
	$fids = $_GET['multi'][0];
	$multiset = 0;
}
if(empty($fids)) {
	cpmsg('forums_edit_nonexistence', 'action=forums&operation=edit'.(!empty($highlight) ? "&highlight=$highlight" : '').(!empty($anchor) ? "&anchor=$anchor" : ''), 'form', [], '<select name="fid">'.forumselect(FALSE, 0, 0, TRUE).'</select>');
}
$mforum = [];
$permnames = [
	'viewperm' => cplang('forums_edit_perm_view'),
	'postperm' => cplang('forums_edit_perm_post'),
	'replyperm' => cplang('forums_edit_perm_reply'),
	'getattachperm' => cplang('forums_edit_perm_getattach'),
	'postattachperm' => cplang('forums_edit_perm_postattach'),
	'postimageperm' => cplang('forums_edit_perm_postimage'),
];
$sysperms = array_keys($permnames);
if(!empty($_G['setting']['plugins']['permtype'])) {
	foreach($_G['setting']['plugins']['permtype'] as $_k => $_v) {
		$permnames[$_k] = lang('plugin/'.$_v['pluginid'], $_v['name']);
	}
}
$perms = array_keys($permnames);
$permcolspan = count($perms) + 1;

$query = table_forum_forum::t()->fetch_all_info_by_fids($fids);
if(empty($query)) {
	cpmsg('forums_nonexistence', '', 'error');
} else {
	foreach($query as $forum) {
		if(isset($pluginvalue[$forum['fid']])) {
			$forum['plugin'] = $pluginvalue[$forum['fid']];
		}
		if(isset($stylevalue[$forum['fid']])) {
			$forum['style'] = $stylevalue[$forum['fid']];
		}
		$forum['fields'] = !empty($forum['fields']) ? json_decode($forum['fields'], true) : [];
		$mforum[] = $forum;
	}
}

$dactionarray = [];
$allowthreadtypes = !in_array('threadtypes', $dactionarray);


$forumkeys = table_common_setting::t()->fetch_setting('forumkeys', true);

$rules = $sub_rules = [];
foreach(table_common_credit_rule::t()->fetch_all_by_action(['reply', 'post', 'digest', 'postattach', 'getattach']) as $value) {
	list($action, $sub) = explode('/', $value['action']);
	if($sub) {
		$sub_rules[$action][] = $value;
	} else {
		$rules[$value['rid']] = $value;
	}
}
$navs = [];
foreach(table_common_nav::t()->fetch_all_by_navtype_type(0, 5) as $nav) {
	$navs[$nav['identifier']] = $nav['id'];
}

if(!submitcheck('detailsubmit') && !submitcheck('multijssubmit')) {
	$anchor = in_array($_GET['anchor'], ['basic', 'extend', 'posts', 'attachtype', 'credits', 'threadtypes', 'threadsorts', 'perm', 'plugin', 'style']) ? $_GET['anchor'] : 'basic';
	shownav('forum', 'forums_edit');

	loadcache('forums');
	$forumselect = '';
	$sgid = 0;
	foreach($_G['cache']['forums'] as $forums) {
		$checked = $fid == $forums['fid'] || (is_array($_GET['multi']) && in_array($ggroup['groupid'], $_GET['multi']));
		if($forums['type'] == 'group') {
			$sgid = $forums['fid'];
			$forumselect .= '</div><em class="cl">'.
				'<span class="right"><input name="checkall_'.$forums['fid'].'" onclick="checkAll(\'value\', this.form, '.$forums['fid'].', \'checkall_'.$forums['fid'].'\')" type="checkbox" class="vmiddle checkbox" /></span>'.
				'<span class="pointer" onclick="sdisplay(\'g_'.$forums['fid'].'\', this)"><img src="'.STATICURL.'image/admincp/desc.gif" class="vmiddle" /></span> <span class="pointer" onclick="location.href=\''.ADMINSCRIPT.'?action=forums&operation=edit&switch=yes&fid='.$forums['fid'].'\'">'.$forums['name'].'</span></em><div id="g_'.$forums['fid'].'" style="display:">';
		} elseif($forums['type'] == 'forum') {
			$forumselect .= '<input class="left checkbox ck" chkvalue="'.$sgid.'" name="multi[]" value="'.$forums['fid'].'" type="checkbox" '.($checked ? 'checked="checked" ' : '').'/><a class="f cl'.($checked ? ' current"' : '').'" href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=forums&operation=edit&switch=yes&fid='.$forums['fid'].($mforum[0]['type'] != 'group' ? '&anchor=\'+currentAnchor' : '\'').'+\'&scrolltop=\'+scrollTopBody()">'.$forums['name'].'</a>';
		} elseif($forums['type'] == 'sub') {
			$forumselect .= '<input class="left checkbox ck" chkvalue="'.$sgid.'" name="multi[]" value="'.$forums['fid'].'" type="checkbox" '.($checked ? 'checked="checked" ' : '').'/><a class="s cl'.($checked ? ' current"' : '').'" href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=forums&operation=edit&switch=yes&fid='.$forums['fid'].($mforum[0]['type'] != 'group' ? '&anchor=\'+currentAnchor' : '\'').'+\'&scrolltop=\'+scrollTopBody()">'.$forums['name'].'</a>';
		}
	}
	$forumselect = '<span id="fselect" class="right popupmenu_dropmenu" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'34\'});$(\'fselect_menu\').style.top=(parseInt($(\'fselect_menu\').style.top)-scrollTopBody())+\'px\';$(\'fselect_menu\').style.left=(parseInt($(\'fselect_menu\').style.left)-document.documentElement.scrollLeft-20)+\'px\'">'.cplang('forums_edit_switch').'<em>&nbsp;&nbsp;</em></span>'.
		'<div id="fselect_menu" class="popupmenu_popup" style="display:none"><div class="fsel"><div>'.$forumselect.'</div></div><div class="cl"><input type="button" class="btn right" onclick="multiselect(\'menuform\')" value="'.cplang('forums_multiedit').'" /></div></div>';

	showformheader('', '', 'menuform', 'get');
	showhiddenfields(['action' => 'forums', 'operation' => 'edit']);
	if(count($mforum) == 1 && $mforum[0]['type'] == 'group') {
		showchildmenu([['nav_forums', 'forums']], (count($mforum) == 1 ? $mforum[0]['name'].'(gid:'.$mforum[0]['fid'].')' : ''), [
			['forums_edit_basic', 'basic', $anchor == 'basic'],
			['forums_edit_perm', 'perm', $anchor == 'perm'],
		], $forumselect, true);
	} else {
		if($multiset && !in_array($anchor, ['basic', 'extend', 'posts', 'perm', 'plugin', 'style'])) {
			$anchor = 'basic';
		}
		showchildmenu([['nav_forums', 'forums']], (count($mforum) == 1 ? $mforum[0]['name'].'(fid:'.$mforum[0]['fid'].')' : cplang('multiedit')), [
			['forums_edit_basic', 'basic', $anchor == 'basic'],
			['forums_edit_extend', 'extend', $anchor == 'extend'],
			['forums_edit_posts', 'posts', $anchor == 'posts'],
			['forums_edit_perm', 'perm', $anchor == 'perm'],
			!$multiset ? ['forums_edit_credits', 'credits', $anchor == 'credits'] : [],
			!$multiset ? ['forums_edit_threadtypes', 'threadtypes', $anchor == 'threadtypes'] : [],
			!$multiset ? ['forums_edit_threadsorts', 'threadsorts', $anchor == 'threadsorts'] : [],
			!$multiset ? ['forums_edit_attachtype', 'attachtype', $anchor == 'attachtype'] : [],
			!$multiset && ($pluginsetting || $stylesetting) ? [['menu' => 'usergroups_edit_other', 'submenu' => [
				!$pluginsetting ? [] : ['forums_edit_plugin', 'plugin', $anchor == 'plugin'],
				!$stylesetting ? [] : ['forums_edit_style', 'style', $anchor == 'style'],
			]]] : [],
			$multiset && $pluginsetting ? ['forums_edit_plugin', 'plugin', $anchor == 'plugin'] : [],
			$multiset && $stylesetting ? ['forums_edit_style', 'style', $anchor == 'style'] : [],
		], $forumselect, true);
	}
	showformfooter();

	$groups = [];
	$query = table_common_usergroup::t()->range_orderby_credit();
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groups[$group['type']][] = $group;
	}

	$styleselect = "<select name=\"styleidnew\"><option value=\"0\">{$lang['use_default']}</option>";
	foreach(table_common_style::t()->fetch_all_data(false, false) as $style) {
		$styleselect .= "<option value=\"{$style['styleid']}\" ".
			($style['styleid'] == $mforum[0]['styleid'] ? 'selected="selected"' : NULL).
			">{$style['name']}</option>\n";
	}
	$styleselect .= '</select>';

	if(!$multiset) {
		$attachtypes = '';
		foreach(table_forum_attachtype::t()->fetch_all_by_fid($fid) as $type) {
			$type['maxsize'] = round($type['maxsize'] / 1024);
			$attachtypes .= showtablerow('', ['class="td25"', 'class="td24"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$type['id']}\" />",
				"<input type=\"text\" class=\"txt\" size=\"10\" name=\"extension[{$type['id']}]\" value=\"{$type['extension']}\" />",
				"<input type=\"text\" class=\"txt\" size=\"15\" name=\"maxsize[{$type['id']}]\" value=\"{$type['maxsize']}\" />"
			], TRUE);
		}
	} else {
		showtips('setting_multi_tips');
	}

	if($multiset) {
		$_G['showsetting_multi'] = 0;
		$_G['showsetting_multicount'] = count($mforum);
		foreach($mforum as $forum) {
			$_G['showtableheader_multi'][] = '<a href="javascript:;" onclick="location.href=\''.ADMINSCRIPT.'?action=forums&operation=edit&fid='.$forum['fid'].'&anchor=\'+$(\'cpform\').anchor.value;return false">'.$forum['name'].'(fid:'.$forum['fid'].')</a>';
		}
	}

	showformheader("forums&operation=edit&fid=$fid&", 'enctype');
	showhiddenfields(['type' => $mforum[0]['type']]);

	if(count($mforum) == 1 && $mforum[0]['type'] == 'group') {
		$mforum[0]['extra'] = dunserialize($mforum[0]['extra']);
		/*search={"forums_admin":"action=forums","forums_edit_basic":"action=forums&operation=edit&anchor=basic"}*/
		showtagheader('div', 'basic', $anchor == 'basic');
		showtableheader();
		showsetting('forums_edit_basic_cat_name', 'namenew', $mforum[0]['name'], 'text');
		showsetting('forums_edit_basic_cat_name_color', 'extranew[namecolor]', $mforum[0]['extra']['namecolor'], 'color');
		showsetting('forums_edit_basic_cat_style', '', '', $styleselect);
		showsetting('forums_edit_extend_forum_horizontal', 'forumcolumnsnew', $mforum[0]['forumcolumns'], 'text');
		showsetting('forums_edit_extend_cat_sub_horizontal', 'catforumcolumnsnew', $mforum[0]['catforumcolumns'], 'text');
		if(!empty($_G['setting']['domain']['root']['forum'])) {
			showsetting('forums_edit_extend_domain', '', '', $_G['scheme'].'://<input type="text" name="domainnew" class="txt" value="'.$mforum[0]['domain'].'" style="width:100px; margin-right:0px;" >.'.$_G['setting']['domain']['root']['forum']);
		} else {
			showsetting('forums_edit_extend_domain', 'domainnew', '', 'text', 'disabled');
		}
		showsetting('forums_cat_display', 'statusnew', $mforum[0]['status'], 'radio');
		showsetting('forums_edit_basic_shownav', 'shownavnew', array_key_exists($mforum[0]['fid'], $navs) ? 1 : 0, 'radio');
		showtablefooter();
		showtips('setting_seo_forum_tips', 'seo_tips', true, 'setseotips');
		showtableheader();
		showsetting('forums_edit_basic_seotitle', 'seotitlenew', dhtmlspecialchars($mforum[0]['seotitle']), 'text');
		showsetting('forums_edit_basic_keyword', 'keywordsnew', dhtmlspecialchars($mforum[0]['keywords']), 'text');
		showsetting('forums_edit_basic_seodescription', 'seodescriptionnew', dhtmlspecialchars($mforum[0]['seodescription']), 'textarea');
		showsubmit('detailsubmit');
		showtablefooter();
		showtagfooter('div');
		/*search*/

		/*search={"forums_admin":"action=forums","forums_edit_perm_forum":"action=forums&operation=edit&anchor=perm"}*/
		showtagheader('div', 'perm', $anchor == 'perm');
		showtableheader('', '');

		$forum['formulaperm'] = dunserialize($forum['formulaperm']);
		$forum['formulapermmessage'] = $forum['formulaperm']['message'];
		$forum['formulapermusers'] = $forum['formulaperm']['users'];
		$forum['formulaperm'] = $forum['formulaperm'][0];

		require_once childfile('forums/perm');

		showsubmit('detailsubmit');
		showtablefooter();
		showtagfooter('div');
		/*search*/

	} else {

		require_once libfile('function/editor');

		$mfids = [];
		foreach($mforum as $forum) {
			$fid = $forum['fid'];
			$mfids[] = $fid;
			if(!$multiset) {
				$fupselect = "<select name=\"fupnew\">\n";
				$query = table_forum_forum::t()->fetch_all_info_by_ignore_fid($fid);
				foreach($query as $fup) {
					$fups[] = $fup;
				}
				if(is_array($fups)) {
					foreach($fups as $forum1) {
						if($forum1['type'] == 'group') {
							$selected = $forum1['fid'] == $forum['fup'] ? "selected=\"selected\"" : NULL;
							$fupselect .= "<option value=\"{$forum1['fid']}\" $selected>{$forum1['name']}</option>\n";
							foreach($fups as $forum2) {
								if($forum2['type'] == 'forum' && $forum2['fup'] == $forum1['fid']) {
									$selected = $forum2['fid'] == $forum['fup'] ? "selected=\"selected\"" : NULL;
									$fupselect .= "<option value=\"{$forum2['fid']}\" $selected>&nbsp; &gt; {$forum2['name']}</option>\n";
								}
							}
						}
					}
					foreach($fups as $forum0) {
						if($forum0['type'] == 'forum' && $forum0['fup'] == 0) {
							$selected = $forum0['fid'] == $forum['fup'] ? "selected=\"selected\"" : NULL;
							$fupselect .= "<option value=\"{$forum0['fid']}\" $selected>{$forum0['name']}</option>\n";
						}
					}
				}
				$fupselect .= '</select>';

				if($forum['threadtypes']) {
					$forum['threadtypes'] = dunserialize($forum['threadtypes']);
					$forum['threadtypes']['status'] = 1;
				} else {
					$forum['threadtypes'] = ['status' => 0, 'required' => 0, 'listable' => 0, 'prefix' => 0, 'options' => []];
				}

				if($forum['threadsorts']) {
					$forum['threadsorts'] = dunserialize($forum['threadsorts']);
					$forum['threadsorts']['status'] = 1;
				} else {
					$forum['threadsorts'] = ['status' => 0, 'required' => 0, 'listable' => 0, 'prefix' => 0, 'options' => []];
				}

				$typeselect = $sortselect = '';
				$supsort = [
					[0, cplang('none')],
				];

				$query = table_forum_threadtype::t()->fetch_all_for_order();
				$typeselect = getthreadclasses_html($fid);
				foreach($query as $type) {
					$typeselected = [];
					$enablechecked = '';

					$keysort = $type['special'] ? 'threadsorts' : 'threadtypes';
					if(isset($forum[$keysort]['types'][$type['typeid']])) {
						$enablechecked = ' checked="checked"';
					}

					$showtype = TRUE;

					loadcache('threadsort_option_'.$type['typeid']);
					if($type['special'] && !$_G['cache']['threadsort_option_'.$type['typeid']]) {
						$showtype = FALSE;
					}
					if($type['special']) {
						$typeselected[3] = $forum['threadsorts']['show'][$type['typeid']] ? ' checked="checked"' : '';
						$sortselect .= $showtype ? showtablerow('', ['class="td25"'], [
							'<input type="checkbox" name="threadsortsnew[options][enable]['.$type['typeid'].']" value="1" class="checkbox"'.$enablechecked.' />',
							$type['name'],
							$type['description'],
							"<input class=\"checkbox\" type=\"checkbox\" name=\"threadsortsnew[options][show][{$type['typeid']}]\" value=\"3\" $typeselected[3] />",
							"<input class=\"radio\" type=\"radio\" name=\"threadsortsnew[defaultshow]\" value=\"{$type['typeid']}\" ".($forum['threadsorts']['defaultshow'] == $type['typeid'] ? 'checked' : '').' />'
						], TRUE) : '';
					}

					$supsort[] = [$type['typeid'], $type['name']];
				}
				$forum['creditspolicy'] = $forum['creditspolicy'] ? dunserialize($forum['creditspolicy']) : [];
			}

			if($forum['autoclose']) {
				$forum['autoclosetime'] = abs($forum['autoclose']);
				$forum['autoclose'] = $forum['autoclose'] / abs($forum['autoclose']);
			}

			if($forum['threadplugin']) {
				$forum['threadplugin'] = dunserialize($forum['threadplugin']);
			}

			$simplebin = sprintf('%09b', $forum['simple']);
			$forum['defaultorderfield'] = bindec(substr($simplebin, 0, 3));
			$forum['defaultorder'] = ($forum['simple'] & 32) ? 1 : 0;
			$forum['subforumsindex'] = bindec(substr($simplebin, 3, 2));
			$forum['subforumsindex'] = $forum['subforumsindex'] == 0 ? -1 : ($forum['subforumsindex'] == 2 ? 0 : 1);
			$forum['simple'] = $forum['simple'] & 1;
			$forum['modrecommend'] = $forum['modrecommend'] ? dunserialize($forum['modrecommend']) : [];
			$forum['formulaperm'] = dunserialize($forum['formulaperm']);
			$forum['viewtype'] = $forum['formulaperm']['viewtype'];
			$forum['medal'] = $forum['formulaperm']['medal'];
			$forum['formulapermmessage'] = $forum['formulaperm']['message'];
			$forum['formulapermusers'] = $forum['formulaperm']['users'];
			$forum['formulaperm'] = $forum['formulaperm'][0];
			$forum['extra'] = dunserialize($forum['extra']);
			$forum['threadsorts'] = is_array($forum['threadsorts']) ? $forum['threadsorts'] : [];
			$forum['threadsorts']['default'] = $forum['threadsorts']['defaultshow'] ? 1 : 0;

			$extraperms = [];
			foreach($perms as $perm) {
				if(in_array($perm, $sysperms)) {
					continue;
				}
				$extraperms[] = $perm;
				if(!empty($forum['extra']['perms'][$perm])) {
					$forum[$perm] = $forum['extra']['perms'][$perm];
				}
			}
			$extraperms = json_encode($extraperms);

			$_G['multisetting'] = $multiset ? 1 : 0;
			showmultititle();
			/*search={"forums_admin":"action=forums","forums_edit_basic":"action=forums&operation=edit&anchor=basic"}*/
			showtagheader('div', 'basic', $anchor == 'basic');
			if(!$multiset) {
				showtips('forums_edit_tips');
			}
			showtableheader('forums_edit_basic');
			showsetting('forums_edit_basic_name', 'namenew', $forum['name'], 'text');
			showsetting('forums_edit_base_name_color', 'extranew[namecolor]', $forum['extra']['namecolor'], 'color');
			if(!$multiset) {
				if($forum['icon']) {
					$valueparse = parse_url($forum['icon']);
					if(isset($valueparse['host'])) {
						$forumicon = $forum['icon'];
					} else {
						$forumicon = $_G['setting']['attachurl'].'common/'.$forum['icon'].'?'.random(6);
					}
					$forumiconhtml = '<label><input type="checkbox" class="checkbox" name="deleteicon" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$forumicon.'" /><br />';
				}
				showsetting('forums_edit_basic_icon', 'iconnew', $forum['icon'], 'filetext', '', 0, $forumiconhtml);
				showsetting('forums_edit_basic_icon_width', 'extranew[iconwidth]', $forum['extra']['iconwidth'], 'text');
				if($forum['banner']) {
					$valueparse = parse_url($forum['banner']);
					if(isset($valueparse['host'])) {
						$forumbanner = $forum['banner'];
					} else {
						$forumbanner = $_G['setting']['attachurl'].'common/'.$forum['banner'].'?'.random(6);
					}
					$forumbannerhtml = '<label><input type="checkbox" class="checkbox" name="deletebanner" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$forumbanner.'" /><br />';
				}
				showsetting('forums_edit_basic_banner', 'bannernew', $forum['banner'], 'filetext', '', 0, $forumbannerhtml);
			}
			showsetting('forums_edit_basic_display', 'statusnew', $forum['status'], 'radio');
			showsetting('forums_edit_basic_shownav', 'shownavnew', array_key_exists($fid, $navs) ? 1 : 0, 'radio');
			if(!$multiset) {
				showsetting('forums_edit_basic_up', '', '', $fupselect);
			}
			showsetting('forums_edit_basic_redirect', 'redirectnew', $forum['redirect'], 'text');
			showsetting('forums_edit_basic_description', 'descriptionnew', htmlspecialchars_decode(html2bbcode($forum['description'])), 'textarea');
			showsetting('forums_edit_basic_rules', 'rulesnew', htmlspecialchars_decode(html2bbcode($forum['rules'])), 'textarea');
			showsetting('forums_edit_basic_keys', 'keysnew', $forumkeys[$fid], 'text');
			if(!empty($_G['setting']['domain']['root']['forum'])) {
				$iname = $multiset ? "multinew[{$_G['showsetting_multi']}][domainnew]" : 'domainnew';
				showsetting('forums_edit_extend_domain', '', '', $_G['scheme'].'://<input type="text" name="'.$iname.'" class="txt" value="'.$forum['domain'].'" style="width:100px; margin-right:0px;" >.'.$_G['setting']['domain']['root']['forum']);
			} elseif(!$multiset) {
				showsetting('forums_edit_extend_domain', 'domainnew', '', 'text', 'disabled');
			}
			showtablefooter();
			if(!$multiset) {
				showtips('setting_seo_forum_tips', 'seo_tips', true, 'setseotips');
			}
			showtableheader();
			showsetting('forums_edit_basic_seotitle', 'seotitlenew', dhtmlspecialchars($forum['seotitle']), 'text');
			showsetting('forums_edit_basic_keyword', 'keywordsnew', dhtmlspecialchars($forum['keywords']), 'text');
			showsetting('forums_edit_basic_seodescription', 'seodescriptionnew', dhtmlspecialchars($forum['seodescription']), 'textarea');
			showtablefooter();
			showtagfooter('div');
			/*search*/

			/*search={"forums_admin":"action=forums","forums_edit_extend":"action=forums&operation=edit&anchor=extend"}*/
			showtagheader('div', 'extend', $anchor == 'extend');
			if(!$multiset) {
				showtips('forums_edit_tips');
			}
			showtableheader('forums_edit_extend', 'nobottom');
			$multi_styleselect = $_GET['multi'] ? preg_replace('/\w+new/', 'multinew['.$_G['showsetting_multi'].'][\\0]', $styleselect) : $styleselect;
			$styleid = $forum['styleid'];
			$multi_styleselect = str_replace("selected=\"selected\"", '', $multi_styleselect);
			$multi_styleselect = str_replace("value=\"$styleid\"", "value=\"$styleid\" selected=\"selected\"", $multi_styleselect);
			showsetting('forums_edit_extend_style', '', '', $multi_styleselect);
			if(!$multiset) {
				showsetting('forums_edit_threadsorts_suptypeid', ['threadsortsnew[suptypeid]', $supsort], $forum['threadsorts']['suptypeid'], 'select');
			}
			if($forum['type'] != 'sub') {
				showsetting('forums_edit_extend_sub_horizontal', 'forumcolumnsnew', $forum['forumcolumns'], 'text');
				showsetting('forums_edit_extend_subforumsindex', ['subforumsindexnew', [
					[-1, cplang('default')],
					[1, cplang('yes')],
					[0, cplang('no')]
				], 1], $forum['subforumsindex'], 'mradio');
				showsetting('forums_edit_extend_simple', 'simplenew', $forum['simple'], 'radio');
			} else {
				if($_GET['multi']) {
					showsetting('forums_edit_extend_sub_horizontal', '', '', cplang('forums_edit_sub_multi_tips'));
					showsetting('forums_edit_extend_subforumsindex', '', '', cplang('forums_edit_sub_multi_tips'));
					showsetting('forums_edit_extend_simple', '', '', cplang('forums_edit_sub_multi_tips'));
				}
			}
			showsetting('forums_edit_extend_widthauto', ['widthautonew', [
				[0, cplang('default')],
				[-1, cplang('forums_edit_extend_widthauto_-1')],
				[1, cplang('forums_edit_extend_widthauto_1')],
			], 1], $forum['widthauto'], 'mradio');
			showsetting('forums_edit_extend_picstyle', 'picstylenew', $forum['picstyle'], 'radio');
			showsetting('forums_edit_extend_allowside', 'allowsidenew', $forum['allowside'], 'radio');
			showsetting('forums_edit_extend_recommend_top', 'allowglobalsticknew', $forum['allowglobalstick'], 'radio');
			showsetting('forums_edit_extend_defaultorderfield', ['defaultorderfieldnew', [
				[0, cplang('forums_edit_extend_order_lastpost')],
				[1, cplang('forums_edit_extend_order_starttime')],
				[2, cplang('forums_edit_extend_order_replies')],
				[3, cplang('forums_edit_extend_order_views')],
				[4, cplang('forums_edit_extend_order_recommends')],
				[5, cplang('forums_edit_extend_order_heats')]
			]], $forum['defaultorderfield'], 'mradio');
			showsetting('forums_edit_extend_defaultorder', ['defaultordernew', [
				[0, cplang('forums_edit_extend_order_desc')],
				[1, cplang('forums_edit_extend_order_asc')]
			]], $forum['defaultorder'], 'mradio');
			if($_G['setting']['allowreplybg']) {
				$replybghtml = '';
				if($forum['replybg']) {
					$replybgurl = parse_url($forum['replybg']);
					if(isset($replybgurl['host'])) {
						$replybgicon = $forum['replybg'];
					} else {
						$replybgicon = $_G['setting']['attachurl'].'common/'.$forum['replybg'].'?'.random(6);
					}
					$replybghtml = '<label><input type="checkbox" class="checkbox" name="delreplybg" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$replybgicon.'" width="200px" />';
				}
				showsetting('forums_edit_extend_reply_background', 'replybgnew', (!$replybgurl['host'] ? str_replace($_G['setting']['attachurl'].'common/', '', $forum['replybg']) : $forum['replybg']), 'filetext', '', 0, $replybghtml);
			}
			showsetting('forums_edit_extend_threadcache', 'threadcachesnew', $forum['threadcaches'], 'text');
			showsetting('forums_edit_extend_relatedgroup', 'relatedgroupnew', $forum['relatedgroup'], 'text');
			showsetting('forums_edit_extend_edit_rules', 'alloweditrulesnew', $forum['alloweditrules'], 'radio');
			showsetting('forums_edit_extend_disablecollect', 'disablecollectnew', $forum['disablecollect'], 'radio');
			showsetting('forums_edit_extend_recommend', 'modrecommendnew[open]', $forum['modrecommend']['open'], 'radio', '', 1);
			showsetting('forums_edit_extend_recommend_sort', ['modrecommendnew[sort]', [
				[1, cplang('forums_edit_extend_recommend_sort_auto')],
				[0, cplang('forums_edit_extend_recommend_sort_manual')],
				[2, cplang('forums_edit_extend_recommend_sort_mix')]]], $forum['modrecommend']['sort'], 'mradio');
			showsetting('forums_edit_extend_recommend_orderby', ['modrecommendnew[orderby]', [
				[0, cplang('forums_edit_extend_recommend_orderby_dateline')],
				[1, cplang('forums_edit_extend_recommend_orderby_lastpost')],
				[2, cplang('forums_edit_extend_recommend_orderby_views')],
				[3, cplang('forums_edit_extend_recommend_orderby_replies')],
				[4, cplang('forums_edit_extend_recommend_orderby_digest')],
				[5, cplang('forums_edit_extend_recommend_orderby_recommend')],
				[6, cplang('forums_edit_extend_recommend_orderby_heats')],
			]], $forum['modrecommend']['orderby'], 'mradio');
			showsetting('forums_edit_extend_recommend_num', 'modrecommendnew[num]', $forum['modrecommend']['num'], 'text');
			showsetting('forums_edit_extend_recommend_imagenum', 'modrecommendnew[imagenum]', $forum['modrecommend']['imagenum'], 'text');
			showsetting('forums_edit_extend_recommend_imagesize', ['modrecommendnew[imagewidth]', 'modrecommendnew[imageheight]'], [intval($forum['modrecommend']['imagewidth']), intval($forum['modrecommend']['imageheight'])], 'multiply');
			showsetting('forums_edit_extend_recommend_maxlength', 'modrecommendnew[maxlength]', $forum['modrecommend']['maxlength'], 'text');
			showsetting('forums_edit_extend_recommend_cachelife', 'modrecommendnew[cachelife]', $forum['modrecommend']['cachelife'], 'text');
			showsetting('forums_edit_extend_recommend_dateline', 'modrecommendnew[dateline]', $forum['modrecommend']['dateline'], 'text');
			showtablefooter();
			showtagfooter('div');
			/*search*/

			/*search={"forums_admin":"action=forums","forums_edit_posts":"action=forums&operation=edit&anchor=posts"}*/
			showtagheader('div', 'posts', $anchor == 'posts');
			if(!$multiset) {
				showtips('forums_edit_tips');
			}
			showtableheader('forums_edit_posts', 'nobottom');
			showsetting('forums_edit_posts_modposts', ['modnewpostsnew', [
				[0, cplang('none')],
				[1, cplang('forums_edit_posts_modposts_threads')],
				[2, cplang('forums_edit_posts_modposts_posts')]
			]], $forum['modnewposts'], 'mradio');
			showsetting('forums_edit_posts_alloweditpost', 'alloweditpostnew', $forum['alloweditpost'], 'radio');
			showsetting('forums_edit_posts_recyclebin', 'recyclebinnew', $forum['recyclebin'], 'radio');
			showsetting('forums_edit_posts_editormode', ['editormodenew', [
				[-1, $lang['forums_edit_posts_editormode_global']],
				[0, $lang['forums_edit_posts_editormode_discuzcode']],
				[1, $lang['forums_edit_posts_editormode_wysiwyg']],
				[2, $lang['forums_edit_posts_editormode_json']],
			]], $forum['editormode'], 'mradio');
			showsetting('forums_edit_posts_html', 'allowhtmlnew', $forum['allowhtml'], 'radio');
			showsetting('forums_edit_posts_bbcode', 'allowbbcodenew', $forum['allowbbcode'], 'radio');
			showsetting('forums_edit_posts_imgcode', 'allowimgcodenew', $forum['allowimgcode'], 'radio');
			showsetting('forums_edit_posts_mediacode', 'allowmediacodenew', $forum['allowmediacode'], 'radio');
			showsetting('forums_edit_posts_smilies', 'allowsmiliesnew', $forum['allowsmilies'], 'radio');
			showsetting('forums_edit_posts_jammer', 'jammernew', $forum['jammer'], 'radio');
			showsetting('forums_edit_posts_anonymous', 'allowanonymousnew', $forum['allowanonymous'], 'radio');
			showsetting('forums_edit_posts_disablethumb', 'disablethumbnew', $forum['disablethumb'], 'radio');
			showsetting('forums_edit_posts_disablewatermark', 'disablewatermarknew', $forum['disablewatermark'], 'radio');

			showsetting('forums_edit_posts_allowpostspecial', ['allowpostspecialnew', [
				cplang('thread_poll'),
				cplang('thread_trade'),
				cplang('thread_reward'),
				cplang('thread_activity'),
				cplang('thread_debate')
			]], $forum['allowpostspecial'], 'binmcheckbox');
			$threadpluginarray = [];
			if(is_array($_G['setting']['threadplugins'])) foreach($_G['setting']['threadplugins'] as $tpid => $data) {
				$threadpluginarray[] = [$tpid, $data['name']];
			}
			if($threadpluginarray) {
				showsetting('forums_edit_posts_threadplugin', ['threadpluginnew', $threadpluginarray], $forum['threadplugin'], 'mcheckbox');
			}
			showsetting('forums_edit_posts_allowspecialonly', 'allowspecialonlynew', $forum['allowspecialonly'], 'radio');
			showsetting('forums_edit_posts_autoclose', ['autoclosenew', [
				[0, cplang('forums_edit_posts_autoclose_none'), ['autoclose_time' => 'none']],
				[1, cplang('forums_edit_posts_autoclose_dateline'), ['autoclose_time' => '']],
				[-1, cplang('forums_edit_posts_autoclose_lastpost'), ['autoclose_time' => '']]
			]], $forum['autoclose'], 'mradio');
			showtagheader('tbody', 'autoclose_time', $forum['autoclose'], 'sub');
			showsetting('forums_edit_posts_autoclose_time', 'autoclosetimenew', $forum['autoclosetime'], 'text');
			showtagfooter('tbody');
			showsetting('forums_edit_posts_attach_ext', 'attachextensionsnew', $forum['attachextensions'], 'text');
			showsetting('forums_edit_posts_allowfeed', 'allowfeednew', $forum['allowfeed'], 'radio');
			showsetting('forums_edit_posts_allowfeed_default', 'fieldsnew[allowfeed_default]', $forum['fields']['allowfeed_default'], 'radio');
			showsetting('forums_edit_posts_commentitem', 'commentitemnew', $forum['commentitem'], 'textarea');
			showsetting('forums_edit_posts_noantitheft', 'noantitheftnew', $forum['noantitheft'], 'radio');
			showsetting('forums_edit_posts_noforumhidewater', 'noforumhidewaternew', $forum['noforumhidewater'], 'radio');
			showsetting('forums_edit_posts_noforumrecommend', 'noforumrecommendnew', $forum['noforumrecommend'], 'radio');

			showtablefooter();
			showtagfooter('div');
			/*search*/

			if(!$multiset) {
				/*search={"forums_admin":"action=forums","forums_edit_attachtype":"action=forums&operation=edit&anchor=attachtype"}*/
				showtagheader('div', 'attachtype', $anchor == 'attachtype');
				showtips('forums_edit_attachtype_tips');
				showtableheader('', 'nobottom');
				showtablerow('class="partition"', ['class="td25"', 'class="td24"'], [cplang('del'), cplang('misc_attachtype_ext'), cplang('misc_attachtype_maxsize')]);
				echo $attachtypes;
				echo '<tr><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 1)" class="addtr">'.$lang['misc_attachtype_add'].'</a></div></tr>';
				showtablefooter();
				showtagfooter('div');
				/*search*/

				/*search={"forums_admin":"action=forums","forums_edit_credits_policy":"action=forums&operation=edit&anchor=credits"}*/
				showtagheader('div', 'credits', $anchor == 'credits');
				if(!$multiset) {
					showtips('forums_edit_tips');
				}
				showtableheader('forums_edit_credits_policy', 'fixpadding nobottom');
				echo '<tr class="header"><th>'.cplang('setting_credits_policy_name').'</th><th>'.cplang('setting_credits_policy_cycletype').'</th><th>'.cplang('setting_credits_policy_rewardnum').'</th><th class="td25">'.cplang('custom').'</th>';
				foreach($_G['setting']['extcredits'] as $i => $extcredit) {
					echo '<th>'.$extcredit['title'].'</th>';
				}
				echo '<th>&nbsp;</th></tr>';

				if(is_array($_G['setting']['extcredits'])) {
					foreach($rules as $rid => $rule) {
						showrulerow($rule);
						if(isset($sub_rules[$rule['action']])) {
							foreach($sub_rules[$rule['action']] as $sub_rule) {
								showrulerow($sub_rule, 1);
							}
						}
					}
				}
				showtablerow('', 'class="lineheight" colspan="13"', cplang('forums_edit_credits_comment', ['fid' => $fid]));

				showtablefooter();
				print <<<EOF
					<script type="text/javascript">
						function modifystate(custom) {
							var trObj = custom.parentNode.parentNode;
							var inputsObj = trObj.getElementsByTagName('input');
							for(key in inputsObj) {
								var obj = inputsObj[key];
								if(typeof obj == 'object' && obj.type != 'checkbox') {
									obj.value = '';
									obj.readOnly = custom.checked ? false : true;
									obj.style.display = obj.readOnly ? 'none' : '';
								}
							}
						}
					</script>
EOF;
				showtagfooter('div');
				/*search*/
			}

			if($allowthreadtypes && !$multiset) {
				$lang_forums_edit_threadtypes_use_cols = cplang('forums_edit_threadtypes_use_cols');
				$lang_forums_edit_threadtypes_use_choice = cplang('forums_edit_threadtypes_use_choice');
				echo <<<EOT
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1,'', 'td25'],
				[1,'<input type="text" size="2" name="newdisplayorder[]" value="0" />'],
				[1,'<input type="text" name="newname[]" />'],
				[1,'<input type="text" name="newicon[]" />'],
				[1,'<input type="hidden" name="newenable[]" value="1"><input type="checkbox" class="checkbox" checked="checked" disabled />'],
				[1,'<input type="hidden" name="newmoderators[]" value="0"><input type="checkbox" class="checkbox" disabled />'],
				[1,'']
			],
			[
				[1,'', 'td25'],
				[1,'<input name="newextension[]" type="text" class="txt" size="10">', 'td24'],
				[1,'<input name="newmaxsize[]" type="text" class="txt" size="15">']
			]
		];
	</script>
EOT;
				/*search={"forums_admin":"action=forums","forums_edit_threadtypes_config":"action=forums&operation=edit&anchor=threadtypes"}*/
				showtagheader('div', 'threadtypes', $anchor == 'threadtypes');
				if(!$multiset) {
					showtips('forums_edit_tips');
				}
				showtableheader('forums_edit_threadtypes_config', 'nobottom');
				showsetting('forums_edit_threadtypes_status', ['threadtypesnew[status]', [
					[1, cplang('yes'), ['threadtypes_config' => '', 'threadtypes_manage' => '']],
					[0, cplang('no'), ['threadtypes_config' => 'none', 'threadtypes_manage' => 'none']]
				], TRUE], $forum['threadtypes']['status'], 'mradio');
				showtagheader('tbody', 'threadtypes_config', $forum['threadtypes']['status']);
				showsetting('forums_edit_threadtypes_required', 'threadtypesnew[required]', $forum['threadtypes']['required'], 'radio');
				showsetting('forums_edit_threadtypes_listable', 'threadtypesnew[listable]', $forum['threadtypes']['listable'], 'radio');
				showsetting('forums_edit_threadtypes_prefix',
					[
						'threadtypesnew[prefix]',
						[
							[0, cplang('forums_edit_threadtypes_noprefix')],
							[1, cplang('forums_edit_threadtypes_textonly')],
							[2, cplang('forums_edit_threadtypes_icononly')],
						],
					],
					$forum['threadtypes']['prefix'], 'mradio'
				);
				showtagfooter('tbody');
				showtablefooter();

				showtagheader('div', 'threadtypes_manage', $forum['threadtypes']['status']);
				showtableheader('forums_edit_threadtypes', 'noborder fixpadding');
				showsubtitle(['delete', 'display_order', cplang('forums_edit_threadtypes_name').' '.cplang('tiny_bbcode_support'), 'forums_edit_threadtypes_icon', 'enable', 'forums_edit_threadtypes_moderators']);
				echo $typeselect;
				echo '<tr><td colspan="7"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.cplang('threadtype_infotypes_add').'</a></div></td></tr>';
				showtablefooter();
				showtagfooter('div');
				showtagfooter('div');
				/*search*/

				/*search={"forums_admin":"action=forums","forums_edit_threadsorts":"action=forums&operation=edit&anchor=threadsorts"}*/
				showtagheader('div', 'threadsorts', $anchor == 'threadsorts');
				if(!$multiset) {
					showtips('forums_edit_tips');
				}
				showtableheader('forums_edit_threadsorts', 'nobottom');
				showsetting('forums_edit_threadsorts_status', ['threadsortsnew[status]', [
					[1, cplang('yes'), ['threadsorts_config' => '', 'threadsorts_manage' => '']],
					[0, cplang('no'), ['threadsorts_config' => 'none', 'threadsorts_manage' => 'none']]
				], TRUE], $forum['threadsorts']['status'], 'mradio');
				showtagheader('tbody', 'threadsorts_config', $forum['threadsorts']['status']);
				showsetting('forums_edit_threadtypes_required', 'threadsortsnew[required]', $forum['threadsorts']['required'], 'radio');
				showsetting('forums_edit_threadtypes_prefix', 'threadsortsnew[prefix]', $forum['threadsorts']['prefix'], 'radio');
				showsetting('forums_edit_threadsorts_default', 'threadsortsnew[default]', $forum['threadsorts']['default'], 'radio', norelatedlink: true);
				showtagfooter('tbody');
				showtablefooter();

				showtagheader('div', 'threadsorts_manage', $forum['threadsorts']['status']);
				showtableheader('', 'noborder fixpadding');
				showsubtitle(['enable', 'forums_edit_threadtypes_name', 'forums_edit_threadtypes_note', 'forums_edit_threadtypes_show', 'forums_edit_threadtypes_defaultshow']);
				echo $sortselect;
				showtablefooter();
				showtagfooter('div');
				showtagfooter('div');
				/*search*/
			}

			/*search={"forums_admin":"action=forums","forums_edit_perm_forum":"action=forums&operation=edit&anchor=perm"}*/
			showtagheader('div', 'perm', $anchor == 'perm');
			if(!$multiset) {
				showtips('forums_edit_tips');
			}
			showtableheader('', '');
			showsetting('forums_edit_perm_price', 'pricenew', $forum['price'], 'text');
			showsetting('forums_edit_perm_viewtype', ['viewtypenew', [
				[0, cplang('forums_edit_perm_viewtype_0')],
				[1, cplang('forums_edit_perm_viewtype_1')],
			]], intval($forum['viewtype']), 'mradio');
			showsetting('forums_edit_perm_passwd', 'passwordnew', $forum['password'], 'text');
			showsetting('forums_edit_perm_users', 'formulapermusersnew', $forum['formulapermusers'], 'textarea');
			if($_G['cache']['medals']) {
				$colums = [];
				loadcache('medals');
				foreach($_G['cache']['medals'] as $medalid => $medal) {
					$colums[] = [$medalid, $medal['name']];
				}
				showtagheader('tbody', '', $_G['setting']['medalstatus']);
				showsetting('forums_edit_perm_medal', ['medalnew', $colums], $forum['medal'], 'mcheckbox');
			}
			showtagfooter('tbody');
			showtablefooter();

			if(!$multiset) {
				require_once childfile('forums/perm');
			}

			if($pluginsetting) {
				showtagfooter('div');
				showtagheader('div', 'plugin', $anchor == 'plugin');
				showtableheader('', 'noborder fixpadding');
				foreach($pluginsetting as $plugind => $setting) {
					showtitle($setting['name']);
					foreach($setting['setting'] as $varid => $var) {
						if(!empty($var['variable']) && str_starts_with($var['variable'], 'fields_')) {
							$variable = str_replace('fields_', '', $var['variable']);
							$varname = 'fieldsnew[plugin]['.$plugind.']['.$variable.']';
							$value = $forum['fields']['plugin'][$plugind][$variable] ?? '';
						} else {
							$varname = 'pluginnew['.$varid.']';
							$value = $forum['plugin'][$varid];
						}
						if($var['type'] != 'select') {
							showsetting($var['title'], $varname, $value, $var['type'], '', 0, $var['description']);
						} else {
							showsetting($var['title'], [$varname, $var['select']], $value, $var['type'], '', 0, $var['description']);
						}
					}
				}
				showtablefooter();
			}
			if($stylesetting) {
				showtagfooter('div');
				showtagheader('div', 'style', $anchor == 'style');
				showtableheader('', 'noborder fixpadding');
				foreach($stylesetting as $setting) {
					showtitle($setting['name']);
					foreach($setting['setting'] as $varid => $var) {
						if($var['type'] != 'select') {
							showsetting($var['title'], 'stylenew['.$varid.']', $forum['style'][$varid], $var['type'], '', 0, $var['description']);
						} else {
							showsetting($var['title'], ['stylenew['.$varid.']', $var['select']], $forum['style'][$varid], $var['type'], '', 0, $var['description']);
						}
					}
				}
				showtablefooter();
			}

			showtagfooter('div');

			showtableheader('', 'notop');
			showsubmit('detailsubmit', 'submit');
			showtablefooter();
			$_G['showsetting_multi']++;
		}
	}

	if($_G['showsetting_multicount'] > 1) {
		$mfids = is_array($mfids) ? $mfids : [$mfids];
		showhiddenfields(['multi' => implode(',', $mfids)]);
		showmulti();
	}

	showformfooter();

} else {

	if(!$multiset) {
		$_GET['multinew'] = [0 => ['single' => 1]];
	}
	$pluginvars = $stylevars = [];
	require_once libfile('function/delete');
	foreach($_GET['multinew'] as $k => $row) {
		if(empty($row['single'])) {
			foreach($row as $key => $value) {
				$_GET[''.$key] = $value;
			}
			$fid = $_GET['multi'][$k];
		}
		$forum = $mforum[$k];

		if(strlen($_GET['namenew']) > 50) {
			cpmsg('forums_name_toolong', '', 'error', ['frame' => $multiset]);
		}

		$domain = '';
		if(!empty($_GET['domainnew']) && !empty($_G['setting']['domain']['root']['forum'])) {
			$domain = strtolower(trim($_GET['domainnew']));
		}
		require_once libfile('function/discuzcode');
		if($_GET['type'] == 'group') {
			if($_GET['namenew']) {
				$newstyleid = intval($_GET['styleidnew']);
				$forumcolumnsnew = $_GET['forumcolumnsnew'] > 1 ? intval($_GET['forumcolumnsnew']) : 0;
				$catforumcolumnsnew = $_GET['catforumcolumnsnew'] > 1 ? intval($_GET['catforumcolumnsnew']) : 0;
				$descriptionnew = preg_replace('/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i', '', discuzcode($_GET['descriptionnew'], 1, 0, 0, 0, 1, 1, 0, 0, 1));
				if(!empty($_G['setting']['domain']['root']['forum'])) {
					deletedomain($fid, 'subarea');
					if(!empty($domain)) {
						domaincheck($domain, $_G['setting']['domain']['root']['forum'], 1, 0);
						table_common_domain::t()->insert(['domain' => $domain, 'domainroot' => $_G['setting']['domain']['root']['forum'], 'id' => $fid, 'idtype' => 'subarea']);
					}
				}
				table_forum_forum::t()->update($fid, [
					'name' => $_GET['namenew'],
					'forumcolumns' => $forumcolumnsnew,
					'catforumcolumns' => $catforumcolumnsnew,
					'domain' => $domain,
					'status' => intval($_GET['statusnew']),
					'styleid' => $newstyleid,
				]);

				require childfile('forums/perm_submit');

				$extranew = is_array($_GET['extranew']) ? $_GET['extranew'] : [];
				$extranew = serialize($extranew);
				table_forum_forumfield::t()->update($fid, [
					'extra' => $extranew,
					'description' => $descriptionnew,
					'seotitle' => $_GET['seotitlenew'],
					'keywords' => $_GET['keywordsnew'],
					'seodescription' => $_GET['seodescriptionnew'],
					'viewperm' => $_GET['viewpermnew'],
					'postperm' => $_GET['postpermnew'],
					'replyperm' => $_GET['replypermnew'],
					'getattachperm' => $_GET['getattachpermnew'],
					'postattachperm' => $_GET['postattachpermnew'],
					'postimageperm' => $_GET['postimagepermnew'],
					'formulaperm' => $_GET['formulapermnew'],
					'spviewperm' => implode("\t", is_array($_GET['spviewpermnew']) ? $_GET['spviewpermnew'] : []),
				]);
				loadcache('forums');
				$subfids = [];
				get_subfids($fid);

				if($newstyleid != $mforum[0]['styleid'] && !empty($subfids)) {
					table_forum_forum::t()->update($subfids, ['styleid' => $newstyleid]);
				}

				if(array_key_exists($fid, $navs) && !$_GET['shownavnew']) {
					table_common_nav::t()->delete($navs[$fid]);
				} elseif(!array_key_exists($fid, $navs) && $_GET['shownavnew']) {
					$data = [
						'url' => 'forum.php?mod=forumdisplay&fid='.$fid,
						'identifier' => $fid,
						'parentid' => 0,
						'name' => $_GET['namenew'],
						'displayorder' => 0,
						'subtype' => '',
						'type' => 5,
						'available' => 1,
						'navtype' => 0
					];
					table_common_nav::t()->insert($data);
				}

				updatecache(['forums', 'setting']);
				cpmsg('forums_edit_succeed', 'mod=forum&action=forums&operation=edit&fid='.$fid.($_GET['anchor'] ? "&anchor={$_GET['anchor']}" : ''), 'succeed', ['frame' => $multiset]);

			} else {
				cpmsg('forums_edit_name_invalid', '', 'error', ['frame' => $multiset]);
			}

		} else {
			$extensionarray = [];
			foreach(explode(',', $_GET['attachextensionsnew']) as $extension) {
				if($extension = trim($extension)) {
					$extensionarray[] = $extension;
				}
			}
			$_GET['attachextensionsnew'] = strtolower(implode(', ', $extensionarray));

			require_once childfile('forums/perm_submit');

			if(!$multiset) {
				if($_GET['delete']) {
					table_forum_attachtype::t()->delete_by_id_fid($_GET['delete'], $fid);
				}

				if(is_array($_GET['extension'])) {
					foreach($_GET['extension'] as $id => $val) {
						table_forum_attachtype::t()->update($id, [
							'extension' => $_GET['extension'][$id],
							'maxsize' => $_GET['maxsize'][$id] * 1024,
						]);
					}
				}

				if(is_array($_GET['newextension'])) {
					foreach($_GET['newextension'] as $key => $value) {
						if($newextension1 = trim($value)) {
							if(table_forum_attachtype::t()->count_by_extension_fid($newextension1, $fid)) {
								cpmsg('attachtypes_duplicate', '', 'error');
							}
							table_forum_attachtype::t()->insert([
								'extension' => $newextension1,
								'maxsize' => $_GET['newmaxsize'][$key] * 1024,
								'fid' => $fid
							]);
						}
					}
				}
			}

			$fupadd = '';
			$forumdata = $forumfielddata = [];
			if($_GET['fupnew'] != $forum['fup'] && !$multiset) {
				if(table_forum_forum::t()->fetch_forum_num('', $fid)) {
					cpmsg('forums_edit_sub_notnull', '', 'error', ['frame' => $multiset]);
				}

				$fup = table_forum_forum::t()->fetch($_GET['fupnew']);

				$fupadd = ", type='".($fup['type'] == 'forum' ? 'sub' : 'forum')."', fup='{$fup['fid']}'";
				$forumdata['type'] = $fup['type'] == 'forum' ? 'sub' : 'forum';
				$forumdata['fup'] = $fup['fid'];
				table_forum_moderator::t()->delete_by_fid_inherited($fid, 1);
				if($fup['inheritedmod']) {
					$query = table_forum_moderator::t()->fetch_all_by_fid($_GET['fupnew'], FALSE);
				} else {
					$query = table_forum_moderator::t()->fetch_all_by_fid_inherited($_GET['fupnew'], 1);
				}
				foreach($query as $mod) {
					table_forum_moderator::t()->insert([
						'uid' => $mod['uid'],
						'fid' => $fid,
						'displayorder' => 0,
						'inherited' => 1
					], false, true);
				}

				$moderators = '';
				$modmemberarray = table_forum_moderator::t()->fetch_all_no_inherited_by_fid($fid);
				$members = table_common_member::t()->fetch_all_username_by_uid(array_keys($modmemberarray));
				$moderators = implode("\t", $members);

				table_forum_forumfield::t()->update($fid, ['moderators' => $moderators]);
			}

			$allowpostspecialtrade = intval($_GET['allowpostspecialnew'][2]);
			$_GET['allowpostspecialnew'] = bindec(intval($_GET['allowpostspecialnew'][6]).intval($_GET['allowpostspecialnew'][5]).intval($_GET['allowpostspecialnew'][4]).intval($_GET['allowpostspecialnew'][3]).intval($_GET['allowpostspecialnew'][2]).intval($_GET['allowpostspecialnew'][1]));
			$allowspecialonlynew = $_GET['allowpostspecialnew'] || $_G['setting']['threadplugins'] && $_GET['threadpluginnew'] ? $_GET['allowspecialonlynew'] : 0;
			$forumcolumnsnew = $_GET['forumcolumnsnew'] > 1 ? intval($_GET['forumcolumnsnew']) : 0;
			$threadcachesnew = max(0, min(100, intval($_GET['threadcachesnew'])));
			$subforumsindexnew = $_GET['subforumsindexnew'] == -1 ? 0 : ($_GET['subforumsindexnew'] == 0 ? 2 : 1);
			$_GET['simplenew'] = $_GET['simplenew'] ?? 0;
			$simplenew = bindec(sprintf('%03d', decbin($_GET['defaultorderfieldnew'])).$_GET['defaultordernew'].sprintf('%02d', decbin($subforumsindexnew)).'00'.$_GET['simplenew']);
			$allowglobalsticknew = $_GET['allowglobalsticknew'] ? 1 : 0;

			if(!empty($_G['setting']['domain']['root']['forum'])) {
				deletedomain($fid, 'forum');
				if(!empty($domain)) {
					domaincheck($domain, $_G['setting']['domain']['root']['forum'], 1, 0);
					table_common_domain::t()->insert(['domain' => $domain, 'domainroot' => $_G['setting']['domain']['root']['forum'], 'id' => $fid, 'idtype' => 'forum']);
				}
			}
			$forumdata = array_merge($forumdata, [
				'status' => $_GET['statusnew'],
				'name' => $_GET['namenew'],
				'styleid' => $_GET['styleidnew'],
				'alloweditpost' => $_GET['alloweditpostnew'],
				'allowpostspecial' => $_GET['allowpostspecialnew'],
				'allowspecialonly' => $allowspecialonlynew,
				'allowhtml' => $_GET['allowhtmlnew'],
				'allowbbcode' => $_GET['allowbbcodenew'],
				'allowimgcode' => $_GET['allowimgcodenew'],
				'allowmediacode' => $_GET['allowmediacodenew'],
				'allowsmilies' => $_GET['allowsmiliesnew'],
				'alloweditrules' => $_GET['alloweditrulesnew'],
				'allowside' => $_GET['allowsidenew'],
				'disablecollect' => $_GET['disablecollectnew'],
				'modnewposts' => $_GET['modnewpostsnew'],
				'recyclebin' => $_GET['recyclebinnew'],
				'jammer' => $_GET['jammernew'],
				'allowanonymous' => $_GET['allowanonymousnew'],
				'forumcolumns' => $forumcolumnsnew,
				'catforumcolumns' => $catforumcolumnsnew,
				'threadcaches' => $threadcachesnew,
				'simple' => $simplenew,
				'allowglobalstick' => $allowglobalsticknew,
				'disablethumb' => $_GET['disablethumbnew'],
				'disablewatermark' => $_GET['disablewatermarknew'],
				'autoclose' => (!empty($_GET['autoclosenew']) && !empty($_GET['autoclosetimenew'])) ? (intval((int)$_GET['autoclosenew'] * (int)$_GET['autoclosetimenew'])) : 0,
				'allowfeed' => $_GET['allowfeednew'],
				'domain' => $domain,
				'editormode' => $_GET['editormodenew'],
			]);
			table_forum_forum::t()->update($fid, $forumdata);

			if(!(table_forum_forumfield::t()->fetch($fid))) {
				table_forum_forumfield::t()->insert(['fid' => $fid]);
			}

			if(!$multiset) {
				$creditspolicynew = [];
				$creditspolicy = $forum['creditspolicy'] ? dunserialize($forum['creditspolicy']) : [];
				foreach($_GET['creditnew'] as $rid => $rule) {
					$creditspolicynew[$rules[$rid]['action']] = $creditspolicy[$rules[$rid]['action']] ?? $rules[$rid];
					$usedefault = !$_GET['usecustom'][$rid];

					if(!$usedefault) {
						foreach($rule as $i => $v) {
							$creditspolicynew[$rules[$rid]['action']]['extcredits'.$i] = is_numeric($v) ? intval($v) : 0;
						}
					}

					$cpfids = explode(',', $rules[$rid]['fids']);
					$cpfidsnew = [];
					foreach($cpfids as $cpfid) {
						if(!$cpfid) {
							continue;
						}
						if($cpfid != $fid) {
							$cpfidsnew[] = $cpfid;
						}
					}
					if(!$usedefault) {
						$cpfidsnew[] = $fid;
						$creditspolicynew[$rules[$rid]['action']]['fids'] = $rules[$rid]['fids'] = implode(',', $cpfidsnew);
					} else {
						$rules[$rid]['fids'] = implode(',', $cpfidsnew);
						unset($creditspolicynew[$rules[$rid]['action']]);
					}
					table_common_credit_rule::t()->update($rid, ['fids' => $rules[$rid]['fids']]);
				}
				$forumfielddata = [];
				$forumfielddata['creditspolicy'] = serialize($creditspolicynew);

				$threadtypesnew = $_GET['threadtypesnew'];
				$threadtypesnew['types'] = $threadtypes['special'] = $threadtypes['show'] = [];
				$threadsortsnew['types'] = $threadsorts['special'] = $threadsorts['show'] = [];

				if($allowthreadtypes) {
					if(is_array($_GET['newname']) && $_GET['newname']) {
						$newname = array_unique($_GET['newname']);
						if($newname) {
							foreach($newname as $key => $val) {
								$newname[$key] = $val = strip_tags(trim(str_replace(["'", "\""], [], $val)), '<font><span><b><strong>');
								if($_GET['newenable'][$key] && $val) {
									$newtypearr = table_forum_threadclass::t()->fetch_by_fid_name($fid, $val);
									$newtypeid = $newtypearr['typeid'];
									if(!$newtypeid) {
										$threadtypes_newdisplayorder = intval($_GET['newdisplayorder'][$key]);
										$threadtypes_newicon = trim($_GET['newicon'][$key]);
										$newtypeid = table_forum_threadclass::t()->insert(['fid' => $fid, 'name' => $val, 'displayorder' => $threadtypes_newdisplayorder, 'icon' => $threadtypes_newicon, 'moderators' => intval($_GET['newmoderators'][$key])], true);
									} else {
										$threadtypes_newicon = $newtypearr['icon'];// 已存在的分类,使用原来属性
										$threadtypes_newdisplayorder = $newtypearr['displayorder'];
										$_GET['newmoderators'][$key] = $newtypearr['moderators'];
									}
									$threadtypesnew['options']['name'][$newtypeid] = $val;
									$threadtypesnew['options']['icon'][$newtypeid] = $threadtypes_newicon;
									$threadtypesnew['options']['displayorder'][$newtypeid] = $threadtypes_newdisplayorder;
									$threadtypesnew['options']['enable'][$newtypeid] = 1;
									$threadtypesnew['options']['moderators'][$newtypeid] = $_GET['newmoderators'][$key];
								}
							}
						}
						$threadtypesnew['status'] = 1;
					} else {
						$newname = [];
					}
					if($threadtypesnew['status']) {
						if(is_array($threadtypesnew['options']) && $threadtypesnew['options']) {
							if(!empty($threadtypesnew['options']['enable'])) {
								$typeids = array_keys($threadtypesnew['options']['enable']);
							} else {
								$typeids = [0];
							}
							foreach(table_forum_threadclass::t()->fetch_all_by_typeid($typeids) as $type) {
								if($threadtypesnew['options']['name'][$type['typeid']] != $type['name'] ||
									$threadtypesnew['options']['displayorder'][$type['typeid']] != $type['displayorder'] ||
									$threadtypesnew['options']['icon'][$type['typeid']] != $type['icon'] ||
									$threadtypesnew['options']['moderators'][$type['typeid']] != $type['moderators']) {
									$threadtypesnew['options']['name'][$type['typeid']] = strip_tags(trim(str_replace(["'", "\""], [], $threadtypesnew['options']['name'][$type['typeid']])), '<font><span><b><strong>');
									table_forum_threadclass::t()->update_by_typeid($type['typeid'], [
										'name' => $threadtypesnew['options']['name'][$type['typeid']],
										'displayorder' => $threadtypesnew['options']['displayorder'][$type['typeid']],
										'icon' => $threadtypesnew['options']['icon'][$type['typeid']],
										'moderators' => $threadtypesnew['options']['moderators'][$type['typeid']],
									]);
								}
							}
							if(!empty($threadtypesnew['options']['delete'])) {
								table_forum_threadclass::t()->delete_by_typeid($threadtypesnew['options']['delete']);
							}
						}
					} else {
						$threadtypesnew = '';
					}
					if($threadtypesnew && $typeids) {
						foreach(table_forum_threadclass::t()->fetch_all_by_typeid($typeids) as $type) {
							if($threadtypesnew['options']['enable'][$type['typeid']]) {
								$threadtypesnew['types'][$type['typeid']] = $threadtypesnew['options']['name'][$type['typeid']];
							}
							$threadtypesnew['icons'][$type['typeid']] = trim($threadtypesnew['options']['icon'][$type['typeid']]);
							$threadtypesnew['moderators'][$type['typeid']] = $threadtypesnew['options']['moderators'][$type['typeid']];
						}
						$threadtypesnew = $threadtypesnew['types'] ? serialize(
							[
								'required' => (bool)$threadtypesnew['required'],
								'listable' => (bool)$threadtypesnew['listable'],
								'prefix' => $threadtypesnew['prefix'],
								'types' => $threadtypesnew['types'],
								'icons' => $threadtypesnew['icons'],
								'moderators' => $threadtypesnew['moderators'],
							]) : '';
					}
					$forumfielddata['threadtypes'] = is_array($threadtypesnew) ? serialize($threadtypesnew) : $threadtypesnew;

					$threadsortsnew = $_GET['threadsortsnew'];
					if(!empty($threadsortsnew['suptypeid'])) {
						$threadsortsnew['required'] = true;
						$threadsortsnew['options']['enable'][$threadsortsnew['suptypeid']] = 1;
						$threadsortsnew['status'] = 1;
						$threadsortsnew['default'] = 1;
						$threadsortsnew['defaultshow'] = $threadsortsnew['suptypeid'];
					}
					if($threadsortsnew['status']) {
						if(is_array($threadsortsnew['options']) && $threadsortsnew['options']) {
							if(!empty($threadsortsnew['options']['enable'])) {
								$sortids = array_keys($threadsortsnew['options']['enable']);
							} else {
								$sortids = [];
							}

							$query = table_forum_threadtype::t()->fetch_all_for_order($sortids);
							foreach($query as $sort) {
								if($threadsortsnew['options']['enable'][$sort['typeid']]) {
									$threadsortsnew['types'][$sort['typeid']] = $sort['name'];
								}
								$threadsortsnew['expiration'][$sort['typeid']] = $sort['expiration'];
								$threadsortsnew['description'][$sort['typeid']] = $sort['description'];
								$threadsortsnew['show'][$sort['typeid']] = $threadsortsnew['options']['show'][$sort['typeid']] ? 1 : 0;
							}
						}

						if($threadsortsnew['default'] && !$threadsortsnew['defaultshow']) {
							cpmsg('forums_edit_threadsort_nonexistence', '', 'error', ['frame' => $multiset]);
						}

						$threadsortsnew = $threadsortsnew['types'] ? serialize(
							[
								'required' => (bool)$threadsortsnew['required'],
								'prefix' => (bool)$threadsortsnew['prefix'],
								'types' => $threadsortsnew['types'],
								'show' => $threadsortsnew['show'],
								'expiration' => $threadsortsnew['expiration'],
								'description' => $threadsortsnew['description'],
								'defaultshow' => $threadsortsnew['default'] ? $threadsortsnew['defaultshow'] : '',
								'suptypeid' => $threadsortsnew['suptypeid'] ? $threadsortsnew['suptypeid'] : 0,
							]) : '';
					} else {
						$threadsortsnew = '';
					}

					$forumfielddata['threadsorts'] = $threadsortsnew;

				}
			}

			$threadpluginnew = serialize($_GET['threadpluginnew']);
			$modrecommendnew = $_GET['modrecommendnew'];
			$modrecommendnew['num'] = $modrecommendnew['num'] ? intval($modrecommendnew['num']) : 10;
			$modrecommendnew['cachelife'] = intval($modrecommendnew['cachelife']);
			$modrecommendnew['maxlength'] = $modrecommendnew['maxlength'] ? intval($modrecommendnew['maxlength']) : 0;
			$modrecommendnew['dateline'] = $modrecommendnew['dateline'] ? intval($modrecommendnew['dateline']) : 0;
			$modrecommendnew['imagenum'] = $modrecommendnew['imagenum'] ? intval($modrecommendnew['imagenum']) : 0;
			$modrecommendnew['imagewidth'] = $modrecommendnew['imagewidth'] ? intval($modrecommendnew['imagewidth']) : 300;
			$modrecommendnew['imageheight'] = $modrecommendnew['imageheight'] ? intval($modrecommendnew['imageheight']) : 250;
			$descriptionnew = preg_replace('/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i', '', discuzcode($_GET['descriptionnew'], 1, 0, 0, 0, 1, 1, 0, 0, 1));
			$rulesnew = preg_replace('/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i', '', discuzcode($_GET['rulesnew'], 1, 0, 0, 0, 1, 1, 0, 0, 1));
			$extranew = is_array($_GET['extranew']) ? $_GET['extranew'] : [];
			$forum['extra'] = dunserialize($forum['extra']);
			$forum['extra']['namecolor'] = $extranew['namecolor'];

			if(!$multiset) {
				if(($_GET['deletebanner'] || $_FILES['bannernew']) && $forum['banner']) {
					$valueparse = parse_url($forum['banner']);
					if(!isset($valueparse['host'])) {
						@unlink($_G['setting']['attachurl'].'common/'.$forum['banner']);
						ftpcmd('delete', 'common/'.$forum['banner']);
					}
					$forumfielddata['banner'] = '';
					if($_GET['bannernew'] == $forum['banner']) {
						$_GET['bannernew'] = '';
					}
				}
				if($_FILES['bannernew']) {
					$bannernew = upload_icon_banner($forum, $_FILES['bannernew'], 'banner');
				} else {
					$bannernew = $_GET['bannernew'];
				}
				if($bannernew) {
					$forumfielddata['banner'] = $bannernew;
				}

				if($_GET['deleteicon'] || $_FILES['iconnew']) {
					$valueparse = parse_url($forum['icon']);
					if(!isset($valueparse['host'])) {
						@unlink($_G['setting']['attachurl'].'common/'.$forum['icon']);
						ftpcmd('delete', 'common/'.$forum['icon']);
					}
					$forumfielddata['icon'] = '';
					$forum['extra']['iconwidth'] = '';
					if($_GET['iconnew'] == $forum['icon']) {
						$_GET['iconnew'] = '';
					}
				}
				if($_FILES['iconnew']) {
					$iconnew = upload_icon_banner($forum, $_FILES['iconnew'], 'icon');
				} else {
					$iconnew = $_GET['iconnew'];
				}
				if($iconnew) {
					$forumfielddata['icon'] = $iconnew;
					if(!$extranew['iconwidth']) {
						$valueparse = parse_url($forumfielddata['icon']);
						if(!isset($valueparse['host'])) {
							$iconnew = $_G['setting']['attachurl'].'common/'.$forumfielddata['icon'];
						}
						if($info = @getimagesize($iconnew)) {
							$extranew['iconwidth'] = $info[0];
						}
					}
					$forum['extra']['iconwidth'] = $extranew['iconwidth'];
				} else {
					$forum['extra']['iconwidth'] = '';
				}
			}

			foreach($perms as $perm) {
				if(in_array($perm, $sysperms)) {
					continue;
				}
				$forum['extra']['perms'][$perm] = $_GET[$perm.'new'] ?? '';
			}
			$extranew = serialize($forum['extra']);

			$forumfielddata = array_merge($forumfielddata, [
				'description' => $descriptionnew,
				'password' => $_GET['passwordnew'],
				'redirect' => $_GET['redirectnew'],
				'rules' => $rulesnew,
				'attachextensions' => $_GET['attachextensionsnew'],
				'modrecommend' => $modrecommendnew && is_array($modrecommendnew) ? serialize($modrecommendnew) : '',
				'seotitle' => $_GET['seotitlenew'],
				'keywords' => $_GET['keywordsnew'],
				'seodescription' => $_GET['seodescriptionnew'],
				'threadplugin' => $threadpluginnew,
				'extra' => $extranew,
				'commentitem' => $_GET['commentitemnew'],
				'formulaperm' => $_GET['formulapermnew'],
				'picstyle' => $_GET['picstylenew'],
				'widthauto' => $_GET['widthautonew'],
				'noantitheft' => intval($_GET['noantitheftnew']),
				'noforumhidewater' => intval($_GET['noforumhidewaternew']),
				'noforumrecommend' => intval($_GET['noforumrecommendnew']),
				'price' => intval($_GET['pricenew']),
				'jointype' => !empty($_GET['viewtypenew']) ? 2 : 0,
				'fields' => !empty($_GET['fieldsnew']) ? json_encode($_GET['fieldsnew']) : '{}',
			]);
			if(!$multiset) {

				if($_GET['delreplybg']) {
					$valueparse = parse_url($forum['replybg']);
					if(!isset($valueparse['host']) && file_exists($_G['setting']['attachurl'].'common/'.$forum['replybg'])) {
						@unlink($_G['setting']['attachurl'].'common/'.$forum['replybg']);
						ftpcmd('delete', 'common/'.$forum['replybg']);
					}
					$_GET['replybgnew'] = '';
				}
				if($_FILES['replybgnew']) {
					$data = ['fid' => "$fid"];
					$replybgnew = upload_icon_banner($data, $_FILES['replybgnew'], 'replybg');
				} else {
					$replybgnew = $_GET['replybgnew'];
				}

				$forumfielddata = array_merge($forumfielddata, [
					'viewperm' => $_GET['viewpermnew'],
					'postperm' => $_GET['postpermnew'],
					'replyperm' => $_GET['replypermnew'],
					'getattachperm' => $_GET['getattachpermnew'],
					'postattachperm' => $_GET['postattachpermnew'],
					'postimageperm' => $_GET['postimagepermnew'],
					'relatedgroup' => $_GET['relatedgroupnew'],
					'spviewperm' => implode("\t", is_array($_GET['spviewpermnew']) ? $_GET['spviewpermnew'] : []),
					'replybg' => $replybgnew
				]);
			}
			if($forumfielddata) {
				table_forum_forumfield::t()->update($fid, $forumfielddata);
			}
			if($pluginsetting) {
				foreach($_GET['pluginnew'] as $pluginvarid => $value) {
					$pluginvars[$pluginvarid][$fid] = $value;
				}
			}
			if($stylesetting) {
				foreach($_GET['stylenew'] as $stylevarid => $value) {
					$stylevars[$stylevarid][$fid] = $value;
				}
			}

			if($modrecommendnew && !$modrecommendnew['sort']) {
				require_once libfile('function/forumlist');
				recommendupdate($fid, $modrecommendnew, '1');
			}

			if($forumkeys[$fid] != $_GET['keysnew'] && preg_match('/^\w*$/', $_GET['keysnew']) && !preg_match('/^\d+$/', $_GET['keysnew'])) {
				$forumkeys[$fid] = $_GET['keysnew'];
				table_common_setting::t()->update_setting('forumkeys', $forumkeys);
			}

		}
		if(array_key_exists($fid, $navs) && !$_GET['shownavnew']) {
			table_common_nav::t()->delete($navs[$fid]);
		} elseif(!array_key_exists($fid, $navs) && $_GET['shownavnew']) {
			$data = [
				'url' => 'forum.php?mod=forumdisplay&fid='.$fid,
				'identifier' => $fid,
				'parentid' => 0,
				'name' => $_GET['namenew'],
				'displayorder' => 0,
				'subtype' => '',
				'type' => 5,
				'available' => 1,
				'navtype' => 0
			];
			table_common_nav::t()->insert($data);
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

	updatecache(['forums', 'setting', 'creditrule', 'attachtype']);
	cpmsg('forums_edit_succeed', 'mod=forum&action=forums&operation=edit&'.($multiset ? 'multi='.implode(',', $_GET['multi']) : "fid=$fid").($_GET['anchor'] ? "&anchor={$_GET['anchor']}" : ''), 'succeed', ['frame' => $multiset]);

}
	