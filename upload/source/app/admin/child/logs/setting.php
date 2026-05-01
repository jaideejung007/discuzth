<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$log = $_G['setting']['log'];
if(!submitcheck('submit')) {
	showformheader('logs&operation=setting');
	showtableheader();
	if($_G['config']['log']['type'] != 'file') {
		$options = [];
		foreach($menu as $row) {
			if(empty($row[0]['menu']) || !str_starts_with($row[0]['menu'], 'nav_logs')) {
				continue;
			}
			foreach($row[0]['submenu'] as $submenu) {
				$txt = $submenu[0];
				parse_str($submenu[1], $p);
				if(empty($p['operation'])) {
					continue;
				}
				if($p['operation'] == 'crime') {
					if(empty($crime)) {
						$options[] = [$p['operation'], cplang('nav_logs_crime')];
						$crime = true;
					}
					continue;
				}
				$options[] = [$p['operation'], cplang($txt)];
			}
		}
		$clearlogstypes = (array)dunserialize($log['clearlogstypes']);
		$allowlogstypes = (array)dunserialize($log['allowlogstypes']);

		showtablefooter();
		showtableheader('', 'noborder fixpadding', 'style="width: auto;"');
		showsubtitle(['name', 'enable', 'logs_clear', 'logs_cleardays']);
		foreach($options as $option) {
			$days = $log['clearlogsdays'][$option[0]] ?? 0;
			showtablerow('', [], [
				$option[1],
				'<input type="checkbox" name="log['.$option[0].'][]" value="1" '.(!empty($log[$option[0]]) ? ' checked' : '').'/>',
				'<input type="checkbox" name="log[clearlogstypes][]" value="'.$option[0].'" '.(in_array($option[0], $clearlogstypes) ? ' checked' : '').'/>',
				'<input type="text" name="log[clearlogsdays]['.$option[0].']" value="'.$days.'" class="txt" style="width: 50px;" />',
			]);
		}
		showtablefooter();
		showtableheader();
	}

	showsubmit('submit');
	showtablefooter();
	showformfooter();
} else {
	$settings = [
		'log' => $_GET['log'],
	];
	table_common_setting::t()->update_batch($settings);
	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=logs', 'succeed');
}
	