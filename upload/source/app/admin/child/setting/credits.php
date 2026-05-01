<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	$extcredits_exists = 0;
	foreach($settingnew['extcredits'] as $val) {
		if(isset($val['available']) && $val['available'] == 1) {
			$extcredits_exists = 1;
			break;
		}
	}
	if(!$extcredits_exists) {
		cpmsg('setting_extcredits_must_available');
	}
	if($settingnew['report_reward']) {
		$settingnew['report_reward']['min'] = intval($settingnew['report_reward']['min']);
		$settingnew['report_reward']['max'] = intval($settingnew['report_reward']['max']);
		if($settingnew['report_reward']['min'] > $settingnew['report_reward']['max']) {
			unset($settingnew['report_reward']);
		}
		if($settingnew['report_reward']['min'] == $settingnew['report_reward']['max']) {
			$settingnew['report_reward'] = ['min' => 0, 'max' => 0];
		}
		$settingnew['report_reward'] = serialize($settingnew['report_reward']);
	}
	$settingnew['creditspolicy'] = @dunserialize($setting['creditspolicy']);
	$settingnew['creditspolicy']['lowerlimit'] = [];
	foreach($settingnew['lowerlimit'] as $key => $value) {
		if($settingnew['extcredits'][$key]['available'] && $value !== '') {
			$settingnew['creditspolicy']['lowerlimit'][$key] = (float)$value;
		}
	}
	unset($settingnew['lowerlimit']);

	if(isset($settingnew['creditsformula']) && isset($settingnew['extcredits']) && isset($settingnew['initcredits']) && isset($settingnew['creditstrans']) && isset($settingnew['creditstax'])) {
		if(empty($settingnew['creditsformula']) || !checkformulacredits($settingnew['creditsformula'])) {
			cpmsg('setting_creditsformula_invalid', '', 'error');
		}

		$extcreditsarray = [];
		if(is_array($settingnew['extcredits'])) {
			foreach($settingnew['extcredits'] as $key => $value) {
				if($value['available'] && !$value['title']) {
					cpmsg('setting_credits_title_invalid', '', 'error');
				}
				$extcreditsarray[$key] =
					[
						'img' => dhtmlspecialchars($value['img']),
						'title' => dhtmlspecialchars($value['title']),
						'unit' => dhtmlspecialchars($value['unit']),
						'ratio' => ($value['ratio'] > 0 ? (float)$value['ratio'] : 0),
						'available' => $value['available'],
						'showinthread' => $value['showinthread'],
						'allowexchangein' => $value['allowexchangein'],
						'allowexchangeout' => $value['allowexchangeout'],
					];
			}
		}

		for($si = 0; $si < 12; $si++) {
			$creditstransi = $si > 0 && !$settingnew['creditstrans'][$si] ? $settingnew['creditstrans'][0] : $settingnew['creditstrans'][$si];
			if($creditstransi && empty($settingnew['extcredits'][$creditstransi]['available']) && $settingnew['creditstrans'][$si] != -1) {
				cpmsg('setting_creditstrans_invalid', '', 'error');
			}
			if(!isset($settingnew['creditstrans'][$si])) {
				$settingnew['creditstrans'][$si] = 0;
			}
		}
		ksort($settingnew['creditstrans']);

		$settingnew['creditsformulaexp'] = $settingnew['creditsformula'];
		foreach(['digestposts', 'posts', 'threads', 'oltime', 'friends', 'doings', 'blogs', 'albums', 'polls', 'sharings', 'extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8'] as $var) {
			if($extcreditsarray[$creditsid = preg_replace('/^extcredits(\d{1})$/', "\\1", $var)]['available']) {
				$replacement = $extcreditsarray[$creditsid]['title'];
			} else {
				$replacement = '{credits_'.strtoupper($var).'}';
			}
			$settingnew['creditsformulaexp'] = str_replace($var, '<u>'.$replacement.'</u>', $settingnew['creditsformulaexp']);
		}
		$settingnew['creditsformulaexp'] = addslashes('<u>{credits_CREDITS}</u>='.$settingnew['creditsformulaexp']);

		$initformula = str_replace('posts', '0', $settingnew['creditsformula']);
		$initformula = str_replace('digest0', '0', $initformula);

		for($i = 1; $i <= 8; $i++) {
			$settingnew['initcredits'][$i] = intval($settingnew['initcredits'][$i]);
			$initformula = str_replace('extcredits'.$i, $settingnew['initcredits'][$i], $initformula);
		}
		$initformula = preg_replace('/[A-Za-z]{1}\w+/', '0', $initformula);
		eval("\$_G['setting']['initcredits'] = round($initformula);");

		$settingnew['extcredits'] = $extcreditsarray;
		$settingnew['initcredits'] = $_G['setting']['initcredits'].','.implode(',', $settingnew['initcredits']);
		if($settingnew['creditstax'] < 0 || $settingnew['creditstax'] >= 1) {
			$settingnew['creditstax'] = 0;
		}

		$settingnew['creditstrans'] = implode(',', $settingnew['creditstrans']);
	}
} else {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['base', 'policytable']) ? $_GET['anchor'] : 'base';
	$current = [$_GET['anchor'] => 1];
	showsubmenu('setting_credits', [
		['setting_credits_base', 'setting&operation=credits&anchor=base', $current['base']],
		['setting_credits_policy', 'credits&operation=list&anchor=policytable', $current['policytable']],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$rules = [];
	foreach(table_common_credit_rule::t()->fetch_all_rule() as $value) {
		$rules[$value['rid']] = $value;
	}

	echo '<div id="base"'.($_GET['anchor'] != 'base' ? ' style="display: none"' : '').'>';

	/*search={"setting_credits":"action=setting&operation=credits","setting_credits_base":"action=setting&operation=credits&anchor=base"}*/
	$setting['extcredits'] = dunserialize($setting['extcredits']);
	$setting['initcredits'] = explode(',', $setting['initcredits']);
	$extcreditsbtn = '';
	for($i = 1; $i <= 8; $i++) {
		$extcredittitle = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : cplang('setting_credits_formula_extcredits').$i;
		$resultstr .= 'result = result.replace(/extcredits'.$i.'/g, \'<u>'.str_replace("'", "\'", $extcredittitle).'</u>\');'."\r\n";
		$extcreditsbtn .= '<a href="###" onclick="creditinsertunit(\'extcredits'.$i.'\')">'.$extcredittitle.'</a> &nbsp;';
	}
	$formulareplace .= '\'<u>'.cplang('setting_credits_formula_digestposts').'</u>\',\'<u>'.cplang('setting_credits_formula_posts').'</u>\'';

	showtableheader('setting_credits_extended', 'fixpadding');
	$title = $creditsetting = [];
	for($i = 1; $i <= 8; $i++) {
		if($i == 1) {
			$title[] = '<font style="font:12px normal normal">'.cplang('setting_credits_available').'</font>';
			$creditsetting[0] = '<td class="td23">'.cplang('credits_title').'</td>';
			$creditsetting[2] = '<td class="td23">'.cplang('credits_img').'</td>';
			$creditsetting[3] = '<td class="td23">'.cplang('credits_unit').'</td>';
			$creditsetting[4] = '<td class="td23">'.cplang('setting_credits_init').'</td>';
			$creditsetting[5] = '<td class="td23">'.cplang('setting_credits_lower_limit').'</td>';
			$creditsetting[6] = '<td class="td23">'.cplang('setting_credits_ratio').'</td>';
			$creditsetting[7] = '<td class="td23">'.cplang('credits_inport').'</td>';
			$creditsetting[8] = '<td class="td23">'.cplang('credits_import').'</td>';
		}
		$title[] = "<input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[extcredits][$i][available]\" value=\"1\" ".($setting['extcredits'][$i]['available'] ? 'checked' : '')." />extcredits$i";
		$creditsetting[0] .= "<td class=\"td32\"><input type=\"text\" class=\"txt\" name=\"settingnew[extcredits][$i][title]\" value=\"{$setting['extcredits'][$i]['title']}\"></td>";
		$creditsetting[2] .= "<td class=\"td32\"><input type=\"text\" class=\"txt\" style=\"margin-right:0\" name=\"settingnew[extcredits][$i][img]\" value=\"{$setting['extcredits'][$i]['img']}\">".($setting['extcredits'][$i]['img'] ? ' <img src="'.$setting['extcredits'][$i]['img'].'" class="vmiddle" />' : '').'</td>';
		$creditsetting[3] .= "<td class=\"td32\"><input type=\"text\" class=\"txt\" name=\"settingnew[extcredits][$i][unit]\" value=\"{$setting['extcredits'][$i]['unit']}\"></td>";
		$creditsetting[4] .= "<td class=\"td32\"><input type=\"text\" class=\"txt\" name=\"settingnew[initcredits][$i]\" value=\"".intval($setting['initcredits'][$i])."\"></td>";
		$creditsetting[5] .= "<td class=\"td32\"><input type=\"text\" class=\"txt\" name=\"settingnew[lowerlimit][$i]\" value=\"{$_G['setting']['creditspolicy']['lowerlimit'][$i]}\"></td>";
		$creditsetting[6] .= "<td class=\"td32\"><input type=\"text\" class=\"txt\" name=\"settingnew[extcredits][$i][ratio]\" value=\"".(float)$setting['extcredits'][$i]['ratio']."\" onkeyup=\"if(this.value != '0' && \$('allowexchangeout$i').checked == false && \$('allowexchangein$i').checked == false) {\$('allowexchangeout$i').checked = true;\$('allowexchangein$i').checked = true;} else if(this.value == '0') {\$('allowexchangeout$i').checked = false;\$('allowexchangein$i').checked = false;}\"></td>";
		$creditsetting[7] .= "<td class=\"td32\"><input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[extcredits][$i][allowexchangeout]\" value=\"1\" ".($setting['extcredits'][$i]['allowexchangeout'] ? 'checked' : '')." id=\"allowexchangeout$i\"></td>";
		$creditsetting[8] .= "<td class=\"td32\"><input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[extcredits][$i][allowexchangein]\" value=\"1\" ".($setting['extcredits'][$i]['allowexchangein'] ? 'checked' : '')." id=\"allowexchangein$i\"></td>";
	}
	showsubtitle($title, 'header sml');
	echo '<tr>'.implode('</tr><tr>', $creditsetting).'</tr>';
	showtablerow('', 'colspan="9" class="lineheight"', $lang['setting_credits_extended_comment']);

	showtableheader('setting_credits');
	?>
	<script type="text/JavaScript">
		function isUndefined(variable) {
			return typeof variable == 'undefined' ? true : false;
		}

		function creditinsertunit(text, textend) {
			insertunit($('creditsformula'), text, textend);
			formulaexp();
		}

		var formulafind = new Array('digestposts', 'posts');
		var formulareplace = new Array(<?php echo $formulareplace?>);

		function formulaexp() {
			var result = $('creditsformula').value;
			<?php
			echo $resultstr;
			echo 'result = result.replace(/digestposts/g, \'<u>'.$lang['setting_credits_formula_digestposts'].'</u>\');';
			echo 'result = result.replace(/posts/g, \'<u>'.$lang['setting_credits_formula_posts'].'</u>\');';
			echo 'result = result.replace(/threads/g, \'<u>'.$lang['setting_credits_formula_threads'].'</u>\');';
			echo 'result = result.replace(/oltime/g, \'<u>'.$lang['setting_credits_formula_oltime'].'</u>\');';
			echo 'result = result.replace(/friends/g, \'<u>'.$lang['setting_credits_formula_friends'].'</u>\');';
			echo 'result = result.replace(/doings/g, \'<u>'.$lang['setting_credits_formula_doings'].'</u>\');';
			echo 'result = result.replace(/blogs/g, \'<u>'.$lang['setting_credits_formula_blogs'].'</u>\');';
			echo 'result = result.replace(/albums/g, \'<u>'.$lang['setting_credits_formula_albums'].'</u>\');';
			echo 'result = result.replace(/sharings/g, \'<u>'.$lang['setting_credits_formula_sharings'].'</u>\');';
			?>
			$('formulapermexp').innerHTML = result;
		}

	</script>

	<?php
	print <<<EOF
			<tr>
				<td class="td27" colspan="2">{$lang['setting_credits_formula']}:</td>
			</tr>
			<tr>
				<td colspan="2" class="rowform">
					<div class="extcredits">
						$extcreditsbtn
						<a href="###" onclick="creditinsertunit(' posts ')">{$lang['setting_credits_formula_posts']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' threads ')">{$lang['setting_credits_formula_threads']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' digestposts ')">{$lang['setting_credits_formula_digestposts']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' oltime ')">{$lang['setting_credits_formula_oltime']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' friends ')">{$lang['setting_credits_formula_friends']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' doings ')">{$lang['setting_credits_formula_doings']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' blogs ')">{$lang['setting_credits_formula_blogs']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' albums ')">{$lang['setting_credits_formula_albums']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' sharings ')">{$lang['setting_credits_formula_sharings']}</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' + ')">&nbsp;+&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' - ')">&nbsp;-&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' * ')">&nbsp;*&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' / ')">&nbsp;/&nbsp;</a>&nbsp;
						<a href="###" onclick="creditinsertunit(' (', ') ')">&nbsp;(&nbsp;)&nbsp;</a>&nbsp;
					</div>
					<div id="formulapermexp" class="margintop marginbot diffcolor2">$formulapermexp</div>
					<textarea name="settingnew[creditsformula]" id="creditsformula" class="marginbot" style="width:80%" rows="3" onkeyup="formulaexp()" onkeydown="textareakey(this, event)">{$setting['creditsformula']}</textarea>
					<script type="text/JavaScript">formulaexp()</script>
					<br /><span class="smalltxt">{$lang['setting_credits_formula_comment']}</span>
				</td>
			</tr>
EOF;

	$setting['creditstrans'] = explode(',', $setting['creditstrans']);
	$_G['setting']['creditstrans'] = [];
	for($si = 0; $si < 14; $si++) {
		$_G['setting']['creditstrans'][$si] = '';
		for($i = 0; $i <= 8; $i++) {
			$_G['setting']['creditstrans'][$si] .= '<option value="'.$i.'" '.($i == $setting['creditstrans'][$si] ? 'selected' : '').'>'.($i ? 'extcredits'.$i.($setting['extcredits'][$i]['title'] ? '('.$setting['extcredits'][$i]['title'].')' : '') : ($si > 0 ? ($si != 11 ? $lang['setting_credits_trans_used'] : $lang['setting_credits_trans_credits']) : $lang['none'])).'</option>';
		}
	}
	showsetting('setting_credits_trans', '', '', '<select onchange="if(this.value > 0) {$(\'creditstransextra\').style.display = \'\';} else {$(\'creditstransextra\').style.display = \'none\';}" name="settingnew[creditstrans][0]">'.$_G['setting']['creditstrans'][0].'</select>');
	showtagheader('tbody', 'creditstransextra', $setting['creditstrans'][0], 'sub');
	showsetting('setting_credits_trans9', '', '', '<select name="settingnew[creditstrans][9]">'.$_G['setting']['creditstrans'][9].'</select>');
	showsetting('setting_credits_trans1', '', '', '<select name="settingnew[creditstrans][1]">'.$_G['setting']['creditstrans'][1].'</select>');
	showsetting('setting_credits_trans2', '', '', '<select name="settingnew[creditstrans][2]">'.$_G['setting']['creditstrans'][2].'</select>');
	showsetting('setting_credits_trans3', '', '', '<select name="settingnew[creditstrans][3]">'.$_G['setting']['creditstrans'][3].'</select>');
	showhiddenfields(['settingnew[creditstrans][4]' => 0]);
	showsetting('setting_credits_trans5', '', '', '<select name="settingnew[creditstrans][5]"><option value="-1">'.$lang['setting_credits_trans5_none'].'</option>'.$_G['setting']['creditstrans'][5].'</select>');
	showsetting('setting_credits_trans6', '', '', '<select name="settingnew[creditstrans][6]">'.$_G['setting']['creditstrans'][6].'</select>');
	$setting['report_reward'] = dunserialize($setting['report_reward']);
	showsetting('setting_credits_trans10', '', '', '<select name="settingnew[creditstrans][10]">'.$_G['setting']['creditstrans'][10].'</select>');
	showsetting('setting_credits_trans8', '', '', '<select name="settingnew[creditstrans][8]">'.$_G['setting']['creditstrans'][8].'</select><br \><br \>'.cplang('report_reward_min').': <input type="text" size="3" name="settingnew[report_reward][min]" value="'.$setting['report_reward']['min'].'"><br />'.cplang('report_reward_max').': <input type="text" size="3" name="settingnew[report_reward][max]" value="'.$setting['report_reward']['max'].'">&nbsp;&nbsp;<br \>'.cplang('report_reward_comment'));
	showsetting('setting_credits_trans11', '', '', '<select name="settingnew[creditstrans][11]">'.$_G['setting']['creditstrans'][11].'</select>');
	showsetting('setting_credits_trans12', '', '', '<select name="settingnew[creditstrans][12]">'.$_G['setting']['creditstrans'][12].'</select>');
	showsetting('setting_credits_trans13', '', '', '<select name="settingnew[creditstrans][13]">'.$_G['setting']['creditstrans'][13].'</select>');

	showtagfooter('tbody');
	showsetting('setting_credits_tax', 'settingnew[creditstax]', $setting['creditstax'], 'text');
	showsetting('setting_credits_mintransfer', 'settingnew[transfermincredits]', $setting['transfermincredits'], 'text');
	showsetting('setting_credits_minexchange', 'settingnew[exchangemincredits]', $setting['exchangemincredits'], 'text');
	showsetting('setting_credits_maxincperthread', 'settingnew[maxincperthread]', $setting['maxincperthread'], 'text');
	showsetting('setting_credits_maxchargespan', 'settingnew[maxchargespan]', $setting['maxchargespan'], 'text');
	showtablefooter();
	echo '</div>';
	showtableheader();
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}