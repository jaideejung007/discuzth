<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('searchsubmit', 1) && empty($_GET['search'])) {
	$newlist = 1;
	$_GET['intype'] = '';
	$_GET['detail'] = 1;
	$_GET['inforum'] = 'all';
	$_GET['starttime'] = dgmdate(TIMESTAMP - 86400 * 30, 'Y-n-j');
}
$intypes = '';
if($_GET['inforum'] && $_GET['inforum'] != 'all' && $_GET['intype']) {
	$foruminfo = table_forum_forumfield::t()->fetch($_GET['inforum']);
	$forumthreadtype = $foruminfo['threadtypes'];
	if($forumthreadtype) {
		$forumthreadtype = dunserialize($forumthreadtype);
		foreach($forumthreadtype['types'] as $typeid => $typename) {
			$intypes .= '<option value="'.$typeid.'"'.($typeid == $_GET['intype'] ? ' selected' : '').'>'.$typename.'</option>';
		}
	}
}
require_once libfile('function/forumlist');
$forumselect = '<b>'.$lang['threads_search_forum'].':</b><br><br><select name="inforum" onchange="ajaxget(\'forum.php?mod=ajax&action=getthreadtypes&selectname=intype&fid=\' + this.value, \'forumthreadtype\')"><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option><option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';
$typeselect = $lang['threads_move_type'].' <span id="forumthreadtype"><select name="intype"><option value=""></option>'.$intypes.'</select></span>';
if(isset($_GET['inforum'])) {
	$forumselect = preg_replace("/(\<option value=\"{$_GET['inforum']}\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
}

$sortselect = '';
$query = table_forum_threadtype::t()->fetch_all_for_order();
foreach($query as $type) {
	if($type['special']) {
		$sortselect .= '<option value="'.$type['typeid'].'">&nbsp;&nbsp;> '.$type['name'].'</option>';
	}
}

if(isset($_GET['insort'])) {
	$sortselect = preg_replace("/(\<option value=\"{$_GET['insort']}\")(\>)/", "\\1 selected=\"selected\" \\2", $sortselect);
}

$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
	function page(number) {
		$('threadforum').page.value=number;
		$('threadforum').searchsubmit.click();
	}
</script>
EOT;
shownav('topic', 'nav_maint_threads'.($operation ? '_'.$operation : ''));
showsubmenusteps('nav_maint_threads'.($operation ? '_'.$operation : ''), empty($newlist) ? [
	['threads_search', !$_GET['searchsubmit']],
	['nav_maint_threads', $_GET['searchsubmit']]
] : '', '', [
	['newlist', 'threads'.($operation ? '&operation='.$operation : ''), !empty($newlist)],
	['search', 'threads'.($operation ? '&operation='.$operation : '').'&search=true', empty($newlist)],
]);
/*search={"nav_maint_threads":"action=threads","newlist":"action=threads"}*/
if(empty($newlist)) {
	$search_tips = 1;
	showtips('threads_tips');
}
/*search*/
/*search={"nav_maint_threads":"action=threads","search":"action=threads&search=true"}*/
showtagheader('div', 'threadsearch', !submitcheck('searchsubmit', 1) && empty($newlist));
showformheader('threads'.($operation ? '&operation='.$operation : ''), '', 'threadforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('threads_search_detail', 'detail', $_GET['detail'], 'radio');
if($operation != 'group') {
	showtablerow('', ['class="rowform" colspan="2" style="width:auto;"'], [$forumselect.$typeselect]);
}
showsetting('threads_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
if(!$fromumanage && !submitcheck('searchsubmit', 1)) {
	empty($_GET['starttime']) && $_GET['starttime'] = date('Y-m-d', time() - 86400 * 30);
}
echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
showsetting('threads_search_time', ['starttime', 'endtime'], [$_GET['starttime'], $_GET['endtime']], 'daterange');
showsetting('threads_search_user', 'users', $_GET['users'], 'text');
showsetting('threads_search_keyword', 'keywords', $_GET['keywords'], 'text');

showtagheader('tbody', 'advanceoption');
showsetting('threads_search_sort', '', '', '<select name="insort"><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option><option value="">&nbsp;</option><option value="0">&nbsp;&nbsp;> '.$lang['threads_search_type_none'].'</option>'.$sortselect.'</select>');
showsetting('threads_search_viewrange', ['viewsmore', 'viewsless'], [$_GET['viewsmore'], $_GET['viewsless']], 'range');
showsetting('threads_search_replyrange', ['repliesmore', 'repliesless'], [$_GET['repliesmore'], $_GET['repliesless']], 'range');
showsetting('threads_search_readpermmore', 'readpermmore', $_GET['readpermmore'], 'text');
showsetting('threads_search_pricemore', 'pricemore', $_GET['pricemore'], 'text');
showsetting('threads_search_noreplyday', 'noreplydays', $_GET['noreplydays'], 'text');
showsetting('threads_search_type', ['specialthread', [
	[0, cplang('unlimited'), ['showspecial' => 'none']],
	[1, cplang('threads_search_include_yes'), ['showspecial' => '']],
	[2, cplang('threads_search_include_no'), ['showspecial' => '']],
], TRUE], $_GET['specialthread'], 'mradio');
showtablerow('id="showspecial" style="display:'.($_GET['specialthread'] ? '' : 'none').'"', 'class="sub" colspan="2"', mcheckbox('special', [
	1 => cplang('thread_poll'),
	2 => cplang('thread_trade'),
	3 => cplang('thread_reward'),
	4 => cplang('thread_activity'),
	5 => cplang('thread_debate')
], $_GET['special'] ? $_GET['special'] : [0]));
showsetting('threads_search_sticky', ['sticky', [
	[0, cplang('unlimited')],
	[1, cplang('threads_search_include_yes')],
	[2, cplang('threads_search_include_no')],
], TRUE], $_GET['sticky'], 'mradio');
showsetting('threads_search_digest', ['digest', [
	[0, cplang('unlimited')],
	[1, cplang('threads_search_include_yes')],
	[2, cplang('threads_search_include_no')],
], TRUE], $_GET['digest'], 'mradio');
showsetting('threads_search_attach', ['attach', [
	[0, cplang('unlimited')],
	[1, cplang('threads_search_include_yes')],
	[2, cplang('threads_search_include_no')],
], TRUE], $_GET['attach'], 'mradio');
showsetting('threads_rate', ['rate', [
	[0, cplang('unlimited')],
	[1, cplang('threads_search_include_yes')],
	[2, cplang('threads_search_include_no')],
], TRUE], $_GET['rate'], 'mradio');
showsetting('threads_highlight', ['highlight', [
	[0, cplang('unlimited')],
	[1, cplang('threads_search_include_yes')],
	[2, cplang('threads_search_include_no')],
], TRUE], $_GET['highlight'], 'mradio');
showsetting('threads_save', 'savethread', $_GET['savethread'], 'radio');
if($operation != 'group') {
	showsetting('threads_hide', 'hidethread', $_GET['hidethread'], 'radio');
}
showtagfooter('tbody');

showsubmit('searchsubmit', 'submit', '', 'more_options');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
if(submitcheck('searchsubmit', 1) || $newlist) {
	$operation == 'group' && $_GET['inforum'] = 'isgroup';

	$conditions['inforum'] = $_GET['inforum'] != '' && $_GET['inforum'] != 'all' && $_GET['inforum'] != 'isgroup' ? $_GET['inforum'] : '';
	$conditions['isgroup'] = $_GET['inforum'] != '' && $_GET['inforum'] == 'isgroup' ? 1 : 0;
	$conditions['intype'] = $_GET['intype'] !== '' ? $_GET['intype'] : '';
	$conditions['insort'] = $_GET['insort'] != '' && $_GET['insort'] != 'all' ? $_GET['insort'] : '';
	$conditions['viewsless'] = $_GET['viewsless'] != '' ? $_GET['viewsless'] : '';
	$conditions['viewsmore'] = $_GET['viewsmore'] != '' ? $_GET['viewsmore'] : '';
	$conditions['repliesless'] = $_GET['repliesless'] != '' ? $_GET['repliesless'] : '';
	$conditions['repliesmore'] = $_GET['repliesmore'] != '' ? $_GET['repliesmore'] : '';
	$conditions['readpermmore'] = $_GET['readpermmore'] != '' ? $_GET['readpermmore'] : '';
	$conditions['pricemore'] = $_GET['pricemore'] != '' ? $_GET['pricemore'] : '';
	$conditions['beforedays'] = $_GET['beforedays'] != '' ? $_GET['beforedays'] : '';
	$conditions['noreplydays'] = $_GET['noreplydays'] != '' ? $_GET['noreplydays'] : '';
	$conditions['starttime'] = !empty($_GET['starttime']) ? $_GET['starttime'] : '';
	$conditions['endtime'] = !empty($_GET['endtime']) ? $_GET['endtime'] : '';
	if(!empty($_GET['savethread'])) {
		$conditions['sticky'] = 4;
		$conditions['displayorder'] = -4;
	}
	if(!empty($_GET['hidethread'])) {
		$conditions['hidden'] = 1;
	}

	if(trim($_GET['keywords'])) {
		$conditions['keywords'] = $_GET['keywords'];
	}

	$conditions['users'] = trim($_GET['users']) ? $_GET['users'] : '';
	if($_GET['sticky'] == 1) {
		$conditions['sticky'] = 1;
	} elseif($_GET['sticky'] == 2) {
		$conditions['sticky'] = 2;
	}
	if($_GET['digest'] == 1) {
		$conditions['digest'] = 1;
	} elseif($_GET['digest'] == 2) {
		$conditions['digest'] = 2;
	}
	if($_GET['attach'] == 1) {
		$conditions['attach'] = 1;
	} elseif($_GET['attach'] == 2) {
		$conditions['attach'] = 2;
	}
	if($_GET['rate'] == 1) {
		$conditions['rate'] = 1;
	} elseif($_GET['rate'] == 2) {
		$conditions['rate'] = 2;
	}
	if($_GET['highlight'] == 1) {
		$conditions['highlight'] = 1;
	} elseif($_GET['highlight'] == 2) {
		$conditions['highlight'] = 2;
	}
	if(!empty($_GET['special'])) {
		$specials = $comma = '';
		foreach($_GET['special'] as $val) {
			$specials .= $comma.'\''.$val.'\'';
			$comma = ',';
		}
		$conditions['special'] = $_GET['special'];
		if($_GET['specialthread'] == 1) {
			$conditions['specialthread'] = 1;
		} elseif($_GET['specialthread'] == 2) {
			$conditions['specialthread'] = 2;
		}
	}

	$fids = [];
	$tids = $threadcount = '0';
	if($conditions) {
		if(empty($_GET['savethread']) && !isset($conditions['displayorder']) && !isset($conditions['sticky'])) {
			$conditions['sticky'] = 5;
		}
		if($_GET['detail']) {
			$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
			$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
			$start = ($page - 1) * $perpage;
			$threads = '';
			$groupsname = $groupsfid = $threadlist = [];
			$threadcount = table_forum_thread::t()->count_search($conditions);
			if($threadcount) {
				foreach(table_forum_thread::t()->fetch_all_search($conditions, 0, $start, $perpage, 'tid', 'DESC', ' FORCE INDEX(PRIMARY) ') as $thread) {
					$fids[] = $thread['fid'];
					if($thread['isgroup']) {
						$groupsfid[$thread['fid']] = $thread['fid'];
					}
					$thread['lastpost'] = dgmdate($thread['lastpost']);
					$threadlist[] = $thread;
				}
				if($groupsfid) {
					$query = table_forum_forum::t()->fetch_all_by_fid($groupsfid);
					foreach($query as $row) {
						$groupsname[$row['fid']] = $row['name'];
					}
				}
				if($threadlist) {
					foreach($threadlist as $thread) {
						$threads .= showtablerow('', ['class="td25"', '', '', '', 'class="td25"', 'class="td25"'], [
							"<input class=\"checkbox\" type=\"checkbox\" name=\"tidarray[]\" value=\"{$thread['tid']}\" />",
							"<a href=\"forum.php?mod=viewthread&tid={$thread['tid']}".($thread['displayorder'] != -4 ? '' : '&modthreadkey='.modauthkey($thread['tid']))."\" target=\"_blank\">{$thread['subject']}</a>".($thread['readperm'] ? " - [{$lang['threads_readperm']} {$thread['readperm']}]" : '').($thread['price'] ? " - [{$lang['threads_price']} {$thread['price']}]" : ''),
							"<a href=\"forum.php?mod=forumdisplay&fid={$thread['fid']}\" target=\"_blank\">".(empty($thread['isgroup']) ? $_G['cache']['forums'][$thread['fid']]['name'] : $groupsname[$thread['fid']]).'</a>',
							"<a href=\"home.php?mod=space&uid={$thread['authorid']}\" target=\"_blank\">{$thread['author']}</a>",
							$thread['replies'],
							$thread['views'],
							$thread['lastpost']
						], TRUE);
					}
				}

				$multi = multi($threadcount, $perpage, $page, ADMINSCRIPT.'?action=threads');
				$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=threads&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
				$multi = str_replace("window.location='".ADMINSCRIPT."?action=threads&amp;page='+this.value", 'page(this.value)', $multi);
			}
		} else {
			$threadcount = table_forum_thread::t()->count_search($conditions);
			if($threadcount) {
				foreach(table_forum_thread::t()->fetch_all_search($conditions, 0, $start, $perpage, 'tid', 'DESC', ' FORCE INDEX(PRIMARY) ') as $thread) {
					$fids[] = $thread['fid'];
					$tids .= ','.$thread['tid'];
				}
			}

			$multi = '';
		}
	}
	$fids = implode(',', array_unique($fids));

	showtagheader('div', 'threadlist', TRUE);
	showformheader('threads&frame=no'.($operation ? '&operation='.$operation : ''), 'target="threadframe"');
	showhiddenfields($_GET['detail'] ? ['fids' => $fids] : ['fids' => $fids, 'tids' => $tids]);
	if(!$search_tips) {
		showtableheader(cplang('threads_new_result').' '.$threadcount);
	} else {
		showtableheader(cplang('threads_result').' '.$threadcount.' <a href="###" onclick="$(\'threadlist\').style.display=\'none\';$(\'threadsearch\').style.display=\'\';$(\'threadforum\').pp.value=\'\';$(\'threadforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>');
	}
	if(!$threadcount) {

		showtablerow('', 'colspan="3"', cplang('threads_thread_nonexistence'));

	} else {

		if($_GET['detail']) {
			showsubtitle(['', 'subject', 'forum', 'author', 'threads_replies', 'threads_views', 'threads_lastpost']);
			echo $threads;
			showtablerow('', ['class="td25" colspan="7"'], ['<input name="chkall" id="chkall" type="checkbox" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'tidarray\', \'chkall\')" /><label for="chkall">'.cplang('select_all').'</label>']);
			showtablefooter();
			showtableheader('operation', 'notop');

		}
		showsubtitle(['', 'operation', 'option']);
		showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
			'<input class="radio" type="radio" id="optype_moveforum" name="optype" value="moveforum" onclick="this.form.modsubmit.disabled=false;">',
			$lang['threads_move_forum'],
			'<select name="toforum"  id="toforum" onchange="$(\'optype_moveforum\').checked=\'checked\';ajaxget(\'forum.php?mod=ajax&action=getthreadtypes&fid=\' + this.value, \'threadtypes\')">'.forumselect(FALSE, 0, 0, TRUE).'</select>'.
			$lang['threads_move_type'].' <span id="threadtypes"><select name="threadtypeid" onchange="$(\'optype_moveforum\').checked=\'checked\'"><option value="0"></option></select></span>'
		]);
		if($operation != 'group') {
			showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
				'<input class="radio" type="radio" id="optype_movesort" name="optype" value="movesort" onclick="this.form.modsubmit.disabled=false;">',
				$lang['threads_move_sort'],
				'<select name="tosort" onchange="$(\'optype_movesort\').checked=\'checked\';"><option value="0">&nbsp;&nbsp;> '.$lang['threads_search_type_none'].'</option>'.$sortselect.'</select>'
			]);
			showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
				'<input class="radio" type="radio" id="optype_stick" name="optype" value="stick" onclick="this.form.modsubmit.disabled=false;">',
				$lang['threads_stick'],
				'<input class="radio" type="radio" name="stick_level" value="0" onclick="$(\'optype_stick\').checked=\'checked\'"> '.$lang['threads_remove'].' &nbsp; &nbsp;<input class="radio" type="radio" name="stick_level" value="1" onclick="$(\'optype_stick\').checked=\'checked\'"> '.$lang['threads_stick_one'].' &nbsp; &nbsp;<input class="radio" type="radio" name="stick_level" value="2" onclick="$(\'optype_stick\').checked=\'checked\'"> '.$lang['threads_stick_two'].' &nbsp; &nbsp;<input class="radio" type="radio" name="stick_level" value="3" onclick="$(\'optype_stick\').checked=\'checked\'"> '.$lang['threads_stick_three']
			]);
			showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
				'<input class="radio" type="radio" id="optype_addstatus" name="optype" value="addstatus" onclick="this.form.modsubmit.disabled=false;">',
				$lang['threads_open_close'],
				'<input class="radio" type="radio" name="status" value="0" onclick="$(\'optype_addstatus\').checked=\'checked\'"> '.$lang['open'].' &nbsp; &nbsp;<input class="radio" type="radio" name="status" value="1"  onclick="$(\'optype_addstatus\').checked=\'checked\'"> '.$lang['closed']
			]);
		}
		showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
			'<input class="radio" type="radio" id="optype_delete" name="optype" value="delete" onclick="this.form.modsubmit.disabled=false;">',
			$lang['threads_delete'],
			'<input class="checkbox" type="checkbox" name="donotupdatemember" id="donotupdatemember" value="1" /><label for="donotupdatemember"> '.$lang['threads_delete_no_update_member'].'</label>'
		]);
		showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
			'<input class="radio" type="radio" name="optype" id="optype_adddigest" value="adddigest" onclick="this.form.modsubmit.disabled=false;">',
			$lang['threads_add_digest'],
			'<input class="radio" type="radio" name="digest_level" value="0" onclick="$(\'optype_adddigest\').checked=\'checked\'"> '.$lang['threads_remove'].' &nbsp; &nbsp;<input class="radio" type="radio" name="digest_level" value="1" onclick="$(\'optype_adddigest\').checked=\'checked\'"> '.$lang['threads_digest_one'].' &nbsp; &nbsp;<input class="radio" type="radio" name="digest_level" value="2" onclick="$(\'optype_adddigest\').checked=\'checked\'"> '.$lang['threads_digest_two'].' &nbsp; &nbsp;<input class="radio" type="radio" name="digest_level" value="3" onclick="$(\'optype_adddigest\').checked=\'checked\'"> '.$lang['threads_digest_three']
		]);
		showtablerow('', ['class="td25"', 'class="td24"', 'class="rowform" style="width:auto;"'], [
			'<input class="radio" type="radio" name="optype" value="deleteattach" onclick="this.form.modsubmit.disabled=false;">',
			$lang['threads_delete_attach'],
			''
		]);

	}

	showsubmit('modsubmit', 'submit', '', '', $multi);
	showtablefooter();
	showformfooter();
	echo '<script type="text/JavaScript">ajaxget(\'forum.php?mod=ajax&action=getthreadtypes&fid=\' + $("toforum").value, \'threadtypes\')</script>';
	echo '<iframe name="threadframe" style="display:none"></iframe>';
	showtagfooter('div');

}
	