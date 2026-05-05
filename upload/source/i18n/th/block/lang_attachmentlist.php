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
	'attachmentlist_name' => 'รายการไฟล์แนบเว็บบอร์ด',
	'attachmentlist_desc' => 'ดึงข้อมูลรายการไฟล์แนบจากเว็บบอร์ดมาแสดงผล',
	'attachmentlist_fids' => 'บอร์ดที่สังกัด',
	'attachmentlist_fids_comment' => 'เลือกบอร์ดที่อนุญาตให้ดึงข้อมูลรูปภาพมาแสดงผล สามารถกด CTRL ค้างไว้เพื่อเลือกหลายบอร์ด หากเลือกทั้งหมดหรือไม่ได้เลือกเลยจะถือว่าไม่จำกัดบอร์ด',
	'attachmentlist_tids' => 'ระบุกระทู้โดยเฉพาะ',
	'attachmentlist_tids_comment' => 'ระบุไอดีกระทู้ (tid) ที่ต้องการ หากมีหลายไอดีให้แยกด้วยเครื่องหมายคอมมา ","',
	'attachmentlist_startrow' => 'แถวเริ่มต้นของข้อมูล',
	'attachmentlist_startrow_comment' => 'หากต้องการกำหนดแถวเริ่มต้นของข้อมูล โปรดระบุตัวเลข (0 คือเริ่มจากแถวแรก)',
	'attachmentlist_items' => 'จำนวนรายการที่แสดง',
	'attachmentlist_items_comment' => 'ระบุจำนวนกระทู้ที่มีไฟล์แนบรูปภาพที่จะแสดงผล โปรดระบุเป็นตัวเลขจำนวนเต็มที่มากกว่า 0',
	'attachmentlist_titlelength' => 'ความยาวของหัวข้อ',
	'attachmentlist_titlelength_comment' => 'กำหนดความยาวสูงสุดสำหรับแสดงชื่อไฟล์แนบหรือหัวข้อกระทู้',
	'attachmentlist_summarylength' => 'ความยาวของเนื้อหา',
	'attachmentlist_summarylength_comment' => 'กำหนดความยาวสูงสุดสำหรับแสดงรายละเอียดไฟล์แนบหรือเนื้อหาในโพสต์',
	'attachmentlist_maxwidth' => 'ความกว้างสูงสุดของรูปภาพ (พิกเซล)',
	'attachmentlist_maxwidth_comment' => 'กำหนดความกว้างสูงสุดของรูปภาพที่จะให้ระบบปรับขนาดให้อัตโนมัติ (0 คือไม่ปรับขนาด)',
	'attachmentlist_maxheight' => 'ความสูงสูงสุดของรูปภาพ (พิกเซล)',
	'attachmentlist_maxheight_comment' => 'กำหนดความสูงสูงสุดของรูปภาพที่จะให้ระบบปรับขนาดให้อัตโนมัติ (0 คือไม่ปรับขนาด)',
	'attachmentlist_threadmethod' => 'ดึงข้อมูลตามรูปแบบกระทู้',
	'attachmentlist_threadmethod_comment' => 'เลือก "ใช่" เพื่อดึงไฟล์แนบตามรายการกระทู้ (1 กระทู้แสดง 1 ไฟล์แนบ) หรือเลือก "ไม่" เพื่อดึงข้อมูลตามรายการไฟล์แนบโดยตรง',
	'attachmentlist_digest' => 'คัดกรองกระทู้สำคัญ',
	'attachmentlist_digest_comment' => 'กำหนดขอบเขตตามระดับความสำคัญของกระทู้ หมายเหตุ: หากเลือกทั้งหมดหรือไม่ได้เลือกเลยจะถือว่าไม่จำกัดความสำคัญ',
	'attachmentlist_digest_0' => 'กระทู้ทั่วไป',
	'attachmentlist_digest_1' => 'สำคัญ ระดับ I',
	'attachmentlist_digest_2' => 'สำคัญ ระดับ II',
	'attachmentlist_digest_3' => 'สำคัญ ระดับ III',
	'attachmentlist_special' => 'คัดกรองกระทู้พิเศษ',
	'attachmentlist_special_comment' => 'เลือกดึงข้อมูลตามประเภทของกระทู้พิเศษ หมายเหตุ: หากเลือกทั้งหมดหรือไม่ได้เลือกเลยจะถือว่าไม่จำกัดประเภท',
	'attachmentlist_special_1' => 'กระทู้แบบสำรวจ',
	'attachmentlist_special_2' => 'กระทู้ค้าขาย',
	'attachmentlist_special_3' => 'กระทู้รางวัล',
	'attachmentlist_special_4' => 'กระทู้กิจกรรม',
	'attachmentlist_special_5' => 'กระทู้โต้วาที',
	'attachmentlist_special_0' => 'กระทู้ทั่วไป',
	'attachmentlist_special_reward' => 'คัดกรองกระทู้รางวัล',
	'attachmentlist_special_reward_comment' => 'ระบุประเภทของกระทู้รางวัลที่ต้องการดึงข้อมูล',
	'attachmentlist_special_reward_0' => 'ทั้งหมด',
	'attachmentlist_special_reward_1' => 'แก้ปัญหาแล้ว',
	'attachmentlist_special_reward_2' => 'ยังไม่แก้ปัญหา',
	'attachmentlist_dateline' => 'เวลาที่อัปโหลดไฟล์แนบ',
	'attachmentlist_dateline_nolimit' => 'ไม่จำกัด',
	'attachmentlist_dateline_hour' => 'ภายใน 1 ชั่วโมง',
	'attachmentlist_dateline_day' => 'ภายใน 24 ชั่วโมง',
	'attachmentlist_dateline_week' => 'ภายใน 1 สัปดาห์',
	'attachmentlist_dateline_month' => 'ภายใน 1 เดือน',
	'attachmentlist_highlight' => 'รับค่าไฮไลต์',

	];

