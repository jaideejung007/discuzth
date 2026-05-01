<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$medals = '';
foreach(table_forum_medal::t()->fetch_all_data(1) as $medal) {
	$image = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
	$medals .= showtablerow('', ['class="td25"', 'class="td23"'], [
		"<input class=\"checkbox\" type=\"checkbox\" name=\"medals[{$medal['medalid']}]\" value=\"1\" />",
		"<img src=\"$image\" />",
		$medal['name']
	], TRUE);
}

if(!$medals) {
	cpmsg('members_edit_medals_nonexistence', 'action=medals', 'error');
}

if(!submitcheck('confermedalsubmit')) {

	shownav('extended', 'nav_medals', 'nav_members_confermedal');
	showsubmenusteps('nav_members_confermedal', [
		['nav_members_select', !$_GET['submit']],
		['nav_members_confermedal', $_GET['submit']],
	], [
		['admin', 'medals', 0],
		['nav_medals_confer', 'members&operation=confermedal', 1],
		['nav_medals_mod', 'medals&operation=mod', 0]
	]);

	showsearchform('confermedal');

	if(submitcheck('submit', 1)) {

		$membernum = countmembers($search_condition, $urladd);

		showtagheader('div', 'confermedal', TRUE);
		showformheader('members&operation=confermedal'.$urladd);
		showboxheader('', 'tb1');

		if(!$membernum) {
			echo '<div class="lineheight">'.$lang['members_search_nonexistence'].'</div>';
			showboxfooter();
		} else {

			showboxrow('', ['class="dcol"', 'class="dcol"'], [
				cplang('members_confermedal_members'),
				cplang('members_search_result', ['membernum' => $membernum])."<a href=\"###\" onclick=\"$('searchmembers').style.display='';$('confermedal').style.display='none';$('step1').className='current';$('step2').className='';\" class=\"act\">{$lang['research']}</a>"
			]);
			showboxfooter();

			showboxheader('members_confermedal');
			showtableheader('', 'noborder');
			showsubtitle(['medals_grant', 'medals_image', 'name']);
			echo $medals;
			showtablefooter();
			showboxfooter();

			showtagheader('div', 'messagebody');
			shownewsletter();
			showtagfooter('div');
			showsubmit('confermedalsubmit', 'submit', 'td', '<input class="checkbox" type="checkbox" name="notifymember" value="1" onclick="$(\'messagebody\').style.display = this.checked ? \'\' : \'none\'" id="grant_notify"/><label for="grant_notify">'.cplang('medals_grant_notify').'</label>');

		}

		showformfooter();
		showtagfooter('div');

	}

} else {
	if(!empty($_POST['conditions'])) $search_condition = dunserialize($_POST['conditions']);
	$membernum = countmembers($search_condition, $urladd);
	notifymembers('confermedal', 'medalletter');

}
	