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

	'login_tips' => 'Discuz! คือแพลตฟอร์มชุมชนสำหรับสร้างเว็บไซต์ระดับมืออาชีพ เปิดตัวโดย <a href="http://cloud.tencent.com" target="_blank">เทนเซ็นต์ คลาวด์</a> และแปลภาษาไทยโดย <a href="https://discuzthai.com" target="_blank">Discuz! Thai</a> เพื่อให้คุณมีเว็บไซต์ที่พร้อมให้บริการครบ จบ ในที่เดียว',
	'login_nosecques' => 'คุณยังไม่ได้ตั้งค่าคำถามความปลอดภัยในการเข้าสู่ระบบ คุณสามารถตั้งค่าคำถามความปลอดภัยได้ที่ข้อมูลส่วนตัวหรือเมนูสมาชิก หรือ <a href="forum.php?mod=memcp&action=profile&typeid=1" target="_blank">คลิกที่นี่เพื่อ</a> เพื่อตั้งค่าคำถามความปลอดภัยของคุณ',
	'copyright' => 'สงวนลิขสิทธิ์ &copy; 2001-'.date('Y').' เทนเซ็นต์คลาวด์',

	'login_cp_guest' => '<h1>คุณยังไม่ได้เข้าสู่ระบบเว็บไซต์</h1><a href="member.php?mod=logging&action=login" class="btn">เข้าสู่ระบบ</a><p>ผู้ดูแลระบบจำเป็นต้องเข้าสู่ระบบงานก่อนถึงจะเข้าถึงส่วนนี้ได้ คุณสมบัตินี้สามารถปิดใช้งานได้ โดยตั้งค่าได้ที่ config/config_global.php</p>',
	'login_cplock' => 'ระบบการจัดการเว็บไซต์ของคุณถูกล็อก! <br>กรุณารออีก<b> {ltime} </b>วินาที แล้วค่อยลองใหม่อีกครั้ง',
	'login_user_lock' => 'คำขอเข้าสู่ระบบนี้ถูกปฏิเสธเนื่องจากเข้าสู่ระบบไม่ถูกต้องมากเกินไป กรุณาลองอีกครั้งในอีก 15 นาที',
	'login_cp_noaccess' => '<b>คุณไม่ได้รับอนุญาตให้เข้าใช้งาน</b><br><br>ระบบได้บันทึกการกระทำของคุณไว้แล้ว ดังนั้นอย่าพยายามทำผิดกฎ',
	'noaccess' => 'คุณไม่ได้รับอนุญาตให้เข้าถึงการตั้งค่าระบบ กรุณาติดต่อผู้ดูแลเว็บไซต์',


);

?>