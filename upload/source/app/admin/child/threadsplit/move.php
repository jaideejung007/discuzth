<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$_G['setting']['bbclosed'] && !IN_DEBUG) {
	cpmsg('threadsplit_forum_must_be_closed', 'action=threadsplit&operation=manage', 'error');
}

require_once libfile('function/forumlist');
$tableselect = '<select name="sourcetableid">';
foreach($threadtableids as $tableid) {
	$selected = $_GET['sourcetableid'] == $tableid ? 'selected="selected"' : '';
	$tableselect .= "<option value=\"$tableid\" $selected>".table_forum_thread::t()->get_table_name($tableid).'</option>';
}
$tableselect .= '</select>';

$forumselect = '<select name="inforum"><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option>'.
	'<option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';
if(isset($_GET['inforum'])) {
	$forumselect = preg_replace("/(\<option value=\"{$_GET['inforum']}\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
}

$typeselect = $sortselect = '';
$query = table_forum_threadtype::t()->fetch_all_for_order();
foreach($query as $type) {
	if($type['special']) {
		$sortselect .= '<option value="'.$type['typeid'].'">&nbsp;&nbsp;> '.$type['name'].'</option>';
	} else {
		$typeselect .= '<option value="'.$type['typeid'].'">&nbsp;&nbsp;> '.$type['name'].'</option>';
	}
}

if(isset($_GET['insort'])) {
	$sortselect = preg_replace("/(\<option value=\"{$_GET['insort']}\")(\>)/", "\\1 selected=\"selected\" \\2", $sortselect);
}

if(isset($_GET['intype'])) {
	$typeselect = preg_replace("/(\<option value=\"{$_GET['intype']}\")(\>)/", "\\1 selected=\"selected\" \\2", $typeselect);
}

$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
	function page(number) {
		$('threadform').page.value=number;
		$('threadform').threadsplit_move_search.click();
	}
</script>
EOT;
shownav('founder', 'nav_threadsplit');
if(!submitcheck('threadsplit_move_submit') && !$_GET['moving']) {
	showsubmenu('nav_threadsplit', [
		['nav_threadsplit_manage', 'threadsplit&operation=manage', 0],
		['nav_threadsplit_move', 'threadsplit&operation=move', 1],
	]);
	/*search={"nav_threadsplit":"action=threadsplit","nav_threadsplit_move":"action=threadsplit&operation=move"}*/
	showtips('threadsplit_move_tips');
	showtagheader('div', 'threadsearch', !submitcheck('threadsplit_move_search'));
	showformheader('threadsplit&operation=move', '', 'threadform');
	showhiddenfields(['page' => $_GET['page']]);
	showtableheader();
	showsetting('threads_search_detail', 'detail', $_GET['detail'], 'radio');
	showsetting('threads_search_sourcetable', '', '', $tableselect);
	showsetting('threads_search_forum', '', '', $forumselect);
	showsetting('threadsplit_move_tidrange', ['tidmin', 'tidmax'], [$_GET['tidmin'], $_GET['tidmax']], 'range');
	showsetting('threads_search_noreplyday', 'noreplydays', $_GET['noreplydays'] ?? 365, 'text');

	showtagheader('tbody', 'advanceoption');
	showsetting('threads_search_time', ['starttime', 'endtime'], [$_GET['starttime'], $_GET['endtime']], 'daterange');
	showsetting('threads_search_type', '', '', '<select name="intype"><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option><option value="">&nbsp;</option><option value="0">&nbsp;&nbsp;> '.$lang['threads_search_type_none'].'</option>'.$typeselect.'</select>');
	showsetting('threads_search_sort', '', '', '<select name="insort"><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option><option value="">&nbsp;</option><option value="0">&nbsp;&nbsp;> '.$lang['threads_search_type_none'].'</option>'.$sortselect.'</select>');
	showsetting('threads_search_viewrange', ['viewsmore', 'viewsless'], [$_GET['viewsmore'], $_GET['viewsless']], 'range');
	showsetting('threads_search_replyrange', ['repliesmore', 'repliesless'], [$_GET['repliesmore'], $_GET['repliesless']], 'range');
	showsetting('threads_search_readpermmore', 'readpermmore', $_GET['readpermmore'], 'text');
	showsetting('threads_search_pricemore', 'pricemore', $_GET['pricemore'], 'text');
	showsetting('threads_search_keyword', 'keywords', $_GET['keywords'], 'text');
	showsetting('threads_search_user', 'users', $_GET['users'], 'text');

	showsetting('threads_search_type', ['specialthread', [
		[0, cplang('unlimited'), ['showspecial' => 'none']],
		[1, cplang('threads_search_include_yes'), ['showspecial' => '']],
		[2, cplang('threads_search_include_no'), ['showspecial' => '']],
	], TRUE], $_GET['specialthread'] ?? 2, 'mradio');
	showtablerow('id="showspecial" style="display:'.($_GET['specialthread'] || !isset($_GET['specialthread']) ? '' : 'none').'"', 'class="sub" colspan="2"', mcheckbox('special', [
		1 => cplang('thread_poll'),
		2 => cplang('thread_trade'),
		3 => cplang('thread_reward'),
		4 => cplang('thread_activity'),
		5 => cplang('thread_debate')
	], $_GET['special'] ? $_GET['special'] : [1, 2, 3, 4, 5]));
	showsetting('threads_search_sticky', ['sticky', [
		[0, cplang('unlimited')],
		[1, cplang('threads_search_include_yes')],
		[2, cplang('threads_search_include_no')],
	], TRUE], $_GET['sticky'] ?? 2, 'mradio');
	showsetting('threads_search_digest', ['digest', [
		[0, cplang('unlimited')],
		[1, cplang('threads_search_include_yes')],
		[2, cplang('threads_search_include_no')],
	], TRUE], $_GET['digest'] ?? 2, 'mradio');
	showsetting('threads_search_attach', ['attach', [
		[0, cplang('unlimited')],
		[1, cplang('threads_search_include_yes')],
		[2, cplang('threads_search_include_no')],
	], TRUE], $_GET['attach'] ?? 0, 'mradio');
	showsetting('threads_rate', ['rate', [
		[0, cplang('unlimited')],
		[1, cplang('threads_search_include_yes')],
		[2, cplang('threads_search_include_no')],
	], TRUE], $_GET['rate'] ?? 2, 'mradio');
	showsetting('threads_highlight', ['highlight', [
		[0, cplang('unlimited')],
		[1, cplang('threads_search_include_yes')],
		[2, cplang('threads_search_include_no')],
	], TRUE], $_GET['highlight'] ?? 2, 'mradio');
	showtagfooter('tbody');

	showsubmit('threadsplit_move_search', 'submit', '', 'more_options');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
	if(submitcheck('threadsplit_move_search')) {
		$searchurladd = [];
		$conditions = [
			'sourcetableid' => $_GET['sourcetableid'],
			'inforum' => $_GET['inforum'],
			'tidmin' => $_GET['tidmin'],
			'tidmax' => $_GET['tidmax'],
			'starttime' => $_GET['starttime'],
			'endtime' => $_GET['endtime'],
			'keywords' => $_GET['keywords'],
			'users' => $_GET['users'],
			'intype' => $_GET['intype'],
			'insort' => $_GET['insort'],
			'viewsmore' => $_GET['viewsmore'],
			'viewsless' => $_GET['viewsless'],
			'repliesmore' => $_GET['repliesmore'],
			'repliesless' => $_GET['repliesless'],
			'readpermmore' => $_GET['readpermmore'],
			'pricemore' => $_GET['pricemore'],
			'noreplydays' => $_GET['noreplydays'],
			'specialthread' => $_GET['specialthread'],
			'special' => $_GET['special'],
			'sticky' => $_GET['sticky'],
			'digest' => $_GET['digest'],
			'attach' => $_GET['attach'],
			'rate' => $_GET['rate'],
			'highlight' => $_GET['highlight'],
		];
		if($_GET['detail']) {
			$pagetmp = $page;
			$threadlist = threadsplit_search_threads($conditions, ($pagetmp - 1) * $topicperpage, $topicperpage);
		} else {
			$threadtomove = threadsplit_search_threads($conditions, null, null, TRUE);
		}

		$fids = [];
		$tids = '0';
		if($_GET['detail']) {
			$threads = '';
			foreach($threadlist as $thread) {
				$fids[] = $thread['fid'];
				$thread['lastpost'] = dgmdate($thread['lastpost']);
				$threads .= showtablerow('', ['class="td25"', '', '', '', '', ''], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"tidarray[]\" value=\"{$thread['tid']}\" checked=\"checked\" />",
					"<a href=\"forum.php?mod=viewthread&tid={$thread['tid']}\" target=\"_blank\">{$thread['subject']}</a>",
					"<a href=\"forum.php?mod=forumdisplay&fid={$thread['fid']}\" target=\"_blank\">{$_G['cache']['forums'][$thread['fid']]['name']}</a>",
					"<a href=\"home.php?mod=space&uid={$thread['authorid']}\" target=\"_blank\">{$thread['author']}</a>",
					$thread['replies'],
					$thread['views']
				], TRUE);
			}
			$multi = multi($threadcount, $topicperpage, $page, ADMINSCRIPT.'?action=threadsplit&amp;operation=move');
			$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=threadsplit&amp;operation=move&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
			$multi = str_replace("window.location='".ADMINSCRIPT."?action=threadsplit&amp;operation=move&amp;page='+this.value", 'page(this.value)', $multi);
		} else {
			foreach($threadlist as $thread) {
				$fids[] = $thread['fid'];
				$tids .= ','.$thread['tid'];
			}
			$multi = '';
		}
		$fids = implode(',', array_unique($fids));

		showtagheader('div', 'threadlist', TRUE);
		showformheader("threadsplit&operation=move&sourcetableid={$_GET['sourcetableid']}&threadtomove=".$threadtomove);
		showhiddenfields($_GET['detail'] ? ['fids' => $fids] : ['conditions' => serialize($conditions)]);
		showtableheader(cplang('threads_result').' '.$threadcount.' <a href="###" onclick="$(\'threadlist\').style.display=\'none\';$(\'threadsearch\').style.display=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'nobottom');
		showsubtitle(['', 'threadsplit_move_to', 'threadsplit_manage_threadcount', 'threadsplit_manage_datalength', 'threadsplit_manage_indexlength', 'threadsplit_manage_table_createtime', 'threadsplit_manage_table_memo']);

		if(!$threadcount) {

			showtablerow('', 'colspan="3"', cplang('threads_thread_nonexistence'));

		} else {
			$threadtable_orig = table_forum_thread::t()->gettablestatus();
			$tableid = 0;

			showtablerow('', ['class="td25"'], ["<input class=\"radio\" ".($_GET['sourcetableid'] == '0' ? 'disabled="disabled"' : '')." type=\"radio\" name=\"tableid\" value=\"0\" />", $threadtable_orig['Name'], $threadtable_orig['Rows'], $threadtable_orig['Data_length'], $threadtable_orig['Index_length'], $threadtable_orig['Create_time'], $threadtable_info[0]['memo']]);
			foreach($threadtableids as $tableid) {
				if($tableid) {
					$tablename = "forum_thread_$tableid";
					$tablestatus = table_forum_thread::t()->gettablestatus($tableid);

					showtablerow('', [], ["<input class=\"radio\" ".($_GET['sourcetableid'] == $tableid ? 'disabled="disabled"' : '')." type=\"radio\" name=\"tableid\" value=\"$tableid\" />", $tablestatus['Name'].($threadtable_info[$tableid]['displayname'] ? ' ('.dhtmlspecialchars($threadtable_info[$tableid]['displayname']).')' : ''), $tablestatus['Rows'], $tablestatus['Data_length'], $tablestatus['Index_length'], $tablestatus['Create_time'], $threadtable_info[$tableid]['memo']]);
				}
			}

			if($_GET['detail']) {

				showtablefooter();
				showtableheader('threads_list', 'notop');
				showsubtitle(['', 'subject', 'forum', 'author', 'threads_replies', 'threads_views']);
				echo $threads;

			}

		}
		showtablefooter();
		if($threadcount) {
			showtableheader('');
			showsetting('threadsplit_move_threads_per_time', 'threads_per_time', 200, 'text');
			showtablefooter();
			showsubmit('threadsplit_move_submit', 'submit', $_GET['detail'] ? '<input name="chkall" id="chkall" type="checkbox" class="checkbox" checked="checked" onclick="checkAll(\'prefix\', this.form, \'tidarray\', \'chkall\')" /><label for="chkall">'.cplang('select_all').'</label>' : '', '', $multi);

		}
		showformfooter();
		showtagfooter('div');
		/*search*/

	}
} else {
	if(!isset($_GET['tableid'])) {
		cpmsg('threadsplit_no_target_table', '', 'error');
	}
	$continue = false;

	$tidsarray = !empty($_GET['tidarray']) ? $_GET['tidarray'] : [];
	if(empty($tidsarray) && !empty($_GET['conditions'])) {
		$conditions = dunserialize($_GET['conditions']);
		$max_threads_move = intval($_GET['threads_per_time']) ? intval($_GET['threads_per_time']) : MAX_THREADS_MOVE;
		$threadlist = threadsplit_search_threads($conditions, 0, $max_threads_move);
		foreach($threadlist as $thread) {
			$tidsarray[] = $thread['tid'];
			$continue = TRUE;
		}
	}
	if(empty($tidsarray[0])) {
		array_shift($tidsarray);
	}

	if(!empty($tidsarray)) {
		$continue = true;
	}
	if($_GET['tableid'] == $_GET['sourcetableid']) {
		cpmsg('threadsplit_move_source_target_no_same', 'action=threadsplit&operation=move', 'error');
	}
	if($continue) {
		$threadtable_target = $_GET['tableid'] ? $_GET['tableid'] : 0;
		$threadtable_source = $_GET['sourcetableid'] ? $_GET['sourcetableid'] : 0;
		table_forum_thread::t()->move_thread_by_tid($tidsarray, $threadtable_source, $threadtable_target);

		table_forum_forumrecommend::t()->delete($tidsarray);

		$completed = intval($_GET['completed']) + count($tidsarray);

		$nextstep = $step + 1;
		cpmsg('threadsplit_moving', "action=threadsplit&operation=move&{$_GET['urladd']}&tableid={$_GET['tableid']}&completed=$completed&sourcetableid={$_GET['sourcetableid']}&threadtomove={$_GET['threadtomove']}&step=$nextstep&moving=1", 'loadingform', ['count' => $completed, 'total' => intval($_GET['threadtomove']), 'threads_per_time' => $_GET['threads_per_time'], 'conditions' => dhtmlspecialchars($_GET['conditions'])]);
	}

	cpmsg('threadsplit_move_succeed', 'action=threadsplit&operation=forumarchive', 'succeed');
}
	