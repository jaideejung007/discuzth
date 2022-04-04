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
	'login_title' => 'ระบบบริหารจัดการเว็บบอร์ด',
	'login_username' => 'USER',
	'login_password' => 'PASS',

	'submit' => 'ลงชื่อเข้าใช้',
	'forcesecques' => 'ต้องระบุ',
	'security_question' => 'คำถาม',
	'security_answer' => 'คำตอบ',
	'security_question_0' => 'เลือกคำถาม (ถ้ากำหนด)',
	'security_question_1' => 'ชื่อแม่ของฉัน',
	'security_question_2' => 'ชื่อปู่ของฉัน',
	'security_question_3' => 'สถานที่เกิดพ่อของฉัน',
	'security_question_4' => 'ชื่อดาราคนโปรดของฉัน',
	'security_question_5' => 'ยี่ห้อคอมพิวเตอร์ของฉัน',
	'security_question_6' => 'อาหารจานโปรดของฉัน',
	'security_question_7' => 'เลขบัตรประชาชน',

	'login_tips' => 'Discuz! ใช้ภาษา PHP และ MySQL รวมไปถึงโซลูชันอื่นๆ ในการพัฒนา เพื่อให้ระบบคอมมูนิวตี้ของคุณทรงประสิทธิภาพ พร้อมเป็นตัวเลือกอันดับแรกในการสร้างแบรนด์ให้กับเว็บไซต์ของคุณได้',
	'login_nosecques' => 'คุณยังไม่ได้ตั้งค่าคำถามความปลอดภัยในการลงชื่อเข้าใช้ คุณสามารถตั้งค่าคำถามความปลอดภัยได้ที่ข้อมูลส่วนตัวหรือเมนูสมาชิก หรือ <a href="forum.php?mod=memcp&action=profile&typeid=1" target="_blank">คลิกที่นี่เพื่อ</a> เพื่อตั้งค่าคำถามความปลอดภัยของคุณ',

	'login_cp_guest' => '<b>คำขอนี้ถูกปฏิเสธเนื่องจากคุณไม่ได้เข้าสู่ระบบ</b><br><br>กรุณา<a href="member.php?mod=logging&action=login">เข้าสู่ระบบ</a>และลองอีกครั้ง',
	'login_cplock' => 'การลงชื่อเข้าใช้การจัดการของคุณถูกล็อก! <br>กรุณารอ อีก<b> {ltime} </b>วินาที แล้วค่อยลองใหม่อีกครั้ง',
	'login_user_lock' => 'คุณใส่รหัสผ่านผิดหลายครั้งเกินไป การร้องขอการลงชื่อเข้าใช้นี้ถูกปฏิเสธ กรุณาลองอีกครั้งหลังจาก 15 นาที',
	'login_cp_noaccess' => '<b>คุณไม่ได้รับอนุญาตให้เข้าใช้งาน</b><br><br>ระบบได้บันทึกการกระทำของคุณไว้แล้ว ดังนั้นอย่าพยายามทำผิดกฎ',
	'noaccess' => 'คุณไม่ได้รับอนุญาตให้เข้าถึงการตั้งค่าระบบ กรุณาติดต่อผู้ดูแลเว็บไซต์',


);

?>