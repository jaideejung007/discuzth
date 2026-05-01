<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('faqsubmit')) {

	shownav('extended', 'faq');
	showsubmenu('faq');
	showformheader('faq&operation=list');
	showtableheader();
	echo '<tr><th class="td25"></th><th class="td25"><strong>'.$lang['display_order'].'</stong></th><th class="td25"><strong>'.$lang['faq_thread'].'</strong></th><th></th></tr>';

	$faqparent = $faqsub = [];
	$faqlists = $faqselect = '';
	foreach(table_forum_faq::t()->fetch_all_by_fpid() as $faq) {
		if(empty($faq['fpid'])) {
			$faqparent[$faq['id']] = $faq;
			$faqselect .= "<option value=\"{$faq['id']}\">{$faq['title']}</option>";
		} else {
			$faqsub[$faq['fpid']][] = $faq;
		}
	}

	foreach($faqparent as $parent) {
		$disabled = !empty($faqsub[$parent['id']]) ? 'disabled' : '';
		showtablerow('', ['', 'class="td23 td28"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$parent['id']}\" $disabled>",
			"<input type=\"text\" class=\"txt\" size=\"3\" name=\"displayorder[{$parent['id']}]\" value=\"{$parent['displayorder']}\">",
			"<div class=\"parentnode\"><input type=\"text\" class=\"txt\" size=\"30\" name=\"title[{$parent['id']}]\" value=\"".dhtmlspecialchars($parent['title'])."\"></div>",
			"<a href=\"".ADMINSCRIPT."?action=faq&operation=detail&id={$parent['id']}\" class=\"act\">".$lang['detail'].'</a>'
		]);
		if(!empty($faqsub[$parent['id']])) {
			foreach($faqsub[$parent['id']] as $sub) {
				showtablerow('', ['', 'class="td23 td28"'], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$sub['id']}\">",
					"<input type=\"text\" class=\"txt\" size=\"3\" name=\"displayorder[{$sub['id']}]\" value=\"{$sub['displayorder']}\">",
					"<div class=\"node\"><input type=\"text\" class=\"txt\" size=\"30\" name=\"title[{$sub['id']}]\" value=\"".dhtmlspecialchars($sub['title'])."\"></div>",
					"<a href=\"".ADMINSCRIPT."?action=faq&operation=detail&id={$sub['id']}\" class=\"act\">".$lang['detail'].'</a>'
				]);
			}
		}
		echo '<tr><td></td><td></td><td colspan="2"><div class="lastnode"><a href="###" onclick="addrow(this, 1, '.$parent['id'].')" class="addtr">'.cplang('faq_additem').'</a></div></td></tr>';
	}
	echo '<tr><td></td><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 0, 0)" class="addtr">'.cplang('faq_addcat').'</a></div></td></tr>';

	echo <<<EOT
<script type="text/JavaScript">
var rowtypedata = [
	[[1,''], [1,'<input name="newdisplayorder[]" value="" size="3" type="text" class="txt">', 'td25'], [1, '<input name="newtitle[]" value="" size="30" type="text" class="txt">'], [1, '<input type="hidden" name="newfpid[]" value="0" />']],
	[[1,''], [1,'<input name="newdisplayorder[]" value="" size="3" type="text" class="txt">', 'td25'], [1, '<div class=\"node\"><input name="newtitle[]" value="" size="30" type="text" class="txt"></div>'], [1, '<input type="hidden" name="newfpid[]" value="{1}" />']]
];
</script>
EOT;

	showsubmit('faqsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();

} else {

	if($_GET['delete']) {
		table_forum_faq::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['title'])) {
		foreach($_GET['title'] as $id => $val) {
			table_forum_faq::t()->update($id, [
				'displayorder' => $_GET['displayorder'][$id],
				'title' => $_GET['title'][$id]
			]);
		}
	}

	if(is_array($_GET['newtitle'])) {
		foreach($_GET['newtitle'] as $k => $v) {
			$v = trim($v);
			if($v) {
				table_forum_faq::t()->insert([
					'fpid' => intval($_GET['newfpid'][$k]),
					'displayorder' => intval($_GET['newdisplayorder'][$k]),
					'title' => $v
				]);
			}
		}
	}

	cpmsg('faq_list_update', 'action=faq&operation=list', 'succeed');

}
	