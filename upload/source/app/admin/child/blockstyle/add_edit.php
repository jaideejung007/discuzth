<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($operation != 'edit') {
	showsubmenu('blockstyle', [
		['list', 'blockstyle', 0],
		['add', 'blockstyle&operation=add', 1]
	]);
}

include_once libfile('function/block');

if(empty($_GET['blockclass'])) {

	$blockclass_sel = '<select name="blockclass">';
	$blockclass_sel .= '<option value="">'.cplang('blockstyle_blockclass_sel').'</option>';
	foreach($_G['cache']['blockclass'] as $key => $value) {
		foreach($value['subs'] as $subkey => $subvalue) {
			$blockclass_sel .= "<option value=\"$subkey\">{$subvalue['name']}</option>";
		}
	}
	$blockclass_sel .= '</select>';
	$adminscript = ADMINSCRIPT;
	$lang_blockclasssel = cplang('blockstyle_blockclass_sel');
	$lang_submit = cplang('submit');
	echo <<<BLOCKCLASSSEL
<form method="post" autocomplete="off" action="$adminscript">
	<div style="margin-top:8px;">
		<table class="tb tb2 nobottom nobdb" cellspacing="3" cellpadding="3">
			<tr>
				<th class="td27">$lang_blockclasssel</th>
			</tr>
			<tr>
				<td class="vtop rowform">$blockclass_sel</td>
			</tr>
			<tr>
				<td class="vtop rowform">
					<input type="hidden" name="action" value="blockstyle" />
					<input type="hidden" name="operation" value="add" />
					<div class="fixsel"><input type="submit" value="$lang_submit" class="btn" /></div>
				</td>
			</tr>
		</table>
	</div>
</form>
BLOCKCLASSSEL;

} else {

	if(submitcheck('stylesubmit')) {
		$arr = [
			'name' => $_POST['name'],
			'blockclass' => $_GET['blockclass'],
		];

		include_once libfile('function/block');
		block_parse_template($_POST['template'], $arr);

		if($_GET['styleid']) {
			$styleid = intval($_GET['styleid']);
			table_common_block_style::t()->update($styleid, $arr);
			require_once libfile('function/block');
			blockclass_cache();
			cpmsg('blockstyle_edit_succeed', 'action=blockstyle&operation=edit&blockclass='.$_GET['blockclass'].'&styleid='.$styleid.'&preview='.($_POST['preview'] ? '1' : '0'), 'succeed');
		} else {
			$styleid = table_common_block_style::t()->insert($arr, true);
			$msg = 'blockstyle_create_succeed';
			require_once libfile('function/block');
			blockclass_cache();
			cpmsg('blockstyle_create_succeed', 'action=blockstyle&operation=edit&blockclass='.$_GET['blockclass'].'&styleid='.$styleid.'&preview='.($_POST['preview'] ? '1' : '0'), 'succeed');
		}
	}

	if($_GET['styleid']) {
		$_GET['styleid'] = intval($_GET['styleid']);
		include_once libfile('function/block');
		$thestyle = block_getstyle($_GET['styleid']);
		if(!$thestyle) {
			cpmsg('blockstyle_not_found!');
		}
		$thestyle['template'] = block_build_template($thestyle['template']);

		$_GET['blockclass'] = $thestyle['blockclass'];
	} else {
		$_GET['styleid'] = 0;
		$thestyle = [
			'template' => "<div class=\"module cl\">\n<ul>\n[loop]\n\t<li><a href=\"{url}\"{target}>{title}</a></li>\n[/loop]\n</ul>\n</div>"
		];
	}

	showchildmenu([['blockstyle', 'blockstyle']], $thestyle['name']);

	showtips('blockstyle_add_tips');

	$theclass = block_getclass($_GET['blockclass']);

	if($preview) {
		echo '<h4 style="margin-bottom:15px;">'.lang('preview').'</h4>'.$preview;
	}

	showformheader('blockstyle&operation='.$operation.'&blockclass='.$_GET['blockclass'].'&styleid='.$_GET['styleid']);
	showtableheader('', 'nobottom');
	if($_GET['styleid']) {
		showtitle('blockstyle_add_editstyle');
	} else {
		showtitle('blockstyle_add_addstyle');
	}
	showsetting('blockstyle_name', 'name', $thestyle['name'], 'text');
	showtablefooter();

	$template = '';
	foreach($theclass['fields'] as $key => $value) {
		if($value['name']) {
			$template .= $value['name'].': <a href="###" onclick="insertunit($(\'jstemplate\'), \'{'.$key.'}\')">{'.$key.'}</a>';
		}
	}
	$template .= '<br />';
	$template .= cplang('blockstyle_add_loop').': <a href="###" onclick="insertunit($(\'jstemplate\'), \'[loop]\n\n[/loop]\')">[loop]...[/loop]</a>';
	$template .= cplang('blockstyle_add_order').': <a href="###" onclick="insertunit($(\'jstemplate\'), \'[order=N]\n\n[/order]\')">[order=N]...[/order]</a>';
	$template .= cplang('blockstyle_add_index').': <a href="###" onclick="insertunit($(\'jstemplate\'), \'[index=N]\n\n[/index]\')">[index=N]...[/index]</a>';
	$template .= cplang('blockstyle_add_urltitle').': <a href="###" onclick=\'insertunit($("jstemplate"), "<a href=\"{url}\"{target}>{title}</a>")\'>&lt;a href=...</a>';
	$template .= cplang('blockstyle_add_picthumb').': <a href="###" onclick=\'insertunit($("jstemplate"), "<img src=\"{pic}\" width=\"{picwidth}\" height=\"{picheight}\" />")\'>&lt;img src=...&gt;</a>';
	if(in_array($_GET['blockclass'], ['forum_thread', 'portal_article', 'group_thread'], true)) {
		$template .= cplang('blockstyle_add_moreurl').': <a href="###" onclick="insertunit($(\'jstemplate\'), \'{moreurl}\')">{moreurl}</a>';
	}
	$template .= cplang('blockstyle_add_currentorder').': <a href="###" onclick="insertunit($(\'jstemplate\'), \'{currentorder}\')">{currentorder}</a>';
	$template .= cplang('blockstyle_add_parity').': <a href="###" onclick="insertunit($(\'jstemplate\'), \'{parity}\')">{parity}</a>';
	$template .= '</div><br />';
	$template .= '<textarea cols="100" rows="5" id="jstemplate" name="template" style="width: 95%;" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.$thestyle['template'].'</textarea>';
	$template .= '<input type="hidden" name="preview" value="0" /><input type="hidden" name="stylesubmit" value="1" />';
	$template .= '<br /><!--input type="button" class="btn" onclick="this.form.preview=\'1\';this.form.submit()" value="'.$lang['preview'].'">&nbsp; &nbsp;--><input type="submit" class="btn" style="margin-left: 0px;" value="'.$lang['submit'].'"></div>';
	echo '<div class="colorbox" style="padding-bottom: 10px;">';
	echo '<div class="extcredits">';
	echo $template;
	echo '</div>';

	showformfooter();
}
	