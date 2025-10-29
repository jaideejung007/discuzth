<?php

// 不升级到 InnoDB 开关
// 仅限极少量特殊场合应用
define("NO_INNODB_FEATURE", false);
// 数据表改名开关
// 仅限极少量特殊场合应用
define("NO_RENAME_TABLE", true);
define('UPGRADE_LOG_PATH', __DIR__.'/../data/log/X3.5_upgrade.php');
empty($_GET['css']) || cssoutput();

@set_time_limit(0);
@ignore_user_abort(TRUE);
ini_set('max_execution_time', 0);
ini_set('mysql.connect_timeout', 0);

include_once('../source/class/class_core.php');
include_once('../source/function/function_core.php');
include_once('../source/function/function_cache.php');

$cachelist = array();
$discuz = C::app();

$discuz->cachelist = $cachelist;
$discuz->init_cron = false;
$discuz->init_setting = true;
$discuz->init_user = false;
$discuz->init_session = false;
$discuz->init_misc = false;

$discuz->init();

header("Content-type: text/html; charset=utf-8");

$config = array(
	'dbcharset' => $_G['config']['db']['1']['dbcharset'],
	'charset' => $_G['config']['output']['charset'],
	'tablepre' => $_G['config']['db']['1']['tablepre']
);

$step = !empty($_GET['step']) && in_array($_GET['step'], array('welcome', 'tips', 'license', 'envcheck', 'confirm', 'config', 'innodb', 'scheme', 'utf8mb4', 'serialize', 'serialize_plugin', 'file', 'dataupdate', 'confirmthaidistrictupgrade', 'thaidistrictupgrade', 'cancelthaidistrictupgrade')) ? $_GET['step'] : 'welcome';
$theurl = htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
$lockfile = DISCUZ_ROOT.'./data/update.lock';

timezone_set();

// PHP 8 拦截
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
	show_msg('เวอร์ชัน PHP ที่โปรแกรมอัปเกรดนี้สนับสนุนคือ 5.6 - 7.4 เท่านั้น กรุณาดาวน์เกรด PHP เป็น 7.4 ก่อนอัปเกรดโปรแกรม Discuz! X และหลังจากอัปเกรดเสร็จแล้ว คุณสามารถสลับไปใช้ PHP 8.0 หรือสูงกว่าได้');
}

// Q004 保证无写入权限时可以正常报错
// https://www.dismall.com/thread-14718-1-1.html
if (!@touch(UPGRADE_LOG_PATH) || @!is_writable(UPGRADE_LOG_PATH)) {
	show_msg('กรุณาเข้าสู่ระบบการจัดการเซิร์ฟเวอร์ เพื่อกำหนดสิทธิ์โฟลเดอร์ data/log ที่อยู่ในโฟลเดอร์ Discuz! X จากนั้นจึงเรียกใช้ไฟล์นี้อีกครั้งเพื่ออัปเกรด');
}

$tables = array();

$sys_tables_not_full = array('pre_common_admincp_cmenu', 'pre_common_admincp_group', 'pre_common_admincp_member', 'pre_common_admincp_perm', 'pre_common_admincp_session', 'pre_common_admingroup', 'pre_common_adminnote', 'pre_common_advertisement', 'pre_common_advertisement_custom', 'pre_common_banned', 'pre_common_block', 'pre_common_block_favorite', 'pre_common_block_item', 'pre_common_block_item_data', 'pre_common_block_permission', 'pre_common_block_pic', 'pre_common_block_style', 'pre_common_block_xml', 'pre_common_cache', 'pre_common_card', 'pre_common_card_log', 'pre_common_card_type', 'pre_common_connect_guest', 'pre_common_credit_log', 'pre_common_credit_log_field', 'pre_common_credit_rule', 'pre_common_credit_rule_log', 'pre_common_credit_rule_log_field', 'pre_common_cron', 'pre_common_devicetoken', 'pre_common_district', 'pre_common_diy_data', 'pre_common_domain', 'pre_common_failedip', 'pre_common_failedlogin', 'pre_common_friendlink', 'pre_common_grouppm', 'pre_common_invite', 'pre_common_magic', 'pre_common_magiclog', 'pre_common_mailcron', 'pre_common_mailqueue', 'pre_common_member', 'pre_common_member_action_log', 'pre_common_member_connect', 'pre_common_member_count', 'pre_common_member_crime', 'pre_common_member_field_forum', 'pre_common_member_field_home', 'pre_common_member_forum_buylog', 'pre_common_member_grouppm', 'pre_common_member_log', 'pre_common_member_magic', 'pre_common_member_medal', 'pre_common_member_newprompt', 'pre_common_member_profile', 'pre_common_member_profile_setting', 'pre_common_member_security', 'pre_common_member_secwhite', 'pre_common_member_stat_field', 'pre_common_member_status', 'pre_common_member_validate', 'pre_common_member_verify', 'pre_common_member_verify_info', 'pre_common_myapp', 'pre_common_myinvite', 'pre_common_mytask', 'pre_common_nav', 'pre_common_onlinetime', 'pre_common_optimizer', 'pre_common_patch', 'pre_common_plugin', 'pre_common_pluginvar', 'pre_common_process', 'pre_common_regip', 'pre_common_relatedlink', 'pre_common_remote_port', 'pre_common_report', 'pre_common_searchindex', 'pre_common_seccheck', 'pre_common_secquestion', 'pre_common_session', 'pre_common_setting', 'pre_common_smiley', 'pre_common_sphinxcounter', 'pre_common_stat', 'pre_common_statuser', 'pre_common_style', 'pre_common_stylevar', 'pre_common_syscache', 'pre_common_tag', 'pre_common_tagitem', 'pre_common_task', 'pre_common_taskvar', 'pre_common_template', 'pre_common_template_block', 'pre_common_template_permission', 'pre_common_uin_black', 'pre_common_usergroup', 'pre_common_usergroup_field', 'pre_common_visit', 'pre_common_word', 'pre_common_word_type', 'pre_connect_disktask', 'pre_connect_feedlog', 'pre_connect_memberbindlog', 'pre_connect_postfeedlog', 'pre_connect_tthreadlog', 'pre_forum_access', 'pre_forum_activity', 'pre_forum_activityapply', 'pre_forum_announcement', 'pre_forum_attachment', 'pre_forum_attachment_0', 'pre_forum_attachment_1', 'pre_forum_attachment_2', 'pre_forum_attachment_3', 'pre_forum_attachment_4', 'pre_forum_attachment_5', 'pre_forum_attachment_6', 'pre_forum_attachment_7', 'pre_forum_attachment_8', 'pre_forum_attachment_9', 'pre_forum_attachment_exif', 'pre_forum_attachment_unused', 'pre_forum_attachtype', 'pre_forum_bbcode', 'pre_forum_collection', 'pre_forum_collectioncomment', 'pre_forum_collectionfollow', 'pre_forum_collectioninvite', 'pre_forum_collectionrelated', 'pre_forum_collectionteamworker', 'pre_forum_collectionthread', 'pre_forum_creditslog', 'pre_forum_debate', 'pre_forum_debatepost', 'pre_forum_faq', 'pre_forum_filter_post', 'pre_forum_forum', 'pre_forum_forum_threadtable', 'pre_forum_forumfield', 'pre_forum_forumrecommend', 'pre_forum_groupcreditslog', 'pre_forum_groupfield', 'pre_forum_groupinvite', 'pre_forum_grouplevel', 'pre_forum_groupuser', 'pre_forum_hotreply_member', 'pre_forum_hotreply_number', 'pre_forum_imagetype', 'pre_forum_medal', 'pre_forum_medallog', 'pre_forum_memberrecommend', 'pre_forum_moderator', 'pre_forum_modwork', 'pre_forum_newthread', 'pre_forum_onlinelist', 'pre_forum_order', 'pre_forum_poll', 'pre_forum_polloption', 'pre_forum_polloption_image', 'pre_forum_pollvoter', 'pre_forum_post', 'pre_forum_post_location', 'pre_forum_post_moderate', 'pre_forum_post_tableid', 'pre_forum_postcache', 'pre_forum_postcomment', 'pre_forum_postlog', 'pre_forum_poststick', 'pre_forum_promotion', 'pre_forum_ratelog', 'pre_forum_relatedthread', 'pre_forum_replycredit', 'pre_forum_rsscache', 'pre_forum_sofa', 'pre_forum_spacecache', 'pre_forum_statlog', 'pre_forum_thread', 'pre_forum_thread_moderate', 'pre_forum_threadaddviews', 'pre_forum_threadcalendar', 'pre_forum_threadclass', 'pre_forum_threadclosed', 'pre_forum_threaddisablepos', 'pre_forum_threadhidelog', 'pre_forum_threadhot', 'pre_forum_threadimage', 'pre_forum_threadlog', 'pre_forum_threadmod', 'pre_forum_threadpartake', 'pre_forum_threadpreview', 'pre_forum_threadprofile', 'pre_forum_threadprofile_group', 'pre_forum_threadrush', 'pre_forum_threadtype', 'pre_forum_trade', 'pre_forum_tradecomment', 'pre_forum_tradelog', 'pre_forum_typeoption', 'pre_forum_typeoptionvar', 'pre_forum_typevar', 'pre_forum_warning', 'pre_home_album', 'pre_home_album_category', 'pre_home_appcreditlog', 'pre_home_blacklist', 'pre_home_blog', 'pre_home_blog_category', 'pre_home_blog_moderate', 'pre_home_blogfield', 'pre_home_class', 'pre_home_click', 'pre_home_clickuser', 'pre_home_comment', 'pre_home_comment_moderate', 'pre_home_docomment', 'pre_home_doing', 'pre_home_doing_moderate', 'pre_home_favorite', 'pre_home_feed', 'pre_home_feed_app', 'pre_home_follow', 'pre_home_follow_feed', 'pre_home_follow_feed_archiver', 'pre_home_friend', 'pre_home_friend_request', 'pre_home_friendlog', 'pre_home_notification', 'pre_home_pic', 'pre_home_pic_moderate', 'pre_home_picfield', 'pre_home_poke', 'pre_home_pokearchive', 'pre_home_share', 'pre_home_share_moderate', 'pre_home_show', 'pre_home_specialuser', 'pre_home_userapp', 'pre_home_userappfield', 'pre_home_visitor', 'pre_mobile_setting', 'pre_mobile_wsq_threadlist', 'pre_portal_article_content', 'pre_portal_article_count', 'pre_portal_article_moderate', 'pre_portal_article_related', 'pre_portal_article_title', 'pre_portal_article_trash', 'pre_portal_attachment', 'pre_portal_category', 'pre_portal_category_permission', 'pre_portal_comment', 'pre_portal_comment_moderate', 'pre_portal_rsscache', 'pre_portal_topic', 'pre_portal_topic_pic', 'pre_security_evilpost', 'pre_security_eviluser', 'pre_security_failedlog', 'pre_common_member_archive', 'pre_common_member_profile_archive', 'pre_common_member_field_forum_archive', 'pre_common_member_field_home_archive', 'pre_common_member_status_archive', 'pre_common_member_count_archive', 'pre_common_member_wechat', 'pre_mobile_wechat_authcode', 'pre_common_member_wechatmp', 'pre_mobile_wsq_threadlist', 'pre_mobile_wechat_resource', 'pre_mobile_wechat_masssend');

$scheme_count = 0;

$tablescachename = 'update_tables';
$tablescachefile = DISCUZ_ROOT . './data/sysdata/cache_' . $tablescachename . '.php';
if (in_array($_GET['step'], array('innodb', 'utf8mb4', 'serialize', 'serialize_plugin'))) {
	if (!file_exists($tablescachefile)) {
		$db_result = DB::fetch_all('SHOW TABLE STATUS WHERE `Name` LIKE \''.$config['tablepre'].'%\';');
		foreach ($db_result as $tb) {
			if (strstr($tb['Name'], $config['tablepre'].'ucenter_') || strstr($tb['Name'], $config['tablepre'].'uc_') || strstr($tb['Name'], $config['tablepre'].'forum_postposition')) {
				continue;
			}
			$tables = array_merge($tables, array(str_replace($config['tablepre'], 'pre_', $tb['Name'])));
		}
		logmessage("write " . str_replace(DISCUZ_ROOT, '', $tablescachefile) . "\t" . count($tables) . ".");
		writetocache($tablescachename, getcachevars(array('tables' => $tables)));
	} else {
		@include_once $tablescachefile;
	}

	if (empty($tables)) {
		logmessage("show table error.");
		show_msg('ไม่สามารถรับตารางฐานข้อมูลได้');
	}

	$table = empty($_GET['table']) ? $tables[0] : $_GET['table'];
	if (!in_array($table, $tables)) {
		logmessage("table name error.");
		show_msg('ชื่อตารางฐานข้อมูลไม่ถูกต้อง');
	}
}

logmessage("<?php exit;?>\t".date("Y-m-d H:i:s"));
logmessage("Query String: ".$_SERVER['QUERY_STRING']);

if (!empty($_GET['lock'])) {
	@touch($lockfile);
	@unlink($theurl);
	logmessage("upgrade success.");
	show_msg('ขอแสดงความยินดี คุณอัปเกรดเป็น Discuz! X3.5 เรียบร้อยแล้ว ขอบคุณที่เลือกใช้ผลิตภัณฑ์ของเรา');
}

if (file_exists($lockfile)) {
	logmessage("upgrade locked.");
	show_msg('กรุณาเข้าสู่ระบบการจัดการเซิร์ฟเวอร์ เพื่อทำการลบไฟล์ data/update.lock ออกด้วยตนเอง เสร็จแล้วเรียกใช้ไฟล์นี้อีกครั้งเพื่อเริ่มอัปเกรด');
}

if ($step == 'welcome') {

	show_msg('<p class="lead">Discuz! X3.5 เป็นซอฟต์แวร์กระดานสนทนาที่พัฒนาขึ้นมาจากรากฐานของ Discuz! X3.4 ซึ่งเป็นซอฟต์แวร์ชั้นนำที่มุ่งหวังจะปรับปรุงความปลอดภัยของระบบให้มีความแข็งเแกร่ง, สนับสนุน IPv6, ขยายขีดความสามารถระบบจัดการต่าง ๆ, อัดประสิทธิภาพให้เต็มเหนี่ยวแม้วันนั้นจะโหลดมาก, ปรับปรุงประสบการณ์การใช้งานของผู้ใช้ รวมไปถึงประสบการณ์การจัดการของระบบหลังบ้านอีกด้วย ซอฟต์แวร์นี้จะอัปเดตอย่างสม่ำเสมอใน Git อย่างเป็นทางการ และ GitHub ของดิสคัสไทย คุณสามารถติดตามการอัปเดตเวอร์ชันใหม่ได้ตามช่องทางที่ให้ไว้เมนูด้านบน</p><p class="lead">ก่อนทำการอัปเกรดจาก Discuz! X3.4 เป็น Discuz! X3.5 คุณควรสำรองข้อมูลทั้งหมด (ไม่ว่าจะเป็นฐานข้อมูลและไฟล์ที่เกี่ยวข้อง) และดำเนินการอัปเกรดด้วยความระมัดระวัง</p><p class="lead">หากคุณพร้อมที่จะอัปเกรดแล้ว ให้คลิกปุ่ม ถัดไป เพื่อดำเนินการต่อ</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=tips\';">ถัดไป ></button></p>', 'ขอต้อนรับสู่โปรแกรมอัปเกรด Discuz! X3.5', 1);

} else if ($step == 'tips') {

	show_msg('<p class="lead">ย้ำอีกครั้ง ในขั้นตอนนี้ ก่อนที่จะดำเนินการอัปเกรดจาก Discuz! X3.4 เป็น Discuz! X3.5 เราขอแนะนำให้คุณสำรองข้อมูลเว็บไซต์ทั้งหมด (รวมถึงฐานข้อมูลและไฟล์ที่เกี่ยวข้อง) และดำเนินการอัปเกรดด้วยความระมัดระวัง ในขณะเดียวกัน เราขอแนะนำให้อัปเกรด MySQL เป็นเวอร์ชัน 5.7 ขึ้นไปก่อนที่จะอัปเกรดเว็บไซต์ เพื่อหลักเลี่ยงการอัปเกรดหยุดชะงัก เนื่องจากปลั๊กอินบางตัวอาจจะมีดัชนีตารางฐานข้อมูลปริมาณมาก</p><p class="lead">เนื่องด้วย UCenter 1.7.0 จะดำเนินการอัปเดตการเข้ารหัสฐานข้อมูลใหม่ หากชื่อผู้ใช้งานในฐานข้อมูล UCenter 1.7.0 รายใดไม่สนับสนุนการเข้ารหัส utf8mb4_unicode_ci จะถูกเปลี่ยนเป็นชื่อผู้ใช้งานที่มีอักษรแบบสุ่มใหม่จำนวน 15 ตัวให้โดยอัตโนมัติในระหว่างกระบวนการอัปเกรด หลังจากการดำเนินการเสร็จแล้ว ให้คุณตรวจสอบไฟล์บันทึกการเปลี่ยนชื่อผู้ใช้งาน (บนแท็บ บันทึกกิจกรรมระบบ ในเมนู จัดการสมาชิก) และดูบันทึกการแจ้งเตือน (บนแท็บ บันทึกการแจ้งเตือน ในเมนู ไฟล์บันทึกระบบ) จากระบบหลังบ้านของ UCenter ว่ามีการดำเนินการแจ้งไปยังผู้ใช้งานที่ได้รับผลกระทบตามแอปที่ได้ผูกไว้กับ UCenter หรือไม่ ก่อนที่จะดำเนินการอัปเกรด Discuz! X3.5 ต่อไป และหลังจากอัปเกรดเสร็จสิ้นทั้งหมดแล้ว กรุณาแจ้งผู้ใช้งานที่ได้รับผลกระทบดังกล่าวด้วยตนเองอีกครั้ง โดยอาจจะแจ้งให้ผู้ใช้งานทำการเปลี่ยนชื่อใหม่ด้วยไอเท็มเปลี่ยนชื่อ หรืออื่น ๆ ผ่านประกาศของเว็บไซต์ หรือทางอีเมล หรือทาง SMS ตามแต่ผู้ดูแลระบบสะดวกที่จะดำเนินการแจ้งให้ผู้ใช้งานทราบ หากคุณต้องการให้ผู้ใช้งานที่ได้รับผลกระทบดังกล่าวเปลี่ยนชื่อได้ด้วยตนเองผ่านการใช้ไอเท็มเปลี่ยนชื่อ อย่าลืมตั้งค่าไอเท็มเปลี่ยนชื่อใน AdminCP ของระบบดิสคัสด้วย เพื่อเป็นทางเลือกให้กับผู้ที่ได้รับผลกระทบสามารถเปลี่ยนชื่อผู้ใช้งานใหม่ได้สะดวกมากยิ่งขึ้น</p><p class="lead">เนื่องด้วย UCenter 1.7.0 จะดำเนินการอัปเดตการเข้ารหัสฐานข้อมูลใหม่ หากผู้ใช้งานรายใดตั้งค่าให้ตอบคำถามความปลอดภัยโดยไม่ได้ใช้ข้อความ ASCII (ภาษาอังกฤษและตัวเลข) ก่อนเข้าสู่ระบบ คำตอบของคำถามความปลอดภัยดังกล่าวจะถูกล้างออกโดยอัตโนมัติ กรุณาแจ้งให้ผู้ใช้ทราบจากผลกระทบดังกล่าวและขอให้ผู้ใช้เมื่อจะเข้าสู่ระบบไม่ต้องกรอกคำตอบของคำถามความปลอดภัยใด ๆ</p><p class="lead">เนื่องด้วย UCenter 1.7.0 จะดำเนินการอัปเดตการเข้ารหัสฐานข้อมูลใหม่ หากผู้ใช้งานรายใดตั้งรหัสผ่านไม่ได้เป็นไปตามมาตรฐานของการตั้งรหัสผ่าน (เช่น ใช้รหัสผ่านที่ไม่ใช่ภาษาอังกฤษ อาจจะเป็นภาษาไทยหรือภาษาอื่น ๆ ลงไปในรหัสผ่าน) ผู้ใช้งานดังกล่าวอาจจะเข้าสู่ระบบไม่ได้ เมื่อคุณพบสถานการณ์นี้ กรุณาแจ้งให้ผู้ใช้งานทำการรีเซ็ตรหัสผ่านใหม่ โดยใช้ฟังก์ชันลืมรหัสผ่านในหน้าเข้าสู่ระบบ แล้วดำเนินการตามขั้นตอนที่แจ้ง ผู้ใช้งานดังกล่าวก็จะสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้อีกครั้ง</p><p class="lead">เนื่องด้วย Discuz! X3.5 ได้อัปเดตวิธีการจัดเก็บการแบนไอพีใหม่ หากคุณตั้งกฎการแบนไอพีเป็นแบบแบนยกชุด (batches) ไว้ การอัปเกรดนี้จะสนับสนุนแค่ไอพีที่เป็นประเภท IPv4 และกฎอื่น ๆ ที่ตั้งค่าเป็นจำนวนเต็ม (integer) ที่มีค่าตั้งแต่ 8 แต่ไม่เกิน 32 เท่านั้น ส่วนกฎอื่น ๆ นอกเหนือจากนี้จะถูกลบออกโดยอัตโนมัติ เราขอแนะนำให้คุณสำรองกฎเดิมของคุณไว้ก่อนการอัปเกรด และเมื่ออัปเกรดเสร็จแล้ว คุณสามารถตั้งค่ากฎเดิมของคุณที่ได้สำรองไว้ก่อนหน้านี้</p><p class="lead">เนื่องด้วย Discuz! X3.5 ได้ปรับปรุงฟีเจอร์การแบน Session และไอพีใหม่ การปรับปรุงฟีเจอร์ดังกล่าวจึงไม่สนับสนุนไลบรารีแคชอื่น ๆ ที่ไม่ใช่ของ Redis เป็นเพราะว่ายังมีการใช้ฟีเจอร์ขั้นสูงบางอย่างของ Redis อยู่ ดังนั้น หากเว็บไซต์ของคุณมีทราฟิกอยู่เป็นจำนวนมาก ขอแนะนำให้คุณถอนการติดตั้งไลบรารีแคชหน่วยความจำเดิมออก แล้วแทนที่ด้วยไลบรารีหน่วยความจำ Redis ใหม่แทน ซึ่งจะช่วยให้การเข้าถึงเว็บไซต์เป็นไปได้อย่างรวดเร็ว โดยไม่ต้องพึ่งพาตาราง HEAP ภายในฐานข้อมูลใด ๆ อีก</p><p class="lead">เนื่องด้วย Discuz! X3.5 ได้ปรับปรุงฟีเจอร์การปิด/เปิดของโมดูลเว็บไซต์ ในระหว่างการอัปเกรด โมดูลเว็บไซต์ที่มีทั้งหมดจะถูกตั้งค่าสถานะเป็นเปิดใช้งานให้โดยอัตโนมัติ กรุณาประเมินความเหมาะสมในการเปิดใช้งานโมดูลของเว็บไซต์ดังกล่าวด้วยตนเอง และสามารถปิดโมดูลเว็บไซต์ได้ หากไม่ต้องการใช้งาน</p><p class="lead">เนื่องด้วย Discuz! X3.5 ได้อัปเดตการเข้ารหัสฐานข้อมูลและเทมเพลทเริ่มต้นใหม่ทั้งหมด โปรแกรมอัปเกรดจะแปลงตารางข้อมูลปลั๊กอิน/เทมเพลทเป็น utf8mb4 และพยายามแปลงการเข้ารหัสไฟล์ปลั๊กอินของคุณด้วย พร้อมทั้งปิดการใช้งานปลั๊กอินที่ไม่ใช่ค่าเริ่มต้นของระบบและคืนค่ากลับไปใช้เทมเพลทเริ่มต้นให้โดยอัตโนมัติเมื่ออัปเกรดเสร็จสมบูรณ์ กรุณาทดสอบการใช้งานปลั๊กอินของคุณหลังจากอัปเกรดอีกครั้งว่ายังสามารถทำงานได้ตามปกติหรือไม่ กรณีปลั๊กอินและเทมเพลทส่วนใหญ่ที่ผ่านการแปลงไฟล์โดยอัตโนมัติ คุณจะสามารถใช้งานได้เหมือนเดิม หรืออาจจะต้องปรับปรุงการตั้งค่าบางอย่างอีกเล็กน้อยเพื่อให้สามารถใช้งานได้สภาพแวดล้อม utf8mb4 ได้สมบูรณ์ ส่วนปลั๊กอินและเทมเพลทบางตัวอาจจะต้องแปลงการเข้ารหัสไฟล์ใหม่ด้วยตนเอง หรืออาจจะต้องปรับปรุงโค้ดภายในปลั๊กอินเพื่อให้สามารถใช้งานใน Discuz! X3.5 ได้อย่างปกติ</p><p class="lead">กรุณาอย่าเรียกใช้งานโปรแกรมอัปเกรดนี้ซ้ำ การเรียกใช้งานซ้ำอาจทำให้เกิดปัญหาที่ไม่คาดคิดได้ หากพบปัญหาระหว่างการอัปเกรด ขอความกรุณาอย่าปิดหน้าเว็บเป็นอันขาด พยายามแก้ไขตามคำแนะนำที่ปรากฎในหน้าเว็บแล้วรีเฟรชหน้าเว็บใหม่ หากยังไม่สามารถแก้ไขปัญหาได้ กรุณาเรียกคืนข้อมูลสำรองและเรียกใช้งานโปรแกรมอัปเกรดนี้ใหม่อีกครั้ง</p><p class="lead">หากคุณอ่านคำแนะนำด้านบนเข้าใจโดยถ่องแท้แล้ว ให้คลิกปุ่ม ถัดไป เพื่อดำเนินการต่อ</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=license\';">ถัดไป ></button> <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=welcome\';">ย้อนกลับ <</button></p>', 'กรุณาอ่านคำแนะนำเกี่ยวกับการอัปเกรด', 1);

} else if ($step == 'license') {

	show_msg('<p class="lead">(ฉบับภาษาจีน) กรุณาคลิกที่ลิงก์ <a href="https://gitee.com/Discuz/DiscuzX/raw/master/readme/license.txt" target="_blank">https://gitee.com/Discuz/DiscuzX/raw/master/readme/license.txt</a> เพื่ออ่านข้อตกลงอนุญาตให้ใช้สิทธิของผู้ใช้ฉบับล่าสุด</p><p class="lead">(ฉบับภาษาไทย) กรุณาคลิกที่ลิงก์ <a href="https://raw.githubusercontent.com/jaideejung007/discuzth/master/readme/license.txt" target="_blank">https://raw.githubusercontent.com/jaideejung007/discuzth/master/readme/license.txt</a> เพื่ออ่านข้อตกลงอนุญาตให้ใช้สิทธิของผู้ใช้ฉบับล่าสุด</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=envcheck\';">ฉันยอมรับและดำเนินการต่อ ></button> <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=tips\';">ย้อนกลับ <</button></p>', 'กรุณาอ่านข้อตกลงอนุญาตให้ใช้สิทธิของผู้ใช้', 1);

} else if ($step == 'envcheck') {

	include_once('../config/config_ucenter.php');
	include_once('../source/discuz_version.php');
	include_once('../uc_client/client.php');

	$tips = '<table class="table table-striped" style="margin: 2em 0;"><thead><tr><th scope="col">ชื่อซอฟต์แวร์</th><th scope="col">ความต้องการขั้นต่ำ</th><th scope="col">สถานะปัจจุบัน</th><th scope="col">สถานะการทดสอบ</th></tr></thead><tbody>';
	$env_ok = true;
	$now_ver = array('Code Version' => preg_replace('/[^0-9.]+/', '', constant('DISCUZ_VERSION')), 'UCenter' => uc_check_version()['db'], 'PHP' => constant('PHP_VERSION'), 'MySQL' => helper_dbtool::dbversion(), 'GD' => (function_exists('gd_info') ? preg_replace('/[^0-9.]+/', '', gd_info()['GD Version']) : false), 'XML' => function_exists('xml_parser_create'), 'JSON' => function_exists('json_encode'), 'mbstring' => (function_exists('mb_convert_encoding') || strtoupper(constant('CHARSET')) == 'UTF-8'), 'Not Slave' => $_config['db']['slave'] == false, 'Not DB Map' => empty($_config['db']['map']));// 对于UTF-8用户，不强制要求mbstring扩展
	$req_ver = array('Code Version' => '3.5', 'UCenter' => '1.7.0', 'PHP' => '5.6.0', 'MySQL' => '5.5.3', 'GD' => '1.0', 'XML' => true, 'JSON' => true, 'mbstring' => true, 'Not Slave' => true, 'Not DB Map' => true);
	$lang_ver = array('Code Version' => 'โค้ดเวอร์ชัน Discuz!', 'UCenter' => 'เวอร์ชัน UCenter', 'PHP' => 'เวอร์ชัน PHP', 'MySQL' => 'เวอร์ชัน MySQL', 'GD' => 'ส่วนขยาย GD', 'XML' => 'ส่วนขยาย XML', 'JSON' => 'ส่วนขยาย JSON', 'mbstring' => 'ส่วนขยาย mbstring / UTF-8', 'Not Slave' => 'ไม่ได้เปิดใช้งานเซิร์ฟเวอร์ Slave', 'Not DB Map' => 'การจัดสรรฐานข้อมูลโดยไม่ใช้การแบ่งพื้นที่');
	foreach ($now_ver as $key => $value) {
		$tips .= "<tr><th>$lang_ver[$key]</th><td>$req_ver[$key]</td><td>$value</td>";
		logmessage("Check $key : $value");
		if ($req_ver[$key] === true) {
			if (!$value) {
				logmessage("Check $key Result: false");
				$tips .= '<td><font color="Red">❌ ไม่ผ่าน</font></td>';
				$env_ok = false;
			} else {
				logmessage("Check $key Result: true");
				$tips .= '<td><font color="Green">✔️ ผ่าน</font></td>';
			}
		} else if (version_compare($value, $req_ver[$key], '<')) {
			logmessage("Check $key Result: false");
			$tips .= '<td><font color="Red">❌ ไม่ผ่าน</font></td>';
			$env_ok = false;
		} else {
			logmessage("Check $key Result: true");
			$tips .= '<td><font color="Green">✔️ ผ่าน</font></td>';
		}
		$tips .= '</tr>';
	}
	$tips .= '</tbody></table><p>';
	$env_ok && $tips .= '<button type="button" class="btn btn-primary" onclick="location.href=\'?step=confirm\';">ถัดไป ></button> ';
	$tips .= '<button type="button" class="btn btn-secondary" onclick="location.href=\'?step=license\';">ก่อนหน้า <</button></p>';
	show_msg($tips, 'การทดสอบสภาวะแวดล้อมของเซิร์ฟเวอร์', 1);

} else if ($step == 'confirm') {

	$myisam_opt = NO_INNODB_FEATURE ? '<button type="button" class="btn btn-secondary" onclick="location.href=\'?step=config&myisam=1\';">อย่าอัปเกรดเป็น InnoDB (ไม่แนะนำ) > </button>' : '';

	show_msg('<p class="lead" style="color: red;font-weight: bold;">โปรแกรมนี้กำลังจะเริ่มอัปเกรด Discuz! X3.4 เป็น Discuz! X3.5 กรุณาตรวจสอบให้แน่ใจอีกครั้งว่าคุณได้สำรองข้อมูลเว็บไซต์ทั้งหมด (รวมถึงฐานข้อมูลและไฟล์ทั้งหมด) ไว้อย่างดีแล้ว! เมื่อคุณคลิก "เริ่มอัปเกรด" กระบวนการอัปเกรดจะเริ่มขึ้นทันทีและไม่สามารถยกเลิกได้!</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=config\';">เริ่มอัปเกรด ></button>  ' . $myisam_opt . ' <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=license\';">ย้อนกลับ <</button></p>', 'ยืนยันการอัปเกรด', 1);

} else if ($step == 'config') {

	logmessage("upgrade start.");

	if (!C::t('common_setting')->fetch('bbclosed')) {
		logmessage("Ultrax is not close, Upgrader Close it");
		C::t('common_setting')->update('bbclosed', 1);
		require_once libfile('function/cache');
		updatecache('setting');
		show_msg('เว็บไซต์ของคุณไม่ได้ตั้งค่าปิดปรับปรุงเว็บไซต์ แต่ไม่เป็นไร เราจัดการให้นะ กรุณารอสักครู่......', 'คำแนะนำการอัปเกรด', 0, $theurl.'?step=config');
	}

	$stepurl = $theurl . empty($_GET['myisam']) ? '?step=innodb' : '?step=scheme&myisam=1';

	$deletevar = array('app', 'home'); // config中需要删除的项目
	$default_config = $_config = array();
	$default_configfile = DISCUZ_ROOT.'./config/config_global_default.php';

	if (!file_exists($default_configfile)) {
		logmessage("config_global_default.php not found, not continue.");
		show_msg('<font color="red"><b>ไม่พบไฟล์ config_global_default.php และไม่สามารถดำเนินการต่อได้ กรุณาเพิ่มไฟล์นี้และรีเฟรชหน้านี้ แล้วเรามาลองกันใหม่</b></font>', 'คำแนะนำการอัปเกรด');
	} else {
		include $default_configfile;
		$default_config = $_config;
	}

	$configfile = DISCUZ_ROOT.'./config/config_global.php';
	include $configfile;

	logmessage("unlink " . str_replace(DISCUZ_ROOT, '', $tablescachefile) . ".");
	@unlink($tablescachefile);

	// 对非 Windows 系统尝试设置 777 权限
	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
		logmessage("set chmod 777 $configfile");
		chmod($configfile, 0777);
	}

	if (save_config_file($configfile, $_config, $default_config, $deletevar)) {
		logmessage("config_global_default.php modify ok, continue.");
		show_msg("ตั้งค่าไฟล์คอนฟิกระบบเสร็จแล้ว และกำลังลุยขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$stepurl");
	} else {
		logmessage("config_global_default.php not write, let user manually move, not continue.");
		show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้คุณโดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ และโฟลเดอร์ "config/" ก็ไม่สามารถเขียนได้เช่นกัน เราจึงบันทึกไฟล์ที่แก้ไขล่าสุดไว้ในโฟลเดอร์ "data/" แทน กรุณาย้ายไฟล์ดังกล่าวไปยังโฟลเดอร์ "config/" ผ่านซอฟต์แวร์ FTP เพื่อเขียนทับไฟล์ต้นฉบับด้วยตนเอง จากนั้น <a href="'.$stepurl.'">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
	}

} else if ($step == 'innodb') {

	@touch(DISCUZ_ROOT.'./data/install.lock');
	@unlink(DISCUZ_ROOT.'./install/index.php');

	if ($table) {
		$sql_check = get_convert_sql('check', $table);
		if (!empty($sql_check)) {
			$result = DB::fetch_first($sql_check);
			// 对文字排序进行过滤，避免不合法文字排序进入升级流程。考虑到部分站长自行进行了 utf8mb4 改造，因此额外添加 utf8mb4_general_ci 。
			// 从 MySQL 8.0.28 开始, utf8_general_ci 更名为 utf8mb3_general_ci
			if (!in_array($result['Collation'], array('utf8mb4_unicode_ci', 'utf8_general_ci', 'utf8mb3_general_ci', 'gbk_chinese_ci', 'big5_chinese_ci', 'utf8mb4_general_ci'))) {
				logmessage("table ".$table." 's ci ".$result['Collation']." not support, not continue.");
				show_msg("<font color=\"red\"><b>ไม่สนับสนุนข้อความที่มี Collation เป็น ".$result['Collation']." ของตาราง ".$table." นี้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ</b></font>", 'คำแนะนำการอัปเกรด');
			}
			if (empty($_GET['scheme']) && get_innodb_scheme_update_sql($table, true)) {
				// 对于因数据库超时而升级失败的特大站点请看此函数
				setdbglobal();
				// Q008 好像有些站点存在 gpmid 索引, 这里尝试删除一下
				// https://www.dismall.com/thread-14718-1-1.html
				if ($table == 'pre_common_member_grouppm') {
					logmessage("pre_common_member_grouppm has gpmid index, DROP it first.");
					$sql = "ALTER TABLE pre_common_member_grouppm DROP INDEX gpmid;";
					logmessage("RUNSQL ".$sql);
					DB::query($sql, 'SILENT');
					logmessage("RUNSQL SILENT Success");
				}
				// 帖子分表特殊处理
				// https://www.dismall.com/thread-15265-1-1.html
				if (preg_match("/^pre_forum_post_(\\d+)$/i", $table)) {
					logmessage("$table is special post table, need special alter.");
					$sql = "ALTER TABLE ".str_replace(' pre_', ' '.$config['tablepre'], $table)." MODIFY COLUMN position INT unsigned NOT NULL DEFAULT \'0\'";
					logmessage("RUNSQL ".$sql);
					DB::query($sql);
					logmessage("RUNSQL Success");
				} else {
					$sql = get_innodb_scheme_update_sql($table);
					logmessage("RUNSQL ".$sql);
					DB::query($sql);
					logmessage("RUNSQL Success");
				}
				show_msg("กำลังดำเนินการอัปเกรดตารางข้อมูล InnoDB และเตรียมการปรับโครงสร้างตาราง $table เรียบร้อยแล้ว ขณะนี้กำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&table=".$table."&scheme=1");
			}
			if ($result['Engine'] != 'InnoDB') {
				$sql = get_convert_sql($step, $table);
				if (!empty($sql)) {
					// 对于因数据库超时而升级失败的特大站点请看此函数
					setdbglobal();
					logmessage("RUNSQL ".$sql);
					DB::query($sql);
					logmessage("RUNSQL Success");
				}
			}
		}
	}

	$tmp = array_flip($tables);
	$tmpid = $tmp[$table];
	$count = count($tables);
	if ($tmpid + 1 < $count) {
		$next = $tables[++$tmpid];
		show_msg("กำลังดำเนินการอัปเกรดตารางข้อมูล InnoDB และอัปเกรดตาราง $table เรียบร้อยแล้ว ความคืบหน้าปัจจุบันคือ $tmpid / $count กำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&table=".$next);
	} else {
		show_msg("การอัปเกรดตารางข้อมูล InnoDB เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=scheme");
	}

} else if ($step == 'scheme') {

	@touch(DISCUZ_ROOT.'./data/install.lock');
	@unlink(DISCUZ_ROOT.'./install/index.php');

	// 结构升级走原有 X3.4 以前的流程, 避免站点小规模自定义数据结构或版本间数据库结构不匹配的影响
	$sqlfile = DISCUZ_ROOT.'./install/data/install.sql';

	if(!file_exists($sqlfile)) {
		logmessage("install.sql not found, not continue");
		show_msg('<font color="red"><b>ไม่พบไฟล์ SQL '.$sqlfile.'</b></font>', 'คำแนะนำการอัปเกรด');
	}

	$sql = implode('', file($sqlfile));
	preg_match_all("/CREATE\s+TABLE.+?pre\_(.+?)\s*\((.+?)\)\s*(ENGINE|TYPE)\s*=\s*(\w+)/is", $sql, $matches);
	$newtables = empty($matches[1])?array():$matches[1];
	$newsqls = empty($matches[0])?array():$matches[0];
	if(empty($newtables) || empty($newsqls)) {
		logmessage("install.sql is empty, not continue");
		show_msg('<font color="red"><b>เนื้อหาไฟล์ SQL ว่างเปล่า กรุณาตรวจสอบ!</b></font>', 'คำแนะนำการอัปเกรด');
	}

	$i = empty($_GET['i'])?0:intval($_GET['i']);
	$count_i = count($newtables);
	if($i>=$count_i) {
		logmessage("install.sql is ok, continue");
		show_msg("การอัปเกรดโครงสร้างฐานข้อมูลเสร็จสมบูรณ์แล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=utf8mb4");
	}
	$newtable = $newtables[$i];

	$specid = intval($_GET['specid']);
	if($specid && in_array($newtable, array('forum_post', 'forum_thread'))) {
		$spectable = $newtable;
		$newtable = get_special_table_by_num($newtable, $specid);
	}

	$newcols = getcolumn($newsqls[$i]);

	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal();

	if(!$query = DB::query("SHOW CREATE TABLE ".DB::table($newtable), 'SILENT')) {
		preg_match("/(CREATE TABLE .+?)\s*(ENGINE|TYPE)\s*=\s*(\w+)/is", $newsqls[$i], $maths);

		$maths[3] = strtoupper($maths[3]);
		$engine = empty($_GET['myisam']) ? 'InnoDB' : $maths[3];
		$usql = $maths[1].' ENGINE='.$engine.' CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		$usql = str_replace("CREATE TABLE IF NOT EXISTS pre_", 'CREATE TABLE IF NOT EXISTS '.$config['tablepre'], $usql);
		$usql = str_replace("CREATE TABLE pre_", 'CREATE TABLE '.$config['tablepre'], $usql);

		logmessage("RUNSQL: ".$usql);

		if(!DB::query($usql, 'SILENT')) {
			logmessage("RUNSQL FAILED");
			show_msg('<font color="red"><b>เกิดข้อผิดพลาดในการเพิ่มตาราง '.DB::table($newtable).' กรุณาเรียกใช้คำสั่ง SQL ต่อไปนี้ด้วยตนเอง แล้วเรียกใช้โปรแกรมอัปเกรดนี้อีกครั้ง:</b></font><br><br>'.dhtmlspecialchars($usql), 'คำแนะนำการอัปเกรด');
		} else {
			logmessage("RUNSQL Success");
			$msg = 'เพิ่มตาราง '.DB::table($newtable).' เรียบร้อยแล้ว';
		}
	} else {
		$value = DB::fetch($query);
		logmessage("TABLE: ".implode(",", $value));
		$oldcols = getcolumn($value['Create Table']);

		$updates = array();
		$allfileds =array_keys($newcols);
		foreach ($newcols as $key => $value) {
			if($key == 'PRIMARY') {
				// 如果在五个白名单表内，则不需要做任何事，反之沿用原有流程
				$nomovetable = array('common_cache', 'common_card', 'common_member_profile_setting', 'common_setting', 'mobile_setting');
				if($value != $oldcols[$key]) {
					if(!empty($oldcols[$key]) && !in_array($newtable, $nomovetable)) {
						// 不再默认对数据表进行更名操作, 如果遇到主键不一致的优先报错处理
						if(NO_RENAME_TABLE) {
							logmessage("Table ".$newtable." PRIMARY KEY Define not same as install.sql, not continue.");
							show_msg("<font color=\"red\"><b>Primary Key ของตาราง ".$newtable." ไม่ตรงกับข้อกำหนดของระบบ และระบบไม่สนับสนุนการอัปเกรดอัตโนมัตินี้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ!</b></font>", 'คำแนะนำการอัปเกรด');
						}
						logmessage("RUNSQL: ".$usql);
						$usql = "RENAME TABLE ".DB::table($newtable)." TO ".DB::table($newtable.'_bak');
						if(!DB::query($usql, 'SILENT')) {
							logmessage("Table ".$newtable." Update Failed.");
							show_msg('<font color="red"><b>พบข้อผิดพลาดในการอัปเกรดตาราง '.DB::table($newtable).' กรุณาเรียกใช้คำสั่ง SQL ต่อไปนี้ด้วยตนเอง แล้วเรียกใช้โปรแกรมอัปเกรดนี้อีกครั้ง:</b></font><br><br><b>คำสั่ง SQL สำหรับอัปเกรด</b>:<div style="position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;">'.dhtmlspecialchars($usql)."</div><br><b>Error</b>: ".DB::error()."<br><b>Errno.</b>: ".DB::errno(), 'คำแนะนำการอัปเกรด');
						} else {
							logmessage("RUNSQL: Success");
							$msg = 'เปลี่ยนชื่อตารางเป็น '.DB::table($newtable).' เรียบร้อยแล้ว';
							show_msg($msg, 'คำแนะนำการอัปเกรด', 0, $theurl.'?step=scheme&i='.$_GET['i']);
						}
					}
					if(!in_array($newtable, $nomovetable)) {
						$updates[] = "ADD PRIMARY KEY $value";
					}
				}
			} elseif ($key == 'KEY') {
				foreach ($value as $subkey => $subvalue) {
					if(!empty($oldcols['KEY'][$subkey])) {
						if($subvalue != $oldcols['KEY'][$subkey]) {
							$updates[] = "DROP INDEX `$subkey`";
							$updates[] = "ADD INDEX `$subkey` $subvalue";
						}
					} else {
						$updates[] = "ADD INDEX `$subkey` $subvalue";
					}
				}
			} elseif ($key == 'UNIQUE') {
				foreach ($value as $subkey => $subvalue) {
					if(!empty($oldcols['UNIQUE'][$subkey])) {
						if($subvalue != $oldcols['UNIQUE'][$subkey]) {
							$updates[] = "DROP INDEX `$subkey`";
							$updates[] = "ADD UNIQUE INDEX `$subkey` $subvalue";
						}
					} else {
						$usql = "ALTER TABLE  ".DB::table($newtable)." DROP INDEX `$subkey`";
						DB::query($usql, 'SILENT');
						$updates[] = "ADD UNIQUE INDEX `$subkey` $subvalue";
					}
				}
			} else {
				if(!empty($oldcols[$key])) {
					if(strtolower($value) != strtolower($oldcols[$key])) {
						$updates[] = "CHANGE `$key` `$key` $value";
					}
				} else {
					$i = array_search($key, $allfileds);
					$fieldposition = $i > 0 ? 'AFTER `'.$allfileds[$i-1].'`' : 'FIRST';
					$updates[] = "ADD `$key` $value $fieldposition";
				}
			}
		}

		if(!empty($updates)) {
			$usql = "ALTER TABLE ".DB::table($newtable)." ".implode(', ', $updates);
			logmessage("RUNSQL: ".$usql);
			if(!DB::query($usql, 'SILENT')) {
				logmessage("RUNSQL: FAILED");
				show_msg('<font color="red"><b>เกิดข้อผิดพลาดในการอัปเกรดตาราง '.DB::table($newtable).' กรุณาเรียกใช้คำสั่งอัปเกรดต่อไปนี้ด้วยตนเอง แล้วเรียกใช้โปรแกรมอัปเกรดอีกครั้ง:</b></font><br><br><b>คำสั่งอัปเกรด SQL</b>:<div style="position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;">'.dhtmlspecialchars($usql)."</div><br><b>Error</b>: ".DB::error()."<br><b>Errno.</b>: ".DB::errno(), 'คำแนะนำการอัปเกรด');
			} else {
				logmessage("RUNSQL: Success");
				$msg = 'อัปเกรดตาราง '.DB::table($newtable).' เรียบร้อยแล้ว';
			}
		} else {
			$msg = 'ตรวจสอบ '.DB::table($newtable).' เรียบร้อยแล้ว ไม่จำเป็นต้องอัปเกรด ข้ามไป';
		}
	}

	if($specid) {
		$newtable = $spectable;
	}

	if(get_special_table_by_num($newtable, $specid+1)) {
		$next = $theurl . '?step='.$step.'&i='.($_GET['i']).'&specid='.($specid + 1)."&myisam=".(empty($_GET['myisam']) ? 0 : 1);
	} else {
		$next = $theurl.'?step='.$step.'&i='.($_GET['i']+1)."&myisam=".(empty($_GET['myisam']) ? 0 : 1);
	}
	show_msg("[ $i / $count_i ] ".$msg.' กำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......', 'คำแนะนำการอัปเกรด', 0, $next);

} else if ($step == 'utf8mb4') {

	$sql_check = get_convert_sql('check', $table);
	if (!empty($sql_check)) {
		$result = DB::fetch_first($sql_check);
		if ($result['Collation'] != 'utf8mb4_unicode_ci') {
			// 对文字排序进行过滤，避免不合法文字排序进入升级流程。考虑到部分站长自行进行了 utf8mb4 改造，因此额外添加 utf8mb4_general_ci 。
			// 从 MySQL 8.0.28 开始, utf8_general_ci 更名为 utf8mb3_general_ci
			if (!in_array($result['Collation'], array('utf8mb4_unicode_ci', 'utf8_general_ci', 'utf8mb3_general_ci', 'gbk_chinese_ci', 'big5_chinese_ci', 'utf8mb4_general_ci'))) {
				logmessage("table ".$table." 's ci ".$result['Collation']." not support, not continue.");
				show_msg("<font color=\"red\"><b>ไม่สนับสนุนข้อความที่มี Collation เป็น ".$result['Collation']." ของตาราง ".$table." นี้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ</b></font>", 'คำแนะนำการอัปเกรด');
			}
			$sql = get_convert_sql('utf8mb4', $table);
			if (!empty($sql)) {
				// 对于因数据库超时而升级失败的特大站点请看此函数
				setdbglobal();
				logmessage("RUNSQL ".$sql);
				DB::query($sql);
				logmessage("RUNSQL Success");
			}
		}
	}

	$tmp = array_flip($tables);
	$tmpid = $tmp[$table];
	$count = count($tables);
	if ($tmpid + 1 < $count) {
		$next = $tables[++$tmpid];
		show_msg("กำลังดำเนินการอัปเกรดตารางข้อมูลเป็น utf8mb4 และอัปเกรดตาราง $table เรียบร้อยแล้ว ความคืบหน้าปัจจุบันคือ $tmpid / $count กำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&table=".$next);
	} else {
		show_msg("การอัปเกรดตารางข้อมูลเป็น utf8mb4 เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=serialize");
	}

} else if ($step == 'serialize') {

	if (!isset($_GET['start']) && !isset($_GET['tid'])) {

		logmessage("start serialize convert.");

		if ($config['dbcharset'] == 'utf8' || $config['dbcharset'] == 'utf8mb4') {
			logmessage("serialize not needed because of this site is ".$config['dbcharset']." site.");
			//show_msg("ไม่จำเป็นต้องแปลงข้อมูลแบบซีเรียลไลซ์ กำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=dataupdate");
			show_msg("ไม่จำเป็นต้องแปลงข้อมูลแบบซีเรียลไลซ์ กำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=confirmthaidistrictupgrade");
		}

		// 针对本地化编码版本转换 UTF-8 版本的情况, 可能前期 InnoDB 或者 UTF8MB4 转换时站长手动删除过不能转换的数据表
		// 因此这里需要删除表缓存, 避免后续序列化转换尤其是第三方序列化转换时尝试升级之前步骤删除的第三方数据表
		$tcfile = DISCUZ_ROOT . './data/sysdata/cache_update_tables.php';
		logmessage("unlink " . str_replace(DISCUZ_ROOT, '', $tcfile) . " for {$config['dbcharset']} version.");
		@unlink($tcfile);

		$configfile = DISCUZ_ROOT.'./config/config_global.php';
		$configfile_uc = DISCUZ_ROOT.'./config/config_ucenter.php';

		// 对非 Windows 系统尝试设置 777 权限
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			logmessage("set chmod 777 $configfile");
			chmod($configfile, 0777);
			logmessage("set chmod 777 $configfile_uc");
			chmod($configfile_uc, 0777);
		}

		if (is_writable($configfile) && is_writable($configfile_uc)) {
			$config = file_get_contents($configfile);
			$config = preg_replace("/\['dbcharset'\] = \s*'.*?'\;/i", "['dbcharset'] = 'utf8mb4';", $config);
			$config = preg_replace("/\['output'\]\['charset'\] = \s*'.*?'\;/i", "['output']['charset'] = 'utf-8';", $config);
			// logmessage("new config_global.php content:");
			// logmessage($config);
			if(file_put_contents($configfile, $config, LOCK_EX) === false) {
				logmessage("config_global.php modify fail, let user manually modify.");
				show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้คุณโดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไข config/config_global.php และ config/config_ucenter.php ด้วยตนเอง เปลี่ยนการกำหนดค่า dbcharset และค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง เปลี่ยนการกำหนดค่าชุดอักขระและค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=serialize&start=0&tid=0">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
			}
			logmessage("config_global.php modify ok, continue.");
			$config_uc = file_get_contents($configfile_uc);
			$config_uc = preg_replace("/define\('UC_DBCHARSET',\s*'.*?'\);/i", "define('UC_DBCHARSET', 'utf8mb4');", $config_uc);
			$config_uc = preg_replace("/define\('UC_CHARSET',\s*'.*?'\);/i", "define('UC_CHARSET', 'utf-8');", $config_uc);
			// logmessage("new config_ucenter.php content:");
			// logmessage($config_uc);
			if(file_put_contents($configfile_uc, $config_uc, LOCK_EX) === false) {
				logmessage("config_ucenter.php modify fail, let user manually modify.");
				show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้คุณโดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไข config/config_ucenter.php ด้วยตนเอง เปลี่ยนค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง เปลี่ยนค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=serialize&start=0&tid=0">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
			}
			logmessage("config_ucenter.php modify ok, continue.");
			show_msg("ตั้งค่าคอนฟิกการแปลงข้อมูลแบบซีเรียลไลซ์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=serialize&fromcharset={$_config['output']['charset']}&start=0&tid=0");
		} else {
			logmessage("config_global.php modify fail, let user manually modify site config and ucenter config.");
			show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้คุณโดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไข config/config_global.php และ config/config_ucenter.php ด้วยตนเอง โดยเปลี่ยนการกำหนดค่า dbcharset และค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง เปลี่ยนการกำหนดค่าชุดอักขระและค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=serialize&fromcharset='.$_config['output']['charset'].'&start=0&tid=0">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
		}

	}

	$fromcharset = isset($_GET['fromcharset']) && strtolower($_GET['fromcharset']) == 'big5' ? 'big5' : 'gbk';

	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal();

	$limit = 1000;
	$nextid = 0;

	$start = empty($_GET['start']) ? 0 : intval($_GET['start']);
	$tid = empty($_GET['tid']) ? 0 : intval($_GET['tid']);

	$arr = get_serialize_list();

	$field = $arr[$tid];
	$arr[$tid];
	$stable = str_replace('pre_', $config['tablepre'], $field[0]);
	$sfield = $field[1];
	$sids = explode(',', $field[2]);
	$sid = $sids[0];
	$sid2 = !empty($sids[1]) ? $sids[1] : '';
	$special = $field[3];

	if (in_array($field[0], $tables)) {
		$isblob = false;
		$to_result = DB::fetch_all("SHOW COLUMNS FROM $stable;");
		foreach ($to_result as $tc) {
			if ($sfield == $tc['Field'] && strstr($tc['Type'], 'blob')) {
				$isblob = true;
			}
		}

		if ($special && empty($sid2)) {
			// 空值不参与序列化转换, 加快数据处理效率
			// https://www.dismall.com/thread-15293-1-1.html
			$sql = "SELECT `$sfield`, `$sid` FROM `$stable` WHERE `$sfield`<>'' AND `$sid` > $start ORDER BY `$sid` ASC LIMIT $limit";
		} else {
			$sql = "SELECT `$sfield`, `$sid` " . (!empty($sid2) ? ", `$sid2`" : "") . " FROM `$stable`";
		}

		logmessage("RUNSQL ".$sql);
		$query = DB::query($sql);
		logmessage("RUNSQL Success");

		while ($values = DB::fetch($query)) {
			if ($special) {
				$nextid = $values[$sid];
			} else {
				$nextid = 0;
			}
			$datanew = '';
			$data = $values[$sfield];
			$dataold = $values[$sfield];
			$id = $values[$sid];
			$id2 = !empty($sid2) && !empty($values[$sid2]) ? $values[$sid2] : '';
			if ($isblob) {
				$tmp = dunserialize($data);
				if ($tmp !== false) {
					$datanew = serialize(diconv_array($tmp, $fromcharset, 'UTF-8'));
				} else {
					$data = diconv($data, $fromcharset, 'UTF-8');
				}
			} else {
				// 还原原编码
				$olddata = diconv($data, 'UTF-8', $fromcharset);
				$tmp = dunserialize($olddata);
				if ($tmp !== false) {
					$datanew = serialize(diconv_array($tmp, $fromcharset, 'UTF-8'));
				}
			}
			// 反序列化转码方式无法处理时，才使用正则替换，因为正则匹配在极端情况下匹配可能不准确，比如值里含有";
			if ($datanew === '') {
				$datanew = preg_replace_callback('/s:([0-9]+?):"([\s\S]*?)";/', '_serialize', $data);
			}
			$datanew = addslashes($datanew);
			if (strcmp($dataold, $datanew) !== 0) {
				$sql = "UPDATE `$stable` SET `$sfield` = '$datanew' WHERE `$sid` = '$id'" . (!empty($sid2) ? " AND `$sid2` = '$id2'" : "");
				logmessage("RUNSQL ".$sql);
				DB::query($sql);
				logmessage("RUNSQL Success");
			}
		}
	}

	if ($nextid) {
		show_msg("กำลังดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์ กำลังอัปเกรดตาราง $stable และกำลังดำเนินการในขั้นตอนต่อไป (กำลังเริ่มต้นการแปลงข้อมูล $tid จาก $nextid) กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=$step&fromcharset={$fromcharset}&tid=$tid&start=$nextid");
	} else {
		if (++$tid < count($arr)) {
			show_msg("กำลังดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์ กำลังอัปเกรดตาราง $stable และกำลังดำเนินการในขั้นตอนต่อไป (กำลังเริ่มต้นการแปลงข้อมูล $tid จาก $nextid) กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=$step&fromcharset={$fromcharset}&tid=$tid&start=0");
		} else {
			show_msg("ดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=serialize_plugin&fromcharset={$fromcharset}");
		}
	}

} else if ($step == 'serialize_plugin') {

	$fromcharset = isset($_GET['fromcharset']) && strtolower($_GET['fromcharset']) == 'big5' ? 'big5' : 'gbk';

	logmessage("start plugin serialize convert.");

	$tables_outside = $arr = array();

	$limit = 1000;
	$nextid = 0;
	$start = empty($_GET['start']) ? 0 : intval($_GET['start']);
	$tid = empty($_GET['tid']) ? 0 : intval($_GET['tid']);
	$cachename = 'update_serialize_plugin';
	$cachefile = DISCUZ_ROOT . './data/sysdata/cache_' . $cachename . '.php';
	if ($tid === 0 || !file_exists($cachefile)) {
		foreach ($tables as $tb) {
			if (in_array(str_replace($config['tablepre'], 'pre_', $tb), $sys_tables_not_full)) {
				continue; // 不对系统内的表做扫描，UCenter表之前已排除过
			}
			if (preg_match('/^'.$config['tablepre'].'forum_(post|thread)_[0-9]+/i', $tb) || strstr($tb, $config['tablepre'].'forum_optionvalue')) {
				continue; // 不对系统内的表做扫描，此处排除主题、帖子分表和分类信息分表
			}
			$tables_outside = array_merge($tables_outside, array($tb));
		}

		// 对于因数据库超时而升级失败的特大站点请看此函数
		setdbglobal();

		foreach ($tables_outside as $to) {
			$fkey = '';
			$to = str_replace('pre_', $config['tablepre'], $to);
			$to_result = DB::fetch_all("SHOW COLUMNS FROM $to;");
			if (!empty($to_result[0]) && strstr($to_result[0]['Type'], 'int') && strstr($to_result[0]['Extra'], 'auto_increment')) {
				$fkey = $to_result[0]['Field'];
			}
			if (empty($fkey)) {
				$keys = array();
				foreach ($to_result as $tc) {
					if ($tc['Key'] === 'PRI') {
						$keys[] = $tc['Field'];
						if (empty($fkey) && strstr($tc['Type'], 'int')) {
							$fkey = $tc['Field'];
						}
					}
				}
				//不处理联合主键的情况
				if (count($keys) > 1) {
					$fkey = '';
				}
			}
			foreach ($to_result as $tc) {
				if (strstr($tc['Type'], 'char') || strstr($tc['Type'], 'text') || strstr($tc['Type'], 'blob')) {
					$tmp_arr = array($to, $tc['Field'], $tc['Type'], $fkey);
					$arr = array_merge($arr, array($tmp_arr));
				}
			}
		}
		logmessage("write " . str_replace(DISCUZ_ROOT, '', $cachefile) . "\t" . count($arr) . ".");
		writetocache($cachename, getcachevars(array('arr' => $arr, 'tables_outside' => $tables_outside)));
	} else {
		@include_once $cachefile;
	}



	$field = $arr[$tid];

	$stable = $field[0];
	$sfield = $field[1];
	$stype = $field[2];
	$skey = $field[3];

	if (!empty($stable) && !empty($sfield)) {
		if (!empty($skey)) {
			// 空值不参与序列化转换, 加快数据处理效率
			// https://www.dismall.com/thread-15293-1-1.html
			$sql = "SELECT `$sfield`, `$skey` FROM `$stable` WHERE `$sfield`<>'' AND `$skey` > $start ORDER BY `$skey` ASC LIMIT $limit";
		} else {
			$sql = "SELECT `$sfield` FROM `$stable`";
		}
		$query = DB::query($sql);

		while ($values = DB::fetch($query)) {
			if (!empty($skey)) {
				$nextid = $values[$skey];
			} else {
				$nextid = 0;
			}
			$datanew = '';
			$data = $values[$sfield];
			if (preg_match('/s:([0-9]+?):"([\s\S]*?)";/', $data) === 1) {
				if (strstr($stype, 'blob')) {
					$tmp = dunserialize($data);
					if ($tmp !== false) {
						$datanew = serialize(diconv_array($tmp, $fromcharset, 'UTF-8'));
					} else {
						$data = diconv($data, $fromcharset, 'UTF-8');
					}
				} else {
					// 还原原编码
					$olddata = diconv($data, 'UTF-8', $fromcharset);
					$tmp = dunserialize($olddata);
					if ($tmp !== false) {
						$datanew = serialize(diconv_array($tmp, $fromcharset, 'UTF-8'));
					}
				}
				// 反序列化转码方式无法处理时，才使用正则替换，因为正则匹配在极端情况下匹配可能不准确，比如值里含有";
				if ($datanew === '') {
					$datanew = preg_replace_callback('/s:([0-9]+?):"([\s\S]*?)";/', '_serialize', $data);
				}
				if (dunserialize($datanew) !== false) {
					$datanew = addslashes($datanew);
					$data = addslashes($data);
					if (strcmp($data, $datanew) !== 0) {
						$sql = "UPDATE `$stable` SET `$sfield` = '$datanew' WHERE `$sfield` = '$data';";
						logmessage("RUNSQL ".$sql);
						DB::query($sql);
						logmessage("RUNSQL Success");
					}
				}
			} elseif (strstr($stype, 'blob')) {
				$datanew = diconv($data, $fromcharset, 'UTF-8');
				$datanew = addslashes($datanew);
				$data = addslashes($data);
				if (strcmp($data, $datanew) !== 0) {
					$sql = "UPDATE `$stable` SET `$sfield` = '$datanew' WHERE `$sfield` = '$data';";
					logmessage("RUNSQL ".$sql);
					DB::query($sql);
					logmessage("RUNSQL Success");
				}
			}
		}
	}

	if ($nextid) {
		show_msg("กำลังดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์ของบุคคลที่สาม กำลังอัปเกรดตาราง $stable และกำลังดำเนินการในขั้นตอนต่อไป (กำลังเริ่มต้นการแปลงข้อมูล $tid จาก $nextid) กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=$step&fromcharset={$fromcharset}&tid=$tid&start=$nextid");
	} else {
		if (++$tid < count($arr)) {
			show_msg("กำลังดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์ของบุคคลที่สาม กำลังอัปเกรดตาราง $stable และกำลังดำเนินการในขั้นตอนต่อไป (กำลังแปลงข้อมูล $tid) จะดำเนินการในเร็ว ๆ นี้ กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=$step&fromcharset={$fromcharset}&tid=$tid&start=0");
		} else {
			logmessage("unlink " . str_replace(DISCUZ_ROOT, '', $cachefile) . ".");
			@unlink($cachefile);
			show_msg("แปลงข้อมูลแบบซีเรียลไลซ์ของบุคคลที่สามเรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=file");
		}
	}

} else if ($step == 'file') {

	logmessage("start file convert.");

	encode_tree(__DIR__.'/../data/log/');
	encode_tree(__DIR__.'/../source/plugin/');
	encode_tree(__DIR__.'/../template/');

	//show_msg("แปลงการเข้ารหัสไฟล์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=dataupdate");
	show_msg("แปลงการเข้ารหัสไฟล์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=confirmthaidistrictupgrade");

} else if ($step == 'confirmthaidistrictupgrade') { /*jaideejung007*/

	show_msg('<p class="lead" style="color: red;font-weight: bold;">ขั้นตอนนี้จะอัปเกรดฐานข้อมูลรายชื่อจังหวัด อำเภอ ตำบลในประเทศไทยใหม่ล่าสุด (จาก ThepExcel ปรับปรุงปี 2565) พร้อมทั้งรายชื่อ 199 ประเทศและอีก 52 ดินแดน (จากสำนักงานราชบัณฑิตยสภา 29 มิถุนายน 2565)</p><p class="lead" style="color: red;font-weight: bold;">ขอแนะนำให้อัปเกรด เนื่องจาก X3.4 ได้ใช้ฐานข้อมูลเก่าตั้งแต่ปี 2555 (ซึ่งปัจจุบันรายชื่อจังหวัด อำเภอ ตำบลมีการอัปเดตใหม่แล้ว) โดยกระบวนการนี้จะลบข้อมูลเก่าในตาราง pre_common_district ออกแล้วนำเข้าใหม่ทั้งหมด</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=thaidistrictupgrade\';">อัปเกรดฐานข้อมูลรายชื่อจังหวัดฯ ></button> <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=cancelthaidistrictupgrade\';">ข้าม (ไม่แนะนำ)</button></p>', 'อัปเกรดฐานข้อมูลรายชื่อจังหวัด อำเภอ ตำบลในประเทศไทยใหม่ล่าสุด', 1);
} else if ($step == 'thaidistrictupgrade') { /*jaideejung007*/

	$sql = thai_district_upgrade_sql('pre_common_district'); // เรียกใช้ TRUNCATE TABLE pre_common_district เพื่อลบข้อมูลในตารางฐานข้อมูลออก
	logmessage("RUNSQL ".$sql);
	DB::query($sql);
	logmessage("RUNSQL Success");
	install_testdata(); // นำเข้าฐานข้อมูลรายชื่อจังหวัดฯ จากไฟล์ common_district_*.sql
	logmessage("import Thai common_district_*.sql ok, continue.");
	show_msg("อัปเกรดฐานข้อมูลรายชื่อจังหวัดฯ เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=dataupdate");

} else if ($step == 'cancelthaidistrictupgrade') { /*jaideejung007*/
	// หากผู้ใช้กดปุ่ม ข้าม การอัปเกรดฐานข้อมูลรายชื่อจังหวัดฯ ให้เรียกคำสั่งนี้
	cancle_thai_district_upgrade();
	logmessage("Skipped import Thai common_district_*.sql, continue.");
	show_msg("คุณได้ข้ามการอัปเกรดฐานข้อมูลรายชื่อจังหวัดฯ กำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=dataupdate");

} else if ($step == 'dataupdate') {

	logmessage("start data update.");
	db_content_update();
	dir_clear(DISCUZ_ROOT.'./data/template');
	dir_clear(DISCUZ_ROOT.'./data/cache');
	dir_clear(DISCUZ_ROOT.'./data/threadcache');
	dir_clear(DISCUZ_ROOT.'./uc_client/data');
	dir_clear(DISCUZ_ROOT.'./uc_client/data/cache');
	//自动删除这个文件，避免站长直接覆盖X3.5文件执行的升级，这个文件会导致缓存更新报错
	@unlink(DISCUZ_ROOT.'./source/function/cache/cache_ipbanned.php');
	//jaideejung007 ลบไฟล์ geoiploc.php ออก เนื่องจากไม่ได้ใช้งานแล้ว
	@unlink(DISCUZ_ROOT.'./data/ipdata/geoiploc.php');
	logmessage("unlink ./data/ipdata/geoiploc.php");
	
	logmessage("unlink " . str_replace(DISCUZ_ROOT, '', $tablescachefile) . ".");
	@unlink($tablescachefile);

	savecache('setting', '');
	C::memory()->clear();

	$configfile = DISCUZ_ROOT.'./config/config_global.php';
	$configfile_uc = DISCUZ_ROOT.'./config/config_ucenter.php';

	// 对非 Windows 系统尝试设置 777 权限
	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
		logmessage("set chmod 777 $configfile");
		chmod($configfile, 0777);
		logmessage("set chmod 777 $configfile_uc");
		chmod($configfile_uc, 0777);
	}

	if (is_writable($configfile) && is_writable($configfile_uc)) {
		$config = file_get_contents($configfile);
		$config = preg_replace("/\['dbcharset'\] = \s*'.*?'\;/i", "['dbcharset'] = 'utf8mb4';", $config);
		$config = preg_replace("/\['output'\]\['charset'\] = \s*'.*?'\;/i", "['output']['charset'] = 'utf-8';", $config);
		if(file_put_contents($configfile, $config, LOCK_EX) === false) {
			logmessage("config_global.php modify fail, let user manually modify.");
			show_msg('<font color="red"><b>อัปเกรดเรียบร้อยแล้ว แต่โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกโดยอัตโนมัติให้สำเร็จได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไขไฟล์ config/config_global.php และไฟล์ config/config_ucenter.php ด้วยตนเอง เปลี่ยนการกำหนดค่า dbcharset และค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง และเปลี่ยนการกำหนดค่าชุดอักขระและค่า UC_CHARSET เป็น utf8 ด้วยตนเอง นอกจากนี้ ให้ดูที่ไฟล์คอนฟิกตัวอย่างของ UCenter เพื่อเพิ่มค่าที่จำเป็นสามค่า UC_STANDALONE / UC_AVTURL / UC_AVTPATH ให้ถูกต้อง กรุณาอย่าเรียกใช้งานขั้นตอนนี้ซ้ำ เพราะอาจทำให้เกิดปัญหาที่ไม่ทราบสาเหตุได้</b></font><iframe src="../misc.php?mod=initsys" style="display:none;"></iframe>', 'คำแนะนำการอัปเกรด');
		}
		logmessage("config_global.php modify ok, continue.");
		$config_uc = file_get_contents($configfile_uc);
		$config_uc = preg_replace("/define\('UC_DBCHARSET',\s*'.*?'\);/i", "define('UC_DBCHARSET', 'utf8mb4');", $config_uc);
		$config_uc = preg_replace("/define\('UC_CHARSET',\s*'.*?'\);/i", "define('UC_CHARSET', 'utf-8');", $config_uc);
		// 只在数据升级增加应该就行, 序列化升级不依赖这个常量
		$config_uc = str_replace("define('UC_CHARSET', 'utf-8');", "define('UC_CHARSET', 'utf-8');\r\ndefine('UC_STANDALONE', 0);\r\ndefine('UC_AVTURL', '');\r\ndefine('UC_AVTPATH', '');\r\n", $config_uc);
		if(file_put_contents($configfile_uc, $config_uc, LOCK_EX) === false) {
			logmessage("config_ucenter.php modify fail, let user manually modify.");
			show_msg('<font color="red"><b>อัปเกรดเรียบร้อยแล้ว แต่โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกโดยอัตโนมัติให้สำเร็จได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไขไฟล์ config/config_ucenter.php ด้วยตนเอง เปลี่ยนค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง และเปลี่ยนค่า UC_CHARSET เป็น utf8 ด้วยตนเอง นอกจากนี้ ให้ดูที่ไฟล์คอนฟิกตัวอย่างของ UCenter เพื่อเพิ่มค่าที่จำเป็นสามค่า UC_STANDALONE / UC_AVTURL / UC_AVTPATH ให้ถูกต้อง ขอความกรุณาอย่าเรียกใช้งานขั้นตอนนี้ซ้ำ เพราะอาจทำให้เกิดปัญหาที่ไม่ทราบสาเหตุได้</b></font><iframe src="../misc.php?mod=initsys" style="display:none;"></iframe>', 'คำแนะนำการอัปเกรด');
		}
		logmessage("config_ucenter.php modify ok, continue.");
	} else {
		logmessage("config_global.php modify fail, let user manually modify 2 files.");
		show_msg('<font color="red"><b>อัปเกรดเรียบร้อยแล้ว แต่โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกโดยอัตโนมัติให้สำเร็จได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไขไฟล์ config/config_global.php และไฟล์ config/config_ucenter.php ด้วยตนเอง เปลี่ยนการกำหนดค่า dbcharset และค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง และเปลี่ยนการกำหนดค่าชุดอักขระและค่า UC_CHARSET เป็น utf8 ด้วยตนเอง นอกจากนี้ ให้ดูที่ไฟล์คอนฟิกตัวอย่างของ UCenter เพื่อเพิ่มค่าที่จำเป็นสามค่า UC_STANDALONE / UC_AVTURL / UC_AVTPATH ให้ถูกต้อง ขอความกรุณาอย่าเรียกใช้งานขั้นตอนนี้ซ้ำ เพราะอาจทำให้เกิดปัญหาที่ไม่ทราบสาเหตุได้</b></font><iframe src="../misc.php?mod=initsys" style="display:none;"></iframe>', 'คำแนะนำการอัปเกรด');
	}

	show_msg('อัปเดตข้อมูลเรียบร้อยแล้ว กำลังอัปเดตไฟล์แคช กรุณารอสักครู่......<iframe src="../misc.php?mod=initsys" style="display:none;" onload="window.location.href=\''.$theurl.'?lock=1\'"></iframe>', 'คำแนะนำการอัปเกรด');

}

function diconv_array($variables, $in_charset, $out_charset) {
	foreach($variables as $_k => $_v) {
		if(is_array($_v)) {
			$variables[$_k] = diconv_array($_v, $in_charset, $out_charset);
		} elseif(is_string($_v)) {
			$variables[$_k] = diconv($_v, $in_charset, $out_charset);
		}
	}
	return $variables;
}

function show_msg($message, $title = 'คำแนะนำการอัปเกรด', $page = 0, $url_forward = '', $time = 1, $noexit = 0, $notice = '') {
	if ($url_forward) {
		$message = "<a href=\"$url_forward\">$message (กำลังข้าม......)</a><br />$notice<script>setTimeout(\"window.location.href ='$url_forward';\", $time);</script>";
	}

	if (!$page) {
		$message = '<p class="lead">'.$message.'</p>';
	}

	show_header();
	print<<<END
<main role="main" class="flex-shrink-0">
<div class="container">
<h1 class="mt-5">$title</h1>
$message
<p>$notice</p>
</div>
</main>
END;
	show_footer();
	!$noexit && exit();
}

function show_header() {
	print<<<END
<!DOCTYPE html>
<html class="h-100">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="Discuz! X Community Team">
<title>โปรแกรมอัปเกรด Discuz! X3.5</title>
<link rel="stylesheet" href="?css=1">
<style>
main > .container {padding: 15px 15px 0;}
.footer {background-color: #f5f5f5;}
.footer > .container {padding-right: 15px; padding-left: 15px;}
code {font-size: 80%;}
.lead {margin: 2em 0; line-height: 2em;}
</style>
</head>

<body class="d-flex flex-column h-100">
<header>
<nav class="navbar navbar-expand-md navbar-light bg-light">
<a class="navbar-brand">โปรแกรมอัปเกรด Discuz! X3.5</a>
<div class="collapse navbar-collapse" id="navbarCollapse">
<ul class="navbar-nav mr-auto">
<li class="nav-item">
<a class="nav-link" href="https://gitee.com/Discuz/DiscuzX/" target="_blank">Git ของ Discuz! X อย่างเป็นทางการ</a>
</li>
<li class="nav-item">
<a class="nav-link" href="https://www.discuz.net/" target="_blank">เว็บไซต์ Discuz! X อย่างเป็นทางการ</a>
</li>
<li class="nav-item">
<a class="nav-link" href="https://github.com/jaideejung007/discuzth" target="_blank">GitHub ของดิสคัส! ทีเอช อย่างเป็นทางการ</a>
</li>
<li class="nav-item">
<a class="nav-link" href="https://discuzth.com/" target="_blank">เว็บไซต์ ดิสคัส! ทีเอช อย่างเป็นทางการ</a>
</li>
</ul>
</div>
</nav>
</header>

END;
}

function show_footer() {
	$date = date("Y");
	print<<<END

<footer class="footer mt-auto py-3">
<div class="container">
<span class="text-muted">Powered by Discuz! X Community Team. Copyright &copy; 2001-$date Tencent Cloud.</span>
</div>
</footer>
</body>
</html>
END;
}

function dir_clear($dir) {
	global $lang;
	if ($directory = @dir($dir)) {
		while($entry = $directory->read()) {
			$filename = $dir.'/'.$entry;
			if (is_file($filename)) {
				@unlink($filename);
			}
		}
		$directory->close();
		@touch($dir.'/index.htm');
	}
}

function get_convert_sql($type, $table) {
	global $config;
	$table = str_replace('pre_', $config['tablepre'], $table);
	if ($type == 'innodb') {
		$query = "ALTER TABLE $table ENGINE=InnoDB;";
	} else if ($type == 'utf8mb4') {
		$query = "ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
	} else if ($type == 'check') {
		$query = "SHOW TABLE STATUS WHERE `Name` = '$table';";
	}
	return $query;
}

function get_innodb_scheme_update_sql($table, $statusonly = false) {
	global $config;
	// 每条数据库处理指令一行
	$query = array(
		'pre_common_admincp_perm' => 'ALTER TABLE pre_common_admincp_perm DROP KEY `cpgroupperm`, ADD KEY `cpgroupperm` (`cpgroupid`, `perm`(40));',
		'pre_common_advertisement_custom' => 'ALTER TABLE pre_common_advertisement_custom DROP KEY `name`, ADD KEY `name` (`name`(100));',
		'pre_common_banned' => 'TRUNCATE TABLE pre_common_banned;',
		'pre_common_block_style' => 'ALTER TABLE pre_common_block_style DROP KEY `hash`, ADD KEY `hash` (`hash`(10)), DROP KEY `blockclass`, ADD KEY `blockclass` (`blockclass`(50));',
		'pre_common_cache' => 'ALTER TABLE pre_common_cache DROP PRIMARY KEY, ADD PRIMARY KEY (`cachekey`(50));',
		'pre_common_card' => 'ALTER TABLE pre_common_card DROP PRIMARY KEY, ADD PRIMARY KEY (`id`(50));',
		'pre_common_member_grouppm' => 'ALTER TABLE pre_common_member_grouppm ADD INDEX gpmid(gpmid);',
		'pre_common_member_profile_setting' => 'ALTER TABLE pre_common_member_profile_setting DROP PRIMARY KEY, ADD PRIMARY KEY (`fieldid`(30));',
		'pre_common_member_secwhite' => 'TRUNCATE TABLE pre_common_member_secwhite;',
		'pre_common_member_security' => 'ALTER TABLE pre_common_member_security DROP KEY `uid`, ADD KEY `uid` (`uid`, `fieldid`(40));',
		// 由于不是每个站点都有用户分表, 因此把他放在预调整能节省开发资源
		'pre_common_member_archive' => 'ALTER TABLE pre_common_member_archive MODIFY COLUMN email varchar(255) NOT NULL DEFAULT \'\', ADD COLUMN `secmobile` varchar(12) NOT NULL DEFAULT \'\' AFTER `password`, ADD COLUMN `secmobicc` varchar(3) NOT NULL DEFAULT \'\' AFTER `password`, ADD COLUMN `secmobilestatus` tinyint(1) NOT NULL DEFAULT \'0\' AFTER avatarstatus, ADD KEY secmobile (`secmobile`, `secmobicc`);',
		'pre_common_member_field_forum_archive' => 'ALTER TABLE pre_common_member_field_forum_archive MODIFY COLUMN authstr varchar(255) NOT NULL DEFAULT \'\', MODIFY COLUMN customshow tinyint(3) unsigned NOT NULL DEFAULT \'26\';',
		'pre_common_member_field_home_archive' => 'ALTER TABLE pre_common_member_field_home_archive ADD COLUMN allowasfollow tinyint(1) NOT NULL DEFAULT \'1\' AFTER addfriend, ADD COLUMN allowasfriend tinyint(1) NOT NULL DEFAULT \'1\' AFTER addfriend;',
		'pre_common_member_profile_archive' => 'ALTER TABLE pre_common_member_profile_archive ADD COLUMN birthcountry varchar(255) NOT NULL DEFAULT \'\' AFTER nationality, ADD COLUMN residecountry varchar(255) NOT NULL DEFAULT \'\' AFTER birthcommunity;',
		'pre_common_member_status_archive' => 'ALTER TABLE pre_common_member_status_archive MODIFY COLUMN regip VARCHAR(45) NOT NULL DEFAULT \'\', MODIFY COLUMN lastip VARCHAR(45) NOT NULL DEFAULT \'\', ADD COLUMN regport SMALLINT(6) unsigned NOT NULL DEFAULT \'0\' AFTER lastip;',
		'pre_common_member_stat_field' => 'ALTER TABLE pre_common_member_stat_field DROP KEY `fieldid`, ADD KEY `fieldid` (`fieldid`(40));',
		'pre_common_process' => 'TRUNCATE TABLE pre_common_process;',
		'pre_common_searchindex' => 'TRUNCATE TABLE pre_common_searchindex;',
		'pre_common_seccheck' => 'TRUNCATE TABLE pre_common_seccheck;',
		'pre_common_setting' => 'ALTER TABLE pre_common_setting DROP PRIMARY KEY, ADD PRIMARY KEY (`skey`(40));',
		'pre_common_session' => 'TRUNCATE TABLE pre_common_session;',
		'pre_common_visit' => 'TRUNCATE TABLE pre_common_visit;',
		'pre_forum_groupfield' => 'ALTER TABLE pre_forum_groupfield DROP KEY `types`, ADD KEY `types` (`fid`, `type`(40)), DROP KEY `type`, ADD KEY `type` (`type`(40));',
		'pre_forum_post' => 'ALTER TABLE pre_forum_post MODIFY COLUMN position INT unsigned NOT NULL DEFAULT \'0\'',
		'pre_forum_rsscache' => 'TRUNCATE TABLE pre_forum_rsscache;',
		'pre_forum_spacecache' => 'TRUNCATE TABLE pre_forum_spacecache;',
		'pre_forum_threaddisablepos' => 'TRUNCATE TABLE pre_forum_threaddisablepos;',
		'pre_home_favorite' => 'ALTER TABLE pre_home_favorite DROP KEY `idtype`, ADD KEY `idtype` (`id`, `idtype`(40)), DROP KEY `uid`, ADD KEY `uid` (`uid`, `idtype`(40), `dateline`);',
		'pre_mobile_setting' => 'ALTER TABLE pre_mobile_setting DROP PRIMARY KEY, ADD PRIMARY KEY (`skey`(40));',
		'pre_portal_topic' => 'ALTER TABLE pre_portal_topic DROP KEY `name`, ADD KEY `name` (`name`(40));'
	);
	return $statusonly ? array_key_exists($table, $query) : str_replace(' pre_', ' '.$config['tablepre'], $query[$table]);
}

function thai_district_upgrade_sql($table, $statusonly = false) { /*jaideejung007*/
	global $config;
	// ลบข้อมูลในตาราง pre_common_district ออกทั้งหมด เพื่อเตรียมนำเขัาข้อมูลใหม่
	$query = array(
		'pre_common_district' => 'TRUNCATE TABLE pre_common_district;' /*jaideejung007*/
	);
	return $statusonly ? array_key_exists($table, $query) : str_replace(' pre_', ' '.$config['tablepre'], $query[$table]);
}

function _serialize($str) {
	$l = strlen($str[2]);
	return 's:'.$l.':"'.$str[2].'";';
}

function get_serialize_list() {
	return array(
		array('pre_common_advertisement', 'parameters', 'advid', TRUE),
		array('pre_common_block', 'param', 'bid', TRUE),
		array('pre_common_block', 'blockstyle', 'bid', TRUE),
		array('pre_common_block_item', 'fields', 'itemid', TRUE),
		array('pre_common_block_item_data', 'showstyle', 'dataid', TRUE),
		array('pre_common_block_item_data', 'fields', 'dataid', TRUE),
		array('pre_common_block_style', 'template', 'styleid', TRUE),
		array('pre_common_block_style', 'fields', 'styleid', TRUE),
		array('pre_common_diy_data', 'diycontent', 'targettplname,tpldirectory', FALSE),
		array('pre_common_magic', 'magicperm', 'magicid', TRUE),
		array('pre_common_member_field_forum', 'groups', 'uid', TRUE),
		array('pre_common_member_field_forum', 'groupterms', 'uid', TRUE),
		array('pre_common_member_field_forum_archive', 'groups', 'uid', TRUE),
		array('pre_common_member_field_forum_archive', 'groupterms', 'uid', TRUE),
		array('pre_common_member_field_home', 'blockposition', 'uid', TRUE),
		array('pre_common_member_field_home', 'privacy', 'uid', TRUE),
		array('pre_common_member_field_home', 'acceptemail', 'uid', TRUE),
		array('pre_common_member_field_home', 'magicgift', 'uid', TRUE),
		array('pre_common_member_field_home_archive', 'blockposition', 'uid', TRUE),
		array('pre_common_member_field_home_archive', 'privacy', 'uid', TRUE),
		array('pre_common_member_field_home_archive', 'acceptemail', 'uid', TRUE),
		array('pre_common_member_field_home_archive', 'magicgift', 'uid', TRUE),
		array('pre_common_member_newprompt', 'data', 'uid', TRUE),
		array('pre_common_member_stat_search', 'condition', 'optionid', TRUE),
		array('pre_common_member_verify_info', 'field', 'vid', TRUE),
		array('pre_common_patch', 'rule', 'serial', TRUE),
		array('pre_common_plugin', 'modules', 'pluginid', TRUE),
		array('pre_common_setting', 'svalue', 'skey', FALSE),
		array('pre_common_syscache', 'data', 'cname', FALSE),
		array('pre_forum_activity', 'ufield', 'tid', TRUE),
		array('pre_forum_forumfield', 'creditspolicy', 'fid', TRUE),
		array('pre_forum_forumfield', 'formulaperm' ,'fid', TRUE),
		array('pre_forum_forumfield', 'threadtypes', 'fid', TRUE),
		array('pre_forum_forumfield', 'supe_pushsetting', 'fid', TRUE),
		array('pre_forum_forumfield', 'modrecommend', 'fid', TRUE),
		array('pre_forum_forumfield', 'extra', 'fid', TRUE),
		array('pre_forum_groupfield', 'data', 'fid', TRUE),
		array('pre_forum_grouplevel', 'creditspolicy', 'levelid', TRUE),
		array('pre_forum_grouplevel', 'postpolicy' ,'levelid', TRUE),
		array('pre_forum_grouplevel', 'specialswitch' ,'levelid', TRUE),
		array('pre_forum_medal', 'permission', 'medalid', TRUE),
		array('pre_forum_postcache', 'comment', 'pid', TRUE),
		array('pre_forum_postcache', 'rate', 'pid', TRUE),
		array('pre_forum_spacecache', 'value', 'uid', TRUE),
		array('pre_forum_threadprofile', 'template', 'id', TRUE),
		array('pre_forum_typeoption', 'rules', 'optionid', TRUE),
		array('pre_home_feed', 'title_data', 'feedid', TRUE),
		array('pre_home_feed', 'body_data', 'feedid', TRUE),
		array('pre_home_share', 'body_data', 'sid', TRUE),
		array('pre_mobile_wechat_resource', 'data', 'id', TRUE),
		array('pre_mobile_wsq_threadlist', 'svalue', 'skey', TRUE),
	);
}

function db_content_update() {
	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal();
	// 开启程序所有功能
	logmessage("open all features.");
	$feats = array('portal', 'forum', 'friend', 'group', 'follow', 'collection', 'guide', 'feed', 'blog', 'doing', 'album', 'share', 'wall', 'homepage', 'ranklist', 'medal', 'task', 'magic', 'favorite');
	foreach ($feats as $type) {
		$funkey = $type.'status';
		$identifier = array('portal' => 1, 'forum' => 2, 'group' => 3, 'feed' => 4, 'ranklist' => 8, 'follow' => 9, 'guide' => 10, 'collection' => 11, 'blog' => 12, 'album' => 13, 'share' => 14, 'doing' => 15, 'friend' => 26, 'favorite' => 27, 'medal' => 29, 'task' => 30, 'magic' => 31);
		$navdata = array('available' => 1);
		$navtype = array(0, 3);
		if (in_array($type, array('blog', 'album', 'share', 'doing', 'follow', 'friend', 'favorite', 'medal', 'task', 'magic'))) {
			$navtype[] = 2;
		}
		C::t('common_nav')->update_by_navtype_type_identifier($navtype, 0, array("$type", "$identifier[$type]"), $navdata);
		C::t('common_setting')->update($funkey, 1);
	}
	// 关闭所有非系统插件
	logmessage("close all plugin without system plugin.");
	DB::query("UPDATE ".DB::table('common_plugin')." SET available='0' WHERE modules NOT LIKE '%s:6:\"system\";i:2;%'");
	// 恢复默认风格
	logmessage("recover default template and style.");
	define('IN_ADMINCP', true);
	require_once libfile('function/admincp');
	require_once libfile('function/importdata');
	$dir = DB::result_first("SELECT t.directory FROM ".DB::table('common_style')." s LEFT JOIN ".DB::table('common_template')." t ON t.templateid=s.templateid WHERE s.styleid='1'");
	import_styles(1, $dir, 1, 0, 0);
	C::t('common_setting')->update('styleid', 1);
	// 标题最小字数设置为 1 , 标题最大字数设置为 80
	// https://gitee.com/Discuz/DiscuzX/issues/I69QWB
	logmessage("update min/maxsubjectsize.");
	C::t('common_setting')->update('minsubjectsize', 1);
	C::t('common_setting')->update('maxsubjectsize', 255);
	// jaideejung007
	// อัปเดตให้เปิดใช้งานฟังก์ชัน "แสดงความคิดเห็นแบบแทรกลงในโพสต์ได้" และ "ให้ข้อความที่ตอบกลับแล้วปรากฎในส่วนของ "แสดงความคิดเห็น" ได้"
	logmessage("update allowpostcomment.");
	C::t('common_setting')->update('allowpostcomment', "a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}");
	// 关闭已经不再支持的前端 MD5 功能
	logmessage("close pwdsafety");
	C::t('common_setting')->update('pwdsafety', 0);
	// 修正微信插件生成的错误电子邮件地址格式
	// 考虑到这种邮箱本来就是无意义的，因此不走高风险的接口同步了，直接每个应用自行完成替换即可
	logmessage("mod null.null email");
	DB::query("UPDATE ".DB::table('common_member')." SET `email` = replace(`email`, 'null.null', 'm.invalid')");
	// 默认开启手机版、优化手机版默认配置
	logmessage("open mobile version and rewrite mobile defualt config");
	DB::query("DELETE FROM ".DB::table('common_setting')." WHERE skey = 'mobile'");
	DB::query("INSERT INTO ".DB::table('common_setting')." VALUES ('mobile','a:13:{s:11:\"allowmobile\";i:1;s:9:\"allowmnew\";i:0;s:13:\"mobileforward\";i:1;s:14:\"mobileregister\";i:1;s:13:\"mobileseccode\";i:0;s:16:\"mobilesimpletype\";i:0;s:15:\"mobilecachetime\";i:0;s:14:\"mobilecomefrom\";s:0:\"\";s:13:\"mobilepreview\";i:0;s:6:\"legacy\";i:1;s:3:\"wml\";i:0;s:6:\"portal\";a:1:{s:6:\"catnav\";i:0;}s:5:\"forum\";a:6:{s:5:\"index\";i:0;s:8:\"statshow\";i:0;s:13:\"displayorder3\";i:1;s:12:\"topicperpage\";i:20;s:11:\"postperpage\";i:10;s:9:\"forumview\";i:0;}}')");
	DB::query("UPDATE ".DB::table('common_nav')." SET url = 'forum.php?showmobile=yes', available = 1 WHERE identifier = 'mobile'");
	// 积分策略清理
	logmessage("common_credit_rule clear");
	DB::query("DELETE FROM ".DB::table('common_credit_rule')." WHERE action IN ('installapp', 'useapp')");
	DB::query("UPDATE ".DB::table('common_credit_rule')." SET extcredits1 = 0, extcredits2 = 0, extcredits3 = 0, extcredits4 = 0, extcredits5 = 0, extcredits6 = 0, extcredits7 = 0, extcredits8 = 0 WHERE action IN ('promotion_visit', 'visit', 'poke')");
	logmessage("wp.qq.com upgrade");
	// QQ Discuz! Code HTTPS 升级
	DB::query("UPDATE ".DB::table('forum_bbcode')." SET replacement = '<a href=\"https://wpa.qq.com/msgrd?v=3&uin={1}&amp;site=[Discuz!]&amp;from=discuz&amp;menu=yes\" target=\"_blank\"><img src=\"static/image/common/qq_big.gif\" border=\"0\"></a>', prompt = 'กรุณากรอกหมายเลข QQ ของคุณ:<a href=\"\" class=\"xi2\" onclick=\"this.href=\'https://wp.qq.com/set.html?from=discuz&uin=\'+$(\'e_cst1_qq_param_1\').value\" target=\"_blank\" style=\"float:right;\">ตั้งค่าสถานะออนไลน์ของ QQ&nbsp;&nbsp;</a>' WHERE tag = 'qq'");
	// 顶部菜单升级
	logmessage("common_nav upgrade");
	DB::query("UPDATE ".DB::table('common_nav')." SET url = 'home.php?mod=space&do=thread&view=me' WHERE identifier = 'thread'");
	// 用户分表更名
	logmessage("cron_member_optimize_daily upgrade");
	DB::query("UPDATE ".DB::table('common_cron')." SET name = 'ตารางผู้ใช้รายวัน' WHERE filename = 'cron_member_optimize_daily.php'");
	// 个人信息国别数据升级
	logmessage("common_district upgrade");
	DB::query("UPDATE ".DB::table('common_member_profile')." SET birthcountry = 'ไทย' WHERE birthprovince != ''");
	DB::query("UPDATE ".DB::table('common_member_profile')." SET residecountry = 'ไทย' WHERE resideprovince != ''");
	DB::query("INSERT INTO ".DB::table('common_member_profile_setting')." VALUES('birthcountry', 1, 0, 0, 'เกิดที่ประเทศ', '', 0, 0, 0, 0, 0, 0, 0, 'select', 0, '', '')");
	DB::query("INSERT INTO ".DB::table('common_member_profile_setting')." VALUES('residecountry', 1, 0, 0, 'ประเทศที่พำนัก', '', 0, 0, 0, 0, 0, 0, 0, 'select', 0, '', '')");
	// 允许用户浏览个人资料页
	logmessage("common_usergroup_field allowviewprofile=1");
	DB::query("UPDATE ".DB::table('common_usergroup_field')." SET allowviewprofile = '1'");
	// 允许用户上传头像
	// https://www.dismall.com/thread-14793-1-1.html
	logmessage("common_usergroup_field allowavatarupload=1");
	DB::query("UPDATE ".DB::table('common_usergroup_field')." SET allowavatarupload = '1'");
	// 老旧系统插件数据清理
	logmessage("remove old system plugins.");
	$plugins = array('cloudstat', 'soso_smilies', 'security', 'pcmgr_url_safeguard', 'manyou', 'cloudcaptcha', 'cloudunion', 'qqgroup', 'xf_storage', 'cloudsearch', 'qqconnect');
	foreach($plugins as $pluginid) {
		$plugin = C::t('common_plugin')->fetch_by_identifier($pluginid);
		if($plugin) {
			$modules = dunserialize($plugin['modules']);
			$modules['system'] = 0;
			$modules = serialize($modules);
			C::t('common_plugin')->update($plugin['pluginid'], array('modules' => $modules));
		}
	}
}

function cancle_thai_district_upgrade() { /*jaideejung007 ฟังก์ชันนี้ เมื่อผู้ใช้กดข้าม การอัปเกรดฐานข้อมูลรายชื่อจังหวัดฯ */
	setdbglobal();
	define('IN_ADMINCP', true);
	require_once libfile('function/admincp');
	require_once libfile('function/importdata');
	// รันคิวรีนี้ เมื่อผู้ใช้กดข้าม การอัปเกรดฐานข้อมูลรายชื่อจังหวัดฯ
	logmessage("common_district upgrade");
	DB::query("INSERT INTO ".DB::table('common_district')." (`name`, `level`, `upid`, `usetype`) VALUES ('ไทย', 0, 0, 3);");
	$district_upid = DB::insert_id();
	DB::query("UPDATE ".DB::table('common_district')." SET upid = $district_upid, usetype = 0 WHERE level = 1 AND upid = 0");
	DB::query("UPDATE ".DB::table('common_member_profile')." SET birthcountry = 'ไทย' WHERE birthprovince != ''");
	DB::query("UPDATE ".DB::table('common_member_profile')." SET residecountry = 'ไทย' WHERE resideprovince != ''");
	DB::query("INSERT INTO ".DB::table('common_member_profile_setting')." VALUES('birthcountry', 1, 0, 0, 'เกิดที่ประเทศ', '', 0, 0, 0, 0, 0, 0, 0, 'select', 0, '', '')");
	DB::query("INSERT INTO ".DB::table('common_member_profile_setting')." VALUES('residecountry', 1, 0, 0, 'ประเทศที่พำนัก', '', 0, 0, 0, 0, 0, 0, 0, 'select', 0, '', '')");
}

function save_config_file($filename, $config, $default, $deletevar) {
	$config = setdefault($config, $default, $deletevar);
	$content = <<<EOT
<?php


\$_config = array();

EOT;
	$content .= getvars(array('_config' => $config));
	$content .= "\r\n// ".str_pad('  THE END  ', 50, '-', STR_PAD_BOTH)." //\r\n\r\n?>";
	if (!empty($_GET['myisam'])) {
		$content = preg_replace("/\['db'\]\['common'\]\['engine'\] = \s*'.*?'\;/i", "['db']['common']['engine'] = 'myisam';", $content);
	}
	// logmessage("new config_global_default.php content:");
	// logmessage($content);
	if (!is_writable($filename) || !($len = file_put_contents($filename, $content))) {
		file_put_contents(DISCUZ_ROOT.'./data/config_global.php', $content);
		return 0;
	}
	return 1;
}

function setdefault($var, $default, $deletevar = array()) {
	foreach ($default as $k => $v) {
		if (!isset($var[$k])) {
			$var[$k] = $default[$k];
		} elseif (is_array($v)) {
			$var[$k] = setdefault($var[$k], $default[$k]);
		}
	}
	foreach ($deletevar as $k) {
		unset($var[$k]);
	}
	return $var;
}

function getvars($data, $type = 'VAR') {
	$evaluate = '';
	foreach($data as $key => $val) {
		if (!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $key)) {
			continue;
		}
		if (is_array($val)) {
			$evaluate .= buildarray($val, 0, "\${$key}")."\r\n";
		} else {
			$val = addcslashes($val, '\'\\');
			$evaluate .= $type == 'VAR' ? "\$$key = '$val';\n" : "define('".strtoupper($key)."', '$val');\n";
		}
	}
	return $evaluate;
}

function buildarray($array, $level = 0, $pre = '$_config') {
	static $ks;
	if ($level == 0) {
		$ks = array();
		$return = '';
	}

	foreach ($array as $key => $val) {
		if(!preg_match("/^[a-zA-Z0-9_\x7f-\xff]+$/", $key)) {
			continue;
		}

		if ($level == 0) {
			$newline = str_pad('  CONFIG '.strtoupper($key).'  ', 70, '-', STR_PAD_BOTH);
			$return .= "\r\n// $newline //\r\n";
			if ($key == 'admincp') {
				$newline = str_pad(' Founders: $_config[\'admincp\'][\'founder\'] = \'1,2,3\'; ', 70, '-', STR_PAD_BOTH);
				$return .= "// $newline //\r\n";
			}
		}

		$ks[$level] = $ks[$level - 1]."['$key']";
		if (is_array($val)) {
			$ks[$level] = $ks[$level - 1]."['$key']";
			$return .= buildarray($val, $level + 1, $pre);
		} else {
			$val =  is_string($val) || strlen($val) > 12 || !preg_match("/^\-?[1-9]\d*$/", $val) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
			$return .= $pre.$ks[$level - 1]."['$key']"." = $val;\r\n";
		}
	}
	return $return;
}

function encode_file($filename) {
	if (file_exists($filename)) {
		$i = pathinfo($filename);
		if (in_array($i['extension'], array('js', 'css', 'php', 'html', 'htm', 'xml'))) {
			$res = file_get_contents($filename);
			// 有的地方说 EUC-CN = GB2312，CP936 = GBK，这里都写上
			$encode = mb_detect_encoding($res, array("ASCII", "UTF-8", "GB2312", "GBK", "EUC-CN", "CP936", "GB18030", "BIG-5"));
			if (in_array($encode, array("GB2312", "GBK", "EUC-CN", "CP936", "GB18030", "BIG-5"))) {
				$res = mb_convert_encoding($res, "UTF-8", $encode);
				// 对非 Windows 系统尝试设置 777 权限
				if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
					logmessage("set chmod 777 $filename");
					chmod($filename, 0777);
				}
				logmessage("file convert $filename");
				file_put_contents($filename, $res);
			}
		}
	}
}

function encode_tree($directory) {
	$tdir = dir($directory);
	while ($file = $tdir->read()) {
		if ((is_dir("$directory/$file")) && ($file != ".") && ($file != "..")) {
			encode_tree("$directory/$file");
		} else {
			$file ="$directory/$file";
			if (is_file($file)) {
				encode_file($file);
			}
		}
	}
}


function get_special_table_by_num($tablename, $num) {
	$tables_array = get_special_tables_array($tablename);

	$num --;
	return isset($tables_array[$num]) ? $tables_array[$num] : FALSE;
}

function get_special_tables_array($tablename) {
	$tablename = DB::table($tablename);
	$tablename = str_replace('_', '\_', $tablename);
	$query = DB::query("SHOW TABLES LIKE '{$tablename}\_%'");
	$dbo = DB::object();
	$tables_array = array();
	while($row = $dbo->fetch_array($query, MYSQLI_NUM)) {
		if(preg_match("/^{$tablename}_(\\d+)$/i", $row[0])) {
			$prefix_len = strlen($dbo->tablepre);
			$row[0] = substr($row[0], $prefix_len);
			$tables_array[] = $row[0];
		}
	}
	return $tables_array;
}

function getcolumn($creatsql) {

	$creatsql = preg_replace("/ COMMENT '.*?'/i", '', $creatsql);
	preg_match("/\((.+)\)\s*(ENGINE|TYPE)\s*\=/is", $creatsql, $matchs);

	$cols = explode("\n", $matchs[1]);
	$newcols = array();
	foreach ($cols as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		$value = remakesql($value);
		if(substr($value, -1) == ',') $value = substr($value, 0, -1);

		$vs = explode(' ', $value);
		$cname = $vs[0];

		if($cname == 'KEY' || $cname == 'INDEX' || $cname == 'UNIQUE') {

			$name_length = strlen($cname);
			if($cname == 'UNIQUE') $name_length = $name_length + 4;

			$subvalue = trim(substr($value, $name_length));
			$subvs = explode(' ', $subvalue);
			$subcname = $subvs[0];
			$newcols[$cname][$subcname] = trim(substr($value, ($name_length+2+strlen($subcname))));

		}  elseif($cname == 'PRIMARY') {

			$newcols[$cname] = trim(substr($value, 11));

		}  else {

			$newcols[$cname] = trim(substr($value, strlen($cname)));
		}
	}
	return $newcols;
}

function remakesql($value) {
	$value = trim(preg_replace("/\s+/", ' ', $value));
	// 去掉 mediumtext 替换为 text 的功能, 避免应设置字段出现问题
	$value = str_replace(array('`',', ', ' ,', '( ' ,' )'), array('', ',', ',','(',')'), $value);
	return $value;
}

function cssoutput() {
	header('Content-Type: text/css; charset=utf-8');
	$data = base64_decode('H4sIAAAAAAACA61ZCXOrOBL+K6ynUk52EAEc7ATX3vd917G3QI3RBBAl5Ngeyv99JCRjYWzyrpn3DGr1pa9brRbv+24c40wAl88EMsahTdgeNfRbWm3ihHECHEnKMRdl0WasEijDJS0OcYOrBjXAabYuaAUoB7rJRRx4QbRGO0heqUAC9kLpAoTJN9tGzvr+3XkW1yiXQoUSRCkrGI8Fl2przKESx4wx5VgOmMhHiWnlVvitJbSpC3yIk4Klr8eEkUNbYr6hVeyvbQcRrusCUHNoBJTuT6WTr3/A6d+64S8lnzv7G2wYOP/4zcz9K0uYYO7s11C8gaApdv4IW5i5P+EUF+7sj3LS+Zv0bObOfk8T4FhQVhnKGQh39hNl0vmZWorzi5J9Q2dnK2PC3w5lwoqZ0W9L6YUo5OKAQ6mHO43wk+9fIB6tNXhfhUEYhS/rDnYsYa3iAjKxTnD6uuFsWxFkGLMsO+aBwQ0JVkvszEACIVgZe5G0e6ynWALFgQnh0DTteMqsQRwKiCvGS1wM3KZVDpyK47Z418a2cDSXNeEfk9ZGRQJJgB9xa1bo+6skyzQUBFKmQyYdqWCMh511OM7ZG/CznmiZLEZ6pCxwtRzJXzFx/6+cQ/afB/2eFrhp/vNgVJiFXnFlUtp2Y1pHygi4NYfB9vzbL//AKob+Cpttgbn7B6gK5koSTpn7M1Y1TFoZJLNil6n4M7blFLjM/93MLSVN4pKCnY8qLTi8F7S18j4r2C7GW8HWqGzQiWJyokk5K4oE8yMtN62cU/uuMHlbUkIKWOsCpAX0YgVOCmgNPZUKcN1AbF7kdN5a2W/DZkin4lNikebIxDzZSr+rk1aOCd02sW/IccZSOVSx0a/ojTZUevHQsq1QOdCzXi9ExgsLQ0O5uh+Mph4+Y8zQ9eq6fM0YLzUm/5JAwg80w3/adMsbxuOa0UoAP/5LHOp+0jVaDAqqRAKWylKI9cyQXS+a0EaBTh6M9AV10mAco5J9izRutKqAGyXjibbGhKhTx78S9rgPXEYLQNu6YJggsxoFbI/p7aXJaNVb0Z8ftOrg18eIgFISBZxmtVUvD1xTJu3KeFmRo1FFDk+i55iHuqJ6BWBikSXvSN/C97tNbbE9r7zo7lTm4XkBz+l6J1FCO47rOOGAX5EaH/EPleSwbKj9Ojw212PN1gGi+J1OzThlB4q1C511U+KPXipFMFXx3FEicn3km9AirvGJ6n1PKiAzFA2z5tFVQ1M0iyIcf1wCodi5LyVVq49Wy3r/0FpmS7w/zT359f54RWi1fL4ptApvCL28hDeFXpY3hILQ929KBYF20ONs18cnK2C/Vj86tOpniAyywVLQaIpCvmhr1tDuZOBQYEHfYP2RUdBqOvsJbmgT+9qZjfQxDtbGeaPx6OlybNkYnwTD3DIijiDu6S3vd7636vbCxVkgWG0qQnfaBPXeaVhBifMVAQhh2atUnaJSdyGvfTmpMJ6FN7WojvLr7re1rN7iR43gtAai5RzB40rkiGVIVcF7RmR9HLUbfJPge99V/3t+9HD0ElFdLUujvu9mn5eCqr3r66fotlHFFApIxfsNkMHJgtme7CO16ELlmIgN29VRezo8WE3FU2pNsnZOOF4QNQ7gBhCtENsK13bzOodWfHt2j5ocE7YbzfVbteaQAW8QB7JNgaCSdR7p4UPbhab31DRcXcDs/syOyLg/U9y6czg3DOuzb7HvqP+9UOFpUiMIF24YRa4XmvToj9uW1Til4hB7y0jPDE/jbujdPJyVBKo5LTE/tOfLwCgj+gbaQrgnDrRYOEzoWr6Ql5GuZZimQ10apU/VNQnq4tkNngyqClTLbI/el0FkOiIxTlVdnjal1zMyFaXJJ5gyqH4sOA2krCLv5skyXUUrYrvaE209H5gpEV6Gy+cLbdFTlCzDS216VZ+qbTJXgoXcgh0mIzzsdPliwHx6wowWZRt7giiKEtvYl0uZMUanO5jW2ORs93DRTVf4bbLNsZuR0aVStdiN+Z5w1ofUZ52L3tao0Y26ow6kM6temNuPTU6O67bRn2B+paeaWkR38iIqoGxOp7L69EWzA1I9oKTE3ZUaJSB2ANVVd7Vlx+oav6TJk36UcFyRUedhg9j1Pt4i0Ee2IZqQnOl2f6o7gNEF59pdd73LqQDU+SYhV2sZutZHy6a9HzF0PdEI5ZCe+o1tWX1Kvhntzjn3hk21f6G0FzrtDqu11g2z3V2PA9nLC7bZFNYl2Qvt/msa7uDTG76rrdulU3aYLOp0oGxWRFN2owM2Vwsvkmsyy9GD663uKd9nM2vNceTfPSqwne6nYohDDfjcBFq3v+XKe9GXQ+Mi7GssYSvJD63dOB30iSvnSKvOh+5LD2c7R++C0fbtmBqBuTiONDh21g8TXamcEJhIZF2N7HUZ0nESl3Nh0guZMN5vCHujfo+WNeMCy8Sz9om6+k9oMkk0PGx6/kItxxlWvPE17OVhSsAk+BSHTvb3Fd8AfywYfZCg7dk0500PV7cMGVyv+tZ3GqO5YFqd2etWQaIl3kC85cX9jGCB42782Lxtvt6XhXu3SOWrI1+r5gfzXIg6fnzc7XbebuExvnkMfd9XzHOn22c/mC/8uaMLhX5/o7D7Kdv/YK76l4X6M79bgFRbY5E7jeDsFX4wVyu4C5991zF/vOgufJmbeQUgpLiWfMrlnlxSAbygJZW2Ar8nG0/CuUN+MP/Dk7PKw1A+gkg/w4V8zh+1F8p3+TZTTeZGI9aO27vsOXvJ8Hl3HHHPbTLAInSBNp9ZL9gsqsV7xSDB4ENkGfR0wFv9GH+EsVkJUpt3vLHNtBojfQS3Vw/mEXOTc5nByG+tUezbbDmSpb3Vce/OVXuyFCiy/71iwaG05+sDWrR2wxNMMVifti6MqDpl21HjAQu3WXShHTF152W5FUDawZ3B4vkO2wSYX7UdAAA=');
	if(empty($_SERVER['HTTP_ACCEPT_ENCODING']) || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false) {
		echo gzdecode($data);
	} else {
		header('Content-Encoding: gzip');
		echo $data;
	}
	exit;
}

function logmessage($message, $close = false) {
	static $fh = false;
	if(!$fh) {
		$fh = fopen(UPGRADE_LOG_PATH, "a+");
	} 
	if($fh) {
		fwrite($fh, $message."\r\n");
		fflush($fh);
		if($close) {
			fclose($fh);
		}
	}
}

function setdbglobal() {
	// 对于因数据库超时而升级失败的特大站点，请赋予升级程序所使用的 MySQL 账户 SUPER privilege 权限，并解除以下三行代码注释后再试
	// DB::query('SET GLOBAL connect_timeout=28800');
	// DB::query('SET GLOBAL wait_timeout=28800');
	// DB::query('SET GLOBAL interactive_timeout=28800');
}

function runquery($sql) { /*jaideejung007*/
	//global $lang, $tablepre, $db;
	global $config;
	// $table = str_replace('pre_', $config['tablepre'], $table);

	if(!isset($sql) || empty($sql)) return;

	$sql = str_replace("\r", "\n", str_replace(' pre_', ' '.$config['tablepre'], $sql));
	$sql = str_replace("\r", "\n", str_replace(' `pre_', ' `'.$config['tablepre'], $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	$oldtablename = "";
	foreach($ret as $query) {
		$query = trim($query);
		if($query) {
			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
				if (DB::query($query, 'SILENT')) {
					logmessage("Create table ok, continue.");
				} else {
					logmessage("Create table failed.");
					show_msg('สร้างตารางข้อมูล '.$name.' ไม่สำเร็จ', 'คำแนะนำการอัปเกรด');
					return false;
				}
			} elseif(substr($query, 0, 6) == 'INSERT') {
				$name = preg_replace("/INSERT\s+INTO\s+[\`]?([a-z0-9_]+)[\`]? .*/is", "\\1", $query);
				if (DB::query($query, 'SILENT')) {
						if($oldtablename != $name) {
						logmessage("Init table data Thai common_district_*.sql ok, continue.");
						$oldtablename = $name;
					}
				} else {
					logmessage("Init table data Thai common_district_*.sql failed.");
					show_msg('นำเข้าข้อมูลในตารางฐานข้อมูล '.$name.' ไม่สำเร็จ', 'คำแนะนำการอัปเกรด');
					return false;
				}
			}else{
				if (!DB::query($query, 'SILENT')) {
					logmessage("import Thai common_district_*.sql failed.");
					show_msg('นำเข้าข้อมูลไม่สำเร็จ', 'คำแนะนำการอัปเกรด');
					return false;
				}
			}

		}
	}
	return true;
}

function install_testdata() { /*jaideejung007*/
	global $config;

	$sqlfile_chk = DISCUZ_ROOT.'./install/data/common_district_1.sql';
	if(!file_exists($sqlfile_chk)) {
		logmessage("common_district_1.sql not found, not continue");
		show_msg('<p><font color="red"><b>ไม่พบไฟล์ SQL ในตำแหน่งที่ตั้งนี้: '.$sqlfile_chk.'</p><p>หากมีการแบ่งไฟล์ SQL ออกเป็นหลาย ๆ พาร์ท กรุณาตั้งชื่อไฟล์ตามรูปแบบนี้ "common_district_X.sql" โดย X คือ ลำดับหมายเลขไฟล์ ตั้งแต่ 1 เป็นต้นไป</b></font></p>', 'คำแนะนำการอัปเกรด');
	}

	$sqlfile = DISCUZ_ROOT.'./install/data/common_district_{#id}.sql';
	for($i = 1; $i < 4; $i++) {
		$sqlfileid = str_replace('{#id}', $i, $sqlfile);
		if(file_exists($sqlfileid)) {
			$sql = file_get_contents($sqlfileid);
			$sql = str_replace("\r\n", "\n", $sql);
			runquery($sql);
		}
	}
}

function timezone_set($timeoffset = 7) {
	if(function_exists('date_default_timezone_set')) {
		@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	}
}