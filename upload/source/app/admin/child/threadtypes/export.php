<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sortid = intval($_GET['sortid']);
$typevarlist = [];
$typevararr = table_forum_typevar::t()->fetch_all_by_sortid($sortid);
$typeoptionarr = table_forum_typeoption::t()->fetch_all(array_keys($typevararr));
$threadtypearr = table_forum_threadtype::t()->fetch($sortid);
foreach($typevararr as $typevar) {
	$typeoption = $typeoptionarr[$typevar['optionid']];
	$typevar = array_merge($threadtypearr, $typevar);
	$typevar = array_merge($typeoption, $typevar);
	$typevar['tpdescription'] = $typeoption['description'];
	$typevar['ttdescription'] = $threadtypearr['description'];
	$typevar['tpexpiration'] = $typeoption['expiration'];
	$typevar['ttexpiration'] = $threadtypearr['expiration'];
	unset($typevar['fid']);
	$typevarlist[] = $typevar;
}
if(empty($typevarlist)) {
	$threadtype = table_forum_threadtype::t()->fetch($sortid);
	$threadtype['ttdescription'] = $threadtype['description'];
	unset($threadtype['fid']);
	$typevarlist[] = $threadtype;
}

if(empty($typevarlist)) {
	cpmsg('threadtype_export_error');
}

exportdata('Discuz! Threadtypes', $typevarlist[0]['typeid'], $typevarlist);
	