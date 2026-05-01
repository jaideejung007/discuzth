<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$step = max(1, intval($_GET['step']));
shownav('tools', 'nav_hookcheck');
showsubmenusteps('nav_hookcheck', [
	['nav_hookcheck_confirm', $step == 1],
	['nav_hookcheck_verify', $step == 2],
	['nav_hookcheck_completed', $step == 3]
]);
showtips('hookcheck_tips');
if($step == 1) {
	$styleselect = "<br><br><select name=\"styleid\">";
	foreach(table_common_style::t()->fetch_all_data() as $style) {
		$styleselect .= "<option value=\"{$style['styleid']}\" ".
			($style['styleid'] == $_G['setting']['styleid'] ? 'selected="selected"' : NULL).
			">{$style['name']}</option>\n";
	}
	$styleselect .= '</select>';
	cpmsg(cplang('hookcheck_tips_step1', ['template' => $styleselect]), 'action=checktools&operation=hookcheck&step=2', 'form', '', FALSE);
} elseif($step == 2) {
	cpmsg(cplang('hookcheck_verifying'), "action=checktools&operation=hookcheck&step=3&styleid={$_POST['styleid']}", 'loading', '', FALSE);
} elseif($step == 3) {
	if(!$discuzfiles = @file('./source/data/admincp/discuzhook.dat')) {
		cpmsg('filecheck_nofound_md5file', '', 'error');
	}

	$discuzhookdata = $hookdata = [];
	$discuzhookdata_hook = [];

	$styleid = intval($_GET['styleid']);
	if(!$styleid) {
		$styleid = $_G['setting']['styleid'];
	}
	$style = table_common_style::t()->fetch_by_styleid($styleid);
	checkhook(substr($style['directory'], 2).'/', '\.htm|\.php', 1);

	foreach($discuzfiles as $line) {
		list($file, $hook) = explode(' *', trim($line));
		if($hook) {
			$discuzhookdata[$file][$hook][] = $hook;
			$discuzhookdata_hook[$file][] = $hook;
		}
	}

	$diffhooklist = $difffilelist = $inotherfilelist = [];
	$diffnum = 0;
	foreach($discuzhookdata as $file => $hook) {
		$dir = dirname($file);
		$filen = str_replace('template/default/', substr($style['directory'], 2).'/', $file);
		if(isset($hookdata[$filen])) {
			foreach($hook as $k => $hookarr) {
				$hooknum = empty($hookarr) ? 0 : count($hookarr);
				$hookdatanum = empty($hookdata[$filen][$k]) ? 0 : count($hookdata[$filen][$k]);
				if(($diff = $hooknum - $hookdatanum) > 0) {
					for($i = 0; $i < $diff; $i++) {
						$diffhooklist[$file][] = $k;
					}
				}
			}
			if(!empty($diffhooklist[$file])) {
				$difffilelist[$dir][] = $file;
				$diffnum++;
			}
		}
	}

	foreach($diffhooklist as $file => $hooks) {
		foreach($hooks as $hook) {
			$exists = false;
			foreach($hookdata as $_file => $hookarr) {
				if(isset($hookarr[$hook])) {
					$exists = $_file;
					break;
				}
			}
			if($exists) {
				$inotherfilelist[$hook][] = $exists;
			}
		}
	}

	foreach($difffilelist as $dir => $files) {
		$dir = str_replace('template/default/', substr($style['directory'], 2).'/', $dir);
		$result .= '<tbody><tr><td class="td30"><a href="javascript:;" onclick="toggle_group(\'dir_'.$dir.'\')" id="a_dir_'.$dir.'">[-]</a></td><td colspan="3"><div class="ofolder">'.$dir.'</div></td></tr></tbody>';
		$result .= '<tbody id="dir_'.$dir.'">';
		foreach($files as $file) {
			$result .= '<tr><td></td><td><em class="files bold">'.basename($file).'</em></td><td>';
			foreach($discuzhookdata_hook[$file] as $hook) {
				$result .= '<p>'.dhtmlspecialchars($hook).'</p>';
			}
			$result .= '</td><td>';
			foreach($diffhooklist[$file] as $hook) {
				$result .= '<p>'.dhtmlspecialchars($hook);
				if(!empty($inotherfilelist[$hook])) {
					$result .= '<span class="xg1">('.str_replace(substr($style['directory'], 2), '', implode(', ', $inotherfilelist[$hook])).')</span>';
				}
				$result .= '</p>';
			}
			$result .= '</td></tr>';
		}
		$result .= '</tbody>';
	}
	if($diffnum > 20) {
		$result .= '<script type="text/javascript">hide_all_hook(\'dir_\', \'tbody\');</script>';
	}
	if($diffnum) {
		showformheader('forums');
		showtableheader('hookcheck_completed');
		showtablerow('', 'colspan="4"', "<div class=\"margintop marginbot\">".
			'<a href="javascript:;" onclick="show_all_hook(\'dir_\', \'tbody\')">'.$lang['show_all'].'</a> | <a href="javascript:;" onclick="hide_all_hook(\'dir_\', \'tbody\')">'.$lang['hide_all'].'</a>'.
			" &nbsp; <em class=\"del\">{$lang['hookcheck_delete']}: $diffnum</em> ".
			'</div>');
		showsubtitle(['', 'filename', 'hookcheck_discuzhook', 'hookcheck_delhook']);
		echo $result;
		showtablefooter();
		showformfooter();
	} else {
		cpmsg('hookcheck_nodelhook', '', 'succeed', '', FALSE);
	}
}
	