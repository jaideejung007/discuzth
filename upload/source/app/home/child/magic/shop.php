<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$operation = empty($operation) ? 'index' : $operation;

if(in_array($operation, ['index', 'hot'])) {

	$subactives[$operation] = 'class="a"';
	$filteradd = '';
	if($operation == 'index') {
		$navtitle = lang('core', 'title_magics_shop');
	} else {
		$navtitle = lang('core', 'title_magics_hot');
	}

	$magiccount = table_common_magic::t()->count_page($operation);
	$multipage = multi($magiccount, $_G['tpp'], $page, "home.php?mod=magic&action=shop&operation=$operation");

	foreach(table_common_magic::t()->fetch_all_page($operation, $start_limit, $_G['tpp']) as $magic) {
		$magic['discountprice'] = $_G['group']['magicsdiscount'] ? intval($magic['price'] * ($_G['group']['magicsdiscount'] / 10)) : intval($magic['price']);
		$eidentifier = explode(':', $magic['identifier']);
		if(count($eidentifier) > 1) {
			$magic['pic'] = 'source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.gif';
		} else {
			$magic['pic'] = STATICURL.'image/magic/'.strtolower($magic['identifier']).'.gif';
		}
		$magiclist[] = $magic;
	}

	$magiccredits = [];
	foreach($magicarray as $magic) {
		$magiccredits[$magic['credit']] = $magic['credit'];
	}

} elseif($operation == 'buy') {

	$magic = table_common_magic::t()->fetch_by_identifier($_GET['mid']);
	if(!$magic || !$magic['available']) {
		showmessage('magics_nonexistence');
	}
	$magicperm = dunserialize($magic['magicperm']);
	$useperm = (strstr($magicperm['usergroups'], "\t{$_G['groupid']}\t") || empty($magicperm['usergroups'])) ? '1' : '0';
	if(!$useperm) {
		showmessage('magics_use_nopermission');
	}
	$querystring = [];
	foreach($_GET as $k => $v) {
		$querystring[] = rawurlencode($k).'='.rawurlencode($v);
	}
	$querystring = implode('&', $querystring);

	$eidentifier = explode(':', $magic['identifier']);
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
	if(method_exists($magicclass, 'buy')) {
		$magicclass->buy();
	}

	$magic['discountprice'] = $_G['group']['magicsdiscount'] ? intval($magic['price'] * ($_G['group']['magicsdiscount'] / 10)) : intval($magic['price']);
	if(count($eidentifier) > 1) {
		$magic['pic'] = 'source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.gif';
	} else {
		$magic['pic'] = STATICURL.'image/magic/'.strtolower($magic['identifier']).'.gif';
	}
	$magic['credit'] = $magic['credit'] ? $magic['credit'] : $_G['setting']['creditstransextra'][3];
	$useperoid = magic_peroid($magic, $_G['uid']);

	if(!submitcheck('operatesubmit')) {

		if($magicperm['targetgroups']) {
			loadcache('usergroups');
			foreach(explode("\t", $magicperm['targetgroups']) as $_G['groupid']) {
				if(isset($_G['cache']['usergroups'][$_G['groupid']])) {
					$targetgroupperm .= $comma.$_G['cache']['usergroups'][$_G['groupid']]['grouptitle'];
					$comma = '&nbsp;';
				}
			}
		}

		if($magicperm['forum']) {
			loadcache('forums');
			foreach(explode("\t", $magicperm['forum']) as $fid) {
				if(isset($_G['cache']['forums'][$fid])) {
					$forumperm .= $comma.'<a href="forum.php?mod=forumdisplay&fid='.$fid.'" target="_blank">'.$_G['cache']['forums'][$fid]['name'].'</a>';
					$comma = '&nbsp;';
				}
			}
		}

		include template('home/space_magic_shop_opreation');
		dexit();

	} else {

		$magicnum = intval($_GET['magicnum']);
		$magic['weight'] = $magic['weight'] * $magicnum;
		$totalprice = $magic['discountprice'] * $magicnum;

		if(getuserprofile('extcredits'.$magic['credit']) < $totalprice) {
			if($_G['setting']['ec_ratio'] && $_G['setting']['creditstrans'][0] == $magic['credit']) {
				showmessage('magics_credits_no_enough_and_charge', '', ['credit' => $_G['setting']['extcredits'][$magic['credit']]['title']]);
			} else {
				showmessage('magics_credits_no_enough', '', ['credit' => $_G['setting']['extcredits'][$magic['credit']]['title']]);
			}
		} elseif($magic['num'] < $magicnum) {
			showmessage('magics_num_no_enough');
		} elseif(!$magicnum || $magicnum < 0) {
			showmessage('magics_num_invalid');
		}

		getmagic($magic['magicid'], $magicnum, $magic['weight'], $totalweight, $_G['uid'], $_G['group']['maxmagicsweight']);
		updatemagiclog($magic['magicid'], '1', $magicnum, $magic['price'].'|'.$magic['credit'], $_G['uid']);

		table_common_magic::t()->update_salevolume($magic['magicid'], $magicnum);
		updatemembercount($_G['uid'], [$magic['credit'] => -$totalprice], true, 'BMC', $magic['magicid']);
		showmessage('magics_buy_succeed', 'home.php?mod=magic&action=mybox', ['magicname' => $magic['name'], 'num' => $magicnum, 'credit' => $totalprice.' '.$_G['setting']['extcredits'][$magic['credit']]['unit'].$_G['setting']['extcredits'][$magic['credit']]['title']]);


	}

} elseif($operation == 'give') {

	if($_G['group']['allowmagics'] < 2) {
		showmessage('magics_nopermission');
	}

	$magic = table_common_magic::t()->fetch_by_identifier($_GET['mid']);
	if(!$magic || !$magic['available']) {
		showmessage('magics_nonexistence');
	}

	$magic['discountprice'] = $_G['group']['magicsdiscount'] ? intval($magic['price'] * ($_G['group']['magicsdiscount'] / 10)) : intval($magic['price']);
	$eidentifier = explode(':', $magic['identifier']);
	if(count($eidentifier) > 1) {
		$magic['pic'] = 'source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.gif';
	} else {
		$magic['pic'] = STATICURL.'image/magic/'.strtolower($magic['identifier']).'.gif';
	}

	if(!submitcheck('operatesubmit')) {

		include libfile('function/friend');
		$buddyarray = friend_list($_G['uid'], 20);
		include template('home/space_magic_shop_opreation');
		dexit();

	} else {

		$magicnum = intval($_GET['magicnum']);
		$totalprice = $magic['price'] * $magicnum;

		if(getuserprofile('extcredits'.$magic['credit']) < $totalprice) {
			if($_G['setting']['ec_ratio'] && $_G['setting']['creditstrans'][0] == $magic['credit']) {
				showmessage('magics_credits_no_enough_and_charge', '', ['credit' => $_G['setting']['extcredits'][$magic['credit']]['title']]);
			} else {
				showmessage('magics_credits_no_enough', '', ['credit' => $_G['setting']['extcredits'][$magic['credit']]['title']]);
			}
		} elseif($magic['num'] < $magicnum) {
			showmessage('magics_num_no_enough');
		} elseif(!$magicnum || $magicnum < 0) {
			showmessage('magics_num_invalid');
		}

		$toname = dhtmlspecialchars(trim($_GET['tousername']));
		if(!$toname) {
			showmessage('magics_username_nonexistence');
		}

		$givemessage = dhtmlspecialchars(trim($_GET['givemessage']));
		givemagic($toname, $magic['magicid'], $magicnum, $magic['num'], $totalprice, $givemessage, $magicarray);
		table_common_magic::t()->update_salevolume($magic['magicid'], $magicnum);
		updatemembercount($_G['uid'], [$magic['credit'] => -$totalprice], true, 'BMC', $magicid);
		showmessage('magics_buygive_succeed', 'home.php?mod=magic&action=shop', ['magicname' => $magic['name'], 'toname' => $toname, 'num' => $magicnum, 'credit' => $_G['setting']['extcredits'][$magic['credit']]['title'].' '.$totalprice.' '.$_G['setting']['extcredits'][$magic['credit']]['unit']], ['locationtime' => true]);

	}

} else {
	showmessage('undefined_action');
}
	