<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang =
	[
	'custom_name' => 'โฆษณาที่กำหนดเอง',
	'custom_desc' => 'คุณสามารถเพิ่มโฆษณาในหน้าใดก็ได้ของเว็บไซต์โดยการเพิ่มโค้ดโฆษณาลงในเทมเพลตหรือไฟล์ HTML เหมาะสำหรับผู้ดูแลเว็บไซต์ที่มีความรู้พื้นฐานเกี่ยวกับ HTML<br /><br />
		<a href="javascript:;" onclick="prompt(\'โปรดคัดลอก (CTRL+C) ข้อความต่อไปนี้ไปวางในเทมเพลต เพื่อแสดงโฆษณาในตำแหน่งนี้\', \'<!--{ad/custom_'.$_GET['customid'].'}-->\')" />การเรียกใช้ภายใน</a>&nbsp;
		<a href="javascript:;" onclick="prompt(\'โปรดคัดลอก (CTRL+C) ข้อความต่อไปนี้ไปวางในไฟล์ HTML เพื่อแสดงโฆษณาในตำแหน่งนี้\', \'&lt;script type=\\\'text/javascript\\\' src=\\\''.$_G['siteurl'].'api.php?mod=ad&adid=custom_'.$_GET['customid'].'\\\'&gt;&lt;/script&gt;\')" />การเรียกใช้ภายนอก</a>',
	'custom_id_notfound' => 'ไม่พบโฆษณาที่กำหนดเอง',
	'custom_codelink' => 'การเรียกใช้ภายใน',
	'custom_text' => 'โฆษณาที่กำหนดเอง',
	];

