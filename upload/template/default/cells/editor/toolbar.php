<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



class editor_toolbar {

	public static $name = 'แถบเครื่องมือตัวแก้ไข';
	public static $useage = '{cells editor/toolbar}';

	public static $pconly = true;

	public static $cellList = array(
		'editor/font' => 'แบบอักษร',
		'editor/fontsize' => 'ขนาดตัวอักษร',
		'editor/hr' => 'เส้นคั่นแนวนอน',
		'editor/bold' => 'ตัวหนา',
		'editor/italic' => 'ตัวเอียง',
		'editor/underline' => 'ขีดเส้นใต้',
		'editor/forecolor' => 'สีตัวอักษร',
		'editor/backcolor' => 'สีพื้นหลัง',
		'editor/url' => 'ลิงก์',
		'editor/unlink' => 'ยกเลิกลิงก์',
		'editor/table' => 'ตาราง',
		'editor/rformat' => 'ล้างรูปแบบข้อความ',
		'editor/autotypeset' => 'จัดรูปแบบอัตโนมัติ',
		'editor/jleft' => 'จัดชิดซ้าย',
		'editor/jcenter' => 'จัดกึ่งกลาง',
		'editor/jright' => 'จัดชิดขวา',
		'editor/fleft' => 'ลอยด้านซ้าย',
		'editor/fright' => 'ลอยด้านขวา',
		'editor/orderlist' => 'รายการแบบลำดับตัวเลข',
		'editor/unorderlist' => 'รายการแบบสัญลักษณ์',
		'editor/smilies' => 'อีโมจิ',
		'editor/image' => 'รูปภาพ',
		'editor/attach' => 'ไฟล์แนบ',
		'editor/media' => 'มัลติมีเดีย',
		'editor/at' => '@เพื่อน',
		'editor/quote' => 'อ้างอิง',
		'editor/code' => 'โค้ด',
		'editor/free' => 'เนื้อหาฟรี',
		'editor/hide' => 'เนื้อหาที่ซ่อนไว้',
		'editor/pasteword' => 'วางจาก Word',
		'editor/downimage' => 'ดาวน์โหลดรูปภาพจากภายนอก',
		'editor/page' => 'ตัวแบ่งหน้า',
		'editor/pindex' => 'สารบัญ',
		'editor/magic' => 'ไอเท็มเวทย์',
		'editor/password' => 'รหัสผ่านโพสต์',
		'editor/postbg' => 'พื้นหลังโพสต์',
		'editor/beginning' => 'แอนิเมชันเริ่มต้น',
		'editor/custom' => 'กำหนดเอง',
		'editor/hook_1' => 'จุดเชื่อมต่อปลั๊กอิน 1',
		'editor/hook_2' => 'จุดเชื่อมต่อปลั๊กอิน 2',
		'editor/hook_3' => 'จุดเชื่อมต่อปลั๊กอิน 3',
	);

	public static $used = array(
	);

	public static $requireList = array(
	);

	public static function getDefault($type = 0) {
		return <<<EOF
<div class="b2r nbr nbl" id="e_adv_s2">
	{cell editor/font}
	{cell editor/fontsize}
	<span id="e_adv_1">
		{cell editor/hr}
		<br />
	</span>
	{cell editor/bold}
	{cell editor/italic}
	{cell editor/underline}
	{cell editor/forecolor}
	{cell editor/backcolor}
	{cell editor/url}
	<span id="e_adv_8">
		{cell editor/unlink}
	</span>
</div>
<div class="b2r nbl" id="e_adv_2">
	<p id="e_adv_3">
		{cell editor/table}
	</p>
	<p>
		{cell editor/rformat}
	</p>
</div>
<div class="b2r">
	<p>
		{cell editor/autotypeset}
		{cell editor/jleft}
		{cell editor/jcenter}
		{cell editor/jright}
	</p>
	<p id="e_adv_4">
		{cell editor/fleft}
		{cell editor/fright}
		{cell editor/orderlist}
		{cell editor/unorderlist}
	</p>
</div>
<div class="b1r" id="e_adv_s1">
	{cell editor/smilies}
	{cell editor/image}
	{cell editor/attach}
	{cell editor/media}
	{cell editor/at}
	{cell editor/hook_1}
</div>
<div class="b2r esb" id="e_adv_s3">
	{cell editor/hook_2}
	{cell editor/quote}
	{cell editor/code}
	{cell editor/free}
	{cell editor/hide}
	{cell editor/pasteword}
	{cell editor/downimage}
	{cell editor/page}
	{cell editor/pindex}
	{cell editor/magic}
	{cell editor/password}
	{cell editor/postbg}
	{cell editor/beginning}
	{cell editor/custom}
	{cell editor/hook_3}
</div>
EOF;

	}

}