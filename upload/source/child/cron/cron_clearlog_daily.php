<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if($_G['setting']['log']['clearlogstypes']) {

	$clearlogstypes = dunserialize($_G['setting']['log']['clearlogstypes']);
	$clearlogstypes = array_unique($clearlogstypes);
	foreach($clearlogstypes as $type) {
		
		if(empty($_G['setting']['log']['clearlogsdays'][$type]) || $_G['setting']['log']['clearlogsdays'][$type] < 0) {
			continue;
		}
		$removetime = TIMESTAMP - $_G['setting']['log']['clearlogsdays'][$type] * 86400;
		table_common_log::t()->delete_by_removetime($removetime, $clearlogstypes);

		if($type == 'credit') {
			table_common_credit_log::t()->delete_by_removetime($removetime);
			table_common_credit_log_field::t()->delete_by_removetime($removetime);
		} elseif($type == 'warn') {
			table_forum_warning::t()->delete_by_removetime($removetime);
		} elseif($type == 'magic') {
			table_common_magiclog::t()->delete_by_removetime($removetime);
		} elseif($type == 'medal') {
			table_forum_medallog::t()->delete_by_removetime($removetime);
		} elseif($type == 'invite') {
			table_common_invite::t()->delete_by_removetime($removetime);
		} elseif($type == 'crime') {
			table_common_member_crime::t()->delete_by_removetime($removetime);
		}
	}

}

