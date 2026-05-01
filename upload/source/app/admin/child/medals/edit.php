<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$medalid = intval($_GET['medalid']);

if(!submitcheck('medaleditsubmit')) {

	$medal = table_forum_medal::t()->fetch($medalid);

	$medal['permission'] = dunserialize($medal['permission']);
	$medal['usergroupallow'] = $medal['permission']['usergroupallow'];
	$medal['usergroups'] = (array)$medal['permission']['usergroups'];
	$medal['permission'] = $medal['permission'][0];

	$credits = [];
	$credits[] = [0, $lang['default']];
	foreach($_G['setting']['extcredits'] as $i => $extcredit) {
		$credits[] = [$i, $extcredit['title']];
	}

	$groupselect = [];
	foreach(table_common_usergroup::t()->range_orderby_credit() as $group) {
		$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'"'.(is_array($medal['usergroups']) && in_array($group['groupid'], $medal['usergroups']) ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
	}
	$usergroups = '<select name="usergroupsnew[]" size="10" multiple="multiple">'.
		'<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';

	shownav('extended', 'nav_medals', 'admin');
	showchildmenu([['nav_medals', 'medals']], $medal['name']);

	showformheader("medals&operation=edit&medalid=$medalid");
	showtableheader('', 'nobottom');
	showsetting('medals_name1', 'namenew', $medal['name'], 'text');
	$image = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
	showsetting('medals_img', '', '', '<input type="text" class="txt" size="30" name="imagenew" value="'.$medal['image'].'" ><img style="max-height:35px;" src="'.$image.'">');
	showsetting('medals_type1', ['typenew', [
		[0, $lang['medals_adminadd'], ['creditdiv' => 'none']],
		[1, $lang['medals_apply_auto'], ['creditdiv' => '']],
		[2, $lang['medals_apply_noauto'], ['creditdiv' => 'none']]
	]], $medal['type'], 'mradio');
	showtagheader('tbody', 'creditdiv', $medal['type'] == 1, 'sub');
	showsetting('medals_credit', ['creditnew', $credits], $medal['credit'], 'select');
	showsetting('medals_price', 'pricenew', $medal['price'], 'text');
	showtagfooter('tbody');
	showsetting('medals_usergroups_allow', 'usergroupallow', $medal['usergroupallow'], 'radio', 0, 1);
	showsetting('medals_usergroups', '', '', $usergroups);
	showtagfooter('tbody');
	showsetting('medals_expr1', 'expirationnew', $medal['expiration'], 'text');
	showsetting('medals_memo', 'descriptionnew', $medal['description'], 'text');
	showtablefooter();

	showtableheader('medals_perm', 'notop');

	$formulareplace .= '\'<u>'.$lang['setting_credits_formula_digestposts'].'</u>\',\'<u>'.$lang['setting_credits_formula_posts'].'</u>\',\'<u>'.$lang['setting_credits_formula_oltime'].'</u>\',\'<u>'.$lang['setting_credits_formula_pageviews'].'</u>\'';

	?>
	<script type="text/JavaScript">
		function medalsinsertunit(text, textend) {
			insertunit($('formulapermnew'), text, textend);
			formulaexp();
		}

		var formulafind = new Array('digestposts', 'posts', 'threads');
		var formulareplace = new Array(<?php echo $formulareplace;?>);

		function formulaexp() {
			var result = $('formulapermnew').value;
			<?php

			$extcreditsbtn = '';
			for($i = 1; $i <= 8; $i++) {
				$extcredittitle = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : $lang['setting_credits_formula_extcredits'].$i;
				echo 'result = result.replace(/extcredits'.$i.'/g, \'<u>'.$extcredittitle.'</u>\');';
				$extcreditsbtn .= '<a href="###" onclick="medalsinsertunit(\'extcredits'.$i.'\')">'.$extcredittitle.'</a> &nbsp;';
			}

			echo 'result = result.replace(/regdate/g, \'<u>'.cplang('forums_edit_perm_formula_regdate').'</u>\');';
			echo 'result = result.replace(/regday/g, \'<u>'.cplang('forums_edit_perm_formula_regday').'</u>\');';
			echo 'result = result.replace(/regip/g, \'<u>'.cplang('forums_edit_perm_formula_regip').'</u>\');';
			echo 'result = result.replace(/lastip/g, \'<u>'.cplang('forums_edit_perm_formula_lastip').'</u>\');';
			echo 'result = result.replace(/buyercredit/g, \'<u>'.cplang('forums_edit_perm_formula_buyercredit').'</u>\');';
			echo 'result = result.replace(/sellercredit/g, \'<u>'.cplang('forums_edit_perm_formula_sellercredit').'</u>\');';
			echo 'result = result.replace(/digestposts/g, \'<u>'.$lang['setting_credits_formula_digestposts'].'</u>\');';
			echo 'result = result.replace(/posts/g, \'<u>'.$lang['setting_credits_formula_posts'].'</u>\');';
			echo 'result = result.replace(/threads/g, \'<u>'.$lang['setting_credits_formula_threads'].'</u>\');';
			echo 'result = result.replace(/oltime/g, \'<u>'.$lang['setting_credits_formula_oltime'].'</u>\');';
			echo 'result = result.replace(/and/g, \'&nbsp;&nbsp;'.$lang['setting_credits_formulaperm_and'].'&nbsp;&nbsp;\');';
			echo 'result = result.replace(/or/g, \'&nbsp;&nbsp;'.$lang['setting_credits_formulaperm_or'].'&nbsp;&nbsp;\');';
			echo 'result = result.replace(/>=/g, \'&ge;\');';
			echo 'result = result.replace(/<=/g, \'&le;\');';

			?>
			$('formulapermexp').innerHTML = result;
		}
	</script>
	<tr>
		<td colspan="2">
			<div class="extcredits">
				<?php echo $extcreditsbtn; ?>
				<a href="###"
				   onclick="medalsinsertunit(' regdate ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_regdate') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' regday ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_regday') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' regip ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_regip') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' lastip ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_lastip') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' buyercredit ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_buyercredit') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' sellercredit ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_sellercredit') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' digestposts ')"><?php echo $lang['setting_credits_formula_digestposts']; ?></a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' posts ')"><?php echo $lang['setting_credits_formula_posts']; ?></a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' threads ')"><?php echo $lang['setting_credits_formula_threads']; ?></a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' oltime ')"><?php echo $lang['setting_credits_formula_oltime']; ?></a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' + ')">&nbsp;+&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' - ')">&nbsp;-&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' * ')">&nbsp;*&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' / ')">&nbsp;/&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' > ')">&nbsp;>&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' >= ')">&nbsp;>=&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' < ')">&nbsp;<&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' <= ')">&nbsp;<=&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' == ')">&nbsp;=&nbsp;</a>&nbsp;
				<a href="###" onclick="medalsinsertunit(' (', ') ')">&nbsp;(&nbsp;)&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' and ')">&nbsp;<?php echo $lang['setting_credits_formulaperm_and']; ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="medalsinsertunit(' or ')">&nbsp;<?php echo $lang['setting_credits_formulaperm_or']; ?>
					&nbsp;</a>&nbsp;<br/>
			</div>
			<div id="formulapermexp" class="marginbot diffcolor2"><?php echo $formulapermexp; ?></div>
			<textarea name="formulapermnew" id="formulapermnew" style="width: 80%" rows="3"
			          onkeyup="formulaexp()"
			          onkeydown="textareakey(this, event)"><?php echo dhtmlspecialchars($medal['permission']); ?></textarea>
			<br/><span class="smalltxt"><?php echo $lang['medals_permformula']; ?></span>
			<br/><?php echo $lang['creditwizard_current_formula_notice']; ?>
			<script type="text/JavaScript">formulaexp()</script>
		</td>
	</tr>
	<?php
	showsubmit('medaleditsubmit');
	showtablefooter();
	showformfooter();

} else {
	if(!checkformulaperm($_GET['formulapermnew'])) {
		cpmsg('forums_formulaperm_error', '', 'error');
	}

	$formulapermary[0] = $_GET['formulapermnew'];
	$formulapermary[1] = preg_replace(
		['/(digestposts|posts|threads|oltime|extcredits[1-8])/', '/(regdate|regday|regip|lastip|buyercredit|sellercredit|field\d+)/'],
		["getuserprofile('\\1')", "\$memberformula['\\1']"],
		$_GET['formulapermnew']);
	$formulapermary['usergroupallow'] = $_GET['usergroupallow'];
	$formulapermary['usergroups'] = (array)$_GET['usergroupsnew'];
	$formulapermnew = serialize($formulapermary);

	$update = [
		'type' => $_GET['typenew'],
		'description' => dhtmlspecialchars($_GET['descriptionnew']),
		'expiration' => intval($_GET['expirationnew']),
		'permission' => $formulapermnew,
		'image' => $_GET['imagenew'],
		'credit' => $_GET['creditnew'],
		'price' => $_GET['pricenew'],
	];
	if($_GET['namenew']) {
		$update['name'] = dhtmlspecialchars($_GET['namenew']);
	}
	table_forum_medal::t()->update($medalid, $update);

	updatecache('medals');
	cpmsg('medals_succeed', 'action=medals&do=editmedals', 'succeed');
}
	