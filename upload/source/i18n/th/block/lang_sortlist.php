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
	'sortlist_fids' => 'บอร์ดที่เกี่ยวข้อง',
	'sortlist_fids_comment' => 'ตั้งค่าบอร์ดที่อนุญาตให้ดึงข้อมูลกระทู้ใหม่มาแสดงผล สามารถกด CTRL ค้างไว้เพื่อเลือกหลายรายการ หรือไม่เลือกเลยเพื่อไม่จำกัด',
	'sortlist_startrow' => 'แถวเริ่มต้นของข้อมูล',
	'sortlist_startrow_comment' => 'ระบุแถวข้อมูลที่ต้องการเริ่มแสดงผล โดยเลข 0 คือเริ่มจากแถวแรก',
	'sortlist_showitems' => 'จำนวนรายการที่แสดง',
	'sortlist_showitems_comment' => 'กำหนดจำนวนรายการกระทู้ที่จะแสดงผลในแต่ละครั้ง โดยระบุเป็นจำนวนเต็มที่มากกว่า 0',
	'sortlist_titlelength' => 'ความยาวหัวข้อสูงสุด',
	'sortlist_titlelength_comment' => 'กำหนดความยาวสูงสุดของหัวข้อกระทู้ หากยาวเกินจะถูกย่อให้อัตโนมัติ โดยเลข 0 คือไม่จำกัด',
	'sortlist_fnamelength' => 'ความยาวหัวข้อรวมชื่อบอร์ด',
	'sortlist_fnamelength_comment' => 'ตั้งค่าว่าจะนำความยาวของชื่อบอร์ดมาคำนวณรวมกับความยาวหัวข้อด้วยหรือไม่',
	'sortlist_summarylength' => 'ความยาวเนื้อหาเรื่องย่อ',
	'sortlist_summarylength_comment' => 'กำหนดจำนวนตัวอักษรของเนื้อหาเรื่องย่อที่จะแสดง โดยเลข 0 คือใช้ค่าเริ่มต้น 255',
	'sortlist_tids' => 'ระบุกระทู้',
	'sortlist_tids_comment' => 'ระบุไอดีกระทู้ (tid) ที่ต้องการแสดง หากมีหลายไอดีให้ใช้เครื่องหมายคอมมา (,) แยก โดยเว้นว่างไว้หากไม่คัดกรอง',
	'sortlist_keyword' => 'คำค้นหาหัวข้อ',
	'sortlist_keyword_comment' => 'ระบุคำสำคัญที่ต้องการค้นหาในหัวข้อ โดยเว้นว่างไว้หากไม่คัดกรอง สามารถใช้ * แทนคำใด ๆ ได้ หรือใช้ AND/OR เพื่อระบุเงื่อนไข',
	'sortlist_typeids' => 'หมวดหมู่กระทู้',
	'sortlist_typeids_comment' => 'เลือกดึงข้อมูลตามหมวดหมู่กระทู้ที่กำหนด โดยเว้นว่างไว้หากไม่คัดกรอง',
	'sortlist_typeids_all' => 'หมวดหมู่กระทู้ทั้งหมด',
	'sortlist_sortids' => 'หมวดหมู่ข้อมูล',
	'sortlist_sortids_comment' => 'เลือกดึงข้อมูลตามหมวดหมู่ข้อมูลที่กำหนด โดยเว้นว่างไว้หากไม่คัดกรอง',
	'sortlist_sortids_all' => 'หมวดหมู่ข้อมูลทั้งหมด',
	'sortlist_digest' => 'คัดกรองกระทู้สำคัญ',
	'sortlist_digest_comment' => 'กำหนดระดับความสำคัญของกระทู้ที่ต้องการดึงข้อมูล หากไม่เลือกเลยจะถือว่าไม่คัดกรอง',
	'sortlist_digest_0' => 'กระทู้ทั่วไป',
	'sortlist_digest_1' => 'สำคัญ ระดับ I',
	'sortlist_digest_2' => 'สำคัญ ระดับ II',
	'sortlist_digest_3' => 'สำคัญ ระดับ III',
	'sortlist_stick' => 'คัดกรองกระทู้ปักหมุด',
	'sortlist_stick_comment' => 'กำหนดขอบเขตของการปักหมุดกระทู้ หากไม่เลือกเลยจะถือว่าไม่คัดกรอง',
	'sortlist_stick_0' => 'กระทู้ทั่วไป',
	'sortlist_stick_1' => 'ปักหมุด ระดับ I',
	'sortlist_stick_2' => 'ปักหมุด ระดับ II',
	'sortlist_stick_3' => 'ปักหมุด ระดับ III',
	'sortlist_special' => 'คัดกรองกระทู้พิเศษ',
	'sortlist_special_comment' => 'กำหนดประเภทกระทู้พิเศษที่ต้องการดึงข้อมูล หากไม่เลือกเลยจะถือว่าไม่คัดกรอง',
	'sortlist_special_1' => 'กระทู้แบบสำรวจ',
	'sortlist_special_2' => 'กระทู้ค้าขาย',
	'sortlist_special_3' => 'กระทู้รางวัล',
	'sortlist_special_4' => 'กระทู้กิจกรรม',
	'sortlist_special_5' => 'กระทู้โต้วาที',
	'sortlist_special_0' => 'กระทู้ทั่วไป',
	'sortlist_special_reward' => 'คัดกรองกระทู้รางวัล',
	'sortlist_special_reward_comment' => 'ตั้งค่าการคัดกรองประเภทของกระทู้รางวัล',
	'sortlist_special_reward_0' => 'ทั้งหมด',
	'sortlist_special_reward_1' => 'แก้ปัญหาแล้ว',
	'sortlist_special_reward_2' => 'ยังไม่แก้ปัญหา',
	'sortlist_recommend' => 'คัดกรองกระทู้แนะนำ',
	'sortlist_recommend_comment' => 'เลือกว่าจะแสดงเฉพาะกระทู้ที่ได้รับการแนะนำเท่านั้นหรือไม่',
	'sortlist_orderby' => 'รูปแบบการจัดเรียงกระทู้',
	'sortlist_orderby_comment' => 'ตั้งค่าฟิลด์หรือรูปแบบที่ใช้ในการจัดเรียงกระทู้',
	'sortlist_orderby_lastpost' => 'เรียงตามเวลาการตอบกลับล่าสุด (ล่าสุดก่อน)',
	'sortlist_orderby_dateline' => 'เรียงตามเวลาที่เผยแพร่ (ล่าสุดก่อน)',
	'sortlist_orderby_replies' => 'เรียงตามจำนวนการตอบกลับ (มากที่สุดก่อน)',
	'sortlist_orderby_views' => 'เรียงตามจำนวนการเข้าชม (มากที่สุดก่อน)',
	'sortlist_orderby_heats' => 'เรียงตามยอดนิยม (สูงสุดก่อน)',
	'sortlist_orderby_recommends' => 'เรียงตามการประเมินกระทู้ (ดีที่สุดก่อน)',
	'sortlist_lastpost' => 'เวลาที่เผยแพร่กระทู้',
	'sortlist_lastpost_nolimit' => 'ไม่จำกัด',
	'sortlist_lastpost_hour' => 'ภายใน 1 ชั่วโมงล่าสุด',
	'sortlist_lastpost_day' => 'ภายใน 1 วันล่าสุด',
	'sortlist_lastpost_week' => 'ภายใน 1 สัปดาห์ล่าสุด',
	'sortlist_lastpost_month' => 'ภายใน 1 เดือนล่าสุด',
	'sortlist_orderby_hours_comment' => 'ระบุจำนวนชั่วโมงย้อนหลังเพื่อคำนวณยอดการเข้าชมสำหรับการจัดเรียง',
	];

