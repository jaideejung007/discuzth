<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class forum_portal_threadlist {

	public static $name = 'รายการกระทู้พอร์ทัลหน้าแรก';
	public static $useage = '{cells forum/portal/threadlist threadlist}';
	public static $cellList = array(
		'forum/threadlist/loop_start' => 'เริ่มต้นส่วนการวนซ้ำ (ต้องมี)',
		'forum/threadlist/loop_end' => 'สิ้นสุดส่วนการวนซ้ำ (ต้องมี)',
		'forum/threadlist/subject' => 'หัวข้อ (ต้องมี)',
		'forum/threadlist/url' => 'URL กระทู้',
		'forum/threadlist/folder_class' => 'สไตล์หัวข้อ',
		'forum/threadlist/icon' => 'ไอคอน',
		'forum/threadlist/message' => 'บทนำ',
		'forum/threadlist/image' => 'รายการรูปภาพ',
		'forum/threadlist/author' => 'ผู้เขียน',
		'forum/threadlist/author_avatar' => 'รูปโปรไฟล์ผู้เขียน',
		'forum/threadlist/dateline' => 'เวลาที่เผยแพร่',
		'forum/threadlist/replies' => 'จำนวนการตอบกลับ',
		'forum/threadlist/views' => 'จำนวนการเข้าชม',
		'forum/threadlist/recommend' => 'จำนวนการแนะนำ',
		'forum/threadlist/lastposter' => 'ผู้ตอบล่าสุด',
		'forum/threadlist/lastpost' => 'เวลาที่ตอบล่าสุด',
		'forum/threadlist/page' => 'เลขหน้า',
		'forum/threadlist/nextpage' => 'โหลดเพิ่มเติม',
	);
	public static $requireList = array(
		'forum/threadlist/loop_start',
		'forum/threadlist/loop_end',
	);

	public static $used = array(
		'forum/threadlist/message' => 'message',
		'forum/threadlist/image' => 'image',
		'forum/threadlist/page' => 'page',
		'forum/threadlist/nextpage' => 'nextpage',
	);

	public static function getDefault($type = 0) {
		if(!$type) {
			return <<<EOF
<div class="tl bm bmw" style="position: relative;">
	<div class="th">
		<table cellspacing="0" cellpadding="0">
			<tr>
				<th colspan="2">
					<div class="tf">
						{lang title}
					</div>
				</th>
				<td class="by">{lang author}</td>
				<td class="num">{lang replies}</td>
				<td class="by">{lang lastpost}</td>
			</tr>
		</table>
	</div>
	<div class="bm_c threadlist" id="threadlistAppend">
		<!--Ajax:InnerStart-->
		<table cellspacing="0" cellpadding="0">
			{cell forum/threadlist/loop_start}		  
			<tbody>
			<tr>
			<td class="icn">
			    {cell forum/threadlist/icon}
			</td>	
			<th class="{cell forum/threadlist/folder_class}">
			    {cell forum/threadlist/subject}
			</th>
			<td class="by">
			    <cite>
			        {cell forum/threadlist/author}
			    </cite>
			    <em>{cell forum/threadlist/dateline}</em>
			</td>
			<td class="num">{cell forum/threadlist/replies}<em>{cell forum/threadlist/views}</em></td>
			<td class="by">
			    <cite>{cell forum/threadlist/lastposter}</cite>
			    <em>{cell forum/threadlist/lastpost}</em>
			</td>
			</tr>
			</tbody>
			{cell forum/threadlist/loop_end}
		</table>
		<!--Ajax:Clear-->{cell forum/threadlist/nextpage}<!--Ajax:/Clear-->
		<!--Ajax:InnerEnd-->
	</div>
</div>

EOF;
		} else {
			return <<<EOF
<div class="threadlist_box mt10 cl">
	<div class="threadlist cl">
		<ul>
		{cell forum/threadlist/loop_start}
			<li class="list">
				<div class="threadlist_top cl">
					{cell forum/threadlist/author_avatar}
					<div class="muser">
						<h3>{cell forum/threadlist/author}</h3>
						<span class="mtime">{cell forum/threadlist/dateline}</span>
					</div>
				</div>
				{cell forum/threadlist/subject}
				<a href="{cell forum/threadlist/url}"><div class="threadlist_mes cl">{cell forum/threadlist/message}</div></a>
				{cell forum/threadlist/image}
				<div class="threadlist_foot cl">
					<ul>
						<li><i class="dm-eye-fill"></i>{cell forum/threadlist/views}</li>
						<li><i class="dm-chat-s-fill"></i>{cell forum/threadlist/replies}</li>
					</ul>
				</div>
			</li>
		{cell forum/threadlist/loop_end}
		</ul>
	</div>
</div>
{cell forum/threadlist/page}
EOF;
		}
	}

}



