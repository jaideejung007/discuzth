<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($operation)) {

	$pid = !empty($_GET['pid']) ? intval($_GET['pid']) : 0;
	$magiccount = table_common_member_magic::t()->count_by_uid($_G['uid']);

	$multipage = multi($magiccount, $_G['tpp'], $page, "home.php?mod=magic&action=mybox&pid=$pid$typeadd");
	$query = table_common_member_magic::t()->fetch_all_magic($_G['uid'], null, $start_limit, $_G['tpp']);
	foreach($query as $value) {
		$magicids[] = $value['magicid'];
	}
	$magicm = table_common_magic::t()->fetch_all($magicids);
	foreach($query as $curmagicid => $mymagic) {
		$mymagic = $mymagic + $magicm[$mymagic['magicid']];
		$eidentifier = explode(':', $mymagic['identifier']);
		if(count($eidentifier) > 1) {
			$mymagic['pic'] = 'source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.gif';
		} else {
			$mymagic['pic'] = STATICURL.'image/magic/'.strtolower($mymagic['identifier']).'.gif';
		}
		$mymagic['weight'] = intval($mymagic['weight'] * $mymagic['num']);
		$mymagiclist[] = $mymagic;
	}
	$navtitle = lang('core', 'title_magics_user');

} else {

	$magicid = intval($_GET['magicid']);
	$membermagic = table_common_member_magic::t()->fetch_magic($_G['uid'], $magicid);
	$magic = $membermagic + table_common_magic::t()->fetch($magicid);

	if(!$membermagic) {
		showmessage('magics_nonexistence');
	} elseif(!$magic['num']) {
		table_common_member_magic::t()->delete_magic($_G['uid'], $magic['magicid']);
		showmessage('magics_nonexistence');
	}
	$magicperm = dunserialize($magic['magicperm']);
	$eidentifier = explode(':', $magic['identifier']);
	if(count($eidentifier) > 1) {
		$magic['pic'] = 'source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.gif';
	} else {
		$magic['pic'] = STATICURL.'image/magic/'.strtolower($magic['identifier']).'.gif';
	}

	if($operation == 'use') {

		$useperm = (strstr($magicperm['usergroups'], "\t{$_G['groupid']}\t") || empty($magicperm['usergroups'])) ? '1' : '0';
		if(!$useperm) {
			showmessage('magics_use_nopermission');
		}

		if($magic['num'] <= 0) {
			table_common_member_magic::t()->delete_magic($_G['uid'], $magic['magicid']);
			showmessage('magics_nopermission');
		}

		$magic['weight'] = intval($magicarray[$magic['magicid']]['weight'] * $magic['num']);

		if(count($eidentifier) > 1) {
			$magicfile = DISCUZ_PLUGIN($eidentifier[0]).'/magic/magic_'.$eidentifier[1].'.php';
			$magicclass = 'magic_'.$eidentifier[1];
		} else {
			$magicfile = DISCUZ_ROOT.'./source/class/magic/magic_'.$magic['identifier'].'.php';
			$magicclass = 'magic_'.$magic['identifier'];
		}

		if(!@include_once $magicfile) {
			showmessage('magics_filename_nonexistence', '', ['file' => basename($magicfile)]);
		}
		$magicclass = new $magicclass;
		$magicclass->magic = $magic;
		$magicclass->parameters = $magicperm;
		$useperoid = magic_peroid($magic, $_G['uid']);

		if(submitcheck('usesubmit')) {
			if(discuz_process::islocked('magiclock_'.$_G['uid'].'_'.$magicid, 0, 1)) {
				showmessage('magics_locked');
			}
			if($useperoid !== true && $useperoid <= 0) {
				showmessage('magics_outofperoid_'.$magic['useperoid'], '', ['usenum' => $magic['usenum']]);
			}
			if(method_exists($magicclass, 'usesubmit')) {
				$magicclass->usesubmit();
			}
			dexit();
		}

		include template('home/space_magic_mybox_opreation');
		dexit();

	} elseif($operation == 'sell') {
		$magic['price'] = $_G['group']['magicsdiscount'] ? intval($magic['price'] * ($_G['group']['magicsdiscount'] / 10)) : intval($magic['price']);
		$discountprice = floor($magic['price'] * $_G['setting']['magicdiscount'] / 100);
		if(!submitcheck('operatesubmit')) {
			include template('home/space_magic_mybox_opreation');
			dexit();
		} else {
			if(discuz_process::islocked('magiclock_'.$_G['uid'].'_'.$magicid, 0, 1)) {
				showmessage('magics_locked');
			}

			$magicnum = intval($_GET['magicnum']);

			if(!$magicnum || $magicnum < 0) {
				showmessage('magics_num_invalid');
			} elseif($magicnum > $magic['num']) {
				showmessage('magics_amount_no_enough');
			}
			usemagic($magic['magicid'], $magic['num'], $magicnum);
			updatemagiclog($magic['magicid'], '2', $magicnum, '0', 0, 'sell');
			$totalprice = $discountprice * $magicnum;
			updatemembercount($_G['uid'], [$magic['credit'] => $totalprice]);
			showmessage('magics_sell_succeed', 'home.php?mod=magic&action=mybox', ['magicname' => $magic['name'], 'num' => $magicnum, 'credit' => $totalprice.' '.$_G['setting']['extcredits'][$magic['credit']]['unit'].$_G['setting']['extcredits'][$magic['credit']]['title']]);
		}

	} elseif($operation == 'drop') {

		if(!submitcheck('operatesubmit')) {
			include template('home/space_magic_mybox_opreation');
			dexit();
		} else {
			$magicnum = intval($_GET['magicnum']);

			if(!$magicnum || $magicnum < 0) {
				showmessage('magics_num_invalid');
			} elseif($magicnum > $magic['num']) {
				showmessage('magics_amount_no_enough');
			}
			usemagic($magic['magicid'], $magic['num'], $magicnum);
			updatemagiclog($magic['magicid'], '2', $magicnum, '0', 0, 'drop');
			showmessage('magics_drop_succeed', 'home.php?mod=magic&action=mybox', ['magicname' => $magic['name'], 'num' => $magicnum], ['locationtime' => true]);
		}

	} elseif($operation == 'give') {

		if($_G['group']['allowmagics'] < 2) {
			showmessage('magics_nopermission');
		}

		if(!submitcheck('operatesubmit')) {

			include libfile('function/friend');
			$buddyarray = friend_list($_G['uid'], 20);

			include template('home/space_magic_mybox_opreation');
			dexit();

		} else {
			if($_G['setting']['submitlock'] && discuz_process::islocked('magiclock_'.$_G['uid'].'_'.$magicid, 0, 1)) {
				showmessage('magics_locked');
			}
			$magicnum = intval($_GET['magicnum']);
			$toname = dhtmlspecialchars(trim($_GET['tousername']));
			if(!$toname) {
				showmessage('magics_username_nonexistence');
			} elseif($magicnum < 0 || $magic['num'] < $magicnum) {
				showmessage('magics_num_invalid');
			}

			$givemessage = dhtmlspecialchars(trim($_GET['givemessage']));
			givemagic($toname, $magic['magicid'], $magicnum, $magic['num'], '0', $givemessage, $magicarray);

		}

	} else {
		showmessage('undefined_action');
	}

}
	