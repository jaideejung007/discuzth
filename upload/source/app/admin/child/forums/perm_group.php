<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$spviewgroup = [];
foreach(['member', 'special', 'specialadmin', 'system'] as $type) {
	$tgroups = is_array($groups[$type]) ? $groups[$type] : [];
	if($tgroups) {
		$s = '';
		$exists = false;
		foreach($tgroups as $group) {
			if($group['groupid'] != 1) {
				$spviewgroup[] = [$group['groupid'], $group['grouptitle']];
			}
			$colums = [
				'<input class="checkbox" title="'.cplang('select_all').'" type="checkbox" name="chkallv'.$group['groupid'].'" onclick="checkAll(\'value\', this.form, '.$group['groupid'].', \'chkallv'.$group['groupid'].'\')" id="chkallv_'.$group['groupid'].'" />',
				'<label for="chkallv_'.$group['groupid'].'"> '.$group['grouptitle'].'</label>', 'g'.$group['groupid']];
			foreach($perms as $perm) {
				$checked = str_contains($forum[$perm], "\t{$group['groupid']}\t") ? 'checked="checked"' : NULL;
				$checked && $exists = true;
				$colums[] = '<input class="checkbox" type="checkbox" name="'.$perm.'[]" value="'.$group['groupid'].'" chkvalue="'.$group['groupid'].'" '.$checked.'>';
			}
			$s .= showtablerow('', ['', '', 'class="lightfont"'], $colums, true);
		}

		$tg = '<a href="javascript:;" onclick="toggle_group(\'ggroup_'.$type.'\', this)">['.($exists ? '-' : '+').']</a>';
		showtablerow('', ['', '', 'class="lightfont" colspan="'.$permcolspan.'"'], [$tg, '<b>'.cplang('usergroups_'.$type).'</b>', 'group']);
		showtagheader('tbody', 'ggroup_'.$type, $exists);
		echo $s;
		showtagfooter('tbody');
	}
}