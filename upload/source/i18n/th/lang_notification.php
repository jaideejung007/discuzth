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

	'type_wall' => 'กระดานข้อความ',
	'type_piccomment' => 'ความคิดเห็นรูปภาพ',
	'type_blogcomment' => 'ความคิดเห็นไดอารี่',
	'type_clickblog' => 'แสดงความรู้สึกต่อไดอารี่',
	'type_clickarticle' => 'แสดงความรู้สึกต่อบทความ',
	'type_clickpic' => 'แสดงความรู้สึกต่อรูปภาพ',
	'type_sharecomment' => 'ความคิดเห็นต่อการแชร์',
	'type_doing' => 'สเตตัส',
	'type_friend' => 'เพื่อน',
	'type_credit' => 'เครดิต',
	'type_bbs' => 'เว็บบอร์ด',
	'type_system' => 'ระบบ',
	'type_thread' => 'กระทู้',
	'type_task' => 'ภารกิจ',
	'type_group' => 'วงใน',

	'mail_to_user' => 'มีการแจ้งเตือนใหม่ถึงคุณ',
	'showcredit' => '{actor} มอบเครดิตติดอันดับให้คุณจำนวน {credit} เครดิต เพื่อช่วยเพิ่มอันดับของคุณใน <a href="misc.php?mod=ranklist&type=member" target="_blank">ท็อปชาร์ตยอดนิยม</a>',
	'share_space' => '{actor} แชร์พื้นที่สเปซของคุณ',
	'share_blog' => '{actor} แชร์ไดอารี่ของคุณ <a href="{url}" target="_blank">{subject}</a>',
	'share_album' => '{actor} แชร์อัลบั้มรูปของคุณ <a href="{url}" target="_blank">{albumname}</a>',
	'share_pic' => '{actor} แชร์รูปภาพในอัลบั้ม {albumname} ของคุณ <a href="{url}" target="_blank"> ดูรูปภาพ</a>',
	'share_thread' => '{actor} แชร์กระทู้ของคุณ <a href="{url}" target="_blank">{subject}</a>',
	'share_article' => '{actor} แชร์บทความของคุณ <a href="{url}" target="_blank">{subject}</a>',
	'magic_present_note' => 'ส่งไอเท็มเวทย์ให้คุณ: <a href="{url}" target="_blank">{name}</a>',
	'friend_add' => '{actor} และคุณกลายเป็นเพื่อนกันเรียบร้อยแล้ว',
	'friend_request' => '{actor} ส่งคำขอเพิ่มคุณเป็นเพื่อน {note}&nbsp;&nbsp;<a onclick="showWindow(this.id, this.href, \'get\', 0);" class="xw1" id="afr_{uid}" href="{url}">อนุมัติคำขอ</a>',
	'doing_reply' => '{actor} ตอบกลับสเตตัสของคุณ <a href="{url}" target="_blank">{summery}</a> &nbsp; <a href="{url}" target="_blank" class="lit">ดูรายละเอียด</a>',
	'wall_reply' => '{actor} ตอบกลับ <a href="{url}" target="_blank">ข้อความบนกระดาน</a> ของคุณ',
	'pic_comment_reply' => '{actor} ตอบกลับ <a href="{url}" target="_blank">ความคิดเห็นรูปภาพ</a> ของคุณ',
	'blog_comment_reply' => '{actor} ตอบกลับ <a href="{url}" target="_blank">ความคิดเห็นไดอารี่</a> ของคุณ',
	'share_comment_reply' => '{actor} ตอบกลับ <a href="{url}" target="_blank">ความคิดเห็นการแชร์</a> ของคุณ',
	'wall' => '{actor} ฝาก <a href="{url}" target="_blank">สมุดเยี่ยม</a> ไว้บนกระดานสมุดเยี่ยมของคุณ',
	'pic_comment' => '{actor} แสดงความคิดเห็นใน <a href="{url}" target="_blank">รูปภาพ</a> ของคุณ',
	'blog_comment' => '{actor} แสดงความคิดเห็นในไดอารี่ <a href="{url}" target="_blank">{subject}</a> ของคุณ',
	'share_comment' => '{actor} แสดงความคิดเห็นใน <a href="{url}" target="_blank">การแชร์</a> ของคุณ',
	'click_blog' => '{actor} แสดงความรู้สึกต่อไดอารี่ <a href="{url}" target="_blank">{subject}</a> ของคุณ',
	'click_pic' => '{actor} แสดงความรู้สึกต่อ <a href="{url}" target="_blank">รูปภาพ</a> ของคุณ',
	'click_article' => '{actor} แสดงความรู้สึกต่อบทความ <a href="{url}" target="_blank">{subject}</a> ของคุณ',
	'show_out' => 'หลังจากที่ {actor} เข้าชมหน้าหลักของคุณ เครดิตสุดท้ายในท็อปชาร์ตยอดนิยมของคุณถูกใช้ไปแล้ว',
	'puse_article' => 'ยินดีด้วยครับ <a href="{url}" target="_blank">{subject}</a> ของคุณถูกเพิ่มเข้าสู่รายการบทความเรียบร้อยแล้ว <a href="{newurl}" target="_blank">คลิกเพื่อเข้าชม</a>',

	'group_member_join' => '{actor} ส่งคำขอเข้าร่วมวงใน <a href="forum.php?mod=group&fid={fid}" target="_blank">{groupname}</a> ของคุณ โปรดตรวจสอบได้ที่ <a href="{url}" target="_blank">ศูนย์จัดการวงใน</a>',
	'group_member_invite' => '{actor} เชิญคุณเข้าร่วมวงใน <a href="forum.php?mod=group&fid={fid}" target="_blank">{groupname}</a> <a href="{url}" target="_blank">คลิกเพื่อเข้าร่วมทันที</a>',
	'group_member_check' => 'คุณผ่านการคัดกรองเข้าร่วมวงใน <a href="{url}" target="_blank">{groupname}</a> แล้ว <a href="{url}" target="_blank">คลิกที่นี่เพื่อเข้าชม</a>',
	'group_member_check_failed' => 'คุณไม่ผ่านการคัดกรองเข้าร่วมวงใน <a href="{url}" target="_blank">{groupname}</a>',
	'group_mod_check' => 'วงใน <a href="{url}" target="_blank">{groupname}</a> ที่คุณสร้างได้รับการอนุมัติแล้ว <a href="{url}" target="_blank">คลิกที่นี่เพื่อเข้าชม</a>',

	'reason_moderate' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} {modaction} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_merge' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} {modaction} (รวมกระทู้) <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_delete_post' => 'โพสต์ของคุณในกระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ถูก {actor} ลบออก <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_delete_comment' => 'เรตติ้งความคิดเห็นของคุณในกระทู้ <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank">{subject}</a> ถูก {actor} ลบออก <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_ban_post' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} {modaction} (ซ่อนโพสต์) <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_warn_post' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} {modaction} (ส่งคำเตือน)<br />
หากคุณได้รับคำเตือนสะสม {warninglimit} ครั้งภายใน {warningexpiration} วัน คุณจะถูกระงับการโพสต์โดยอัตโนมัติเป็นเวลา {warningexpiration} วัน<br />
จนถึงขณะนี้ คุณถูกเตือนไปแล้ว {authorwarnings} ครั้ง โปรดระมัดระวัง! <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_move' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} ย้ายไปยังบอร์ด <a href="forum.php?mod=forumdisplay&fid={tofid}" target="_blank">{toname}</a> <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_copy' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} คัดลอกไปยัง <a href="forum.php?mod=viewthread&tid={threadid}" target="_blank">{subject}</a> <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_remove_reward' => 'กระทู้รางวัล <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} ยกเลิกสถานะรางวัล <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_stamp_update' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} เพิ่มตราประทับ {stamp} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_stamp_delete' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} ยกเลิกตราประทับ <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_stamplist_update' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} เพิ่มไอคอน {stamp} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_stamplist_delete' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูก {actor} ยกเลิกไอคอน <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_stickreply' => 'โพสต์ตอบกลับของคุณในกระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ถูก {actor} ปักหมุดไว้ด้านบน <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_stickdeletereply' => 'โพสต์ตอบกลับของคุณในกระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ถูก {actor} ยกเลิกการปักหมุด <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_quickclear' => '{cleartype} ของคุณถูกล้างข้อมูลโดย {actor} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'reason_live_update' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูกตั้งค่าเป็นกระทู้ถ่ายทอดสดโดย {actor} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',
	'reason_live_cancle' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูกยกเลิกการถ่ายทอดสดโดย {actor} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'modthreads_delete' => 'กระทู้ {threadsubject} ที่คุณเผยแพร่ถูกปฏิเสธโดยผู้ดูแล {modusername} และถูกลบออกแล้ว',

	'modthreads_delete_reason' => 'กระทู้ {threadsubject} ที่คุณเผยแพร่ถูกปฏิเสธโดยผู้ดูแล {modusername} และถูกลบออกแล้ว <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',
	'modthreads_dismiss' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{threadsubject}</a> ของคุณถูกตีกลับโดยผู้ดูแล {modusername} และถูกเก็บไว้ในฉบับร่าง โปรดแก้ไขและส่งใหม่อีกครั้ง!',

	'modthreads_dismiss_reason' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{threadsubject}</a> ของคุณถูกตีกลับโดยผู้ดูแล {modusername} และถูกเก็บไว้ในฉบับร่าง โปรดแก้ไขและส่งใหม่อีกครั้ง! <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',
	'modthreads_validate' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{threadsubject}</a> ที่คุณเผยแพร่ได้รับการอนุมัติโดยผู้ดูแล {modusername} เรียบร้อยแล้ว! &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',

	'modreplies_delete' => 'โพสต์ตอบกลับของคุณถูกปฏิเสธโดยผู้ดูแล {modusername} และถูกลบออกแล้ว <p class="summary">เนื้อหา: <span>{post}</span></p>',

	'modreplies_delete_reason' => 'โพสต์ตอบกลับของคุณถูกปฏิเสธโดยผู้ดูแล {modusername} และถูกลบออกแล้ว <p class="summary">เนื้อหา: <span>{post}</span></p><div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'modreplies_dismiss' => 'โพสต์ตอบกลับของคุณถูกตีกลับโดยผู้ดูแล {modusername}! &nbsp; <a href="forum.php?mod=post&action=edit&fid={fid}&pid={pid}&tid={tid}" target="_blank" class="lit">แก้ไขใหม่ &rsaquo;</a> <p class="summary">เนื้อหา: <span>{post}</span></p>',

	'modreplies_dismiss_reason' => 'โพสต์ตอบกลับของคุณถูกตีกลับโดยผู้ดูแล {modusername}! &nbsp; <a href="forum.php?mod=post&action=edit&fid={fid}&pid={pid}&tid={tid}" target="_blank" class="lit">แก้ไขใหม่ &rsaquo;</a> <p class="summary">เนื้อหา: <span>{post}</span></p><div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'modreplies_validate' => 'โพสต์ตอบกลับของคุณได้รับการอนุมัติโดยผู้ดูแล {modusername} เรียบร้อยแล้ว! &nbsp; <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a> <p class="summary">เนื้อหา: <span>{post}</span></p>',

	'transfer' => 'คุณได้รับเครดิตโอนจาก {actor} จำนวน {credit} &nbsp; <a href="home.php?mod=spacecp&ac=credit&op=log&suboperation=creditslog" target="_blank" class="lit">ดูประวัติ &rsaquo;</a>
<p class="summary">{actor} ฝากข้อความว่า: <span>{transfermessage}</span></p>',

	'addfunds' => 'คำขอเติมเครดิตของคุณเสร็จสมบูรณ์แล้ว เครดิตได้ถูกเพิ่มเข้าบัญชีของคุณเรียบร้อยแล้ว &nbsp; <a href="home.php?mod=spacecp&ac=credit&op=base" target="_blank" class="lit">ดูเครดิตของฉัน &rsaquo;</a>
<p class="summary">หมายเลขรายการ: <span>{orderid}</span></p><p class="summary">รายจ่าย: <span>{price} บาท</span></p><p class="summary">รายรับ: <span>{value}</span></p>',

	'rate_reason' => 'โพสต์ของคุณในกระทู้ <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank">{subject}</a> ถูกให้คะแนนเรตติ้ง {ratescore} โดย {actor} <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'recommend_note_post' => 'ยินดีด้วยครับ โพสต์ของคุณ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ได้รับการคัดเลือกโดยบรรณาธิการ',

	'rate_removereason' => 'เรตติ้งคะแนน {ratescore} ในกระทู้ <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank">{subject}</a> ของคุณ <div class="quote"><blockquote>เหตุผลเดิม: {reason}</blockquote></div> ได้ถูกยกเลิกโดย {actor}',

	'trade_seller_send' => '<a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> ได้ซื้อสินค้า <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> ของคุณและชำระเงินแล้ว โปรดดำเนินการจัดส่งสินค้า &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a>',

	'trade_buyer_confirm' => 'สินค้า <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> ที่คุณซื้อ ได้ถูกจัดส่งโดย <a href="home.php?mod=space&uid={sellerid}" target="_blank">{seller}</a> แล้ว โปรดยืนยันการรับสินค้า &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a>',

	'trade_fefund_success' => 'คืนเงินค่าสินค้า <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> สำเร็จแล้ว &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ให้คะแนนประเมิน &rsaquo;</a>',

	'trade_success' => 'รายการซื้อขายสินค้า <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> เสร็จสมบูรณ์แล้ว &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ให้คะแนนประเมิน &rsaquo;</a>',

	'trade_order_update_sellerid' => 'ผู้ขาย <a href="home.php?mod=space&uid={sellerid}" target="_blank">{seller}</a> ได้แก้ไขข้อมูลรายการซื้อขายสินค้า <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> โปรดยืนยันการเปลี่ยนแปลง &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a>',

	'trade_order_update_buyerid' => 'ผู้ซื้อ <a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> ได้แก้ไขข้อมูลรายการซื้อขายสินค้า <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> โปรดยืนยันการเปลี่ยนแปลง &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a>',

	'eccredit' => '{actor} ได้ให้คะแนนประเมินความน่าเชื่อถือแก่คุณ &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">ให้คะแนนตอบกลับ &rsaquo;</a>',

	'activity_notice' => '{actor} ส่งคำขอเข้าร่วมกิจกรรม <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณ โปรดพิจารณาอนุมัติ &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a>',

	'activity_apply' => 'ผู้จัดกิจกรรม {actor} ได้อนุมัติคำขอเข้าร่วมกิจกรรม <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณแล้ว &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a> <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'activity_replenish' => 'ผู้จัดกิจกรรม {actor} แจ้งให้คุณกรอกข้อมูลการลงทะเบียนกิจกรรม <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> เพิ่มเติม &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a> <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'activity_delete' => 'ผู้จัดกิจกรรม {actor} ได้ปฏิเสธคำขอเข้าร่วมกิจกรรม <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณ &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a> <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'activity_cancel' => '{actor} ได้ยกเลิกการเข้าร่วมกิจกรรม <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณ &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">ดูรายละเอียด &rsaquo;</a> <div class="quote"><blockquote>เหตุผล: {reason}</blockquote></div>',

	'activity_notification' => 'มีการแจ้งเตือนจากผู้จัดกิจกรรม {actor} เกี่ยวกับกิจกรรม <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">ดูรายละเอียดกิจกรรม &rsaquo;</a> <div class="quote"><blockquote>ข้อความ: {msg}</blockquote></div>',

	'reward_question' => 'กระทู้รางวัล <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณได้รับการเลือกคำตอบยอดเยี่ยมโดย {actor} แล้ว &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',

	'reward_bestanswer' => 'การตอบกลับของคุณได้รับการเลือกให้เป็นคำตอบยอดเยี่ยมในกระทู้รางวัล <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> โดยเจ้าของกระทู้ {actor} &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',

	'reward_bestanswer_moderator' => 'การตอบกลับของคุณในกระทู้รางวัล <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ได้รับการเลือกให้เป็นคำตอบยอดเยี่ยม &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',

	'comment_add' => '{actor} ได้ให้เรตติ้งความคิดเห็นในโพสต์ที่คุณเคยเขียนไว้ในกระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> &nbsp; <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',

	'reppost_noticeauthor' => '{actor} ได้ตอบกลับโพสต์ของคุณในกระทู้ <a href="forum.php?mod=redirect&goto=findpost&ptid={tid}&pid={pid}" target="_blank">{subject}</a> &nbsp; <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank" class="lit">เข้าชม</a>',

	'task_reward_credit' => 'ยินดีด้วยครับ คุณทำภารกิจสำเร็จ: <a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a> ได้รับเครดิตรางวัล {creditbonus} &nbsp; <a href="home.php?mod=spacecp&ac=credit&op=base" target="_blank" class="lit">ดูเครดิตของฉัน &rsaquo;</a></p>',

	'task_reward_magic' => 'ยินดีด้วยครับ คุณทำภารกิจสำเร็จ: <a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a> ได้รับไอเท็มเวทย์รางวัล <a href="home.php?mod=magic&action=mybox" target="_blank">{rewardtext}</a> จำนวน {bonus} ชิ้น',

	'task_reward_medal' => 'ยินดีด้วยครับ คุณทำภารกิจสำเร็จ: <a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a> ได้รับเหรียญรางวัล <a href="home.php?mod=medal" target="_blank">{rewardtext}</a> ระยะเวลา {bonus} วัน',

	'task_reward_medal_forever' => 'ยินดีด้วยครับ คุณทำภารกิจสำเร็จ: <a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a> ได้รับเหรียญรางวัล <a href="home.php?mod=medal" target="_blank">{rewardtext}</a> แบบถาวร',

	'task_reward_invite' => 'ยินดีด้วยครับ คุณทำภารกิจสำเร็จ: <a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a> ได้รับ <a href="home.php?mod=spacecp&ac=invite" target="_blank">รหัสเชิญ {rewardtext} รหัส</a> ระยะเวลา {bonus} วัน',

	'task_reward_group' => 'ยินดีด้วยครับ คุณทำภารกิจสำเร็จ: <a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a> ได้รับสิทธิ์กลุ่มผู้ใช้งาน {rewardtext} ระยะเวลา {bonus} วัน &nbsp; <a href="home.php?mod=spacecp&ac=usergroup" target="_blank" class="lit">ดูสิทธิ์การใช้งานของฉัน &rsaquo;</a>',

	'user_usergroup' => 'กลุ่มผู้ใช้งานของคุณได้รับการอัปเกรดเป็น {usergroup} &nbsp; <a href="home.php?mod=spacecp&ac=usergroup" target="_blank" class="lit">ดูสิทธิ์การใช้งานของฉัน &rsaquo;</a>',

	'grouplevel_update' => 'ยินดีด้วยครับ วงใน {groupname} ของคุณได้รับการอัปเกรดเป็นระดับ {newlevel}',

	'thread_invite' => '{actor} เชิญคุณให้ {invitename} ในกระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',
	'blog_invite' => '{actor} เชิญคุณให้เข้าชมไดอารี่ <a href="home.php?mod=space&uid={uid}&do=blog&id={blogid}" target="_blank">{subject}</a> &nbsp; <a href="home.php?mod=space&uid={uid}&do=blog&id={blogid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',
	'article_invite' => '{actor} เชิญคุณให้เข้าชมบทความ <a href="{url}" target="_blank">{subject}</a> &nbsp; <a href="{url}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',
	'invite_friend' => 'ยินดีด้วยครับ คุณเชิญ {actor} สำเร็จและได้กลายเป็นเพื่อนกันแล้ว',

	'poke_request' => '<a href="{fromurl}" class="xi2">{fromusername}</a>: <span class="xw0">{pokemsg}&nbsp;</span><a href="home.php?mod=spacecp&ac=poke&op=reply&uid={fromuid}&from=notice" id="a_p_r_{fromuid}" class="xw1" onclick="showWindow(this.id, this.href, \'get\', 0);">ทักทายกลับ</a><span class="pipe">|</span><a href="home.php?mod=spacecp&ac=poke&op=ignore&uid={fromuid}&from=notice" id="a_p_i_{fromuid}" onclick="showWindow(\'pokeignore\', this.href, \'get\', 0);">ละเว้น</a>',

	'profile_verify_error' => 'การตรวจสอบข้อมูล {verify} ถูกปฏิเสธ โปรดแก้ไขข้อมูลในฟิลด์ต่อไปนี้: <br/>{profile}<br/>เหตุผลที่ปฏิเสธ: {reason}',
	'profile_verify_pass' => 'ยินดีด้วยครับ ข้อมูล {verify} ของคุณผ่านการตรวจสอบแล้ว',
	'profile_verify_pass_refusal' => 'ขอแสดงความเสียใจด้วยครับ ข้อมูล {verify} ของคุณถูกปฏิเสธการอนุมัติ',
	'member_ban_speak' => 'คุณถูก {user} ปิดกั้นการโพสต์ ระยะเวลา: {day} วัน (0 คือถาวร) เหตุผล: {reason}',
	'member_ban_visit' => 'คุณถูก {user} ปิดกั้นการเข้าชมเว็บไซต์ ระยะเวลา: {day} วัน (0 คือถาวร) เหตุผล: {reason}',
	'member_ban_status' => 'บัญชีของคุณถูกล็อกโดย {user} เหตุผล: {reason}',
	'member_change_usergroup' => 'คุณถูก {user} เปลี่ยนกลุ่มผู้ใช้งานเป็น {groupname} ระยะเวลา: {day} (0 คือถาวร) โดยมีกลุ่มเพิ่มเติมคือ {extgroupinfo} เหตุผล: {reason}',
	'member_change_credits' => 'คะแนนเครดิตของคุณถูกปรับเปลี่ยนโดย {user} ประเภทและค่าที่ปรับคือ {extcredits} เหตุผล: {reason}',

	'member_follow' => 'มีกิจกรรมใหม่ {count} รายการจากคนที่คุณติดตาม <a href="home.php?mod=follow">คลิกเพื่อเข้าชม</a>',
	'member_follow_add' => '{actor} ได้ติดตามคุณแล้ว <a href="home.php?mod=follow&do=follower">คลิกเพื่อดูผู้ติดตาม</a>',

	'member_moderate_invalidate' => 'บัญชีของคุณไม่ผ่านการตรวจสอบจากผู้ดูแล โปรด <a href="home.php?mod=spacecp&ac=profile">ส่งข้อมูลการลงทะเบียนใหม่อีกครั้ง</a> <br />ข้อความจากผู้ดูแล: <b>{remark}</b>',
	'member_moderate_validate' => 'บัญชีของคุณผ่านการตรวจสอบและอนุมัติแล้ว <br />ข้อความจากผู้ดูแล: <b>{remark}</b>',
	'member_moderate_invalidate_no_remark' => 'บัญชีของคุณไม่ผ่านการตรวจสอบจากผู้ดูแล โปรด <a href="home.php?mod=spacecp&ac=profile">ส่งข้อมูลการลงทะเบียนใหม่อีกครั้ง</a>',
	'member_moderate_validate_no_remark' => 'บัญชีของคุณผ่านการตรวจสอบและอนุมัติเรียบร้อยแล้ว',
	'manage_verifythread' => 'มีกระทู้ใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=threads&dateline=all">ดำเนินการทันที</a>',
	'manage_verifypost' => 'มีการตอบกลับใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=replies&dateline=all">ดำเนินการทันที</a>',
	'manage_verifyuser' => 'มีสมาชิกใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=members">ดำเนินการทันที</a>',
	'manage_verifyblog' => 'มีไดอารี่ใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=blogs">ดำเนินการทันที</a>',
	'manage_verifydoing' => 'มีสเตตัสใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=doings">ดำเนินการทันที</a>',
	'manage_verifypic' => 'มีรูปภาพใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=pictures">ดำเนินการทันที</a>',
	'manage_verifyshare' => 'มีการแชร์ใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=shares">ดำเนินการทันที</a>',
	'manage_verifycommontes' => 'มีความคิดเห็นใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=comments">ดำเนินการทันที</a>',
	'manage_verifyrecycle' => 'มีกระทู้ในถังรีไซเคิลรอการจัดการ <a href="admin.php?action=recyclebin">ดำเนินการทันที</a>',
	'manage_verifyrecyclepost' => 'มีการตอบกลับในถังรีไซเคิลรอการจัดการ <a href="admin.php?action=recyclebinpost">ดำเนินการทันที</a>',
	'manage_verifyarticle' => 'มีบทความใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=articles">ดำเนินการทันที</a>',
	'manage_verifymedal' => 'มีคำขอเหรียญรางวัลใหม่รอการพิจารณา <a href="admin.php?action=medals&operation=mod">ดำเนินการทันที</a>',
	'manage_verifyacommont' => 'มีความคิดเห็นบทความใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=articlecomments">ดำเนินการทันที</a>',
	'manage_verifytopiccommont' => 'มีความคิดเห็นหัวข้อพิเศษใหม่รอการคัดกรอง <a href="admin.php?action=moderate&operation=topiccomments">ดำเนินการทันที</a>',
	'manage_verify_field' => 'มีรายการ {verifyname} ใหม่รอการจัดการ <a href="admin.php?action=verify&operation=verify&do={doid}">ดำเนินการทันที</a>',
	'system_notice' => '{subject}<p class="summary">{message}</p>',
	'system_adv_expiration' => 'โฆษณาในเว็บไซต์ของคุณจะหมดอายุในอีก {day} วัน โปรดดำเนินการจัดการโดยเร็ว: <br />{advs}',
	'report_change_credits' => '{actor} ได้จัดการรายงานของคุณแล้ว: {creditchange} {msg}',
	'at_message' => '<a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> ได้พูดถึงคุณในกระทู้ <a href="forum.php?mod=redirect&goto=findpost&ptid={tid}&pid={pid}" target="_blank">{subject}</a> <div class="quote"><blockquote>{message}</blockquote></div><a href="forum.php?mod=redirect&goto=findpost&ptid={tid}&pid={pid}" target="_blank">คลิกเพื่อไปดู</a>',
	'at_doing' => '<a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> ได้พูดถึงคุณในสเตตัส <a href="home.php?mod=space&do=doing&doid={doid}" target="_blank">คลิกเพื่อไปดู</a>',
	'new_report' => 'มีรายงานใหม่จาก {username} รอการจัดการ <a href="admin.php?action=report" target="_blank">คลิกเข้าสู่ศูนย์จัดการเพื่อดำเนินการ</a>',
	'new_post_report' => 'มีรายงานใหม่จาก {username} รอการจัดการ <a href="forum.php?mod=modcp&action=report&fid={fid}" target="_blank">คลิกเข้าสู่แผงควบคุมผู้ดูแลเพื่อดำเนินการ</a>',
	'magics_receive' => 'คุณได้รับไอเท็มเวทย์ {magicname} จาก {actor}
<p class="summary">{actor} ฝากข้อความว่า: <span>{msg}</span></p>
<p class="mbn"><a href="home.php?mod=magic" target="_blank">ส่งไอเท็มเวทย์กลับ</a><span class="pipe">|</span><a href="home.php?mod=magic&action=mybox" target="_blank">ดูคลังไอเท็มเวทย์ของฉัน</a></p>',
	'invite_collection' => '{actor} เชิญคุณให้เข้าร่วมดูแลคลังกระทู้ <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> <br /> <a href="forum.php?mod=collection&action=edit&op=acceptinvite&ctid={ctid}&dateline={dateline}">ยอมรับคำเชิญ</a>',
	'collection_removed' => 'คลังกระทู้ <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> ที่คุณร่วมดูแล ถูกปิดโดย {actor}',
	'exit_collection' => 'คุณได้ยกเลิกการร่วมดูแลคลังกระทู้ <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> เรียบร้อยแล้ว',
	'collection_becommented' => 'คลังกระทู้ <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> ของคุณมีความคิดเห็นใหม่',
	'collection_befollowed' => 'คลังกระทู้ <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> ของคุณมีสมาชิกมาติดตามใหม่',
	'collection_becollected' => 'ยินดีด้วยครับ กระทู้ <a href="forum.php?mod=viewthread&tid={tid}">{threadname}</a> ของคุณถูกเพิ่มเข้าสู่คลังกระทู้ <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> เรียบร้อยแล้ว',

	'pmreportcontent' => '{pmreportcontent}',

	'thread_hidden' => 'กระทู้ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ของคุณถูกระบุว่าเป็นโพสต์ขยะโดยสมาชิกหลายคน และถูกซ่อนไว้ในขณะนี้ &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">เข้าชม &rsaquo;</a>',

	'forum_member_new' => '{actor} ส่งคำขอเข้าร่วม <a href="forum.php?mod=forumdisplay&fid={fid}" target="_blank">{forumname}</a> โปรดตรวจสอบได้ที่ <a href="{url}" target="_blank">แผงควบคุมการจัดการ</a>',
	'forum_member_check' => 'คุณผ่านการคัดกรองเข้าร่วมบอร์ด <a href="{url}" target="_blank">{forumname}</a> แล้ว <a href="{url}" target="_blank">คลิกที่นี่เพื่อเข้าชม</a>',
	'forum_member_check_failed' => 'คุณไม่ผ่านการคัดกรองเข้าร่วมบอร์ด <a href="{url}" target="_blank">{forumname}</a>',

	];

