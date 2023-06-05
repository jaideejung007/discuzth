<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_admincp_login.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'admincp_title' => 'ศูนย์กลางจัดการเว็บไซต์ <span>Discuz!</span>',
	'login_title' => 'เข้าสู่ระบบศูนย์กลางจัดการเว็บไซต์',
	'login_username' => 'ชื่อผู้ใช้',
	'login_password' => 'รหัสผ่าน',
	'login_dk_light_mode' => 'โหมดสว่าง',
	'login_dk_by_system' => 'ตามค่าระบบ',
	'login_dk_normal_mode' => 'โหมดปกติ',
	'login_dk_dark_mode' => 'โหมดกลางคืน',

	'submit' => 'เข้าสู่ระบบ',
	'forcesecques' => 'ต้องระบุ',
	'security_question' => 'คำถามความปลอดภัย',
	'security_answer' => 'คำตอบ',
	'security_question_0' => 'เลือกคำถาม (หากคุณกำหนดไว้)',
	'security_question_1' => 'ชื่อแม่ของฉัน',
	'security_question_2' => 'ชื่อปู่ของฉัน',
	'security_question_3' => 'สถานที่เกิดพ่อของฉัน',
	'security_question_4' => 'ชื่อดาราคนโปรดของฉัน',
	'security_question_5' => 'ยี่ห้อคอมพิวเตอร์ของฉัน',
	'security_question_6' => 'อาหารจานโปรดของฉัน',
	'security_question_7' => 'เลขบัตรประจำตัวประชาชน',

	'login_tips' => 'Discuz! เป็นแพลตฟอร์มสร้างเว็บไซต์ที่มุ่งเน้นไปที่การสร้างชุมชนออนไลน์ ช่วยให้เว็บไซต์สามารถให้บริการแบบครบ จบ ในที่เดียว',
	'login_nosecques' => 'คุณยังไม่ได้ตั้งค่าคำถามความปลอดภัยในการเข้าสู่ระบบ คุณสามารถตั้งค่าคำถามความปลอดภัยได้ที่ข้อมูลส่วนตัวหรือเมนูสมาชิก หรือ <a href="forum.php?mod=memcp&action=profile&typeid=1" target="_blank">คลิกที่นี่เพื่อ</a> เพื่อตั้งค่าคำถามความปลอดภัยของคุณ',
	'copyright' => '&copy; 2001-'.date('Y').' <a href="https://code.dismall.com/" target="_blank">Discuz! Team</a>.',

	'login_cp_guest' => '<h1>คุณยังไม่ได้เข้าสู่ระบบเว็บไซต์</h1><a href="member.php?mod=logging&action=login" class="btn">เข้าสู่ระบบ</a><p>ผู้ดูแลเว็บไซต์ได้ตั้งค่าให้เข้าสู่ระบบก่อนถึงจะสามารถเข้าถึงส่วนนี้ได้ สามารถปิดการใช้งานฟังก์ชันนี้ได้โดยแก้ไขไฟล์ config/config_global.php</p>',
	'login_cplock' => 'ระบบการจัดการเว็บไซต์ของคุณถูกล็อก! <br>กรุณารออีก<b> {ltime} </b>วินาที แล้วค่อยลองใหม่อีกครั้ง',
	'login_user_lock' => 'คำขอเข้าสู่ระบบนี้ถูกปฏิเสธเนื่องจากเข้าสู่ระบบไม่ถูกต้องมากเกินไป กรุณาลองอีกครั้งในอีก 15 นาที',
	'login_cp_noaccess' => '<b>คุณไม่ได้รับอนุญาตให้เข้าใช้งาน</b><br><br>ระบบได้บันทึกการกระทำของคุณไว้แล้ว ดังนั้นอย่าพยายามทำผิดกฎ',
	'login_ip_noaccess' => '<a href="https://discuzthai.com/thread-41074-1-1.html" target="_blank">การเปลี่ยน IP ใหม่อาจทำให้เกิดปัญหาเข้าสู่ระบบไม่ได้ ตรวจสอบวิธีแก้ปัญหาได้ที่นี่</a>',
	'noaccess' => 'คุณไม่ได้รับอนุญาตให้เข้าถึงการตั้งค่าระบบ กรุณาติดต่อผู้ดูแลเว็บไซต์',


);

?>