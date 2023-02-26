<?php

@set_time_limit(0);
@ignore_user_abort(TRUE);
ini_set('max_execution_time', 0);
ini_set('mysql.connect_timeout', 0);

include_once('../source/class/class_core.php');
include_once('../source/function/function_core.php');

$cachelist = array();
$discuz = C::app();

$discuz->cachelist = $cachelist;
$discuz->init_cron = false;
$discuz->init_setting = true;
$discuz->init_user = false;
$discuz->init_session = false;
$discuz->init_misc = false;

$discuz->init();

// 出生国家/居住国家不会被老版本升级程序导入
// https://gitee.com/oldhuhu/DiscuzX34235/pulls/25
if(!DB::result_first("SELECT * FROM ".DB::table('common_member_profile_setting')." WHERE fieldid='birthcountry'")) {
	DB::query("INSERT INTO ".DB::table('common_member_profile_setting')." VALUES('birthcountry', 1, 0, 0, 'เกิดที่ประเทศ', '', 0, 0, 0, 0, 0, 0, 0, 'select', 0, '', '')");
	DB::query("INSERT INTO ".DB::table('common_member_profile_setting')." VALUES('residecountry', 1, 0, 0, 'ประเทศที่พำนัก', '', 0, 0, 0, 0, 0, 0, 0, 'select', 0, '', '')");
}

exit("หลังจากที่คุณเรียกไฟล์อัปเกรดนี้เสร็จสิ้นแล้ว กรุณาเข้าสู่ระบบการจัดการ AdminCP เพื่ออัปเดตแคช เราขออภัยในความไม่สะดวกที่เกิดจากความผิดปกติในครั้งนี้ และขอขอบคุณสำหรับความเข้าใจและการสนับสนุนด้วยดีเสมอมา");