<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = [
	'accessKeyId' => 'accessKey Id',
	'accessKeyId_comment' => 'accessKey Id รับได้จาก "RAM Access Control" ในระบบหลังบ้านของ Alibaba Cloud โดยสร้างผู้ใช้ย่อย ตรวจสอบการอนุญาตเข้าถึงแบบ Programming Access และมอบสิทธิ์ "Manage SMS Service"',
	'accessKeySecret' => 'accessKey Secret',
	'accessKeySecret_comment' => 'accessKey Secret รับได้จาก "RAM Access Control" ในระบบหลังบ้านของ Alibaba Cloud โดยสร้างผู้ใช้ย่อย ตรวจสอบการอนุญาตเข้าถึงแบบ Programming Access และมอบสิทธิ์ "Manage SMS Service"',
	'signname' => 'ลายเซ็น SMS ในประเทศ',
	'signname_comment' => 'ลายเซ็น SMS ในประเทศ ให้ยื่นขอผ่านระบบ Alibaba Cloud SMS และเมื่อได้รับการอนุมัติแล้วให้นำชื่อลายเซ็นมาระบุที่นี่',
	'templateid' => 'ไอดีเทมเพลต SMS ในประเทศ',
	'templateid_comment' => 'ไอดีเทมเพลต SMS ในประเทศ ให้ยื่นขอผ่านระบบ Alibaba Cloud SMS และเมื่อได้รับการอนุมัติแล้วให้นำไอดีเทมเพลตมาระบุที่นี่ ตัวอย่าง: SMS_12345678',
	'signnamegj' => 'ลายเซ็น SMS ระหว่างประเทศ',
	'signnamegj_comment' => 'ลายเซ็น SMS ระหว่างประเทศ ให้ยื่นขอผ่านระบบ Alibaba Cloud SMS และเมื่อได้รับการอนุมัติแล้วให้นำชื่อลายเซ็นมาระบุที่นี่',
	'templateidgj' => 'ไอดีเทมเพลต SMS ระหว่างประเทศ',
	'templateidgj_comment' => 'ไอดีเทมเพลต SMS ระหว่างประเทศ ให้ยื่นขอผ่านระบบ Alibaba Cloud SMS และเมื่อได้รับการอนุมัติแล้วให้นำไอดีเทมเพลตมาระบุที่นี่ ตัวอย่าง: SMS_12345678',
	'senderid' => 'รหัสผู้ส่ง (Long Code) สำหรับพื้นที่สหรัฐอเมริกา แคนาดา ฯลฯ',
	'senderid_comment' => 'รหัสผู้ส่ง (Long Code) สำหรับพื้นที่สหรัฐอเมริกา แคนาดา ฯลฯ โปรดติดต่อฝ่ายบริการลูกค้าของ Alibaba Cloud เพื่อรับรหัสนี้',
	'senderidtemplate' => 'เนื้อหาข้อความฉบับเต็มสำหรับพื้นที่สหรัฐอเมริกา แคนาดา ฯลฯ',
	'senderidtemplate_comment' => 'เนื้อหาข้อความฉบับเต็มสำหรับพื้นที่สหรัฐอเมริกา แคนาดา ฯลฯ โดยยึดตามเนื้อหาที่ลงทะเบียนไว้ ตัวอย่าง: Your verification code is: ${code}.',
	'senderidareacode' => 'รหัสประเทศที่เกี่ยวข้องสำหรับพื้นที่สหรัฐอเมริกา แคนาดา ฯลฯ (แยกด้วยคอมมา)',
	'senderidareacode_comment' => 'รหัสประเทศที่เกี่ยวข้องสำหรับพื้นที่สหรัฐอเมริกา แคนาดา ฯลฯ (แยกด้วยคอมมา)',
	'alirich' => 'รหัสผู้ส่ง (Long Code) สำหรับไต้หวัน นิวซีแลนด์ ฯลฯ (คงค่า Alirich)',
	'alirich_comment' => 'รหัสผู้ส่ง (Long Code) สำหรับไต้หวัน นิวซีแลนด์ ฯลฯ (คงค่า Alirich) โปรดปรึกษาฝ่ายบริการลูกค้าของ Alibaba Cloud สำหรับรายละเอียด',
	'alirichtemplate' => 'เนื้อหาข้อความฉบับเต็มสำหรับไต้หวัน นิวซีแลนด์ ฯลฯ',
	'alirichtemplate_comment' => 'เนื้อหาข้อความฉบับเต็มสำหรับไต้หวัน นิวซีแลนด์ ฯลฯ โดยยึดตามเนื้อหาที่ลงทะเบียนไว้ ตัวอย่าง: Your verification code is: ${code}.',
	'alirichareacode' => 'รหัสประเทศที่เกี่ยวข้องสำหรับไต้หวัน นิวซีแลนด์ ฯลฯ (แยกด้วยคอมมา)',
	'alirichareacode_comment' => 'รหัสประเทศที่เกี่ยวข้องสำหรับไต้หวัน นิวซีแลนด์ ฯลฯ (แยกด้วยคอมมา) ตัวอย่าง: 886,64,62,84',
];

