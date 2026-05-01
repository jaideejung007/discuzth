<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(FORMHASH != $_GET['formhash']) {
	cpmsg('undefined_action');
}

$ajax = $_GET['ajax'];
$confirmed = $_GET['confirmed'];
$finished = $_GET['finished'];
$total = intval($_GET['total']);
$pp = intval($_GET['pp']);
$currow = intval($_GET['currow']);

if($_GET['ajax']) {
	require_once libfile('function/post');
	$tids = [];

	foreach(table_forum_thread::t()->fetch_all_by_fid($fid, $pp) as $thread) {
		$tids[] = $thread['tid'];
	}
	require_once libfile('function/delete');
	deletethread($tids);
	deletedomain($fid, 'forum');
	deletedomain($fid, 'subarea');
	if($currow + $pp > $total) {
		table_forum_forum::t()->delete_by_fid($fid);
		table_common_nav::t()->delete_by_type_identifier(5, $fid);
		table_home_favorite::t()->delete_by_id_idtype($fid, 'fid');
		table_forum_moderator::t()->delete_by_fid($fid);
		table_common_member_forum_buylog::t()->delete_by_fid($fid);
		table_forum_access::t()->delete_by_fid($fid);

		$forumkeys = table_common_setting::t()->fetch_setting('forumkeys', true);
		unset($forumkeys[$fid]);
		table_common_setting::t()->update_setting('forumkeys', $forumkeys);
		echo 'TRUE';
		exit;
	}

	echo 'GO';
	exit;

} else {

	if($_GET['finished']) {
		$vfidstr = !empty($_GET['vfid']) ? '&fid='.$_GET['vfid'] : '';

		updatecache('forums');
		cpmsg('forums_delete_succeed', 'action=forums'.$vfidstr, 'succeed');
	}

	if(table_forum_forum::t()->fetch_forum_num('', $fid)) {
		cpmsg('forums_delete_sub_notnull', '', 'error');
	}

	$vfidstr = !empty($_GET['vfid']) ? '&vfid='.$_GET['vfid'] : '';

	if(!$_GET['confirmed']) {

		cpmsg('forums_delete_confirm', "action=forums&operation=delete&fid=$fid&formhash=".FORMHASH.$vfidstr, 'form');

	} else {

		$threads = table_forum_thread::t()->count_by_fid($fid);
		cpmsg('forums_delete_alarm', "action=forums&operation=delete&fid=$fid&confirmed=1&formhash=".FORMHASH.$vfidstr, 'loadingform', '', '<div id="percent">0%</div>', FALSE);

		echo "
			<div id=\"statusid\" style=\"display:none\"></div>
			<script type=\"text/JavaScript\">
				var xml_http_building_link = '".cplang('xml_http_building_link')."';
				var xml_http_sending = '".cplang('xml_http_sending')."';
				var xml_http_loading = '".cplang('xml_http_loading')."';
				var xml_http_load_failed = '".cplang('xml_http_load_failed')."';
				var xml_http_data_in_processed = '".cplang('xml_http_data_in_processed')."';
				var adminfilename = '".ADMINSCRIPT."';
				function forumsdelete(url, total, pp, currow) {

					var x = new Ajax('HTML', 'statusid');
					x.get(url+'&ajax=1&pp='+pp+'&total='+total+'&currow='+currow, function(s) {
						if(s != 'GO') {
							location.href = adminfilename + '?action=forums&operation=delete&finished=1&formhash=".FORMHASH.$vfidstr."';
						}

						currow += pp;
						var percent = ((currow / total) * 100).toFixed(0);
						percent = percent > 100 ? 100 : percent;
						document.getElementById('percent').innerHTML = percent+'%';
						document.getElementById('percent').style.backgroundPosition = '-'+percent+'%';

						if(currow < total) {
							forumsdelete(url, total, pp, currow);
						}
					});
				}
				forumsdelete(adminfilename + '?action=forums&operation=delete&fid=$fid&confirmed=1&formhash=".FORMHASH.$vfidstr."', $threads, 2000, 0);
			</script>
			";
	}
}
	