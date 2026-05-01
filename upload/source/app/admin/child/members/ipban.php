<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(array_key_exists('security', $_G['config']) && array_key_exists('useipban', $_G['config']['security']) && $_G['config']['security']['useipban'] == 0) {
	cpmsg('members_ipban_closed', '', 'error');
}

if(!$_GET['ipact']) {
	if(!submitcheck('ipbansubmit')) {

		require_once libfile('function/misc');

		$iptoban = getgpc('ip');

		$ipbanned = '';
		foreach(table_common_banned::t()->fetch_all_order_dateline() as $banned) {
			$disabled = $_G['adminid'] != 1 && $banned['admin'] != $_G['member']['username'] ? 'disabled' : '';
			$banned['dateline'] = dgmdate($banned['dateline'], 'Y-m-d');
			$banned['expiration'] = dgmdate($banned['expiration'], 'Y-m-d');
			$theip = "{$banned['ip']}";
			$ipbanned .= showtablerow('', ['class="td25"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[{$banned['id']}]\" value=\"{$banned['id']}\" $disabled />",
				$theip,
				convertip($theip),
				$banned['admin'],
				$banned['dateline'],
				"<input type=\"text\" class=\"txt\" size=\"10\" name=\"expirationnew[{$banned['id']}]\" value=\"{$banned['expiration']}\" $disabled />"
			], TRUE);
		}
		shownav('user', 'nav_members_ipban');
		showsubmenu('nav_members_ipban', [
			['nav_members_ipban', 'members&operation=ipban', 1],
			['nav_members_ipban_output', 'members&operation=ipban&ipact=input', 0]
		]);
		showtips('members_ipban_tips');
		showformheader('members&operation=ipban');
		showtableheader();
		showsubtitle(['', 'ip', 'members_ipban_location', 'operator', 'start_time', 'end_time']);
		echo $ipbanned;
		showtablerow('', ['', 'class="td28" colspan="3"', 'class="td28" colspan="2"'], [
			$lang['add_new'],
			'<input type="text" class="txt" name="ipnew" value="'.$iptoban.'" style="width: 200px;">',
			$lang['validity'].': <input type="text" class="txt" name="validitynew" value="30" size="3"> '.$lang['days']
		]);
		showsubmit('ipbansubmit', 'submit', 'del');
		showtablefooter();
		showformfooter();

	} else {

		if(!empty($_GET['delete'])) {
			table_common_banned::t()->delete_by_id($_GET['delete'], $_G['adminid'], $_G['username']);
		}

		if($_GET['ipnew'] != '') {
			$ipnew = ip::to_ip($_GET['ipnew']);
			$is_cidr = ip::validate_cidr($ipnew, $ipnew);
			if(!ip::validate_ip($ipnew) && !$is_cidr) {
				cpmsg('members_ipban_formaterror', '', 'error');
			}

			if($_G['adminid'] != 1 && $is_cidr) {
				cpmsg('members_ipban_nopermission', '', 'error');
			}

			if(ip::check_ip($_G['clientip'], $ipnew)) {
				cpmsg('members_ipban_illegal', '', 'error');
			}

			if($banned = table_common_banned::t()->fetch_by_ip($ipnew)) {
				cpmsg('members_ipban_invalid', '', 'error');
			}

			$expiration = TIMESTAMP + $_GET['validitynew'] * 86400;

			list($lower, $upper) = ip::calc_cidr_range($ipnew, true);

			$data = [
				'ip' => $ipnew,
				'lowerip' => $lower,
				'upperip' => $upper,
				'admin' => $_G['username'],
				'dateline' => $_G['timestamp'],
				'expiration' => $expiration,
			];
			table_common_banned::t()->insert($data);
		}

		if(is_array($_GET['expirationnew'])) {
			foreach($_GET['expirationnew'] as $id => $expiration) {
				table_common_banned::t()->update_expiration_by_id($id, strtotime($expiration), $_G['adminid'], $_G['username']);
			}
		}

		cpmsg('members_ipban_succeed', 'action=members&operation=ipban', 'succeed');

	}
} elseif($_GET['ipact'] == 'input') {
	if($_G['adminid'] != 1) {
		cpmsg('members_ipban_nopermission', '', 'error');
	}
	if(!submitcheck('ipbansubmit')) {
		shownav('user', 'nav_members_ipban');
		showsubmenu('nav_members_ipban', [
			['nav_members_ipban', 'members&operation=ipban', 0],
			['nav_members_ipban_output', 'members&operation=ipban&ipact=input', 1]
		]);
		showtips('members_ipban_input_tips');
		showformheader('members&operation=ipban&ipact=input');
		showtableheader();
		showsetting('members_ipban_input', 'inputipbanlist', '', 'textarea');
		showsubmit('ipbansubmit', 'submit');
		showtablefooter();
		showformfooter();
	} else {
		$iplist = explode("\n", str_replace("\r", '', $_GET['inputipbanlist']));
		foreach($iplist as $banip) {
			if(str_contains($banip, ',')) {
				list($banipaddr, $expiration) = explode(',', $banip);
				$expiration = strtotime($expiration);
			} else {
				list($banipaddr, $expiration) = explode(';', $banip);
				$expiration = TIMESTAMP + ($expiration ? $expiration : 30) * 86400;
			}
			if(!trim($banipaddr)) {
				continue;
			}
			if(str_contains($banipaddr, '/')) {
				// 对于 CIDR 需要校验其合法性, 并判断是否有设置 CIDR 的权限
				if($_G['adminid'] != 1 || !ip::validate_cidr($banipaddr, $banipaddr)) {
					continue;
				}
			} else if(str_contains($banipaddr, '*')) {
				// 对于带 * 的旧版规则的处理, 只支持转换为标准的 CIDR 网段, 不支持凑段
				// * 与 CIDR 一样, 需要判断权限
				if($_G['adminid'] != 1) {
					continue;
				}
				// 设置掩码并分解 IP 地址为四段, 如果分解失败或不是四段则忽略
				$mask = 0;
				$ipnew = explode('.', $banipaddr);
				if(!is_array($ipnew) || count($ipnew) != 4) {
					continue;
				}
				// 只支持能够转化为标准 ABC 类的地址, 否则忽略
				for($i = 0; $i < 4; $i++) {
					if(strcmp($ipnew[$i], '*') === 0) {
						if($i == 0) {
							// * 开头不是合法 IP , 忽略
							break;
						} else if($mask) {
							// 如果子网掩码存在, 则更新本段为 0
							$ipnew[$i] = 0;
						} else {
							// 如果子网掩码不存在, 则更新本段为 0 , 并生成子网掩码
							$ipnew[$i] = 0;
							$mask = $i * 8;
						}
					} else {
						// 如果 * 后面跟数字, 或者不是合法的 IP, 则此条不做转换
						if($mask || !is_numeric($ipnew[$i]) || $ipnew[$i] < 0 || $ipnew[$i] > 255) {
							$mask = 0;
							break;
						}
					}
				}
				// 如果生成了子网掩码, 则尝试拼接 CIDR 并送校验, 忽略无法通过校验的规则
				if($mask) {
					$banipaddr = implode('.', $ipnew);
					$banipaddr = $banipaddr.'/'.$mask;
					if(!ip::validate_cidr($banipaddr, $banipaddr)) {
						continue;
					}
				} else {
					continue;
				}
			} else if(!ip::validate_ip($banipaddr)) {
				// 忽略不合法的 IP 地址
				continue;
			}

			$checkexists = table_common_banned::t()->fetch_by_ip($banipaddr);
			if($checkexists) {
				continue;
			}
			list($lower, $upper) = ip::calc_cidr_range($banipaddr, true);

			$data = [
				'ip' => $banipaddr,
				'lowerip' => $lower,
				'upperip' => $upper,
				'admin' => $_G['username'],
				'dateline' => $_G['timestamp'],
				'expiration' => $expiration,
			];
			table_common_banned::t()->insert($data, false, true);
		}

		cpmsg('members_ipban_succeed', 'action=members&operation=ipban&ipact=input', 'succeed');
	}
} elseif($_GET['ipact'] == 'output') {
	ob_end_clean();
	dheader('Cache-control: max-age=0');
	dheader('Expires: '.gmdate('D, d M Y H:i:s', TIMESTAMP - 31536000).' GMT');
	dheader('Content-Encoding: none');
	dheader('Content-Disposition: attachment; filename=IPBan.csv');
	dheader('Content-Type: text/plain');
	foreach(table_common_banned::t()->fetch_all_order_dateline() as $banned) {
		$banned['expiration'] = dgmdate($banned['expiration']);
		echo "{$banned['ip']},{$banned['expiration']}\n";
	}
	define('FOOTERDISABLED', 1);
	exit();
}
	