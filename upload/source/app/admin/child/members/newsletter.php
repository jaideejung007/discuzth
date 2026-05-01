<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('newslettersubmit')) {
	loadcache('newsletter_detail');
	$newletter_detail = get_newsletter('newsletter_detail');
	$newletter_detail = dunserialize($newletter_detail);
	if($newletter_detail && $newletter_detail['uid'] == $_G['uid']) {
		if($_GET['goon'] == 'yes') {
			cpmsg("{$lang['members_newsletter_send']}: ".cplang('members_newsletter_processing', ['current' => $newletter_detail['current'], 'next' => $newletter_detail['next'], 'search_condition' => $newletter_detail['search_condition']]), $newletter_detail['action'], 'loadingform');
		} elseif($_GET['goon'] == 'no') {
			del_newsletter('newsletter_detail');
		} else {
			cpmsg('members_edit_continue', '', '', '', '<input type="button" class="btn" value="'.$lang['ok'].'" onclick="location.href=\''.ADMINSCRIPT.'?action=members&operation=newsletter&goon=yes\'">&nbsp;&nbsp;<input type="button" class="btn" value="'.$lang['cancel'].'" onclick="location.href=\''.ADMINSCRIPT.'?action=members&operation=newsletter&goon=no\';">');
			exit;
		}
	}
	if($_GET['do'] == 'sms') {
		shownav('user', 'nav_members_newsletter_sms');
		showsubmenusteps('nav_members_newsletter_sms', [
			['nav_members_select', !$_GET['submit']],
			['nav_members_notify', $_GET['submit']],
		]);
		showtips('members_newsletter_sms_tips');
	} else if($_GET['do'] == 'mobile') {
		shownav('user', 'nav_members_newsletter_mobile');
		showsubmenusteps('nav_members_newsletter_mobile', [
			['nav_members_select', !$_GET['submit']],
			['nav_members_notify', $_GET['submit']],
		]);
		showtips('members_newsletter_mobile_tips');
	} else {
		shownav('user', 'nav_members_newsletter');
		showsubmenusteps('nav_members_newsletter', [
			['nav_members_select', !$_GET['submit']],
			['nav_members_notify', $_GET['submit']],
		], [], [['members_grouppmlist_newsletter', 'members&operation=newsletter', 1], ['members_grouppmlist', 'members&operation=grouppmlist', 0]]);
	}
	showsearchform('newsletter');

	if(submitcheck('submit', 1)) {
		$dostr = '';
		if($_GET['do'] == 'sms') {
			$search_condition['secmobile'] = true;
			$dostr = '&do=sms';
		} else if($_GET['do'] == 'mobile') {
			$search_condition['token_noempty'] = 'token';
			$dostr = '&do=mobile';
		}
		$membernum = countmembers($search_condition, $urladd);

		showtagheader('div', 'newsletter', TRUE);
		showformheader('members&operation=newsletter'.$urladd.$dostr);
		showhiddenfields(['notifymember' => 1]);
		showboxheader('', 'tb1');

		if(!$membernum) {
			echo '<div class="lineheight">'.$lang['members_search_nonexistence'].'</div>';
			showboxfooter();
		} else {
			showboxrow('', ['class="dcol"', 'class="dcol"'], [
				cplang('members_newsletter_members'),
				cplang('members_search_result', ['membernum' => $membernum])."<a href=\"###\" onclick=\"$('searchmembers').style.display='';$('newsletter').style.display='none';$('step1').className='current';$('step2').className='';\" class=\"act\">{$lang['research']}</a>"
			]);
			showboxfooter();

			shownewsletter();

			$search_condition = serialize($search_condition);
			showtableheader();
			showsubmit('newslettersubmit', 'submit', '', '<input type="hidden" name="conditions" value=\''.$search_condition.'\' />');
			showtablefooter();
		}

		showformfooter();
		showtagfooter('div');

	}

} else {

	$search_condition = dunserialize($_POST['conditions']);
	$membernum = countmembers($search_condition, $urladd);
	notifymembers('newsletter', 'newsletter');

}
	