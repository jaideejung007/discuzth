<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('sorttemplatesubmit')) {
	$threadtype = table_forum_threadtype::t()->fetch($_GET['sortid']);
	$showoption = '';
	$typevararr = table_forum_typevar::t()->fetch_all_by_sortid($_GET['sortid'], 'ASC');
	$typeoptionarr = table_forum_typeoption::t()->fetch_all(array_keys($typevararr));
	foreach($typevararr as $option) {
		$option['title'] = $typeoptionarr[$option['optionid']]['title'];
		$option['type'] = $typeoptionarr[$option['optionid']]['type'];
		$option['identifier'] = $typeoptionarr[$option['optionid']]['identifier'];
		$showoption .= '<button onclick="settip(this, \''.$option['identifier'].'\')" type="button" style="margin: 5px 5px 5px 0px;">'.$option['title'].'</button>&nbsp;&nbsp;';
	}
	unset($typevararr, $typeoptionarr);
	require_once libfile('function/discuzcode');
	$name = discuzcode($threadtype['name'], 0, 0, 0, 1, 1, 0, 0, 0, 0, 0);
	showchildmenu([['threadtype_infotypes', 'threadtypes']], $name, [
		['config', 'threadtypes&operation=sortdetail&sortid='.$_GET['sortid'], 0],
		['threadtype_template', 'threadtypes&operation=sorttemplate&sortid='.$_GET['sortid'], 1],
	]);
	showtips('threadtype_tips');
	showtableheader();
	echo '<tr><td>';
	showformheader('threadtypes&operation=sorttemplate&sortid='.$_GET['sortid']);
	echo '<script type="text/JavaScript">var currentAnchor = \'ltype\';</script>'.
		'<div class="itemtitle" style="width:100%;margin-bottom:5px;padding-left: 0px;"><ul class="tab1" id="submenu">'.
		'<li id="nav_ttype" onclick="showanchor(this)" class="current"><a href="#"><span>'.$lang['threadtype_template_viewthread'].'</span></a></li>'.
		'<li id="nav_stype" onclick="showanchor(this)"><a href="#"><span>'.$lang['threadtype_template_forumdisplay'].'</span></a></li>'.
		'<li id="nav_ptype" onclick="showanchor(this)"><a href="#"><span>'.$lang['threadtype_template_post'].'</span></a></li>'.
		'<li id="nav_btype" onclick="showanchor(this)"><a href="#"><span>'.$lang['threadtype_template_diy'].'</span></a></li>'.
		'</ul></div>';

	echo '<div id="ttype">'.
		$showoption.
		'<div id="ttype_tip"></div>'.
		'<textarea cols="100" rows="15" id="ttypetemplate" name="typetemplate" style="width: 95%;" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.dhtmlspecialchars($threadtype['template']).'</textarea>'.
		'</div>';

	echo '<div id="stype" style="display:none">'.
		'<button onclick="settip(this, \'subject\', \'subject/'.$lang['threadtype_template_threadtitle'].'|subject_url/'.$lang['threadtype_template_threadurl'].'|tid/'.$lang['threadtype_template_threadid'].'\')" type="button">'.$lang['threadtype_template_threadtitle'].'</button>&nbsp;&nbsp;'.
		'<textarea id="subject_sample" style="display:none" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)"><a href="{subject_url}">{subject}</a></textarea>'.
		'<button onclick="settip(this, \'\', \'dateline/'.$lang['threadtype_template_dateline'].'\')" type="button">'.$lang['threadtype_template_dateline'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'author\', \'author/'.$lang['threadtype_template_author'].'|authorid/'.$lang['threadtype_template_authorid'].'|author_url/'.$lang['threadtype_template_authorurl'].'|avatar_small/'.$lang['threadtype_template_authoravatar'].'|author_verify/'.$lang['threadtype_template_authorverify'].'\')" type="button">'.$lang['threadtype_template_threadauthor'].'</button>&nbsp;&nbsp;'.
		'<textarea id="author_sample" style="display:none" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)"><a href="{author_url}">{author}</a></textarea>'.
		'<button onclick="settip(this, \'\', \'views/'.$lang['threadtype_template_threadviews'].'\')" type="button">'.$lang['threadtype_template_threadviews'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'\', \'replies/'.$lang['threadtype_template_threadreplies'].'\')" type="button">'.$lang['threadtype_template_threadreplies'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'lastpost\', \'lastpost/'.$lang['threadtype_template_lastpostdateline'].'|lastpost_url/'.$lang['threadtype_template_lastposturl'].'|lastposter/'.$lang['threadtype_template_lastpostuser'].'|lastposter_url/'.$lang['threadtype_template_lastpostuserurl'].'\')" type="button">'.$lang['threadtype_template_lastpost'].'</button>&nbsp;&nbsp;'.
		'<textarea id="lastpost_sample" style="display:none" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)"><a href="{lastpost_url}">{lastpost}</a> by <a href="{lastposter_url}">{lastposter}</a></textarea>'.
		'<button onclick="settip(this, \'typename\', \'typename/'.$lang['threadtype_template_threadtypename'].'|typename_url/'.$lang['threadtype_template_threadtypeurl'].'\')" type="button">'.$lang['threadtype_template_threadtype'].'</button>&nbsp;&nbsp;'.
		'<textarea id="typename_sample" style="display:none" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)"><a href="{typename_url}">{typename}</a></textarea>'.
		'<button onclick="settip(this, \'\', \'attachment/'.$lang['threadtype_template_attachmentexist'].'\')" type="button">'.$lang['threadtype_template_attachment'].'</button>&nbsp;&nbsp'.
		'<button onclick="settip(this, \'\', \'modcheck/'.$lang['threadtype_template_modcheck'].'\')" type="button">'.$lang['threadtype_template_threadmod'].'</button>&nbsp;&nbsp'.
		'<button onclick="settip(this, \'loop\', \'/'.$lang['threadtype_template_loop'].'\')" type="button">[loop]...[/loop]</button>&nbsp;&nbsp;'.
		'<textarea id="loop_sample" style="display:none" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">
<table><tr><td>'.$lang['threadtype_template_title'].'</td></tr>
[loop]<tr><td><a href="{subject_url}">{subject}</a></td></tr>[/loop]
</table>
			</textarea>'.
		'<br />'.
		$showoption.
		'<div id="stype_tip"></div>'.
		'<textarea cols="100" rows="15" id="stypetemplate" name="stypetemplate" style="width: 95%;" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.dhtmlspecialchars($threadtype['stemplate']).'</textarea>'.
		'</div>';

	echo '<div id="ptype" style="display:none">'.
		$showoption.
		'<div id="ptype_tip"></div>'.
		'<textarea cols="100" rows="15" id="ptypetemplate" name="ptypetemplate" style="width: 95%;" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.dhtmlspecialchars($threadtype['ptemplate']).'</textarea>'.
		'</div>';

	echo '<div id="btype" style="display:none">'.
		'<button onclick="settip(this, \'subject\', \'subject/'.$lang['threadtype_template_threadtitle'].'|subject_url/'.$lang['threadtype_template_threadurl'].'|tid/'.$lang['threadtype_template_threadid'].'\')" type="button">'.$lang['threadtype_template_threadtitle'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'\', \'dateline/'.$lang['threadtype_template_dateline'].'\')" type="button">'.$lang['threadtype_template_dateline'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'author\', \'author/'.$lang['threadtype_template_author'].'|authorid/'.$lang['threadtype_template_authorid'].'|author_url/'.$lang['threadtype_template_authorurl'].'|avatar_small/'.$lang['threadtype_template_authoravatar'].'|author_verify/'.$lang['threadtype_template_authorverify'].'\')" type="button">'.$lang['threadtype_template_threadauthor'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'\', \'views/'.$lang['threadtype_template_threadviews'].'\')" type="button">'.$lang['threadtype_template_threadviews'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'\', \'replies/'.$lang['threadtype_template_threadreplies'].'\')" type="button">'.$lang['threadtype_template_threadreplies'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'lastpost\', \'lastpost/'.$lang['threadtype_template_lastpostdateline'].'|lastpost_url/'.$lang['threadtype_template_lastposturl'].'|lastposter/'.$lang['threadtype_template_lastpostuser'].'|lastposter_url/'.$lang['threadtype_template_lastpostuserurl'].'\')" type="button">'.$lang['threadtype_template_lastpost'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'typename\', \'typename/'.$lang['threadtype_template_threadtypename'].'|typename_url/'.$lang['threadtype_template_threadtypeurl'].'\')" type="button">'.$lang['threadtype_template_threadtype'].'</button>&nbsp;&nbsp;'.
		'<button onclick="settip(this, \'\', \'attachment/'.$lang['threadtype_template_attachmentexist'].'\')" type="button">'.$lang['threadtype_template_attachment'].'</button>&nbsp;&nbsp'.
		'<button onclick="settip(this, \'loop\', \'/'.$lang['threadtype_template_loop'].'\')" type="button">[loop]...[/loop]</button>&nbsp;&nbsp;'.
		'<br />'.
		$showoption.
		'<div id="btype_tip"></div>'.
		'<textarea cols="100" rows="15" id="btypetemplate" name="btypetemplate" style="width: 95%;" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.dhtmlspecialchars($threadtype['btemplate']).'</textarea>'.
		'</div>'.
		'<div class="fixsel"><input type="submit" class="btn" name="sorttemplatesubmit" value="'.$lang['submit'].'"></div></form>';

	echo '<script>
		function settip(obj, id, tips) {
			var tips = !tips ? 0 : tips.split(\'|\');
			var tipid = obj.parentNode.id + \'_tip\', s1 = \'\', s2 = \'\', s3 = \'\';
			if(!tips) {
				s1 += \'<td>{\' + id + \'}</td>\';
				s2 += \'<td>'.$lang['threadtype_template_varname'].'(\' + obj.innerHTML + \')</td>\';
				s1 += \'<td>{\' + id + \'_value}</td>\';
				s2 += \'<td>'.$lang['threadtype_template_varvalue'].'</td>\';
				s1 += \'<td>{\' + id + \'_unit}</td>\';
				s2 += \'<td>'.$lang['threadtype_template_varunit'].'</td>\';
				if(obj.parentNode.id == \'ptype\') {
					s1 += \'<td>{\' + id + \'_required}</td>\';
					s2 += \'<td>'.$lang['threadtype_template_requiredflag'].'</td>\';
					s1 += \'<td>{\' + id + \'_tips}</td>\';
					s2 += \'<td>'.$lang['threadtype_template_tipflag'].'</td>\';
					s1 += \'<td>{\' + id + \'_description}</td>\';
					s2 += \'<td>'.$lang['threadtype_template_briefdes'].'</td>\';
				}
				if(obj.parentNode.id == \'ptype\') {
					s3 = \'<dt><strong class="rq">{\' + id + \'_required}</strong>{\' + id + \'}</dt><dd>{\' + id + \'_value} {\' + id + \'_unit} {\' + id + \'_tips} {\' + id + \'_description}</dd>\r\n\';
				} else {
					s3 = obj.parentNode.id == \'ttype\' ? \'<dt>{\' + id + \'}:</dt><dd>{\' + id + \'_value} {\' + id + \'_unit}</dd>\r\n\' : \'<p><em>{\' + id + \'}:</em>{\' + id + \'_value} {\' + id + \'_unit}</p>\r\n\';
				}
			} else {
				for(i = 0;i < tips.length;i++) {
					var i0 = tips[i].substr(0, tips[i].indexOf(\'/\'));
					var i1 = tips[i].substr(tips[i].indexOf(\'/\') + 1);
					if(i0) {
						s1 += \'<td>{\' + i0 + \'}</td>\';
					}
					s2 += \'<td>\' + i1 + \'</td>\';
				}
				if($(id + \'_sample\')) {
					s3 = $(id + \'_sample\').innerHTML;
				}
			}
			$(tipid).innerHTML = \'<table class="tb tb2">\' +
				(s1 ? \'<tr><td class="bold" width="75">'.$lang['threadtype_template_tag'].'</td>\' + s1 + \'</tr>\' : \'\') +
				\'<tr><td class="bold" width="75">'.$lang['threadtype_template_intro'].'</td>\' + s2 + \'</tr></table>\';
			if(s3) {
				$(tipid).innerHTML += \'<table class="tb tb2"><tr><td class="bold" width="75">'.$lang['threadtype_template_example'].'</td><td colspan="6"><textarea style="width: 95%;" rows="2" readonly onclick="this.select()" id="\' + obj.parentNode.id + \'_sample">\' + s3 + \'</textarea></td></tr></table>\';
			}
		}
		</script>';
	echo '</td></tr>';
	showtablefooter();
} else {
	table_forum_threadtype::t()->update($_GET['sortid'], [
		'special' => 1,
		'template' => $_GET['typetemplate'],
		'stemplate' => $_GET['stypetemplate'],
		'ptemplate' => $_GET['ptypetemplate'],
		'btemplate' => $_GET['btypetemplate'],
		'expiration' => $_GET['typeexpiration'],
	]);
	updatecache('threadsorts');
	cpmsg('threadtype_infotypes_succeed', 'action=threadtypes&operation=sorttemplate&sortid='.$_GET['sortid'], 'succeed');
}
	