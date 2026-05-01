<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$checkstatus = $optimizer->check();

table_common_optimizer::t()->update_optimizer($type.'_checkrecord', ($checkstatus['status'] == 1 ? $checkstatus['status'] : 0));
table_common_optimizer::t()->update_optimizer($check_record_time_key, $_G['timestamp']);

include template('common/header_ajax');
echo '<script type="text/javascript">updatecheckstatus(\''.$type.'\', \''.$checkstatus['lang'].'\', \''.$checkstatus['status'].'\', \''.$checkstatus['type'].'\', \''.$checkstatus['extraurl'].'\');</script>';
include template('common/footer_ajax');
exit;
	