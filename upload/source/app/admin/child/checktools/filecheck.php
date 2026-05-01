<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$homecheck = !empty($_GET['homecheck']);

if(!$homecheck) {
	$step = max(1, intval($_GET['step']));
	shownav('tools', 'nav_filecheck');
	showsubmenusteps('nav_filecheck', [
		['nav_filecheck_confirm', $step == 1],
		['nav_filecheck_verify', $step == 2],
		['nav_filecheck_completed', $step == 3]
	]);
} else {
	define('FOOTERDISABLED', true);
	$step = 3;
}

if($step == 1) {
	cpmsg(cplang('filecheck_tips_step1'), 'action=checktools&operation=filecheck&step=2', 'button', '', FALSE);
} elseif($step == 2) {
	cpmsg(cplang('filecheck_verifying'), 'action=checktools&operation=filecheck&step=3', 'loading', '', FALSE);
} elseif($step == 3) {

	$result = (new check())->run();

	if($result === false) {
		if(!$homecheck) {
			cpmsg('filecheck_nofound_md5file', '', 'error');
		} else {
			ajaxshowheader();
			ajaxshowfooter();
		}
	} else {
		list($modifiedfiles, $deletedfiles, $unknownfiles, $doubt, $dirlist) = $result;
	}

	if($homecheck) {
		ajaxshowheader();
		echo "<div><em class=\"".($modifiedfiles ? 'edited' : 'correct')."\">{$lang['filecheck_modify']}<span class=\"bignum\">$modifiedfiles</span></em>".
			"<em class=\"".($deletedfiles ? 'del' : 'correct')."\">{$lang['filecheck_delete']}<span class=\"bignum\">$deletedfiles</span></em>".
			"<em class=\"unknown\">{$lang['filecheck_unknown']}<span class=\"bignum\">$unknownfiles</span></em>".
			"<em class=\"unknown\">{$lang['filecheck_doubt']}<span class=\"bignum\">$doubt</span></em></div><p>".
			$lang['filecheck_last_homecheck'].': '.dgmdate(TIMESTAMP, 'u').' <a href="'.ADMINSCRIPT.'?action=checktools&operation=filecheck&step=3">['.$lang['filecheck_view_list'].']</a></p>';
		ajaxshowfooter();
	}

	$result = $resultjs = '';
	$dirnum = 0;
	foreach($dirlist as $status => $filelist) {
		$dirnum++;
		$class = $status == 'modify' ? 'edited' : ($status == 'del' ? 'del' : 'unknown');
		$result .= '<tbody id="status_'.$status.'" style="display:'.($status != 'modify' ? 'none' : '').'">';
		foreach($filelist as $dir => $files) {
			$result .= '<tr><td colspan="4"><div class="ofolder">'.$dir.'</div><div class="margintop marginbot">';
			foreach($files as $filename => $file) {
				$result .= '<tr><td><em class="files bold">'.$filename.'</em></td><td style="text-align: right">'.$file[0].'&nbsp;&nbsp;</td><td>'.$file[1].'</td><td><em class="'.$class.'">&nbsp;</em></td></tr>';
			}
		}
		$result .= '</tbody>';
		$resultjs .= '$(\'status_'.$status.'\').style.display=\'none\';';
	}

	$result .= '<script>function showresult(o) {'.$resultjs.'$(\'status_\' + o).style.display=\'\';}</script>';
	showtips('filecheck_tips');
	showboxheader('filecheck_completed');
	echo '<div>'.
		"<em class=\"edited\">{$lang['filecheck_modify']}: $modifiedfiles</em> ".($modifiedfiles > 0 ? "<a href=\"###\" onclick=\"showresult('modify')\">[{$lang['view']}]</a> " : '').
		" &nbsp; <em class=\"del\">{$lang['filecheck_delete']}: $deletedfiles</em> ".($deletedfiles > 0 ? "<a href=\"###\" onclick=\"showresult('del')\">[{$lang['view']}]</a> " : '').
		" &nbsp; <em class=\"unknown\">{$lang['filecheck_unknown']}: $unknownfiles</em> ".($unknownfiles > 0 ? "<a href=\"###\" onclick=\"showresult('add')\">[{$lang['view']}]</a> " : '').
		($doubt > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;<em class=\"unknown\">{$lang['filecheck_doubt']}: $doubt</em> <a href=\"###\" onclick=\"showresult('doubt')\">[{$lang['view']}]</a> " : '').
		"</div></div><div class=\"boxbody\">";
	showtableheader();
	showsubtitle(['filename', '', 'lastmodified', '']);
	echo $result;
	showtablefooter();
	showboxfooter();

}
	