<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('rewardsubmit')) {

	shownav('user', 'nav_members_reward');
	showsubmenusteps('nav_members_reward', [
		['nav_members_select', !$_GET['submit']],
		['nav_members_reward', $_GET['submit']],
	]);

	showsearchform('reward');

	if(submitcheck('submit', 1)) {

		$membernum = countmembers($search_condition, $urladd);
		showtagheader('div', 'reward', TRUE);
		showformheader('members&operation=reward'.$urladd);
		showboxheader('', 'tb1');

		if(!$membernum) {
			echo '<div class="lineheight">'.$lang['members_search_nonexistence'].'</div>';
			showboxfooter();
		} else {

			$creditscols = ['credits_title'];
			$creditsvalue = $resetcredits = [];
			$js_extcreditids = '';
			for($i = 1; $i <= 8; $i++) {
				$js_extcreditids .= (isset($_G['setting']['extcredits'][$i]) ? ($js_extcreditids ? ',' : '').$i : '');
				$creditscols[] = isset($_G['setting']['extcredits'][$i]) ? $_G['setting']['extcredits'][$i]['title'] : 'extcredits'.$i;
				$creditsvalue[] = isset($_G['setting']['extcredits'][$i]) ? '<input type="text" class="txt" size="3" id="addextcredits['.$i.']" name="addextcredits['.$i.']" value="0"> '.$_G['setting']['extcredits'][$i]['unit'] : '<input type="text" class="txt" size="3" value="N/A" disabled>';
				$resetcredits[] = isset($_G['setting']['extcredits'][$i]) ? '<input type="checkbox" id="resetextcredits['.$i.']" name="resetextcredits['.$i.']" value="1" class="radio" disabled> '.$_G['setting']['extcredits'][$i]['unit'] : '<input type="checkbox" disabled  class="radio">';
			}
			$creditsvalue = array_merge(['<input type="radio" name="updatecredittype" id="updatecredittype0" value="0" class="radio" onclick="var extcredits = new Array('.$js_extcreditids.'); for(k in extcredits) {$(\'resetextcredits[\'+extcredits[k]+\']\').disabled = true; $(\'addextcredits[\'+extcredits[k]+\']\').disabled = false;}" checked="checked" /><label for="updatecredittype0">'.$lang['members_reward_value'].'</label>'], $creditsvalue);
			$resetcredits = array_merge(['<input type="radio" name="updatecredittype" id="updatecredittype1" value="1" class="radio" onclick="var extcredits = new Array('.$js_extcreditids.'); for(k in extcredits) {$(\'addextcredits[\'+extcredits[k]+\']\').disabled = true; $(\'resetextcredits[\'+extcredits[k]+\']\').disabled = false;}" /><label for="updatecredittype1">'.$lang['members_reward_clean'].'</label>'], $resetcredits);

			showboxrow('', ['class="dcol"', 'class="dcol"'], [
				cplang('members_reward_members'),
				cplang('members_search_result', ['membernum' => $membernum])."<a href=\"###\" onclick=\"$('searchmembers').style.display='';$('reward').style.display='none';$('step1').className='current';$('step2').className='';\" class=\"act\">{$lang['research']}</a>"
			]);
			showboxfooter();

			showboxheader('nav_members_reward', 'nobottom');
			showtableheader('', 'noborder');
			showsubtitle($creditscols);
			showtablerow('', ['class="td23"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"'], $creditsvalue);
			showtablerow('', ['class="td23"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"'], $resetcredits);
			showtablefooter();
			showboxfooter();

			showtagheader('div', 'messagebody');
			shownewsletter();
			showtagfooter('div');
			showtableheader();
			showsubmit('rewardsubmit', 'submit', '', '<input class="checkbox" type="checkbox" name="notifymember" value="1" onclick="$(\'messagebody\').style.display = this.checked ? \'\' : \'none\'" id="credits_notify" /><label for="credits_notify">'.cplang('members_reward_notify').'</label>');
			showtablefooter();

		}

		showformfooter();
		showtagfooter('div');

	}

} else {
	if(!empty($_POST['conditions'])) $search_condition = dunserialize($_POST['conditions']);
	$membernum = countmembers($search_condition, $urladd);
	notifymembers('reward', 'creditsnotify');

}
	