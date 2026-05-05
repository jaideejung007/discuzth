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

	'feed_blog_password' => '{actor} ได้เผยแพร่ไดอารี่ใหม่ที่มีการป้องกันด้วยรหัสผ่าน {subject}',
	'feed_blog_title' => '{actor} ได้เผยแพร่ไดอารี่ใหม่',
	'feed_blog_body' => '<b>{subject}</b><br />{summary}',
	'feed_album_title' => '{actor} ได้อัปเดตอัลบั้มรูป',
	'feed_album_body' => '<b>{album}</b><br />รวม {picnum} รูปภาพ',
	'feed_pic_title' => '{actor} ได้อัปโหลดรูปภาพใหม่',
	'feed_pic_body' => '{title}',


	'feed_poll' => '{actor} ได้สร้างโพลโหวตใหม่',

	'feed_comment_space' => '{actor} ได้ฝากสมุดเยี่ยมไว้บนกระดานสมุดเยี่ยมของ {touser}',
	'feed_comment_image' => '{actor} ได้แสดงความคิดเห็นในรูปภาพของ {touser}',
	'feed_comment_blog' => '{actor} ได้แสดงความคิดเห็นในไดอารี่ {blog} ของ {touser}',
	'feed_comment_poll' => '{actor} ได้แสดงความคิดเห็นในโพล {poll} ของ {touser}',
	'feed_comment_event' => '{actor} ได้ฝากข้อความในกิจกรรม {event} ที่จัดโดย {touser}',
	'feed_comment_share' => '{actor} ได้แสดงความคิดเห็นในการแชร์ {share} ของ {touser}',

	'feed_showcredit' => '{actor} ได้มอบเครดิตติดอันดับจำนวน {credit} เครดิต ให้แก่ {fusername} เพื่อช่วยเพิ่มอันดับใน <a href="misc.php?mod=ranklist&type=member" target="_blank">ท็อปชาร์ตยอดนิยม</a>',
	'feed_showcredit_self' => '{actor} ได้เพิ่มเครดิตติดอันดับจำนวน {credit} เครดิต เพื่อเพิ่มอันดับของตัวเองใน <a href="misc.php?mod=ranklist&type=member" target="_blank">ท็อปชาร์ตยอดนิยม</a>',
	'feed_doing_title' => '{actor}：{message}',
	'feed_friend_title' => '{actor} และ {touser} ได้กลายเป็นเพื่อนกันเรียบร้อยแล้ว',


	'feed_click_blog' => '{actor} ได้ส่ง “{click}” ให้กับไดอารี่ {subject} ของ {touser}',
	'feed_click_thread' => '{actor} ได้ส่ง “{click}” ให้กับกระทู้ {subject} ของ {touser}',
	'feed_click_pic' => '{actor} ได้ส่ง “{click}” ให้กับรูปภาพของ {touser}',
	'feed_click_article' => '{actor} ได้ส่ง “{click}” ให้กับบทความ {subject} ของ {touser}',


	'feed_task' => '{actor} ทำภารกิจสำเร็จ: {task}',
	'feed_task_credit' => '{actor} ทำภารกิจสำเร็จ: {task} และได้รับรางวัล {credit} เครดิต',

	'feed_profile_update_base' => '{actor} ได้อัปเดตข้อมูลพื้นฐานของตนเอง',
	'feed_profile_update_contact' => '{actor} ได้อัปเดตข้อมูลการติดต่อของตนเอง',
	'feed_profile_update_edu' => '{actor} ได้อัปเดตประวัติการศึกษาของตนเอง',
	'feed_profile_update_work' => '{actor} ได้อัปเดตข้อมูลการทำงานของตนเอง',
	'feed_profile_update_info' => '{actor} ได้อัปเดตข้อมูลส่วนตัวของตนเอง',
	'feed_profile_update_bbs' => '{actor} ได้อัปเดตข้อมูลเว็บบอร์ดของตนเอง',
	'feed_profile_update_verify' => '{actor} ได้อัปเดตข้อมูลการยืนยันตัวตนของตนเอง',

	'feed_add_attachsize' => '{actor} ใช้ {credit} เครดิต แลกพื้นที่เก็บไฟล์แนบเพิ่ม {size} ตอนนี้สามารถอัปโหลดรูปภาพได้มากขึ้นแล้ว (<a href="home.php?mod=spacecp&ac=credit&op=addsize">ฉันต้องการแลกบ้าง</a>)',

	'feed_invite' => '{actor} ส่งคำเชิญและกลายเป็นเพื่อนกับ {username} เรียบร้อยแล้ว',

	'magicuse_thunder_announce_title' => '<strong>{username} ส่ง “เสียงคำรามกึกก้อง”</strong>',
	'magicuse_thunder_announce_body' => 'สวัสดีทุกคน ผมออนไลน์แล้วนะ!<br /><a href="home.php?mod=space&uid={uid}" target="_blank">แวะมาทักทายที่สเปซของผมได้นะครับ</a>',


	'feed_thread_title' => '{actor} ได้ตั้งกระทู้ใหม่',
	'feed_thread_message' => '<b>{subject}</b><br />{message}',

	'feed_reply_title' => '{actor} ได้ตอบกลับกระทู้ของ {author}: {subject}',
	'feed_reply_title_anonymous' => '{actor} ได้ตอบกลับกระทู้ {subject}',
	'feed_reply_message' => '',

	'feed_thread_poll_title' => '{actor} ได้สร้างโพลโหวตใหม่',
	'feed_thread_poll_message' => '<b>{subject}</b><br />{message}',

	'feed_thread_votepoll_title' => '{actor} ได้ร่วมลงคะแนนในโพล: {subject}',
	'feed_thread_votepoll_message' => '',

	'feed_thread_goods_title' => '{actor} ได้ลงขายสินค้าใหม่',
	'feed_thread_goods_message_1' => '<b>{itemname}</b><br />ราคา {itemprice} บาท และใช้ {itemcredit} {creditunit}',
	'feed_thread_goods_message_2' => '<b>{itemname}</b><br />ราคา {itemprice} บาท',
	'feed_thread_goods_message_3' => '<b>{itemname}</b><br />ราคา {itemcredit} {creditunit}',

	'feed_thread_reward_title' => '{actor} ได้ตั้งกระทู้รางวัลใหม่',
	'feed_thread_reward_message' => '<b>{subject}</b><br />รางวัล {rewardprice} {extcredits}',

	'feed_reply_reward_title' => '{actor} ได้ตอบกลับกระทู้รางวัล: {subject}',
	'feed_reply_reward_message' => '',

	'feed_thread_activity_title' => '{actor} ได้สร้างกิจกรรมใหม่',
	'feed_thread_activity_message' => '<b>{subject}</b><br />เวลาเริ่มต้น：{starttimefrom}<br />สถานที่จัดกิจกรรม：{activityplace}<br />{message}',

	'feed_reply_activity_title' => '{actor} ได้ลงชื่อเข้าร่วมกิจกรรม: {subject}',
	'feed_reply_activity_message' => '',

	'feed_thread_debate_title' => '{actor} ได้สร้างกระทู้โต้วาทีใหม่',
	'feed_thread_debate_message' => '<b>{subject}</b><br />ฝ่ายสนับสนุน：{affirmpoint}<br />ฝ่ายค้าน：{negapoint}<br />{message}',

	'feed_thread_debatevote_title_1' => '{actor} เข้าร่วมการโต้วาที {subject} ในฐานะฝ่ายสนับสนุน',
	'feed_thread_debatevote_title_2' => '{actor} เข้าร่วมการโต้วาที {subject} ในฐานะฝ่ายค้าน',
	'feed_thread_debatevote_title_3' => '{actor} เข้าร่วมการโต้วาที {subject} ในฐานะฝ่ายเป็นกลาง',
	'feed_thread_debatevote_message_1' => '',
	'feed_thread_debatevote_message_2' => '',
	'feed_thread_debatevote_message_3' => '',

	];

