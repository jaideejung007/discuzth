<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('delmedalsubmit')) {
	if(is_array($_GET['delete']) && !empty($_GET['delete'])) {
		$ids = [];
		foreach($_GET['delete'] as $id) {
			$ids[] = $id;
		}
		table_forum_medallog::t()->update($ids, ['type' => 3]);
		cpmsg('medals_invalidate_succeed', 'action=medals&operation=mod', 'succeed');
	} else {
		cpmsg('medals_please_input', 'action=medals&operation=mod', 'error');
	}
} elseif(submitcheck('modmedalsubmit')) {

	if(is_array($_GET['delete']) && !empty($_GET['delete'])) {
		$ids = $comma = '';
		foreach($_GET['delete'] as $id) {
			$ids .= "$comma'$id'";
			$comma = ',';
		}

		$query = DB::query('SELECT me.id, me.uid, me.medalid, me.dateline, me.expiration, mf.medals
					FROM '.DB::table('forum_medallog').' me
					LEFT JOIN '.DB::table('common_member_field_forum')." mf USING (uid)
					WHERE id IN ($ids)");

		loadcache('medals');
		while($modmedal = DB::fetch($query)) {
			$modmedal['medals'] = empty($medalsnew[$modmedal['uid']]) ? $modmedal['medals'] : $medalsnew[$modmedal['uid']];

			foreach($modmedal['medals'] = explode("\t", $modmedal['medals']) as $key => $modmedalid) {
				list($medalid, $medalexpiration) = explode('|', $modmedalid);
				if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
					$medalsnew[$modmedal['uid']][$key] = $modmedalid;
				}
			}
			$medalstatus = empty($modmedal['expiration']) ? 0 : 1;
			$modmedal['expiration'] = $modmedal['expiration'] ? (TIMESTAMP + $modmedal['expiration'] - $modmedal['dateline']) : '';
			$medalsnew[$modmedal['uid']][] = $modmedal['medalid'].(empty($modmedal['expiration']) ? '' : '|'.$modmedal['expiration']);
			table_forum_medallog::t()->update($modmedal['id'], ['type' => 1, 'status' => $medalstatus, 'expiration' => $modmedal['expiration'], 'dateline' => TIMESTAMP]);
			table_common_member_medal::t()->insert(['uid' => $modmedal['uid'], 'medalid' => $modmedal['medalid']], 0, 1);
		}

		foreach($medalsnew as $key => $medalnew) {
			$medalnew = array_unique($medalnew);
			$medalnew = implode("\t", $medalnew);
			table_common_member_field_forum::t()->update($key, ['medals' => $medalnew]);
		}
		cpmsg('medals_validate_succeed', 'action=medals&operation=mod', 'succeed');
	} else {
		cpmsg('medals_please_input', 'action=medals&operation=mod', 'error');
	}
} else {

	$medals = '';
	$medallogs = $medalids = $uids = [];
	foreach(table_forum_medallog::t()->fetch_all_by_type(2) as $id => $medal) {
		$medal['dateline'] = dgmdate($medal['dateline'], 'Y-m-d H:i');
		$medal['expiration'] = empty($medal['expiration']) ? $lang['medals_forever'] : dgmdate($medal['expiration'], 'Y-m-d H:i');
		$medalids[$medal['medalid']] = $medal['medalid'];
		$uids[$medal['uid']] = $medal['uid'];
		$medallogs[$id] = $medal;
	}
	$medalnames = table_forum_medal::t()->fetch_all($medalids);
	$medalusers = table_common_member::t()->fetch_all($uids);
	foreach($medallogs as $id => $medal) {
		$medals .= showtablerow('', '', [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$id\">",
			"<a href=\"home.php?mod=space&username=".rawurlencode($medalusers[$medal['uid']]['username'])."\" target=\"_blank\">{$medalusers[$medal['uid']]['username']}</a>",
			$medalnames[$medal['medalid']]['name'],
			$medal['dateline'],
			$medal['expiration']
		], TRUE);
	}

	shownav('extended', 'nav_medals', 'nav_medals_mod');
	showsubmenu('nav_medals', [
		['admin', 'medals', 0],
		['nav_medals_confer', 'members&operation=confermedal', 0],
		['nav_medals_mod', 'medals&operation=mod', 1]
	]);
	showformheader('medals&operation=mod');
	showtableheader('medals_mod');
	showtablerow('', '', [
		'',
		cplang('medals_user'),
		cplang('medals_name'),
		cplang('medals_date'),
		cplang('medals_expr'),
	]);
	echo $medals;
	showsubmit('modmedalsubmit', 'medals_modpass', 'select_all', '<input type="submit" class="btn" value="'.cplang('medals_modnopass').'" name="delmedalsubmit"> ');
	showtablefooter();
	showformfooter();
}
	