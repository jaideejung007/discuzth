<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$fid = $_GET['fid'];
$ajax = $_GET['ajax'];
$confirmed = $_GET['confirmed'];
$finished = $_GET['finished'];
$total = intval($_GET['total']);
$pp = intval($_GET['pp']);
$currow = intval($_GET['currow']);
if($ajax && $_GET['formhash'] == formhash()) {
	ob_end_clean();
	require_once libfile('function/post');
	$tids = [];
	foreach(table_forum_thread::t()->fetch_all_by_fid($fid, $pp) as $thread) {
		$tids[] = $thread['tid'];
	}
	require_once libfile('function/delete');
	deletethread($tids);

	if($currow + $pp > $total) {
		table_forum_forum::t()->delete_by_fid($fid);
		table_home_favorite::t()->delete_by_id_idtype($fid, 'gid');
		table_forum_moderator::t()->delete_by_fid($fid);
		table_forum_access::t()->delete_by_fid($fid);

		echo 'TRUE';
		exit;
	}

	echo 'GO';
	exit;

} else {
	if($finished) {
		updatecache('grouptype');
		cpmsg('grouptype_delete_succeed', 'action=group&operation=type', 'succeed');

	}

	if(table_forum_forum::t()->fetch_forum_num('group', $fid)) {
		cpmsg('grouptype_delete_sub_notnull', '', 'error');
	}

	if(!$confirmed) {

		cpmsg('grouptype_delete_confirm', "action=group&operation=deletetype&fid=$fid", 'form');

	} else {

		$threads = table_forum_thread::t()->count_by_fid($fid);//群组不展示了  废弃代码
		$formhash = formhash();
		cpmsg('grouptype_delete_alarm', "action=group&operation=deletetype&fid=$fid&confirmed=1&formhash=$formhash", 'loadingform', [], '<div id="percent">0%</div>', FALSE);
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
							location.href = adminfilename + '?action=group&operation=deletetype&finished=1';
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
				forumsdelete(adminfilename + '?action=group&operation=deletetype&fid=$fid&confirmed=1&formhash=$formhash', $threads, 2000, 0);
			</script>
			";
	}
}
	