<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtips('forums_edit_perm_tips');
echo '<style>'.
	' .permtable{ width: auto !important; }'.
	' .permtable td{ padding: 5px; border-bottom: 1px solid #E6E6E6;}'.
	' .permtable .header.noborder td { border: none; }'.
	' .permtable .header th, .permtable .header td {background: var(--admincp-bgf2) !important;}</style>';

$forum['extra'] = dunserialize($forum['extra']);

$titles = ['', '', cplang('forums_edit_permformula_cell'),];
$chkalls = ['', '', '',];
$widths = ['width="30"', 'width="150"', 'width="60"'];
$i = 1;
foreach($permnames as $perm => $permname) {
	$titles[] = '<label for="chkall'.$i.'"><b>'.$permname.'</b></label>';
	$chkalls[] = '<input class="checkbox" type="checkbox" name="chkall'.$i.'" onclick="checkAll(\'prefix\', this.form, \'^'.$perm.'\', \'chkall'.$i.'\')" id="chkall'.$i.'" />';
	$widths[] = 'width=60';
	$i++;
}
showtableheader('forums_edit_perm_forum', 'noborder fixpadding permtable');
showtablerow('class="header"', [], $titles);
showtablerow('', $widths, $chkalls);

$permfiles = ['group', 'verify', 'account', 'tag', 'plugin'];
foreach($permfiles as $permfile) {
	require_once childfile('forums/perm_'.$permfile);
}

showtablerow('', 'class="lineheight" colspan="8"', cplang('forums_edit_perm_forum_comment'));

$permformulastr = '<p class="bold" style="margin-bottom: 10px">'.cplang('forums_edit_permformula').':</p>'.cplang('forums_edit_permformula_comment').
	'<table>';
$permtexts = ['forums_edit_perm_view', 'forums_edit_perm_post', 'forums_edit_perm_reply', 'forums_edit_perm_getattach', 'forums_edit_perm_postattach', 'forums_edit_perm_postimage'];
foreach($permnames as $perm => $permname) {
	preg_match("/(^|\t)_formula\[(.+?)\](\t|$)/", $forum[$perm], $r);
	$permformulastr .= '<tr><th>'.$permname.'</th><td>'.
		'<input name="permformula['.$perm.']" value="'.$r[2].'" type="text" class="txt" style="width:500px"></td></tr>';
}
$permformulastr .= '</table>';
showtablerow('', 'class="lineheight" colspan="8"', $permformulastr);
showtablefooter();

showtableheader('forums_edit_perm_formula', 'fixpadding');
$formulareplace .= '\'<u>'.cplang('setting_credits_formula_digestposts').'</u>\',\'<u>'.cplang('setting_credits_formula_posts').'</u>\'';

?>
	<script type="text/JavaScript">
		var extraperms = <?php echo $extraperms;?>;
		function foruminsertunit(text, textend) {
			insertunit($('formulapermnew'), text, textend);
			formulaexp();
		}

		var formulafind = new Array('digestposts', 'posts');
		var formulareplace = new Array(<?php echo $formulareplace?>);

		function formulaexp() {
			var result = $('formulapermnew').value;
			<?php

			$extcreditsbtn = '';
			for($i = 1; $i <= 8; $i++) {
				$extcredittitle = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : cplang('setting_credits_formula_extcredits').$i;
				echo 'result = result.replace(/extcredits'.$i.'/g, \'<u>'.str_replace("'", "\'", $extcredittitle).'</u>\');';
				$extcreditsbtn .= '<a href="###" onclick="foruminsertunit(\'extcredits'.$i.'\')">'.$extcredittitle.'</a> &nbsp;';
			}

			$profilefields = '';
			foreach(table_common_member_profile_setting::t()->fetch_all_by_available_unchangeable(1, 1) as $profilefield) {
				echo 'result = result.replace(/'.$profilefield['fieldid'].'/g, \'<u>'.str_replace("'", "\'", $profilefield['title']).'</u>\');';
				$profilefields .= '<a href="###" onclick="foruminsertunit(\' '.$profilefield['fieldid'].' \')">&nbsp;'.$profilefield['title'].'&nbsp;</a>&nbsp;';
			}

			echo 'result = result.replace(/regdate/g, \'<u>'.cplang('forums_edit_perm_formula_regdate').'</u>\');';
			echo 'result = result.replace(/regday/g, \'<u>'.cplang('forums_edit_perm_formula_regday').'</u>\');';
			echo 'result = result.replace(/regip/g, \'<u>'.cplang('forums_edit_perm_formula_regip').'</u>\');';
			echo 'result = result.replace(/lastip/g, \'<u>'.cplang('forums_edit_perm_formula_lastip').'</u>\');';
			echo 'result = result.replace(/buyercredit/g, \'<u>'.cplang('forums_edit_perm_formula_buyercredit').'</u>\');';
			echo 'result = result.replace(/sellercredit/g, \'<u>'.cplang('forums_edit_perm_formula_sellercredit').'</u>\');';
			echo 'result = result.replace(/digestposts/g, \'<u>'.cplang('setting_credits_formula_digestposts').'</u>\');';
			echo 'result = result.replace(/posts/g, \'<u>'.cplang('setting_credits_formula_posts').'</u>\');';
			echo 'result = result.replace(/threads/g, \'<u>'.cplang('setting_credits_formula_threads').'</u>\');';
			echo 'result = result.replace(/oltime/g, \'<u>'.cplang('setting_credits_formula_oltime').'</u>\');';
			echo 'result = result.replace(/and/g, \'&nbsp;&nbsp;<b>'.cplang('forums_edit_perm_formula_and').'</b>&nbsp;&nbsp;\');';
			echo 'result = result.replace(/or/g, \'&nbsp;&nbsp;<b>'.cplang('forums_edit_perm_formula_or').'</b>&nbsp;&nbsp;\');';
			echo 'result = result.replace(/>=/g, \'&ge;\');';
			echo 'result = result.replace(/<=/g, \'&le;\');';
			echo 'result = result.replace(/==/g, \'=\');';

			?>
			$('formulapermexp').innerHTML = result;
		}
	</script>
	<tr>
		<td colspan="2">
			<div class="extcredits">
				<?php echo $extcreditsbtn; ?>
				<a href="###"
				   onclick="foruminsertunit(' regdate ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_regdate') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' regday ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_regday') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' regip ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_regip') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' lastip ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_lastip') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' buyercredit ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_buyercredit') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' sellercredit ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_sellercredit') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' digestposts ')"><?php echo cplang('forums_edit_perm_formula_digestposts') ?></a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' posts ')"><?php echo cplang('forums_edit_perm_formula_posts') ?></a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' threads ')"><?php echo cplang('forums_edit_perm_formula_threads') ?></a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' oltime ')"><?php echo cplang('forums_edit_perm_formula_oltime') ?></a>&nbsp;
				<a href="###" onclick="foruminsertunit(' + ')">&nbsp;+&nbsp;</a>&nbsp;
				<a href="###" onclick="foruminsertunit(' - ')">&nbsp;-&nbsp;</a>&nbsp;
				<a href="###" onclick="foruminsertunit(' * ')">&nbsp;*&nbsp;</a>&nbsp;
				<a href="###" onclick="foruminsertunit(' / ')">&nbsp;/&nbsp;</a>&nbsp;
				<a href="###" onclick="foruminsertunit(' > ')">&nbsp;>&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' >= ')">&nbsp;>=&nbsp;</a>&nbsp;
				<a href="###" onclick="foruminsertunit(' < ')">&nbsp;<&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' <= ')">&nbsp;<=&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' == ')">&nbsp;=&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' != ')">&nbsp;!=&nbsp;</a>&nbsp;
				<a href="###" onclick="foruminsertunit(' (', ') ')">&nbsp;(&nbsp;)&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' and ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_and') ?>
					&nbsp;</a>&nbsp;
				<a href="###"
				   onclick="foruminsertunit(' or ')">&nbsp;<?php echo cplang('forums_edit_perm_formula_or') ?>
					&nbsp;</a>&nbsp;<br/>
				<?php echo $profilefields; ?>


				<div id="formulapermexp"
				     class="margintop marginbot diffcolor2"><?php echo $formulapermexp ?></div>
			</div>
			<textarea name="formulapermnew" id="formulapermnew"
			          class="marginbot" style="width:80%" rows="3"
			          onkeyup="formulaexp()"
			          onkeydown="textareakey(this, event)"><?php echo dhtmlspecialchars($forum['formulaperm']) ?></textarea>
			<script type="text/JavaScript">formulaexp()</script>
			<br/><span
				class="smalltxt"><?php cplang('forums_edit_perm_formula_comment', null, true); ?></span>
		</td>
	</tr>
<?php

showtablefooter();
showtableheader('', 'noborder fixpadding');
$forum['spviewperm'] = explode("\t", $forum['spviewperm']);
showsetting('forums_edit_perm_spview', ['spviewpermnew', $spviewgroup], $forum['spviewperm'], 'mcheckbox');
showsetting('forums_edit_perm_formulapermmessage', 'formulapermmessagenew', $forum['formulapermmessage'], 'textarea');
showtablefooter();
/*search*/