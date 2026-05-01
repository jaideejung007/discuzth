<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

array_splice($menu['system']['menu']['global'], 4, 0, [
	['setting_memory', 'setting_memory'],
	['setting_serveropti', 'setting_serveropti'],
]);

array_splice($menu['system']['menu']['global'], 9, 0, [
	['founder_perm_credits', 'credits'],
]);

array_splice($menu['system']['menu']['style'], 8, 0, [
	['setting_editor_code', 'misc_bbcode'],
]);

array_splice($menu['system']['menu']['user'], 1, 0, [
	['founder_perm_members_group', 'members_group'],
	['founder_perm_members_access', 'members_access'],
	['founder_perm_members_credit', 'members_credit'],
	['founder_perm_members_medal', 'members_medal'],
	['founder_perm_members_repeat', 'members_repeat'],
	['founder_perm_members_clean', 'members_clean'],
	['founder_perm_members_edit', 'members_edit'],
]);

array_splice($menu['system']['menu']['group'], 1, 0, [
	['founder_perm_group_editgroup', 'group_editgroup'],
	['founder_perm_group_deletegroup', 'group_deletegroup'],
]);

array_splice($menu['system']['menu']['extended'], 4, 0, [
	['founder_perm_members_confermedal', 'members_confermedal'],
]);

array_splice($menu['system']['menu']['extended'], 7, 0, [
	['founder_perm_ec_qpay', 'ec_qpay'],
	['founder_perm_ec_wechat', 'ec_wechat'],
	['founder_perm_ec_alipay', 'ec_alipay'],
	['founder_perm_ec_credit', 'ec_credit'],
	['founder_perm_ec_orders', 'ec_orders'],
	['founder_perm_tradelog', 'tradelog'],
]);