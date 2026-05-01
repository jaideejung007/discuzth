<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['srchname']) {
	$addurl = '&srchname='.$_GET['srchname'];
}
if(submitcheck('submit') && $_GET['tagids']) {
	$class_tag = new tag();
	if($_GET['operate_type'] == 'delete') {
		$class_tag->delete_tag($_GET['tagids'], 'uid');
		cpmsg('usertag_delete_succeed', 'action=usertag'.$addurl, 'succeed');
	} elseif($_GET['operate_type'] == 'merge' && $_GET['newtag']) {
		$data = $class_tag->merge_tag($_GET['tagids'], $_GET['newtag'], 'uid');
		if($data != 'succeed') {
			cpmsg($data);
		}
		cpmsg('usertag_merge_succeed', 'action=usertag'.$addurl, 'succeed');
	}
}
/*search={"usertag":"action=usertag"}*/
showsubmenu('usertag', [
	['usertag_list', 'usertag', 1],
	['usertag_add', 'usertag&operation=add', 0],
]);
showboxheader();
echo '<form method="post">'.$lang['keywords'].': <input type="text" name="srchname" value="'.$_GET['srchname'].'" /> &nbsp;<input type="submit" name="usertag_search" value="'.$lang['search'].'" class="btn" /> </form>';
showboxfooter();
showformheader('usertag'.$addurl);
$tagcount = table_common_tag::t()->fetch_all_by_status(3, $_GET['srchname'], 0, 0, 1);
showtableheader(cplang('usertag_count', ['tagcount' => $tagcount]));
if($tagcount) {
	showsubtitle(['', 'tagname', 'usernum', 'operation']);
	$query = table_common_tag::t()->fetch_all_by_status(3, $_GET['srchname'], $start, $lpp);
	foreach($query as $row) {
		showtablerow('', ['class="td25"', 'width=100', ''], [
			'<input type="checkbox" class="checkbox" name="tagids[]" value="'.$row['tagid'].'" />',
			$row['tagname'],
			$row['related_count'],
			'<a href="'.ADMINSCRIPT.'?action=members&operation=search&submit=1&tagid='.$row['tagid'].'" target="_blank">'.cplang('view').$lang['usertag_user'].'</a>&nbsp;|&nbsp;<a href="'.ADMINSCRIPT.'?action=members&operation=newsletter&tagid='.$row['tagid'].'&submit=1" target="_blank">'.$lang['usertag_send_notice'].'</a>'
		]);
	}
	$multipage = multi($tagcount, $lpp, $page, ADMINSCRIPT."?action=usertag$addurl&lpp=$lpp", 0, 3);
	showtablerow('', ['class="td25" colspan="3"'], ['<input name="chkall" id="chkall" type="checkbox" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'tagids\', \'chkall\')" /><label for="chkall">'.cplang('select_all').'</label>']);
	showtablerow('', ['class="td25"', 'colspan="2"'], [
		cplang('operation'), '<input class="radio" type="radio" name="operate_type" value="delete"> '.cplang('delete').' &nbsp; &nbsp;<input class="radio" type="radio" name="operate_type" value="merge"> '.cplang('mergeto').' <input name="newtag" value="" class="txt" type="text">'
	]);
	showsubmit('submit', 'submit', '', '', $multipage);
}

showtablefooter();
showformfooter();
/*search*/
	