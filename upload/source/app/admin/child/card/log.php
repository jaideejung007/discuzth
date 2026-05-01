<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showformheader('card&operation=log&');
showtableheader();

$perpage = max(20, empty($_GET['perpage']) ? 20 : intval($_GET['perpage']));
$start_limit = ($page - 1) * $perpage;

$do = in_array($_GET['do'], ['add', 'task', 'del', 'cron']) ? $_GET['do'] : 'add';
$operation = 0;
switch($do) {
	case 'add':
		$operation = 1;
		break;
	case 'task':
		$operation = 2;
		break;
	case 'del':
		$operation = 3;
		break;
	case 'cron':
		$operation = 9;
		break;
}

if($do == 'add' || $do == 'task') {
	$showtabletitle = [
		cplang('time'),
		cplang('card_log_operation'),
		cplang('card_log_user'),
		cplang('card_log_rule'),
		cplang('card_log_add_info'),
		cplang('card_log_description')
	];
} elseif($do == 'del') {
	$showtabletitle = [
		cplang('time'),
		cplang('card_log_operation'),
		cplang('card_log_user'),
		cplang('card_log_del_info')
	];

} elseif($do == 'cron') {
	$showtabletitle = [
		cplang('time'),
		cplang('card_log_operation'),
		cplang('card_log_cron_info')
	];
}

showtablerow('class="header"', ['class="td21"', 'class="td23"', 'class="td23"', 'class="td21"', 'class="td23"'], $showtabletitle);

$count = table_common_card_log::t()->count_by_operation($operation);
if($count) {
	$url = ADMINSCRIPT.'?action=card&operation=log&do='.$do.'&page='.$page.'&perpage='.$perpage;
	$multipage = multi($count, $perpage, $page, $url, 0, 3);

	foreach(table_common_card_log::t()->fetch_all_by_operation($operation, $start_limit, $perpage) as $result) {
		$result['info_arr'] = dunserialize($result['info']);
		if($result['operation'] == 1 || $result['operation'] == 2) {
			$result['cardrule_arr'] = dunserialize($result['cardrule']);
			$showrule = [
				$result['cardrule_arr']['rule'],
				cplang('card_log_price').' : '.$result['cardrule_arr']['price'].cplang('card_make_price_unit'),
				cplang('card_log_make_num').' : '.$result['cardrule_arr']['num'],
				cplang('card_extcreditsval').' : '.$result['cardrule_arr']['extcreditsval'].$_G['setting']['extcredits'][$result['cardrule_arr']['extcreditskey']]['title'],
				cplang('card_make_cleardateline').' : '.($result['cardrule_arr']['cleardateline'] ? dgmdate($result['cardrule_arr']['cleardateline'], 'Y-m-d H:i') : cplang('card_make_cleardateline_none')),
			];

			$showinfo = [
				cplang('succeed_num').' : '.$result['info_arr']['succeed_num'],
				cplang('fail_num').' : '.$result['info_arr']['fail_num']
			];
			$showtablerow = [
				dgmdate($result['dateline']),
				$result['operation'] == 1 ? cplang('card_log_operation_add') : cplang('card_log_operation_task'),
				$result['username'],
				implode('<br />', $showrule),
				implode('<br />', $showinfo),
				$result['description']
			];
		} elseif($result['operation'] == 3 || $result['operation'] == 9) {
			$showinfo = [
				cplang('card_log_num').$result['info_arr']['num'],
			];
			$showtablerow = $result['operation'] == 3 ? [
				dgmdate($result['dateline']),
				cplang('card_log_operation_del'),
				$result['username'],
				implode('<br />', $showinfo),
			] : [
				dgmdate($result['dateline']),
				cplang('card_log_operation_cron'),
				implode('<br />', $showinfo),
			];
		}
		showtablerow('', ['class="smallefont"'], $showtablerow);
	}
} else {

}
showsubmit('', '', '', '', $multipage);
showtablefooter();
showformfooter();
	