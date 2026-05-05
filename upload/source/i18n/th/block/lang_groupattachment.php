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
	'groupattachment_name' => 'รายการไฟล์แนบเว็บบอร์ด',
	'groupattachment_desc' => 'การดึงข้อมูลรายการไฟล์แนบจากเว็บบอร์ด',
	'groupattachment_fids' => 'ระบุวงใน',
	'groupattachment_fids_comment' => 'ระบุไอดีวงใน หากมีหลายไอดีให้ใช้เครื่องหมายคอมมา (,) แยก',
	'groupattachment_tids' => 'ระบุกระทู้',
	'groupattachment_tids_comment' => 'ระบุไอดีกระทู้ หากมีหลายไอดีให้ใช้เครื่องหมายคอมมาแยก',
	'groupattachment_gtids' => 'หมวดหมู่วงใน',
	'groupattachment_gtids_comment' => 'ระบุหมวดหมู่ที่วงในสังกัด สามารถกด CTRL ค้างไว้เพื่อเลือกหลายรายการ หรือไม่เลือกเลยเพื่อไม่จำกัด',
	'groupattachment_startrow' => 'แถวเริ่มต้นของข้อมูล',
	'groupattachment_startrow_comment' => 'ระบุแถวข้อมูลที่ต้องการเริ่มแสดงผล โดยเลข 0 คือเริ่มจากแถวแรก',
	'groupattachment_items' => 'จำนวนรายการที่แสดง',
	'groupattachment_items_comment' => 'กำหนดจำนวนรายการกระทู้ที่มีรูปภาพที่จะแสดงผลในแต่ละครั้ง โดยระบุเป็นจำนวนเต็มที่มากกว่า 0',
	'groupattachment_titlelength' => 'ความยาวชื่อ',
	'groupattachment_titlelength_comment' => 'กำหนดความยาวสูงสุดในการแสดงชื่อไฟล์แนบหรือหัวข้อโพสต์',
	'groupattachment_summarylength' => 'ความยาวเนื้อหา',
	'groupattachment_summarylength_comment' => 'กำหนดความยาวสูงสุดในการแสดงรายละเอียดไฟล์แนบหรือเนื้อหาโพสต์',
	'groupattachment_maxwidth' => 'ความกว้างสูงสุดของรูปภาพ (พิกเซล)',
	'groupattachment_maxwidth_comment' => 'กำหนดความกว้างของรูปภาพที่ต้องการให้ย่อหรือขยายโดยอัตโนมัติ โดยเลข 0 คือไม่ปรับขนาด',
	'groupattachment_maxheight' => 'ความสูงสูงสุดของรูปภาพ (พิกเซล)',
	'groupattachment_maxheight_comment' => 'กำหนดความสูงของรูปภาพที่ต้องการให้ย่อหรือขยายโดยอัตโนมัติ โดยเลข 0 คือไม่ปรับขนาด',
	'groupattachment_threadmethod' => 'รูปแบบการดึงข้อมูลกระทู้',
	'groupattachment_threadmethod_comment' => 'เลือก "ใช่" เพื่อดึงไฟล์แนบตามรายการกระทู้ (1 กระทู้แสดง 1 ไฟล์แนบ) หรือเลือก "ไม่" เพื่อดึงข้อมูลตามรายการไฟล์แนบโดยตรง',
	'groupattachment_digest' => 'คัดกรองกระทู้สำคัญ',
	'groupattachment_digest_comment' => 'กำหนดระดับความสำคัญของกระทู้ที่ต้องการดึงข้อมูล หากไม่เลือกเลยจะถือว่าไม่คัดกรอง',
	'groupattachment_digest_0' => 'กระทู้ทั่วไป',
	'groupattachment_digest_1' => 'สำคัญ ระดับ I',
	'groupattachment_digest_2' => 'สำคัญ ระดับ II',
	'groupattachment_digest_3' => 'สำคัญ ระดับ III',
	'groupattachment_special' => 'คัดกรองกระทู้พิเศษ',
	'groupattachment_special_comment' => 'กำหนดประเภทกระทู้พิเศษที่ต้องการดึงข้อมูล หากไม่เลือกเลยจะถือว่าไม่คัดกรอง',
	'groupattachment_special_1' => 'กระทู้แบบสำรวจ',
	'groupattachment_special_2' => 'กระทู้ค้าขาย',
	'groupattachment_special_3' => 'กระทู้รางวัล',
	'groupattachment_special_4' => 'กระทู้กิจกรรม',
	'groupattachment_special_5' => 'กระทู้โต้วาที',
	'groupattachment_special_0' => 'กระทู้ทั่วไป',
	'groupattachment_special_reward' => 'คัดกรองกระทู้รางวัล',
	'groupattachment_special_reward_comment' => 'ตั้งค่าการคัดกรองประเภทของกระทู้รางวัล',
	'groupattachment_special_reward_0' => 'ทั้งหมด',
	'groupattachment_special_reward_1' => 'แก้ปัญหาแล้ว',
	'groupattachment_special_reward_2' => 'ยังไม่แก้ปัญหา',
	'groupattachment_dateline' => 'ช่วงเวลาที่อัปโหลดไฟล์',
	'groupattachment_dateline_nolimit' => 'ไม่จำกัด',
	'groupattachment_dateline_hour' => 'ภายใน 1 ชั่วโมงล่าสุด',
	'groupattachment_dateline_day' => 'ภายใน 24 ชั่วโมงล่าสุด',
	'groupattachment_dateline_week' => 'ภายใน 1 สัปดาห์ล่าสุด',
	'groupattachment_dateline_month' => 'ภายใน 1 เดือนล่าสุด',
	'groupattachment_gviewperm' => 'สิทธิ์การเข้าชมวงใน',
	'groupattachment_gviewperm_nolimit' => 'ไม่จำกัด',
	'groupattachment_gviewperm_only_member' => 'เฉพาะสมาชิก',
	'groupattachment_gviewperm_all_member' => 'ทุกคน',
	'groupattachment_highlight' => 'แสดงไฮไลต์',
	];

