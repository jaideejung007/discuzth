<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$removetime = TIMESTAMP - $_G['setting']['smstimelimit'] + 86400;

foreach(table_common_smslog::t()->fetch_all_by_dateline($removetime, '<=') as $smslog) {
	table_common_smslog::t()->insert_archiver($smslog);
	table_common_smslog::t()->delete($smslog['smslogid']);
}