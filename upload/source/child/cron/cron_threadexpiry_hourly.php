<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

table_common_seccheck::t()->delete_expiration();

$actionarray = [];
foreach(table_forum_threadmod::t()->fetch_all_by_expiration_status($_G['timestamp']) as $expiry) {
	switch($expiry['action']) {
		case 'TOK':
		case 'EST':
			$actionarray['UES'][] = $expiry['tid'];
			break;
		case 'CCK':
		case 'EHL':
			$actionarray['UEH'][] = $expiry['tid'];
			break;
		case 'CLK':
		case 'ECL':
			$actionarray['UEC'][] = $expiry['tid'];
			break;
		case 'EOP':
			$actionarray['UEO'][] = $expiry['tid'];
			break;
		case 'EDI':
			$actionarray['UED'][] = $expiry['tid'];
			break;
		case 'SPA':
			$actionarray['SPD'][] = $expiry['tid'];
			break;
	}
}

if($actionarray) {

	foreach($actionarray as $action => $tids) {


		switch($action) {

			case 'UES':
				table_forum_thread::t()->update($actionarray[$action], ['displayorder' => 0], true);
				table_forum_threadmod::t()->update_by_tid_action($tids, ['EST', 'TOK'], ['status' => 0]);
				require_once libfile('function/cache');
				updatecache('globalstick');
				break;

			case 'UEH':
				table_forum_thread::t()->update($actionarray[$action], ['highlight' => 0], true);
				table_forum_threadmod::t()->update_by_tid_action($tids, ['EHL', 'CCK'], ['status' => 0]);
				break;

			case 'UEC':
			case 'UEO':
				$closed = $action == 'UEO' ? 1 : 0;
				table_forum_thread::t()->update($actionarray[$action], ['closed' => $closed], true);
				table_forum_threadmod::t()->update_by_tid_action($tids, ['EOP', 'ECL', 'CLK'], ['status' => 0]);
				break;

			case 'UED':
				table_forum_threadmod::t()->update_by_tid_action($tids, ['EDI'], ['status' => 0]);
				$digestarray = $authoridarry = [];
				foreach(table_forum_thread::t()->fetch_all_by_tid($actionarray[$action]) as $digest) {
					$authoridarry[] = $digest['authorid'];
					$digestarray[$digest['digest']][] = $digest['authorid'];
				}
				foreach($digestarray as $digest => $authorids) {
					batchupdatecredit('digest', $authorids, ["digestposts=digestposts+'-1'"], -$digest, $fid = 0);
				}
				table_forum_thread::t()->update($actionarray[$action], ['digest' => 0], true);
				break;

			case 'SPD':
				table_forum_thread::t()->update($actionarray[$action], ['stamp' => -1], true);
				table_forum_threadmod::t()->update_by_tid_action($tids, ['SPA'], ['status' => 0]);
				break;

		}
	}

	require_once libfile('function/post');

	foreach($actionarray as $action => $tids) {
		updatemodlog(implode(',', $tids), $action, 0, 1);
	}

}

