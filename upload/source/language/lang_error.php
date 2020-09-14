<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_error.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'System Message' => 'ข้อมูลเกี่ยวกับไซต์',

	'config_notfound' => 'ไม่พบไฟล์การตั้งค่าของ "config_global.php" หรือไม่สามารถเข้าถึงได้ ตรวจสอบให้แน่ใจว่าคุณติดตั้งดิสคัสได้อย่างถูกต้องแล้ว?',
	'template_notfound' => 'ไม่พบเทมเพลท หรือไม่สามารถเข้าถึงได้',
	'directory_notfound' => 'ไม่พบโฟลเดอร์ หรือไม่สามารถเข้าถึงได้',
	'request_tainting' => 'การร้องขอการเข้าถึงของคุณประกอบด้วยอักขระที่ไม่ถูกต้อง ได้รับการปฏิเสธโดยระบบ',
	'db_help_link' => 'คลิกที่นี่เพื่อขอความช่วยเหลือ',
	'db_error_message' => 'ข้อความแสดงข้อผิดพลาด',
	'db_error_sql' => '<b>SQL</b>: $sql<br />',
	'db_error_backtrace' => '<b>Backtrace</b>: $backtrace<br />',
	'db_error_no' => 'รหัสข้อผิดพลาด',
	'db_notfound_config' => 'ไม่พบไฟล์การตั้งค่าของ "config_global.php" หรือไม่สามารถเข้าถึงได้',
	'db_notconnect' => 'ไม่สามารถเชื่อมต่อไปยังเซิร์ฟเวอร์ฐานข้อมูล',
	'db_security_error' => 'คำแนะนำการค้นหาเกี่ยวกับการถูกคุกคามด้านความปลอดภัย',
	'db_query_sql' => 'คำแนะนำในการค้นหา',
	'db_query_error' => 'ข้อผิดพลาดในการค้นหา',
	'db_config_db_not_found' => 'ข้อผิดพลาดในการกำหนดค่าฐานข้อมูล กรุณาตรวจสอบไฟล์ config_global.php',
	'system_init_ok' => 'การเริ่มต้นระบบเว็บไซต์เสร็จสมบูรณ์ กรุณา<a href="index.php">คลิกที่นี่เพื่อเข้าสู่เว็บไซต์</a>',
	'backtrace' => 'ข้อมูลการดำเนินการ',
	'error_end_message' => '<a href="http://{host}">{host}</a> ข้อความแสดงข้อผิดพลาดนี้ได้ถูกบันทึกไว้ในรายละเอียด ขออภัยในความไม่สะดวกในการเข้าถึง',
	'suggestion_plugin' => 'หากคุณเป็นผู้ดูแลเว็บ ขอแนะนำให้คุณลองปิด<a href="admin.php?action=plugins&frames=yes" class="guess" target="_blank">{guess}</a> ปลั๊กอินและ<a href="admin.php?action=tools&operation=updatecache&frames=yes" class="guess" target="_blank">อัปเดตแคช</a>ใน AdminCP หากปัญหาได้รับการแก้ไขหลังจากปิดปลั๊กอินแล้ว ขอแนะนำให้คุณติดต่อผู้จัดหาปลั๊กอินเพื่อขอความช่วยเหลือดังกล่าว',
	'suggestion' => 'หากคุณเป็นผู้ดูแลเว็บขอแนะนำให้คุณลอง<a href="admin.php?action=tools&operation=updatecache&frames=yes" target="_blank">อัปเดตแคช</a>ใน AdminCP หรือคุณสามารถใช้<a href="https://www.discuz.net/" target="_blank">เว็บไซต์อย่างเป็นทางการของ Discuz! </a> เพื่อขอความช่วยเหลือ หากคุณแน่ใจว่านี่เป็นข้อบกพร่องของโปรแกรม คุณสามารถ <a href="https://gitee.com/ComsenzDiscuz/DiscuzX/issues" target="_blank">ส่งรายการ Issue</a> ถึงเราได้โดยตรง',

	'file_upload_error_-101' => 'อัปโหลดล้มเหลว! ไฟล์ที่จะอัปโหลดไม่อยู่ หรือไม่ถูกต้อง กรุณาย้อนกลับ',
	'file_upload_error_-102' => 'อัปโหลดล้มเหลว! รูปแบบของไฟล์ไม่ถูกต้อง กรุณาย้อนกลับ',
	'file_upload_error_-103' => 'อัปโหลดล้มเหลว! ไม่สามารถเขียนไปยังไฟล์ หรือเขียนล้มเหลว กรุณาย้อนกลับ',
	'file_upload_error_-104' => 'อัปโหลดล้มเหลว! รูปแบบไฟล์ของรูปภาพไม่ถูกต้อง กรุณาย้อนกลับ',
);

?>