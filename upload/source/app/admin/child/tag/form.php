<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

/*search={"tag":"action=tag"}*/
$tagname = trim($_GET['tagname']);
$status = $_GET['status'];
if(!$status) {
	$table_status = NULL;
} else {
	$table_status = $status;
}
$ppp = $_GET['perpage'];
$startlimit = ($page - 1) * $ppp;
$multipage = '';
$totalcount = table_common_tag::t()->fetch_all_by_status($table_status, $tagname, 0, 0, 1);
$multipage = multi($totalcount, $ppp, $page, ADMINSCRIPT."?action=tag&operation=admin&searchsubmit=yes&tagname=$tagname&perpage=$ppp&status=$status");
$query = table_common_tag::t()->fetch_all_by_status($table_status, $tagname, $startlimit, $ppp);
showformheader('tag&operation=admin');
showtableheader(cplang('tag_result').' '.$totalcount.' <a href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=tag&operation=admin;\'" class="act lightlink normal">'.cplang('research').'</a>', 'nobottom');
showhiddenfields(['page' => $_GET['page'], 'tagname' => $tagname, 'status' => $status, 'perpage' => $ppp]);
showsubtitle(['', 'tagname', 'misc_tag_status', 'misc_tag_related_count', 'misc_tag_hot_score', 'misc_tag_created_at', 'misc_tag_updated_at']);
foreach($query as $result) {
	if($result['status'] == 0) {
		$tagstatus = cplang('misc_tag_status_0');
	} elseif($result['status'] == 1) {
		$tagstatus = cplang('misc_tag_status_1');
	}
	showtablerow('', ['class="td25"', 'width=400', ''], [
		"<input class=\"checkbox\" type=\"checkbox\" name=\"tagidarray[]\" value=\"{$result['tagid']}\" />",
		$result['tagname'],
		$tagstatus,
		$result['related_count'],
		sprintf("%.2f", $result['hot_score']),
		dgmdate($result['created_at']),
		dgmdate($result['updated_at'])
	]);
}
showtablerow('', ['class="td25" colspan="3"'], ['<input name="chkall" id="chkall" type="checkbox" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'tagidarray\', \'chkall\')" /><label for="chkall">'.cplang('select_all').'</label>']);
showtablerow('', ['class="td25"', 'colspan="2"'], [
	cplang('operation'),
	'<input class="radio" type="radio" name="operate_type" value="open" checked> '.cplang('misc_tag_status_0').' &nbsp; &nbsp;<input class="radio" type="radio" name="operate_type" value="close"> '.cplang('misc_tag_status_1').' &nbsp; &nbsp;<input class="radio" type="radio" name="operate_type" value="delete"> '.cplang('delete').' &nbsp; &nbsp;<input class="radio" type="radio" name="operate_type" value="merge"> '.cplang('mergeto').' <input name="newtag" value="" class="txt" type="text">'
]);
showsubmit('submit', 'submit', '', '', $multipage);
showtablefooter();
showformfooter();
/*search*/
		