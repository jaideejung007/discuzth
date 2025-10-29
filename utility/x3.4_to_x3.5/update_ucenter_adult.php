<?php

// 不升级到 InnoDB 开关
// 仅限极少量特殊场合应用
define("NO_INNODB_FEATURE", false);
define('UPGRADE_LOG_PATH', __DIR__.'/../data/logs/X3.5_upgrade_ucenter.php');
empty($_GET['css']) || cssoutput();

@set_time_limit(0);
@ignore_user_abort(TRUE);
ini_set('max_execution_time', 0);
ini_set('mysql.connect_timeout', 0);

define("IN_UC", TRUE);
define('UC_ROOT', realpath('..').'/');

require UC_ROOT.'./data/config.inc.php';
require UC_ROOT.'./lib/dbi.class.php';
require UC_ROOT.'./release/release.php';

header("Content-type: text/html; charset=utf-8");

$step = !empty($_GET['step']) && in_array($_GET['step'], array('welcome', 'tips', 'license', 'envcheck', 'confirm', 'secques', 'innodb', 'utf8mb4_other', 'scheme', 'utf8mb4_user', 'serialize', 'file', 'dataupdate', 'sendnote')) ? $_GET['step'] : 'welcome';
$theurl = htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
$lockfile = UC_ROOT.'./data/upgrade.lock';

timezone_set();

$tables = array('uc_admins', 'uc_applications', 'uc_badwords', 'uc_domains', 'uc_failedlogins', 'uc_feeds', 'uc_friends', 'uc_mailqueue', 'uc_memberfields', 'uc_members', 'uc_mergemembers', 'uc_newpm', 'uc_notelist', 'uc_pm_indexes', 'uc_pm_lists', 'uc_pm_members', 'uc_pm_messages_0', 'uc_pm_messages_1', 'uc_pm_messages_2', 'uc_pm_messages_3', 'uc_pm_messages_4', 'uc_pm_messages_5', 'uc_pm_messages_6', 'uc_pm_messages_7', 'uc_pm_messages_8', 'uc_pm_messages_9', 'uc_protectedmembers', 'uc_settings', 'uc_sqlcache', 'uc_tags', 'uc_vars');

$table = empty($_GET['table']) ? 'uc_admins' : (in_array($_GET['table'], $tables) ? $_GET['table'] : 'uc_admins');

// PHP 8 拦截
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
	show_msg('เวอร์ชัน PHP ที่โปรแกรมอัปเกรดสนับสนุนคือ 5.6 - 7.4 เท่านั้น กรุณาดาวน์เกรด PHP เป็น 7.4 ก่อนอัปเกรดโปรแกรม UCenter และแอปพลิเคชันอื่น ๆ หลังจากนั้น หากอัปเกรดโปรแกรมและแอปพลิเคชันทั้งหมดแล้ว คุณสามารถสลับไปใช้ PHP 8.0 หรือสูงกว่าได้');
}

// Q004 保证无写入权限时可以正常报错
// https://www.dismall.com/thread-14718-1-1.html
if (!@touch(UPGRADE_LOG_PATH) || @!is_writable(UPGRADE_LOG_PATH)) {
	show_msg('กรุณาเข้าสู่ระบบการจัดการเซิร์ฟเวอร์ เพื่อกำหนดสิทธิ์โฟลเดอร์ data/logs ที่อยู่ในโฟลเดอร์ UCenter จากนั้นจึงเรียกใช้ไฟล์นี้อีกครั้งเพื่ออัปเกรด');
}

logmessage("<?php exit;?>\t".date("Y-m-d H:i:s"));
logmessage("Query String: ".$_SERVER['QUERY_STRING']);

$scheme_count = 0;

if (!empty($_GET['lock'])) {
	@touch($lockfile);
	@unlink($theurl);
	logmessage("upgrade success.");
	show_msg('ขอแสดงความยินดี คุณอัปเกรดเป็น UCenter 1.7.0 เรียบร้อยแล้ว ขอบคุณที่เลือกใช้ผลิตภัณฑ์ของเรา');
}

if (file_exists($lockfile)) {
	logmessage("upgrade locked.");
	show_msg('กรุณาเข้าสู่ระบบเซิร์ฟเวอร์ จากนั้นลบไฟล์ data/upgrade.lock ออกด้วยตนเอง และเรียกใช้ไฟล์นี้อีกครั้งเพื่ออัปเกรด');
}

if ($step == 'welcome') {

	show_msg('<p class="lead">UCenter เป็นสะพานในการส่งข้อมูลระหว่างผลิตภัณฑ์ Discuz! X และ Tencent Cloud โดยผู้ดูแลเว็บไซต์สามารถทำการผนวกรวมกับผลิตภัณฑ์ Discuz! X กับ Tencent Cloud ได้โดยไม่มีปัญหา และสามารถเข้าสู่ระบบพร้อมกันบนหลายแหล่งและประมวลผลข้อมูลของชุมชนอื่น ๆ ได้ด้วย UCenter มีอินเทอร์เฟซที่สร้างมาเป็นอย่างดี คุณสามารถต่อกับแอปพลิเคชันอินเทอร์เน็ตจากแพลตฟอร์มอื่น ๆ ที่เป็นบุคคลที่สามได้ สามารถเพิ่มพลังงานให้กับระบบชุมชนของคุณเมื่อใดก็ได้</p><p class="lead">UCenter 1.7.0 มีการพัฒนาตามวัตถุประสงค์ของผลิตภัณฑ์ที่ผ่านมาอย่างดี โดยที่ Discuz! เป็นซอฟต์แวร์ชั้นนำที่มุ่งหวังจะปรับปรุงความปลอดภัยของระบบให้มีความแข็งเแกร่ง, สนับสนุน IPv6, ขยายขีดความสามารถระบบจัดการต่าง ๆ, อัดประสิทธิภาพให้เต็มเหนี่ยวแม้วันนั้นจะโหลดมาก, ปรับปรุงประสบการณ์การใช้งานของผู้ใช้ รวมไปถึงประสบการณ์การจัดการของระบบหลังบ้านอีกด้วย ซอฟต์แวร์นี้จะอัปเดตอย่างสม่ำเสมอใน Git อย่างเป็นทางการ และ GitHub ของดิสคัสไทย คุณสามารถติดตามการอัปเดตเวอร์ชันใหม่ได้ตามช่องทางที่ให้ไว้เมนูด้านบน</p><p class="lead">ก่อนทำการอัปเกรดจาก UCenter 1.6.0 เป็น UCenter 1.7.0 คุณควรสำรองข้อมูลทั้งหมด (ไม่ว่าจะเป็นฐานข้อมูลและไฟล์ที่เกี่ยวข้อง) และดำเนินการอัปเกรดด้วยความระมัดระวัง</p><p class="lead">หากคุณพร้อมที่จะอัปเกรดแล้ว ให้คลิกปุ่ม ถัดไป เพื่อดำเนินการต่อ</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=tips\';">ถัดไป ></button></p>', 'ขอต้อนรับสู่โปรแกรมอัปเกรด UCenter 1.7.0', 1);

} else if ($step == 'tips') {

	show_msg('<p class="lead">ย้ำอีกครั้ง ในขั้นตอนนี้ ก่อนที่จะดำเนินการอัปเกรดจาก UCenter 1.6.0 เป็น UCenter 1.7.0 เราขอแนะนำให้คุณสำรองข้อมูลเว็บไซต์ทั้งหมด (รวมถึงฐานข้อมูลและไฟล์ที่เกี่ยวข้อง) และดำเนินการอัปเกรดด้วยความระมัดระวัง ในขณะเดียวกัน เราขอแนะนำให้อัปเกรด MySQL เป็นเวอร์ชัน 5.7 ขึ้นไปก่อนที่จะอัปเกรดเว็บไซต์ เพื่อหลักเลี่ยงการอัปเกรดหยุดชะงัก เนื่องจากปลั๊กอินบางตัวอาจจะมีดัชนีตารางฐานข้อมูลปริมาณมาก</p><p class="lead">เนื่องด้วย UCenter 1.7.0 จะดำเนินการอัปเดตการเข้ารหัสฐานข้อมูลใหม่ หากชื่อผู้ใช้งานในฐานข้อมูล UCenter 1.7.0 รายใดไม่สนับสนุนการเข้ารหัส utf8mb4_unicode_ci จะถูกเปลี่ยนเป็นชื่อผู้ใช้งานที่มีอักษรแบบสุ่มใหม่จำนวน 15 ตัวให้โดยอัตโนมัติในระหว่างกระบวนการอัปเกรด หลังจากการดำเนินการเสร็จแล้ว ให้คุณตรวจสอบไฟล์บันทึกการเปลี่ยนชื่อผู้ใช้งาน (บนแท็บ บันทึกกิจกรรมระบบ ในเมนู จัดการสมาชิก) และดูบันทึกการแจ้งเตือน (บนแท็บ บันทึกการแจ้งเตือน ในเมนู ไฟล์บันทึกระบบ) จากระบบหลังบ้านของ UCenter ว่ามีการดำเนินการแจ้งไปยังผู้ใช้งานที่ได้รับผลกระทบตามแอปที่ได้ผูกไว้กับ UCenter หรือไม่ ก่อนที่จะดำเนินการอัปเกรด Discuz! X3.5 ต่อไป และหลังจากอัปเกรดเสร็จสิ้นทั้งหมดแล้ว กรุณาแจ้งผู้ใช้งานที่ได้รับผลกระทบดังกล่าวด้วยตนเองอีกครั้ง โดยอาจจะแจ้งให้ผู้ใช้งานทำการเปลี่ยนชื่อใหม่ด้วยไอเท็มเปลี่ยนชื่อ หรืออื่น ๆ ผ่านประกาศของเว็บไซต์ หรือทางอีเมล หรือทาง SMS ตามแต่ผู้ดูแลระบบสะดวกที่จะดำเนินการแจ้งให้ผู้ใช้งานทราบ หากคุณต้องการให้ผู้ใช้งานที่ได้รับผลกระทบดังกล่าวเปลี่ยนชื่อได้ด้วยตนเองผ่านการใช้ไอเท็มเปลี่ยนชื่อ อย่าลืมตั้งค่าไอเท็มเปลี่ยนชื่อใน AdminCP ของระบบดิสคัสด้วย เพื่อเป็นทางเลือกให้กับผู้ที่ได้รับผลกระทบสามารถเปลี่ยนชื่อผู้ใช้งานใหม่ได้สะดวกมากยิ่งขึ้น</p><p class="lead">เนื่องด้วย UCenter 1.7.0 จะดำเนินการอัปเดตการเข้ารหัสฐานข้อมูลใหม่ หากผู้ใช้งานรายใดตั้งค่าให้ตอบคำถามความปลอดภัยโดยไม่ได้ใช้ข้อความ ASCII (ภาษาอังกฤษและตัวเลข) ก่อนเข้าสู่ระบบ คำตอบของคำถามความปลอดภัยดังกล่าวจะถูกล้างออกโดยอัตโนมัติ กรุณาแจ้งให้ผู้ใช้ทราบจากผลกระทบดังกล่าวและขอให้ผู้ใช้เมื่อจะเข้าสู่ระบบไม่ต้องกรอกคำตอบของคำถามความปลอดภัยใด ๆ</p><p class="lead">เนื่องด้วย UCenter 1.7.0 จะดำเนินการอัปเดตการเข้ารหัสฐานข้อมูลใหม่ หากผู้ใช้งานรายใดตั้งรหัสผ่านไม่ได้เป็นไปตามมาตรฐานของการตั้งรหัสผ่าน (เช่น ใช้รหัสผ่านที่ไม่ใช่ภาษาอังกฤษ อาจจะเป็นภาษาไทยหรือภาษาอื่น ๆ ลงไปในรหัสผ่าน) ผู้ใช้งานดังกล่าวอาจจะเข้าสู่ระบบไม่ได้ เมื่อคุณพบสถานการณ์นี้ กรุณาแจ้งให้ผู้ใช้งานทำการรีเซ็ตรหัสผ่านใหม่ โดยใช้ฟังก์ชันลืมรหัสผ่านในหน้าเข้าสู่ระบบ แล้วดำเนินการตามขั้นตอนที่แจ้ง ผู้ใช้งานดังกล่าวก็จะสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้อีกครั้ง</p><p class="lead">กรุณาอย่าเรียกใช้งานโปรแกรมอัปเกรดนี้ซ้ำ การเรียกใช้งานซ้ำอาจทำให้เกิดปัญหาที่ไม่คาดคิดได้ หากพบปัญหาระหว่างการอัปเกรด ขอความกรุณาอย่าปิดหน้าเว็บเป็นอันขาด พยายามแก้ไขตามคำแนะนำที่ปรากฎในหน้าเว็บแล้วรีเฟรชหน้าเว็บใหม่ หากยังไม่สามารถแก้ไขปัญหาได้ กรุณาเรียกคืนข้อมูลสำรองและเรียกใช้งานโปรแกรมอัปเกรดนี้ใหม่อีกครั้ง</p><p class="lead">หากคุณอ่านคำแนะนำด้านบนเข้าใจโดยถ่องแท้แล้ว ให้คลิกปุ่ม ถัดไป เพื่อดำเนินการต่อ</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=license\';">ถัดไป ></button> <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=welcome\';">ย้อนกลับ <</button></p>', 'กรุณาอ่านคำแนะนำเกี่ยวกับการอัปเกรด', 1);

} else if ($step == 'license') {

	show_msg('<p class="lead">(ฉบับภาษาจีน) กรุณาคลิกที่ลิงก์ <a href="https://gitee.com/Discuz/DiscuzX/raw/master/readme/license.txt" target="_blank">https://gitee.com/Discuz/DiscuzX/raw/master/readme/license.txt</a> เพื่ออ่านข้อตกลงอนุญาตให้ใช้สิทธิของผู้ใช้ฉบับล่าสุด</p><p class="lead">(ฉบับภาษาไทย) กรุณาคลิกที่ลิงก์ <a href="https://raw.githubusercontent.com/jaideejung007/discuzth/master/readme/license.txt" target="_blank">https://raw.githubusercontent.com/jaideejung007/discuzth/master/readme/license.txt</a> เพื่ออ่านข้อตกลงอนุญาตให้ใช้สิทธิของผู้ใช้ฉบับล่าสุด</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=envcheck\';">ฉันยอมรับและดำเนินการต่อ ></button> <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=tips\';">ย้อนกลับ <</button></p>', 'กรุณาอ่านข้อตกลงอนุญาตให้ใช้สิทธิของผู้ใช้', 1);

} else if ($step == 'envcheck') {

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	$tips = '<table class="table table-striped" style="margin: 2em 0;"><thead><tr><th scope="col">ชื่อซอฟต์แวร์</th><th scope="col">ความต้องการขั้นต่ำ</th><th scope="col">สถานะปัจจุบัน</th><th scope="col">สถานะการทดสอบ</th></tr></thead><tbody>';
	$env_ok = true;
	$uc_db_ver = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."settings WHERE `k` = 'version'");
	$now_ver = array('Code Version' => constant('UC_SERVER_VERSION'), 'DB Version' => $uc_db_ver['v'], 'PHP' => constant('PHP_VERSION'), 'MySQL' => $db->version(), 'GD' => (function_exists('gd_info') ? preg_replace('/[^0-9.]+/', '', gd_info()['GD Version']) : false), 'XML' => function_exists('xml_parser_create'), 'JSON' => function_exists('json_encode'), 'mbstring' => (function_exists('mb_convert_encoding') || strtoupper(constant('UC_CHARSET')) == 'UTF-8'));// 对于UTF-8用户，不强制要求mbstring扩展
	$req_ver = array('Code Version' => '1.7.0', 'DB Version' => '1.6.0', 'PHP' => '5.6.0', 'MySQL' => '5.5.3', 'GD' => '1.0', 'XML' => true, 'JSON' => true, 'mbstring' => true);
	$lang_ver = array('Code Version' => 'โค้ดเวอร์ชัน UCenter', 'DB Version' => 'ข้อมูลเวอร์ชัน UCenter (เดิม)', 'PHP' => 'เวอร์ชัน PHP', 'MySQL' => 'เวอร์ชัน MySQL', 'GD' => 'ส่วนขยาย GD', 'XML' => 'ส่วนขยาย XML', 'JSON' => 'ส่วนขยาย JSON', 'mbstring' => 'ส่วนขยาย mbstring / UTF-8');
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

	$myisam_opt = NO_INNODB_FEATURE ? '<button type="button" class="btn btn-secondary" onclick="location.href=\'?step=secques&myisam=1\';">ไม่ต้องอัปเกรดเป็น InnoDB (ไม่แนะนำ) > </button>' : '';

	$myisam_opt .= in_array(strtolower(UC_DBCHARSET), array('utf8', 'utf8mb4')) ? '' : ' <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=scheme&myisam=1\';">ไม่ต้องอัปเกรดเป็น InnoDB และไม่ต้องล้างคำถามความปลอดภัย (ไม่แนะนำ) > </button>';

	$secques_opt = in_array(strtolower(UC_DBCHARSET), array('utf8', 'utf8mb4')) ? '' : '<button type="button" class="btn btn-secondary" onclick="location.href=\'?step=innodb\';">ไม่ต้องล้างคำถามความปลอดภัย (ไม่แนะนำ) > </button>';

	show_msg('<p class="lead" style="color: red;font-weight: bold;">โปรแกรมนี้กำลังจะเริ่มอัปเกรดจาก UCenter 1.6.0 เป็น UCenter 1.7.0 กรุณาตรวจสอบให้แน่ใจอีกครั้งว่าคุณได้สำรองข้อมูลเว็บไซต์ทั้งหมด (รวมถึงฐานข้อมูลและไฟล์ที่เกี่ยวข้อง) ไว้อย่างดีแล้ว! เมื่อคุณคลิก "เริ่มอัปเกรด" กระบวนการอัปเกรดจะเริ่มขึ้นทันทีและไม่สามารถยกเลิกได้!</p><p class="lead" style="color: red;font-weight: bold;">ก่อนที่จะดำเนินการอัปเกรด กรุณาตรวจสอบการเชื่อมต่อระหว่างแอปพลิเคชัน (ในที่นี้คือ Discuz!) กับ UCenter ว่ายังอยู่ในสถานะเชื่อมต่อหรือไม่ และกรุณาให้การยืนยันว่า แอปพลิเคชันที่เชื่อมต่อกับระบบอื่น ๆ ที่ไม่ใช่ Discuz! X ได้นำอินเทอร์เฟซการเปลี่ยนชื่อการแจ้งเตือนของ UCenter มาใช้อย่างถูกต้องแล้ว มิฉะนั้น การอัปเกรด UCenter อาจไม่สามารถดำเนินการได้เสร็จสมบูรณ์</p><p><button type="button" class="btn btn-primary" onclick="location.href=\'?step=secques\';">เริ่มอัปเกรด ></button> ' . $secques_opt .' ' . $myisam_opt . ' <button type="button" class="btn btn-secondary" onclick="location.href=\'?step=license\';">ย้อนกลับ <</button></p>', 'ยืนยันการอัปเกรด', 1);

} else if ($step == 'secques') {

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal($db);

	$secques_result = "เว็บไซต์ที่ใช้เวอร์ชันเข้ารหัสแบบ UTF-8 ไม่จำเป็นต้องล้างคำถามความปลอดภัย กำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......";

	if(!in_array(strtolower(UC_DBCHARSET), array('utf8', 'utf8mb4'))) {
		logmessage("clear secques");
		$db->query(str_replace(' uc_', ' '.UC_DBTABLEPRE, 'UPDATE uc_members SET `secques`=\'\';'));
		$secques_result = "คำถามความปลอดภัยถูกล้างแล้ว และกำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......";
	}

	$type = empty($_GET['myisam']) ? 'InnoDB' : 'MyISAM';
	$next = $type == 'InnoDB' ? 'innodb' : 'scheme&myisam=1';

	show_msg($secques_result, 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$next);

} else if ($step == 'innodb') {

	@touch(UC_ROOT.'./data/install.lock');
	@unlink(UC_ROOT.'./install/index.php');

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	if ($table) {
		$sql_check = get_convert_sql('check', $table);
		if (!empty($sql_check)) {
			$result = $db->fetch_first($sql_check);
			// 对文字排序进行过滤，避免不合法文字排序进入升级流程。考虑到部分站长自行进行了 utf8mb4 改造，因此额外添加 utf8mb4_general_ci 。
			// 从 MySQL 8.0.28 开始, utf8_general_ci 更名为 utf8mb3_general_ci
			if (!in_array($result['Collation'], array('utf8mb4_unicode_ci', 'utf8_general_ci', 'utf8mb3_general_ci', 'gbk_chinese_ci', 'big5_chinese_ci', 'utf8mb4_general_ci'))) {
				logmessage("table ".$table." 's ci ".$result['Collation']." not support, not continue.");
				show_msg("<font color=\"red\"><b>ไม่สนับสนุนข้อความที่มี Collation เป็น ".$result['Collation']." ของตาราง ".$table." นี้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ</b></font>", 'คำแนะนำการอัปเกรด');
			}
			if ($table == 'uc_badwords') {
				// 对于因数据库超时而升级失败的特大站点请看此函数
				setdbglobal($db);
				$sql = str_replace(' uc_', ' '.UC_DBTABLEPRE, 'ALTER TABLE uc_badwords DROP KEY `find`, ADD KEY `find` (`find`(100));');
				logmessage("RUNSQL: $sql");
				$db->query($sql);
				logmessage("RUNSQL Success");
			}
			if ($result['Engine'] != 'InnoDB') {
				$sql = get_convert_sql($step, $table);
				if (!empty($sql)) {
					// 对于因数据库超时而升级失败的特大站点请看此函数
					setdbglobal($db);
					logmessage("RUNSQL: $sql");
					$db->query($sql);
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
		show_msg("กำลังดำเนินการอัปเกรดตารางข้อมูลเป็น InnoDB และอัปเกรดตาราง $table เรียบร้อยแล้ว ความคืบหน้าปัจจุบันคือ $tmpid / $count กำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&table=".$next);
	} else {
		show_msg("อัปเกรดตารางข้อมูลเป็น InnoDB เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=scheme");
	}

} else if ($step == 'scheme') {

	@touch(UC_ROOT.'./data/install.lock');
	@unlink(UC_ROOT.'./install/index.php');

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$type = empty($_GET['myisam']) ? 'InnoDB' : 'MyISAM';

	$sql = get_scheme_update_sql($id, $type);
	// Q004 好像有些站点不存在 email 索引, 这里尝试判断一下, 遇到了就不删除直接跳过
	// https://www.dismall.com/thread-14718-1-1.html
	if ($sql == 'ALTER TABLE uc_members DROP KEY `email`;') {
		$found = false;
		$db_result_index = $db->fetch_all("SHOW INDEX FROM ".UC_DBTABLEPRE."members;");
		foreach ($db_result_index as $dbi) {
			if ($dbi['Key_name'] == 'email') {
				$found = true;
				break;
			}
		}
		if(!$found) {
			show_msg("กำลังดำเนินการอัปเกรดโครงสร้างฐานข้อมูล ความคืบหน้าปัจจุบันคือ $id / $scheme_count และกำลังดำเนินการขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&myisam=".(empty($_GET['myisam']) ? 0 : 1)."&id=".++$id);
		}
	}
	if (!empty($sql)) {
		// 对于因数据库超时而升级失败的特大站点请看此函数
		setdbglobal($db);
		logmessage("RUNSQL: $sql");
		$db->query($sql);
		logmessage("RUNSQL Success");
		show_msg("กำลังดำเนินการอัปเกรดโครงสร้างฐานข้อมูล ความคืบหน้าปัจจุบันคือ $id / $scheme_count และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&myisam=".(empty($_GET['myisam']) ? 0 : 1)."&id=".++$id);
	} else {
		show_msg("การอัปเกรดโครงสร้างฐานข้อมูลเรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=utf8mb4_user");
	}

} else if ($step == 'utf8mb4_user') {

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	$sql_check = get_convert_sql('check', 'uc_members');
	if (!empty($sql_check)) {
		$result = $db->fetch_first($sql_check);
		if ($result['Collation'] != 'utf8mb4_unicode_ci') {
			// 对文字排序进行过滤，避免不合法文字排序进入升级流程。考虑到部分站长自行进行了 utf8mb4 改造，因此额外添加 utf8mb4_general_ci 。
			// 从 MySQL 8.0.28 开始, utf8_general_ci 更名为 utf8mb3_general_ci
			if (!in_array($result['Collation'], array('utf8mb4_unicode_ci', 'utf8_general_ci', 'utf8mb3_general_ci', 'gbk_chinese_ci', 'big5_chinese_ci', 'utf8mb4_general_ci'))) {
				logmessage("table uc_members 's ci ".$result['Collation']." not support, not continue.");
				show_msg("<font color=\"red\"><b>ไม่สนับสนุนข้อความที่มี Collation เป็น ".$result['Collation']." ของตาราง uc_members กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ!</b></font>", 'คำแนะนำการอัปเกรด');
			}
			$sql = get_convert_sql('utf8mb4', 'uc_members');
			if (!empty($sql)) {
				// 对于因数据库超时而升级失败的特大站点请看此函数
				setdbglobal($db);
				logmessage("RUNSQL: $sql");
				$db->query($sql, 'SILENT');
			}
		} else {
			logmessage("RUNSQL Success");
			show_msg("อัปเกรดตารางผู้ใช้งานเป็น utf8mb4 เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=utf8mb4_other");
		}
	}

	if ($db->errno() == 1062 && !isset($_GET['fast'])) {// 1062属于编码的问题，进行转换编码
		// 使用宅魂提供的 SQL 语句批量处理用户重名问题
		$sql = "SELECT group_concat(uid) AS uids, group_concat(username), count(1) AS count FROM ".UC_DBTABLEPRE."members GROUP BY CONVERT(username USING utf8mb4) COLLATE utf8mb4_unicode_ci HAVING count > 1;";
		logmessage("RUNSQL: $sql");
		$result = $db->fetch_all($sql);
		foreach ($result as $key => $value) {
			$arr = explode(',', $value['uids']);
			// 对 UID 排序, 最小的 UID 不参与随机命名
			if (is_array($arr) && sort($arr)) {
				array_shift($arr);
			}
			foreach ($arr as $uid) {
				if(!chgusername($uid, true)) {
					logmessage("CHGUSERNAME UID $uid Failed.");
					show_msg("<font color=\"red\"><b>ไม่สามารถตั้งชื่อแบบสุ่มของ UID ".$uid." นี้ได้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ</b></font>", 'คำแนะนำการอัปเกรด');
				} else {
					logmessage("CHGUSERNAME UID $uid Success.");
				}
			}
		}
		show_msg("ชื่อผู้ใช้งานบางรายไม่สนับสนุนการเข้ารหัส utf8mb4 ชื่อเหล่านี้จะถูกตั้งชื่อแบบสุ่มให้โดยอัตโนมัติ กำลังอัปเกรดตารางผู้ใช้งานเป็น utf8mb4 และขั้นตอนต่อไปจะดำเนินการในเร็ว ๆ นี้ กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&fast=true");
	} elseif ($db->errno() == 1062 && isset($_GET['fast'])) {
		preg_match('/Duplicate entry \'(.*)\' for key/', $db->error(), $matches);
		$username = $matches[1];
		if (chgusername(addslashes($username))) {
			$fast = isset($_GET['fast']) ? '&fast=true' : '';
			logmessage("CHGUSERNAME USERNAME $username Success.");
			show_msg("ชื่อผู้ใช้งาน ".$username." ไม่สนับสนุนการเข้ารหัส utf8mb4 และจะถูกตั้งชื่อแบบสุ่มให้โดยอัตโนมัติ กำลังอัปเกรดตารางผู้ใช้งานเป็น utf8mb4 และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step.$fast);
		} else {
			logmessage("CHGUSERNAME USERNAME $username Failed.");
			show_msg("<font color=\"red\"><b>ชื่อผู้ใช้งาน ".$username." ไม่สนับสนุนการเข้ารหัส utf8mb4 และไม่สามารถตั้งชื่อแบบสุ่มให้โดยอัตโนมัติได้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ</b></font>", 'คำแนะนำการอัปเกรด');
		}
	} elseif ($db->errno() != 0) {
		// 对于因数据库超时而升级失败的特大站点请看此函数
		setdbglobal($db);
		logmessage("RUNSQL: $sql");
		$db->query($sql);// 如果程序接不住，那就不接了，直接报错
		logmessage("RUNSQL Success");
	} else {
		show_msg("ตารางผู้ใช้งานได้รับการอัปเกรดเป็น utf8mb4 เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=utf8mb4_other");
	}

} else if ($step == 'utf8mb4_other') {

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	$sql_check = get_convert_sql('check', $table);
	if (!empty($sql_check)) {
		$result = $db->fetch_first($sql_check);
		if ($result['Collation'] != 'utf8mb4_unicode_ci') {
			// 对文字排序进行过滤，避免不合法文字排序进入升级流程。考虑到部分站长自行进行了 utf8mb4 改造，因此额外添加 utf8mb4_general_ci 。
			// 从 MySQL 8.0.28 开始, utf8_general_ci 更名为 utf8mb3_general_ci
			if (!in_array($result['Collation'], array('utf8mb4_unicode_ci', 'utf8_general_ci', 'utf8mb3_general_ci', 'gbk_chinese_ci', 'big5_chinese_ci', 'utf8mb4_general_ci'))) {
				logmessage("table ".$table." 's ci ".$result['Collation']." not support, not continue.");
				show_msg("<font color=\"red\"><b>ไม่สนับสนุนข้อความที่มี Collation เป็น ".$result['Collation']." ของตาราง ".$table." นี้ กรุณาแก้ไขด้วยตนเองก่อนดำเนินการต่อ</b></font>", 'คำแนะนำการอัปเกรด');
			}
			$sql = get_convert_sql('utf8mb4', $table);
			if (!empty($sql) && $table != 'uc_members') {
				// 对于因数据库超时而升级失败的特大站点请看此函数
				setdbglobal($db);
				logmessage("RUNSQL: $sql");
				$db->query($sql);
				logmessage("RUNSQL Success");
			}
		}
	}

	$tmp = array_flip($tables);
	$tmpid = $tmp[$table];
	$count = count($tables);
	if ($tmpid + 1 < $count) {
		$next = $tables[++$tmpid];
		show_msg("กำลังดำเนินการอัปเกรดตารางข้อมูลเป็น utf8mb4 อัปเกรดตาราง $table เรียบร้อยแล้ว ความคืบหน้าปัจจุบันคือ $tmpid / $count และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=".$step."&table=".$next);
	} else {
		show_msg("อัปเกรดตารางข้อมูลเป็น utf8mb4 เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=serialize");
	}

} else if ($step == 'serialize') {

	if (!isset($_GET['start']) && !isset($_GET['tid'])) {

		if (constant('UC_DBCHARSET') == 'utf8' || constant('UC_DBCHARSET') == 'utf8mb4') {
			show_msg("ไม่จำเป็นต้องแปลงข้อมูลแบบซีเรียลไลซ์ กำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=dataupdate");
		}
	
		$configfile = UC_ROOT.'./data/config.inc.php';
		// 对非 Windows 系统尝试设置 777 权限
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			logmessage("set chmod 777 $configfile");
			chmod($configfile, 0777);
		}
		if (is_writable($configfile)) {
			$config = file_get_contents($configfile);
			$config = preg_replace("/define\('UC_DBCHARSET',\s*'.*?'\);/i", "define('UC_DBCHARSET', 'utf8mb4');", $config);
			$config = preg_replace("/define\('UC_CHARSET',\s*'.*?'\);/i", "define('UC_CHARSET', 'utf-8');", $config);
			if(file_put_contents($configfile, $config, LOCK_EX) === false) {
				logmessage("config.inc.php modify fail, let user manually modify.");
				show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้โดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไขไฟล์ data/config.inc.php ด้วยตนเอง เปลี่ยนค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง เปลี่ยนค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=serialize&start=0&tid=0">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
			}
			logmessage("config.inc.php modify ok, continue.");
			show_msg("ตั้งค่าคอนฟิกการแปลงข้อมูลแบบซีเรียลไลซ์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=serialize&start=0&tid=0");
		} else {
			logmessage("config.inc.php modify fail, let user manually modify.");
			show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้โดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไขไฟล์ data/config.inc.php ด้วยตนเอง เปลี่ยนค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง เปลี่ยนค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=serialize&start=0&tid=0">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
		}

	}

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal($db);

	$limit = 100000;
	$nextid = 0;

	$start = empty($_GET['start']) ? 0 : intval($_GET['start']);
	$tid = empty($_GET['tid']) ? 0 : intval($_GET['tid']);

	$arr = get_serialize_list();

	$field = $arr[$tid];
	$stable = str_replace('uc_', constant('UC_DBTABLEPRE'), $field[0]);
	$sfield = $field[1];
	$sid = $field[2];
	$special = $field[3];

	if ($special) {
		// 空值不参与序列化转换, 加快数据处理效率
		// https://www.dismall.com/thread-15293-1-1.html
		$sql = "SELECT `$sfield`, `$sid` FROM `$stable` WHERE `$sfield`<>'' AND `$sid` > $start ORDER BY `$sid` ASC LIMIT $limit";
	} else {
		$sql = "SELECT `$sfield`, `$sid` FROM `$stable`";
	}

	$query = $db->query($sql);

	$dum = '';

	while ($values = $db->fetch_array($query)) {
		if ($special) {
			$nextid = $values[$sid];
		} else {
			$nextid = 0;
		}
		$data = $values[$sfield];
		$dataold = $values[$sfield];
		$id = $values[$sid];
		$data = preg_replace_callback('/s:([0-9]+?):"([\s\S]*?)";/', '_serialize', $data);
		$data = addslashes($data);
		if (strcmp($dataold, $datanew) !== 0) {
			$sql = "UPDATE `$stable` SET `$sfield` = '$data' WHERE `$sid` = '$id';";
			logmessage("RUNSQL: $sql");
			$db->query($sql);
			logmessage("RUNSQL Success");
		}
	}

	if ($nextid) {
		show_msg("กำลังดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์ กำลังอัปเกรดตาราง $stable และกำลังดำเนินการในขั้นตอนต่อไป (กำลังเริ่มต้นการแปลงข้อมูล $tid จาก $nextid) กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=$step&tid=$tid&start=$nextid");
	} else {
		if (++$tid < count($arr)) {
			show_msg("กำลังดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์ กำลังอัปเกรดตาราง $stable และกำลังดำเนินการในขั้นตอนต่อไป (กำลังเริ่มต้นการแปลงข้อมูล $tid จาก $nextid) กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=$step&tid=$tid&start=0");
		} else {
			show_msg("ดำเนินการแปลงข้อมูลแบบซีเรียลไลซ์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=file");
		}
	}

} else if ($step == 'file') {

	logmessage("start file convert.");

	encode_tree(__DIR__.'/../data/logs/');

	show_msg("แปลงการเข้ารหัสไฟล์เรียบร้อยแล้ว และกำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=dataupdate");

} else if ($step == 'dataupdate') {

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal($db);

	logmessage("update version in db.");
	$db->query("REPLACE INTO ".constant('UC_DBTABLEPRE')."settings (k, v) VALUES('version', '1.7.0')");//note 记录数据库版本

	logmessage("clear cache dir.");
	dir_clear(UC_ROOT.'./data/view');
	dir_clear(UC_ROOT.'./data/cache');

	if (is_dir(UC_ROOT.'./plugin/setting')) {
		dir_clear(UC_ROOT.'./plugin/setting');
		@unlink(UC_ROOT.'./plugin/setting/index.htm');
		@rmdir(UC_ROOT.'./plugin/setting');
	}

	logmessage("clear cache dir.");
	$configfile = UC_ROOT.'./data/config.inc.php';
	// 对非 Windows 系统尝试设置 777 权限
	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
		logmessage("set chmod 777 $configfile");
		chmod($configfile, 0777);
	}
	if (is_writable($configfile)) {
		$config = file_get_contents($configfile);
		$config = preg_replace("/define\('UC_DBCHARSET',\s*'.*?'\);/i", "define('UC_DBCHARSET', 'utf8mb4');", $config);
		$config = preg_replace("/define\('UC_CHARSET',\s*'.*?'\);/i", "define('UC_CHARSET', 'utf-8');", $config);
		// 插入 ONLYREMOTEADDR 设置, 并完善 IPGETTER 对 PHP 5.6 的兼容
		$config = str_replace("define('UC_CHARSET', 'utf-8');", "define('UC_CHARSET', 'utf-8');\r\ndefine('UC_ONLYREMOTEADDR', 1);\r\ndefine('UC_IPGETTER', 'header');\r\n// define('UC_IPGETTER_HEADER', serialize(array('header' => 'HTTP_X_FORWARDED_FOR')));", $config);
		if(file_put_contents($configfile, $config, LOCK_EX) === false) {
			logmessage("config.inc.php modify fail, let user manually modify.");
			show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้โดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไข data/config.inc.php ด้วยตนเอง โดยเปลี่ยนค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง และเปลี่ยนค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=sendnote">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
		}
		logmessage("config.inc.php modify ok, continue.");
	} else {
		logmessage("config.inc.php modify fail, let user manually modify.");
		show_msg('<font color="red"><b>โปรแกรมไม่สามารถแก้ไขไฟล์คอนฟิกให้โดยอัตโนมัติได้ เนื่องจากไฟล์คอนฟิกไม่สามารถเขียนได้ คุณต้องแก้ไข data/config.inc.php ด้วยตนเอง โดยเปลี่ยนค่า UC_DBCHARSET เป็น utf8mb4 ด้วยตนเอง และเปลี่ยนค่า UC_CHARSET เป็น utf8 ด้วยตนเอง จากนั้น <a href="'.$theurl.'?step=sendnote">คลิกที่นี่</a> เพื่อดำเนินการต่อ</b></font>', 'คำแนะนำการอัปเกรด');
	}

	show_msg("อัปเดตแคชเสร็จสมบูรณ์แล้ว กำลังดำเนินการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=sendnote");

} else if ($step == 'sendnote') {

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, 'utf8mb4');

	// 对于因数据库超时而升级失败的特大站点请看此函数
	setdbglobal($db);

	$retry = empty($_GET['retry']) ? 0 : (int)$_GET['retry'];

	chgusername_sendallnote($retry);

	show_msg("ส่งการแจ้งเตือนถึงสมาชิกที่ได้รับผลกระทบเรียบร้อยแล้ว และกำลังเตรียมการในขั้นตอนต่อไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?lock=1");
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
<title>โปรแกรมอัปเกรด UCenter 1.7.0</title>
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
<a class="navbar-brand">โปรแกรมอัปเกรด UCenter 1.7.0</a>
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
<a class="nav-link" href="https://discuzth.com/" target="_blank">เว็บไซต์ดิสคัส! ทีเอช อย่างเป็นทางการ</a>
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

function chgusername($masterkey, $isuid = false) {
	global $db;
	if ($isuid) {
		$masterkey = intval($masterkey);
		$user = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE uid='$masterkey'");
	} else {
		$user = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE username='$masterkey'");
	}
	if ($user['uid']) {
		$uid = $user['uid'];
		$username = $user['username'];
		$newusername = get_random_username();
		$extinfo = 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername);
		$db->query("UPDATE ".UC_DBTABLEPRE."members SET username='$newusername' WHERE uid='$uid'");
		$db->query("INSERT INTO ".UC_DBTABLEPRE."memberlogs SET uid='$uid', action='renameuser', extra='$extinfo'");
		return chgusername_note('renameuser', $extinfo);
	} else {
		return false;
	}
}

function chgusername_note($operation, $getdata = '') {
	global $db;
	$extra = '';
	$apps = $db->fetch_all("SELECT * FROM ".UC_DBTABLEPRE."applications");

	foreach((array)$apps as $appid => $app) {
		$appadd[] = 'app'.$app['appid']."='0'";
	}

	if ($appadd) {
		$extra = implode(',', $appadd);
		$extra = $extra ? ', '.$extra : '';
	}

	$getdata = addslashes($getdata);
	$db->query("INSERT INTO ".UC_DBTABLEPRE."notelist SET getdata='$getdata', operation='$operation', pri='0', postdata=''$extra");

	if ($db->insert_id()) {
		return $db->query("REPLACE INTO ".UC_DBTABLEPRE."vars (name, value) VALUES ('noteexists', '1');");
	} else {
		return false;
	}
}

function get_random_username() {
	global $db;
	$tmp = random(15);
	foreach (range(1, 3) as $try) {
		$user = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE username='$tmp'");
		if(isset($user['uid']) && $try < 3) {
			$tmp = random(15);
			continue;
		} else {
			break;
		}
	}
	return $tmp;
}

function random($length, $numeric = 0) {
	if ($numeric) {
		$hash = sprintf('%0'.$length.'d', compromise_random_int(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = '0123456789abcdef';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[compromise_random_int(0, $max)];
		}
	}
	return $hash;
}

function compromise_random_int($min, $max) {
	if (function_exists('random_int')) {
		try {
			return random_int($min, $max);
		} catch (Exception $e) {
			return mt_rand($min, $max);
		}
	} else {
		return mt_rand($min, $max);
	}
}

function dir_clear($dir) {
	$directory = dir($dir);
	while ($entry = $directory->read()) {
		$filename = $dir.'/'.$entry;
		if (is_file($filename)) {
			@unlink($filename);
		}
	}
	@touch($dir.'/index.htm');
	$directory->close();
}

function get_convert_sql($type, $table) {
	$table = str_replace('uc_', UC_DBTABLEPRE, $table);
	if ($type == 'innodb') {
		$query = "ALTER TABLE $table ENGINE=InnoDB;";
	} else if ($type == 'utf8mb4') {
		$query = "ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
	} else if ($type == 'check') {
		$query = "SHOW TABLE STATUS WHERE `Name` = '$table';";
	}
	return $query;
}

function get_scheme_update_sql($id, $type = "InnoDB") {
	global $scheme_count;
	// 每条数据库处理指令一行
	// 修正微信插件生成的错误电子邮件地址格式
	// 考虑到这种邮箱本来就是无意义的，因此不走高风险的接口同步了，直接每个应用自行完成替换即可
	$query = array(
		'TRUNCATE TABLE uc_vars;',
		'UPDATE uc_members SET `email` = replace(`email`, \'null.null\', \'m.invalid\');',
		'ALTER TABLE uc_members DROP KEY `email`;',
		'ALTER TABLE uc_members MODIFY COLUMN email varchar(255) NOT NULL DEFAULT \'\', MODIFY COLUMN regip VARCHAR(45) NOT NULL DEFAULT \'\', MODIFY COLUMN `password` varchar(255) NOT NULL DEFAULT \'\', MODIFY COLUMN salt varchar(20) NOT NULL DEFAULT \'\', ADD COLUMN `secmobile` varchar(12) NOT NULL DEFAULT \'\' AFTER `password`, ADD COLUMN `secmobicc` varchar(3) NOT NULL DEFAULT \'\' AFTER `password`, ADD KEY secmobile (`secmobile`, `secmobicc`);',
		'ALTER TABLE uc_members ADD KEY `email` (`email`(40));',
		'CREATE TABLE IF NOT EXISTS uc_memberlogs (lid int(10) unsigned NOT NULL AUTO_INCREMENT, uid mediumint(8) unsigned NOT NULL, action varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'\', extra varchar(255) NOT NULL COLLATE utf8mb4_unicode_ci DEFAULT \'\', PRIMARY KEY(lid)) ENGINE='.$type.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
		'ALTER TABLE uc_domains MODIFY COLUMN ip varchar(45) NOT NULL default \'\'',
		'ALTER TABLE uc_failedlogins MODIFY COLUMN ip varchar(45) NOT NULL default \'\', MODIFY COLUMN count tinyint(3) unsigned NOT NULL default \'0\'',
		'ALTER TABLE uc_protectedmembers MODIFY COLUMN appid tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_lists MODIFY COLUMN pmtype tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_0 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_1 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_2 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_3 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_4 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_5 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_6 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_7 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_8 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';',
		'ALTER TABLE uc_pm_messages_9 MODIFY COLUMN delstatus tinyint(3) unsigned NOT NULL default \'0\';'
	);
	$scheme_count = count($query);
	if ($id + 1 > $scheme_count) {
		return '';
	} else {
		return str_replace(' uc_', ' '.UC_DBTABLEPRE, $query[$id]);
	}
}

function _serialize($str) {
        $l = strlen($str[2]);
        return 's:'.$l.':"'.$str[2].'";';
}

function get_serialize_list() {
	return array(
		array('uc_applications', 'extra', 'appid', TRUE),
		array('uc_pm_lists', 'lastmessage', 'plid', TRUE),
		array('uc_settings', 'v', 'k', TRUE),
	);
}

function chgusername_sendallnote($retry) {
	global $db, $theurl;
	$retrytimes = $failedstd = 0;
	$retrytimes = $retry;
	$sql = "SELECT * FROM ".UC_DBTABLEPRE."notelist WHERE closed='0' AND operation='renameuser' ORDER BY pri DESC, noteid ASC";
	logmessage("RUNSQL: $sql");
	$notes = $db->fetch_all($sql);
	logmessage("RUNSQL Success");
	if (!empty($notes)) {
		$apps = $db->fetch_all("SELECT * FROM ".UC_DBTABLEPRE."applications");
		// 失败标准: 当前所有的未发送通知 * 当前所有的应用个数(发给每个应用) * 3(重试系数), 保底重试 30 次
		$failedstd = max((int)count($notes) * (int)count($apps) * 3, 30);
		foreach ((array)$notes as $key => $note) {
			foreach((array)$apps as $appid => $app) {
				$appnotes = $note['app'.$app['appid']];
				if ($app['recvnote'] && $appnotes != 1 && $appnotes > -5) {
					logmessage("SendNote: {$app['appid']} ".implode(" ", $note));
					if (chgusername_sendonenote($app['appid'], 0, $note)) {
						logmessage("SendNote Success");
						// 发成功了, 重试次数清 0
						$retrytimes = 0;
						continue;
					} else {
						if ($retrytimes > $failedstd) {
							logmessage("SendNote Failed");
							show_msg("<font color=\"red\"><b>อัปเกรดเรียบร้อยแล้ว แต่ไม่สามารถส่งการแจ้งเตือนบางรายการถึงสมาชิกบางคนได้ กรุณาเข้าสู่ระบบศูนย์การจัดการผู้ใช้ UCenter และตรวจสอบว่าได้ส่งการแจ้งเตือนทั้งหมดแล้วหรือไม่ โดยดูได้ในแถบรายการการแจ้งเตือนของเมนูรายการข้อมูล หากการส่งล้มเหลว ให้ตรวจสอบว่า การเชื่อมต่อระหว่างเว็บไซต์และ UCenter เป็นปกติหรือไม่ และหลังจากส่งการแจ้งเตือนทั้งหมดเรียบร้อยแล้ว คุณสามารถดำเนินการอัปเกรดแอปพลิเคชันที่เหลือต่อได้ หลังจากที่โปรแกรมอื่น ๆ ได้ดำเนินการเสร็จหมดแล้ว ขอความกรุณาอย่าเรียกใช้งานขั้นตอนนี้ซ้ำ เพราะอาจทำให้เกิดปัญหาที่ไม่ทราบสาเหตุได้</b></font>", 'คำแนะนำการอัปเกรด');
						} else {
							logmessage("SendNote Failed, But I Can Retry");
							// 当刷新重试的时候, 失败标准次数是不准确的, 因此不显示
							$retrytimes++;
							if ($retrytimes == 1) {
								$failedstd = "ไม่ทราบข้อผิดพลาดที่เกิดขึ้น";
							}
							show_msg("การส่งการแจ้งเตือนนี้ได้ทำการลองส่งอีก $retrytimes ครั้งแล้ว โดยได้ตั้งค่าการลองส่งซ้ำคือ $failedstd ครั้ง ดังนั้นระบบกำลังจะดำเนินการขั้นตอนถัดไป กรุณารอสักครู่......", 'คำแนะนำการอัปเกรด', 0, "$theurl?step=sendnote&retry=$retrytimes", '8000');
						}
					}
				}
			}
		}
	}
}

function chgusername_sendonenote($appid, $noteid = 0, $note = '') {
	global $db;
	require_once UC_ROOT.'./lib/xml.class.php';
	$return = FALSE;
	$app = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."applications WHERE appid='$appid'");
	if ($noteid) {
		$note = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."notelist WHERE noteid='$noteid'");
	}

	// 不考虑数据库方式发送，一律走实现更简单的接口方式
	$url = chgusername_get_url_code($note['operation'], $note['getdata'], $appid);
	$note['postdata'] = str_replace(array("\n", "\r"), '', $note['postdata']);
	$response = trim(dfopen2($url, 0, $note['postdata'], '', 1, $app['ip'], 60, TRUE));

	logmessage("SendNote Response: $response");

	$returnsucceed = $response != '' && ($response == 1 || is_array(xml_unserialize($response)));
	$time = time();

	if ($returnsucceed) {
		$db->query("UPDATE ".UC_DBTABLEPRE."notelist SET app$appid='1', totalnum=totalnum+1, succeednum=succeednum+1, dateline='$time' WHERE noteid='$note[noteid]'", 'SILENT');
		$return = TRUE;
	} else {
		$db->query("UPDATE ".UC_DBTABLEPRE."notelist SET app$appid = app$appid-'1', totalnum=totalnum+1, dateline='$time' WHERE noteid='$note[noteid]'", 'SILENT');
		$return = FALSE;
	}
	return $return;
}

function chgusername_get_url_code($operation, $getdata, $appid) {
	global $db;
	$app = $db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."applications WHERE appid='$appid'");
	$authkey = $app['authkey'];
	$url = $app['url'];
	$apifilename = isset($app['apifilename']) && $app['apifilename'] ? $app['apifilename'] : 'uc.php';
	$action = 'action=renameuser';
	$code = urlencode(authcode("$action&".($getdata ? "$getdata&" : '')."time=".time(), 'ENCODE', $authkey));
	return $url."/api/$apifilename?code=$code";
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function dfopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCODE', $allowcurl = TRUE) {
	$__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
	if ($__times__ > 2) {
		return '';
	}
	$url .= (strpos($url, '?') === FALSE ? '?' : '&')."__times__=$__times__";
	return dfopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block, $encodetype, $allowcurl);
}

function dfopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCODE', $allowcurl = TRUE) {
	$return = '';
	$matches = parse_url($url);
	$scheme = strtolower($matches['scheme']);
	$host = $matches['host'];
	$path = !empty($matches['path']) ? $matches['path'].(!empty($matches['query']) ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : ($scheme == 'https' ? 443 : 80);

	if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
		$ch = curl_init();
		$ip && curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ".$host));
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		// 在请求主机名并非一个合法 IP 地址, 使用 CURLOPT_RESOLVE 设置固定的 IP 地址与域名关系
		if(!filter_var($host, FILTER_VALIDATE_IP)) {
			curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
			curl_setopt($ch, CURLOPT_RESOLVE, array("$host:$port:$ip"));
			curl_setopt($ch, CURLOPT_URL, $scheme.'://'.$host.':'.$port.$path);
		} else {
			curl_setopt($ch, CURLOPT_URL, $scheme.'://'.($ip ? $ip : $host).':'.$port.$path);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if($encodetype == 'URLENCODE') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			} else {
				parse_str($post, $postarray);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postarray);
			}
		}
		if($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno || $status['http_code'] != 200) {
			return;
		} else {
			return !$limit ? $data : substr($data, 0, $limit);
		}
	}

	if($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		if($allowcurl) {
			$encodetype = 'URLENCODE';
		}
		$boundary = $encodetype == 'URLENCODE' ? '' : '; boundary='.trim(substr(trim($post), 2, strpos(trim($post), "\n") - 2));
		$header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data$boundary\r\n";
		$header .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= 'Content-Length: '.strlen($post)."\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cache-Control: no-cache\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header.$post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$header .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header;
	}

	$fpflag = 0;
	$context = array();
	if($scheme == 'https') {
		$context['ssl'] = array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'peer_name' => $host,
			'SNI_enabled' => true,
			'SNI_server_name' => $host
		);
	}
	if(ini_get('allow_url_fopen')) {
		$context['http'] = array(
			'method' => $post ? 'POST' : 'GET',
			'header' => $header,
			'timeout' => $timeout
		);
		if($post) {
			$context['http']['content'] = $post;
		}
		$context = stream_context_create($context);
		$fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
		$fpflag = 1;
	} elseif(function_exists('stream_socket_client')) {
		$context = stream_context_create($context);
		$fp = @stream_socket_client(($scheme == 'https' ? 'ssl://' : '').($ip ? $ip : $host).':'.$port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
	} else {
		$fp = @fsocketopen(($scheme == 'https' ? 'ssl://' : '').($scheme == 'https' ? $host : ($ip ? $ip : $host)), $port, $errno, $errstr, $timeout);
	}

	if(!$fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		if(!$fpflag) {
			@fwrite($fp, $out);
		}
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out']) {
			while (!feof($fp) && !$fpflag) {
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
					break;
				}
			}

			$stop = false;
			while(!feof($fp) && !$stop) {
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if($limit) {
					$limit -= strlen($data);
					$stop = $limit <= 0;
				}
			}
		}
		@fclose($fp);
		return $return;
	}
}

function fsocketopen($hostname, $port = 80, &$errno = null, &$errstr = null, $timeout = 15) {
	$fp = '';
	if(function_exists('fsockopen')) {
		$fp = @fsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('pfsockopen')) {
		$fp = @pfsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('stream_socket_client')) {
		$fp = @stream_socket_client($hostname.':'.$port, $errno, $errstr, $timeout);
	}
	return $fp;
}

function encode_file($filename) {
	if (file_exists($filename)) {
		$i = pathinfo($filename);
		if (in_array($i['extension'], array('js', 'css', 'php', 'html', 'htm'))) {
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

function cssoutput() {
	header('Content-Type: text/css; charset=utf-8');
	$data = base64_decode('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');
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

function setdbglobal($db) {
	// 对于因数据库超时而升级失败的特大站点，请赋予升级程序所使用的 MySQL 账户 SUPER privilege 权限，并解除以下三行代码注释后再试
	// $db->query('SET GLOBAL connect_timeout=28800');
	// $db->query('SET GLOBAL wait_timeout=28800');
	// $db->query('SET GLOBAL interactive_timeout=28800');
}

function timezone_set($timeoffset = 7) {
	if(function_exists('date_default_timezone_set')) {
		@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	}
}