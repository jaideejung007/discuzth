<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class forum_portal_threadlist {

	public static $name = '主题列表';
	public static $useage = '{cells threadlist threadlist}';
	public static $cellList = array(
		'forum/threadlist/loop_start' => '循环体开始 (必须包含)',
		'forum/threadlist/loop_end' => '循环体结束 (必须包含)',
		'forum/threadlist/subject' => '标题 (必须包含)',
		'forum/threadlist/url' => '主题 URL',
		'forum/threadlist/folder_class' => '标题样式',
		'forum/threadlist/icon' => '图标',
		'forum/threadlist/forum_name' => '版块或分类',
		'forum/threadlist/message' => '简介',
		'forum/threadlist/image' => '图片列表',
		'forum/threadlist/author' => '作者',
		'forum/threadlist/author_avatar' => '作者头像',
		'forum/threadlist/dateline' => '发布时间',
		'forum/threadlist/replies' => '回复数',
		'forum/threadlist/views' => '查看数',
		'forum/threadlist/lastposter' => '最后回帖人',
		'forum/threadlist/lastpost' => '最后回复时间',
		'forum/threadlist/page' => '翻页',
		'forum/threadlist/nextpage' => '继续加载',
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
<div class="timeline_entry_list">
<div class="timeline_entry_list_container">
<div class="tb-c threadlist">
	<!--Ajax:InnerStart-->
	<div class="forumportal_listc cl">
		<ul>
		    {cell forum/threadlist/loop_start}		    
				<li class="kmlist">
					
					{cell forum/threadlist/subject}
					<div class="kmtxt">{cell forum/threadlist/message}</div>
					{cell forum/threadlist/image}
					<div class="kmfoot">
						{cell forum/threadlist/author_avatar}{cell forum/threadlist/replies}{cell forum/threadlist/views}
						{cell forum/threadlist/author}{cell forum/threadlist/dateline}{cell forum/threadlist/forum_name}
					</div>
				</li>
		    {cell forum/threadlist/loop_end}
		</ul>
	</div>
	<div id="threadlistAppend" class="forumportal_pages cl">
		<!--Ajax:Clear-->{cell forum/threadlist/nextpage}<!--Ajax:/Clear-->
	</div>
	<!--Ajax:InnerEnd-->
</div>
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