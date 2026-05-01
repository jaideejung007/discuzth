<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($_GET['do']) || $_GET['do'] == 'tradeinfo') {

	if($_GET['do'] == 'tradeinfo') {
		$_GET['pid'] = intval($_GET['pid']);
	} else {
		$_GET['pid'] = '';
		!$tradenum && $allowpostreply = FALSE;
	}

	$query = table_forum_trade::t()->fetch_all_thread_goods($_G['tid'], $_GET['pid']);
	$trades = $tradesstick = [];
	$tradelist = 0;
	if(empty($_GET['do'])) {
		$sellerid = 0;
		$listcount = count($query);
		$tradelist = $tradenum - $listcount;
	}

	$tradesaids = $tradespids = [];
	foreach($query as $trade) {
		if($trade['expiration']) {
			$trade['expiration'] = ($trade['expiration'] - TIMESTAMP) / 86400;
			if($trade['expiration'] > 0) {
				$trade['expirationhour'] = floor(($trade['expiration'] - floor($trade['expiration'])) * 24);
				$trade['expiration'] = floor($trade['expiration']);
			} else {
				$trade['expiration'] = -1;
			}
		}
		$tradesaids[] = $trade['aid'];
		$tradespids[] = $trade['pid'];
		if($trade['displayorder'] < 0) {
			$trades[$trade['pid']] = $trade;
		} else {
			$tradesstick[$trade['pid']] = $trade;
		}
	}
	if(empty($_GET['do'])) {
		$tradepostlist = table_forum_post::t()->fetch_all_post('tid:'.$_G['tid'], $tradespids);
	}
	$trades = $tradesstick + $trades;
	unset($trade);

	if($tradespids) {
		foreach(table_forum_attachment_n::t()->fetch_all_by_id('tid:'.$_G['tid'], 'pid', $tradespids) as $attach) {
			if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
				$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
				$trades[$attach['pid']]['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
				$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
			}
		}
	}

	if($_GET['do'] == 'tradeinfo') {
		$trade = $trades[$_GET['pid']];
		unset($trades);
		$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid']);
		if($post) {
			$post = array_merge(table_common_member_status::t()->fetch($post['authorid']), table_common_member_profile::t()->fetch($post['authorid']),
				$post, getuserbyuid($post['authorid']));
			if($_G['setting']['verify']['enabled']) {
				$post = array_merge($post, table_common_member_verify::t()->fetch($post['authorid']));
			}
		}

		$postlist[$post['pid']] = viewthread_procpost($post, $lastvisit, $ordertype);

		$usertrades = $userthreads = [];
		if(!$_G['inajax']) {
			$limit = 6;
			$query = table_forum_trade::t()->fetch_all_for_seller($_G['forum_thread']['authorid'], $limit + 1, $_G['tid']);
			$usertradecount = 0;
			foreach($query as $usertrade) {
				if($usertrade['pid'] == $post['pid']) {
					continue;
				}
				$usertradecount++;
				$usertrades[] = $usertrade;
				if($usertradecount == $limit) {
					break;
				}
			}

		}

		if($_G['forum_attachpids'] && !defined('IN_ARCHIVER')) {
			require_once libfile('function/attachment');
			parseattach($_G['forum_attachpids'], $_G['forum_attachtags'], $postlist, [$trade['aid']]);
		}

		$post = $postlist[$_GET['pid']];

		$post['buyerrank'] = 0;
		if($post['buyercredit']) {
			foreach($_G['setting']['ec_credit']['rank'] as $level => $credit) {
				if($post['buyercredit'] <= $credit) {
					$post['buyerrank'] = $level;
					break;
				}
			}
		}
		$post['sellerrank'] = 0;
		if($post['sellercredit']) {
			foreach($_G['setting']['ec_credit']['rank'] as $level => $credit) {
				if($post['sellercredit'] <= $credit) {
					$post['sellerrank'] = $level;
					break;
				}
			}
		}

		$navtitle = $trade['subject'];

		if($post['authorid']) {
			$online = $sessioninfo = C::app()->session->fetch_by_uid($post['authorid']) && empty($sessioninfo['invisible']) ? 1 : 0;
		}

		
		if($post['content'] && !in_array($post['content'], ['{}', null, 'null', ''])) {
			list($parserData, $styleData) = editor::parser($post['content']);
			if($_G['setting']['editor_global_css']) {
				$styleData .= $_G['setting']['editor_global_css'];
			}
			if(!defined('IN_RESTFUL')) {
				$post['message'] = $json_content = $parserData.$styleData;
			} else {
				$post['message'] = $json_content = $parserData;
				if($_REQUEST['removestyle']) {
					$pattern = '/\<style(\s+.*?)?\>/s';
					$styleData = preg_replace($pattern, '', $styleData);
					$pattern = '/\<\/style(\s+.*?)?\>/s';
					$styleData = preg_replace($pattern, '', $styleData);
				}
				$styleData = str_replace(["\r", "\n", "\t"], '', $styleData);
				$post['style'] = $styleData;
			}
		}
		

		include template('forum/trade_info');
		exit;

	}
}

