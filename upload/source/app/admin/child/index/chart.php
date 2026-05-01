<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$days = 30 * 86400;
$field = '*';
$begin = dgmdate(time() - $days, 'Ymd');
$end = dgmdate(time(), 'Ymd');
$cols = [];
$cols['login'] = ['login', 'mobilelogin', 'register', 'invite'];
$cols['forum'] = ['thread', 'poll', 'activity', 'reward', 'debate', 'trade', 'post'];

$count = 0;
foreach(table_common_stat::t()->fetch_all_stat($begin, $end, $field) as $value) {
	$data[substr($value['daytime'], 4, 4)] = $value;
}

for($i = time() - $days; $i <= time(); $i += 86400) {
	$xaxis .= "<value xid='$count'>".dgmdate($i, 'md').'</value>';
	$value = $data[dgmdate($i, 'md')];
	foreach($cols as $ck => $cvs) {
		if($ck == 'login') {
			$graph['login'] .= "<value xid='$count'>".($value['login'] + 0).'</value>';
			$graph['register'] .= "<value xid='$count'>".($value['register'] + 0).'</value>';
		} else {
			$num = 0;
			foreach($cvs as $cvk) {
				$num = $value[$cvk] + $num;
			}
			$graph[$ck] .= "<value xid='$count'>".$num.'</value>';
		}
	}
	$count++;
}
$xml = '';
$xml .= '<'."?xml version=\"1.0\" encoding=\"utf-8\"?>";
$xml .= '<chart><xaxis>';
$xml .= $xaxis;
$xml .= '</xaxis><graphs>';
$count = 0;
foreach($graph as $key => $value) {
	$title = lang('spacecp', "do_stat_$key");
	if($title == '') {
		continue;
	}
	$xml .= "<graph gid='$count' title='".$title."'>";
	$xml .= $value;
	$xml .= '</graph>';
	$count++;
}
$xml .= '</graphs></chart>';

@header('Expires: -1');
@header('Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0', FALSE);
@header('Pragma: no-cache');
@header('Content-type: application/xml; charset=utf-8');
echo $xml;
exit();