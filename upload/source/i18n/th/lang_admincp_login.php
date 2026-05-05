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
	'admincp_title' => '{bbname} ศูนย์ดูแลระบบ',
	'login_title' => 'เข้าสู่ระบบศูนย์ดูแลระบบ',
	'login_username' => 'ชื่อผู้ใช้',
	'login_password' => 'รหัสผ่าน',
	'login_dk_light_mode' => 'โหมดสว่าง',
	'login_dk_by_system' => 'ตามระบบ',
	'login_dk_normal_mode' => 'โหมดปกติ',
	'login_dk_dark_mode' => 'โหมดกลางคืน',

	'submit' => 'ส่งข้อมูล',
	'forcesecques' => 'จำเป็นต้องกรอก',
	'security_question' => 'คำถามยืนยันตัวตน',
	'security_answer' => 'คำตอบ',
	'security_question_0' => 'ไม่มีคำถามยืนยันตัวตน',
	'security_question_1' => 'ชื่อมารดาของคุณ',
	'security_question_2' => 'ชื่อคุณปู่ของคุณ',
	'security_question_3' => 'เมืองที่บิดาของคุณเกิด',
	'security_question_4' => 'ชื่อของครูคนหนึ่งของคุณ',
	'security_question_5' => 'รุ่นของคอมพิวเตอร์ส่วนตัวของคุณ',
	'security_question_6' => 'ชื่อร้านอาหารที่คุณชอบที่สุด',
	'security_question_7' => 'เลข 4 ตัวท้ายของใบขับขี่',
	'other_loginname' => 'เข้าสู่ระบบด้วยบัญชีอื่น',

	'login_tips' => 'Discuz! คือแพลตฟอร์มสร้างเว็บไซต์ระดับมืออาชีพที่เน้นชุมชนเป็นหลัก ช่วยให้เว็บไซต์ได้รับบริการแบบครบวงจรในที่เดียว',
	'login_nosecques' => 'คุณยังไม่ได้ใช้การเข้าสู่ระบบแบบปลอดภัย โปรดตั้งค่าคำถามยืนยันตัวตนในหน้าส่วนตัวก่อนเข้าใช้ศูนย์ดูแลระบบ คุณสามารถ <a href="forum.php?mod=memcp&action=profile&typeid=1" target="_blank">คลิกที่นี่</a> เพื่อเข้าสู่การตั้งค่าคำถามยืนยันตัวตน',
	'copyright' => '&copy; 2001-'.date('Y').' <a href="https://code.dismall.com/" target="_blank">Discuz! Team</a>.',

	'login_cp_guest' => '<h1>คุณยังไม่ได้เข้าสู่ระบบ</h1><a href="member.php?mod=logging&action=login" class="btn">เข้าสู่ระบบ</a><p>หากผู้ดูแลระบบต้องการบังคับให้เข้าสู่ระบบ สามารถแก้ไขไฟล์ config/config_global.php เพื่อปิดฟีเจอร์นี้ได้</p>',
	'login_cplock' => 'แผงควบคุมระบบของคุณถูกล็อก<br>โปรดรออีก<b> {ltime} </b>วินาที ก่อนเข้าสู่ศูนย์ดูแลระบบอีกครั้ง',
	'login_user_lock' => 'เนื่องจากคุณกรอกรหัสผ่านผิดเกินจำนวนครั้งที่กำหนด คำขอเข้าสู่ระบบนี้จึงถูกปฏิเสธ โปรดลองใหม่ในอีก 15 นาที',
	'login_cp_noaccess' => '<b>ศูนย์ดูแลระบบ (หรือการดำเนินการนี้) ไม่เปิดให้เข้าถึงสำหรับบัญชีปัจจุบัน</b><br><br>โปรดเปลี่ยนไปใช้บัญชีที่มีสิทธิ์และเข้าสู่ระบบอีกครั้ง',
	'login_ip_noaccess' => '<a href="https://go.discuzth.com/dzx-fix-admin-login-howto" target="_blank">การเปลี่ยนแปลงของไอพีอาจทำให้การเข้าสู่ระบบไม่สำเร็จ ดูวิธีแก้ไข</a>',
	'noaccess' => 'สิทธิ์การดูแลระบบส่วนหลัง (หรือการดำเนินการนี้) ยังไม่เปิดให้คุณใช้งาน โปรดติดต่อผู้ดูแลระบบ',

	'qrcode_login' => 'เข้าสู่ระบบด้วยคิวอาร์โค้ด',
	'pwd_login' => 'เข้าสู่ระบบด้วยรหัสผ่าน',
	'qrcode_wechat_scan' => 'โปรดใช้ WeChat สแกนคิวอาร์โค้ดเพื่อเข้าสู่ระบบ',

	];

