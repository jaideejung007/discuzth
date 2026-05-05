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
	'articlelist_aids' => 'ระบุบทความ',
	'articlelist_aids_comment' => 'ระบุไอดีบทความ (aid) หากมีหลายบทความให้แยกด้วยเครื่องหมายคอมมา (,)',
	'articlelist_uids' => 'UID ของผู้เขียน',
	'articlelist_uids_comment' => 'ระบุไอดีผู้ใช้ (uid) ของผู้เขียน หากมีหลายคนให้แยกด้วยเครื่องหมายคอมมา (,)',
	'articlelist_startrow' => 'แถวเริ่มต้นของข้อมูล',
	'articlelist_startrow_comment' => 'หากต้องการกำหนดแถวเริ่มต้นของข้อมูล โปรดระบุตัวเลข (0 คือเริ่มจากแถวแรก)',
	'articlelist_tag' => 'แท็กรวม',
	'articlelist_tag_comment' => 'ระบุแท็กที่ต้องการดึงข้อมูลมาแสดง',
	'articlelist_titlelength' => 'ความยาวหัวข้อ',
	'articlelist_titlelength_comment' => 'กำหนดความยาวสูงสุดของหัวข้อบทความ',
	'articlelist_summarylength' => 'ความยาวเรื่องย่อ',
	'articlelist_summarylength_comment' => 'กำหนดความยาวสูงสุดของเนื้อหาเรื่องย่อ',
	'articlelist_starttime' => 'เวลาเผยแพร่ - เริ่มต้น',
	'articlelist_starttime_comment' => 'ดึงบทความที่เผยแพร่หลังจากเวลาที่ระบุ',
	'articlelist_endtime' => 'เวลาเผยแพร่ - สิ้นสุด',
	'articlelist_endtime_comment' => 'ดึงบทความที่เผยแพร่ก่อนเวลาที่ระบุ',
	'articlelist_catid' => 'หมวดหมู่บทความ',
	'articlelist_catid_comment' => 'เลือกหมวดหมู่บทความที่สังกัด',
	'articlelist_picrequired' => 'คัดกรองบทความที่ไม่มีหน้าปก',
	'articlelist_picrequired_comment' => 'กำหนดว่าจะคัดกรองบทความที่ไม่ได้ตั้งค่ารูปหน้าปกออกหรือไม่',
	'articlelist_orderby' => 'รูปแบบการเรียงลำดับ',
	'articlelist_orderby_comment' => 'เลือกฟิลด์หรือรูปแบบที่ต้องการใช้ในการเรียงลำดับบทความ',
	'articlelist_orderby_dateline' => 'เรียงตามเวลาเผยแพร่ (ล่าสุดก่อน)',
	'articlelist_orderby_viewnum' => 'เรียงตามจำนวนการเข้าชม (มากที่สุดก่อน)',
	'articlelist_orderby_commentnum' => 'เรียงตามจำนวนความคิดเห็น (มากที่สุดก่อน)',
	'articlelist_orderby_click' => 'เรียงตามจำนวนความรู้สึก {clickname} (มากที่สุดก่อน)',
	'articlelist_publishdateline' => 'ช่วงเวลาที่เผยแพร่',
	'articlelist_publishdateline_nolimit' => 'ไม่จำกัด',
	'articlelist_publishdateline_hour' => 'ภายใน 1 ชั่วโมง',
	'articlelist_publishdateline_day' => 'ภายใน 24 ชั่วโมง',
	'articlelist_publishdateline_week' => 'ภายใน 7 วัน',
	'articlelist_publishdateline_month' => 'ภายใน 1 เดือน',
	'articlelist_keyword' => 'คำค้นหาหัวข้อ',
	'articlelist_keyword_comment' => 'ระบุคำค้นหาที่ต้องการให้มีในหัวข้อบทความ หมายเหตุ: เว้นว่างไว้หากไม่ต้องการคัดกรอง; สามารถใช้เครื่องหมายดอกจัน * เป็นตัวแทนคำได้; หากต้องการค้นหาหลายคำพร้อมกันให้ใช้ช่องว่างหรือ AND เชื่อม (เช่น win32 AND unix); หากต้องการค้นหาคำใดคำหนึ่งให้ใช้ | หรือ OR เชื่อม (เช่น win32 OR unix)',
	];

