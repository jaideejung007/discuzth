<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$extra = (!empty($_GET['fid']) ? '&fid='.$_GET['fid'] : '').(!empty($_GET['page']) ? '&page='.$_GET['page'] : '').(isset($_GET['kw']) && $_GET['kw'] !== '' ? '&kw='.urlencode($_GET['kw']) : '');

if(!submitcheck('editsubmit')) {
	$vfid = !empty($_GET['fid']) ? $_GET['fid'] : 0;
	$kw = $_GET['kw'] ?? false;
	shownav('forum', 'forums_admin');
	showsubmenu('forums_admin');
	showtips('forums_admin_tips');

	require_once libfile('function/forumlist');
	$forums = str_replace("'", "\'", forumselect(false, 0, 0, 1));

	?>
	<script type="text/JavaScript">
		var forumselect = '<?php echo $forums;?>';
		var rowtypedata = [
			[[1, ''], [1, '<input type="text" class="txt" name="newcatorder[]" value="0" />', 'td25'], [5, '<div><input name="newcat[]" value="<?php cplang('forums_admin_add_category_name', null, true);?>" size="20" type="text" class="txt" /><a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true);?></a></div>']],
			[[1, ''], [1, '<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [5, '<div class="board"><input name="newforum[{1}][]" value="<?php cplang('forums_admin_add_forum_name', null, true);?>" size="20" type="text" class="txt" /><a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true);?></a><select name="newinherited[{1}][]"><option value=""><?php cplang('forums_edit_newinherited', null, true);?></option>' + forumselect + '</select></div>']],
			[[1, ''], [1, '<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [5, '<div class="childboard"><input name="newforum[{1}][]" value="<?php cplang('forums_admin_add_forum_name', null, true);?>" size="20" type="text" class="txt" /><a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true);?></a>&nbsp;<label><input name="inherited[{1}][]" type="checkbox" class="checkbox" value="1">&nbsp;<?php cplang('forums_edit_inherited', null, true);?></label></div>']],
		];
	</script>
	<?php

	$max_rows = 10;
	$ppp = 20;
	$toggle_max = 100;

	$forumcount = table_forum_forum::t()->fetch_forum_num();

	$query = table_forum_forum::t()->fetch_all_forum_for_sub_order();
	$groups = $forums = $subs = $fids = $showed = $fnames = $vSubs = [];
	foreach($query as $forum) {
		$inSearch = $kw !== false && str_contains($forum['name'], $kw);
		if($forum['type'] == 'group') {
			$fnames[$forum['fid']] = $forum['name'];
			$groups[$forum['fid']] = $forum;
			if($vfid == $forum['fid']) {
				$vForum = $forum;
			}
			if(!$vfid && $inSearch) {
				$vSubs[] = $forum;
			}
		} elseif($forum['type'] == 'sub') {
			$subs[$forum['fup']][$forum['fid']] = $forum;
			if($vfid == $forum['fup']) {
				if($kw === false || $inSearch) {
					$vSubs[] = $forum;
				}
			} elseif($inSearch) {
				$vSubs[] = $forum;
			}
		} else {
			$fnames[$forum['fid']] = $groups[$forum['fup']]['name'].' &raquo; '.$forum['name'];
			$forums[$forum['fup']][$forum['fid']] = $forum;
			if($vfid == $forum['fid']) {
				$vForum = $forum;
			}
			if($vfid == $forum['fup']) {
				if($kw === false || $inSearch) {
					$vSubs[] = $forum;
				}
			} elseif(!$vfid && $inSearch) {
				$vSubs[] = $forum;
			}
		}
		$fids[] = $forum['fid'];
	}

	$searchStr = '<div style="float:right"><input type="text" id="srchforumipt" name="kw" value="'.dhtmlspecialchars($kw).'" class="txt" /> <input type="submit" class="btn" onclick="this.form.page.value = 0" value="'.cplang('search').'" /></div>';

	if($vfid || $kw !== false) {

		echo '
<style>
.tb2 tr:first-child th, .tb2 tr:first-child td { border-top: 1px dotted #DEEFFB; }
.st-d .tb2 tr:first-child th, .st-d .tb2 tr:first-child td { border-top: 1px dotted #282828; }
</style>';

		$page = max(1, $_GET['page']);
		$start = ($page - 1) * $ppp;

		$vSubsCurrent = array_slice($vSubs, $start, $ppp);

		showformheader('forums'.$extra);
		$pathStr = '<a href="'.ADMINSCRIPT.'?action=forums">'.cplang('home').'</a> &raquo; ';
		if($kw !== false) {
			$pathStr .= cplang('search');
		} else {
			$pathStr .= ($vForum['type'] == 'forum' ? '<a href="'.ADMINSCRIPT.'?action=forums&fid='.$vForum['fup'].'">'.$groups[$vForum['fup']]['name'].'</a> &raquo; ' : '').$vForum['name'];
		}
		echo '<div class="forumheader">'.$searchStr.'<b>'.$pathStr.'</b></div>';
		showtableheader();
		showsubtitle(['', 'display_order', 'forums_admin_name', '', 'forums_moderators', '<a href="javascript:;" onclick="if(getmultiids()) window.open(\''.ADMINSCRIPT.'?action=forums&operation=edit&multi=\' + getmultiids());return false;">'.$lang['multiedit'].'</a>']);

		$forums = $fidShowed = [];
		foreach($vSubsCurrent as $forum) {
			if($kw && !isset($fidShowed[$forum['fup']]) && !empty($fnames[$forum['fup']])) {
				echo '<tr class="hover"><td class="td25"></td><td class="td25"></td><td colspan="4">'.$fnames[$forum['fup']].'</td></tr>';
			}
			$fidShowed[$forum['fup']] = $forum['fup'];
			if($forum['type'] == 'group') {
				$type = 'cat';
			} elseif($forum['type'] == 'forum') {
				$type = '';
			} else {
				$type = 'sub';
			}
			$showed[] = showforum($forum, $type);
			if(isset($subs[$forum['fid']])) {
				$fid_count = count($subs[$forum['fid']]);
				if($fid_count > $max_rows) {
					foreach($subs[$forum['fid']] as $sub) {
						$f = ['fid' => $forum['fid']];
						$showed[] = showforum($f, 'sub', '', false, $fid_count);
						break;
					}
				} else {
					foreach($subs[$forum['fid']] as $sub) {
						$showed[] = showforum($sub, 'sub');
						$lastfid = $sub['fid'];
					}
					showforum($forum, $lastfid, 'lastchildboard');
				}
			}
		}

		if($kw === false) {
			showforum($vForum, '', $vForum['type'] == 'group' ? 'lastboard' : 'addchildboard');
		}
		echo '<tr><td></td><td colspan="5">'.multi(count($vSubs), $ppp, $page, ADMINSCRIPT.'?action=forums&fid='.$vfid.(isset($_GET['kw']) && $_GET['kw'] !== '' ? '&kw='.urlencode($_GET['kw']) : '')).'</td></tr>';

		showhiddenfields(['fid' => $vfid, 'page' => $page]);
	} else {
		showformheader('forums');
		echo '<div class="forumheader">'.$searchStr.'<a href="javascript:;" onclick="show_all()">'.cplang('show_all').'</a> | <a href="javascript:;" onclick="hide_all()">'.cplang('hide_all').'</a></div>';
		showtableheader();
		showsubtitle(['', 'display_order', 'forums_admin_name', '', 'forums_moderators', '<a href="javascript:;" onclick="if(getmultiids()) window.open(\''.ADMINSCRIPT.'?action=forums&operation=edit&multi=\' + getmultiids());return false;">'.$lang['multiedit'].'</a>']);

		foreach($groups as $id => $gforum) {
			$toggle = $forumcount > $toggle_max && isset($forums[$id]) && is_array($forums[$id]) && count($forums[$id]) > 2;
			$showed[] = showforum($gforum, 'group', '', $toggle);
			if(!empty($forums[$id])) {
				$fid_count = count($forums[$id]);
				if($fid_count > $max_rows) {
					foreach($forums[$id] as $forum) {
						$f = ['fid' => $forum['fup']];
						$showed[] = showforum($f, '', '', false, $fid_count);
						break;
					}
				} else {
					foreach($forums[$id] as $forum) {
						$showed[] = showforum($forum);
						$lastfid = 0;
						if(!empty($subs[$forum['fid']])) {
							$fid_count = count($subs[$forum['fid']]);
							if($fid_count > $max_rows) {
								$first = array_shift($subs[$forum['fid']]);
								$f = ['fid' => $first['fup']];
								$showed[] = showforum($f, 'sub', '', false, $fid_count);
								$lastfid = $f['fid'];
							} else {
								foreach($subs[$forum['fid']] as $sub) {
									$showed[] = showforum($sub, 'sub');
									$lastfid = $sub['fid'];
								}
							}
						}
						showforum($forum, $lastfid, 'lastchildboard');
					}
				}
			}
			showforum($gforum, '', 'lastboard');
		}

		if(count($fids) != count($showed)) {
			foreach($fids as $fid) {
				if(!in_array($fid, $showed)) {
					//	table_forum_forum::t()->update($fid, array('fup' => '0', 'type' => 'forum'));
				}
			}
		}

		showforum($gforum, '', 'last');
	}

	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

} else {
	$usergroups = [];
	$query = table_common_usergroup::t()->range();
	foreach($query as $group) {
		$usergroups[$group['groupid']] = $group;
	}

	if(is_array($_GET['order'])) {
		foreach($_GET['order'] as $fid => $value) {
			table_forum_forum::t()->update($fid, ['name' => $_GET['name'][$fid], 'displayorder' => $_GET['order'][$fid]]);
		}
	}

	if(is_array($_GET['newcat'])) {
		foreach($_GET['newcat'] as $key => $forumname) {
			if(empty($forumname)) {
				continue;
			}
			$fid = table_forum_forum::t()->insert(['type' => 'group', 'name' => $forumname, 'status' => 1, 'displayorder' => $_GET['newcatorder'][$key]], 1);
			table_forum_forumfield::t()->insert(['fid' => $fid]);
		}
	}

	$table_forum_columns = ['fup', 'type', 'name', 'status', 'displayorder', 'styleid', 'allowsmilies',
		'allowhtml', 'allowbbcode', 'allowimgcode', 'allowanonymous', 'allowpostspecial', 'alloweditrules',
		'alloweditpost', 'modnewposts', 'recyclebin', 'jammer', 'forumcolumns', 'threadcaches', 'disablewatermark', 'disablethumb',
		'autoclose', 'simple', 'allowside', 'allowfeed'];
	$table_forumfield_columns = ['fid', 'attachextensions', 'threadtypes', 'viewperm', 'postperm', 'replyperm',
		'getattachperm', 'postattachperm', 'postimageperm'];

	if(is_array($_GET['newforum'])) {

		foreach($_GET['newforum'] as $fup => $forums) {

			$fupforum = table_forum_forum::t()->get_forum_by_fid($fup);
			if(empty($fupforum)) continue;

			if($fupforum['fup']) {
				$groupforum = table_forum_forum::t()->get_forum_by_fid($fupforum['fup']);
			} else {
				$groupforum = $fupforum;
			}

			foreach($forums as $key => $forumname) {

				if(empty($forumname) || strlen($forumname) > 50) continue;

				$forum = $forumfields = [];
				$inheritedid = !empty($_GET['inherited'][$fup]) ? $fup : (!empty($_GET['newinherited'][$fup][$key]) ? $_GET['newinherited'][$fup][$key] : '');

				if(!empty($inheritedid)) {

					$forum = table_forum_forum::t()->get_forum_by_fid($inheritedid);
					$forumfield = table_forum_forum::t()->get_forum_by_fid($inheritedid, null, 'forumfield');

					foreach($table_forum_columns as $field) {
						$forumfields[$field] = $forum[$field];
					}

					foreach($table_forumfield_columns as $field) {
						$forumfields[$field] = $forumfield[$field];
					}

				} else {
					$forumfields['allowsmilies'] = $forumfields['allowbbcode'] = $forumfields['allowimgcode'] = 1;
					$forumfields['allowpostspecial'] = 1;
					$forumfields['allowside'] = 0;
					$forumfields['allowfeed'] = 0;
					$forumfields['recyclebin'] = 1;
				}

				$forumfields['fup'] = $fup ? $fup : 0;
				$forumfields['type'] = $fupforum['type'] == 'forum' ? 'sub' : 'forum';
				$forumfields['styleid'] = $groupforum['styleid'];
				$forumfields['name'] = $forumname;
				$forumfields['status'] = 1;
				$forumfields['displayorder'] = $_GET['neworder'][$fup][$key];

				$data = [];
				foreach($table_forum_columns as $field) {
					if(isset($forumfields[$field])) {
						$data[$field] = $forumfields[$field];
					}
				}

				$forumfields['fid'] = $fid = table_forum_forum::t()->insert($data, 1);

				$data = [];
				$forumfields['threadtypes'] = copy_threadclasses($forumfields['threadtypes'], $fid);
				foreach($table_forumfield_columns as $field) {
					if(isset($forumfields[$field])) {
						$data[$field] = $forumfields[$field];
					}
				}

				table_forum_forumfield::t()->insert($data);

				foreach(table_forum_moderator::t()->fetch_all_by_fid($fup, false) as $mod) {
					if($mod['inherited'] || $fupforum['inheritedmod']) {
						table_forum_moderator::t()->insert(['uid' => $mod['uid'], 'fid' => $fid, 'inherited' => 1], false, true);
					}
				}
			}
		}
	}


	updatecache('forums');

	cpmsg('forums_update_succeed', 'action=forums'.$extra, 'succeed');
}
	