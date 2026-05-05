<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



class forum_portal_navlist {

	public static $name = 'เมนูนำทางพอร์ทัลหน้าแรก';
	public static $useage = '{cells forum/portal/navlist}';
	public static $cellList = array(
		'forum/portal/navlist/loop_start' => 'เริ่มต้นส่วนการวนซ้ำ (ต้องมี)',
		'forum/portal/navlist/loop_end' => 'สิ้นสุดส่วนการวนซ้ำ (ต้องมี)',
		'forum/portal/navlist/name' => 'ข้อความเมนูนำทาง',
		'forum/portal/navlist/url' => 'ลิงก์เมนูนำทาง',
		'forum/portal/navlist/current_class' => 'สไตล์เมนูนำทางปัจจุบัน',
	);
	public static $requireList = array(
		'forum/portal/navlist/loop_start',
		'forum/portal/navlist/loop_end',
	);

	public static $used = array();

	public static function getDefault($type = 0) {
		if(!$type) {
			return <<<EOF
<div class="portal_nav">
<div class="portal_nav_container">
	<ul class=" cl">
	{cell forum/portal/navlist/loop_start}
	<li{cell forum/portal/navlist/current_class}><a href="{cell forum/portal/navlist/url}" ajaxtarget="threadlist">{cell forum/portal/navlist/name}</a></li>
	{cell forum/portal/navlist/loop_end}
	</ul>
</div>
</div>
EOF;
		} else {
			return <<<EOF
<div class="dhnav_box"><div id="dhnav"><div id="dhnav_li"><ul class="flex-box">
{cell forum/portal/navlist/loop_start}
<li{cell forum/portal/navlist/current_class}><a href="{cell forum/portal/navlist/url}" ajaxtarget="threadlist">{cell forum/portal/navlist/name}</a></li>
{cell forum/portal/navlist/loop_end}
</ul></div></div></div>
EOF;
		}
	}

}