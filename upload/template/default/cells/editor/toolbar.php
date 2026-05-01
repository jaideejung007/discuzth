<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



class editor_toolbar {

	public static $name = '编辑器工具栏';
	public static $useage = '{cells editor/toolbar}';

	public static $pconly = true;

	public static $cellList = array(
		'editor/font' => '字体',
		'editor/fontsize' => '字体大小',
		'editor/hr' => '水平线',
		'editor/bold' => '加粗',
		'editor/italic' => '斜体',
		'editor/underline' => '下划线',
		'editor/forecolor' => '字体颜色',
		'editor/backcolor' => '背景颜色',
		'editor/url' => '链接',
		'editor/unlink' => '移除链接',
		'editor/table' => '表格',
		'editor/rformat' => '清除文本格式',
		'editor/autotypeset' => '自动排版',
		'editor/jleft' => '左对齐',
		'editor/jcenter' => '居中对齐',
		'editor/jright' => '右对齐',
		'editor/fleft' => '左浮动',
		'editor/fright' => '右浮动',
		'editor/orderlist' => '有序列表',
		'editor/unorderlist' => '无序列表',
		'editor/smilies' => '表情',
		'editor/image' => '图片',
		'editor/attach' => '附件',
		'editor/media' => '媒体',
		'editor/at' => '@朋友',
		'editor/quote' => '引用',
		'editor/code' => '代码',
		'editor/free' => '免费内容',
		'editor/hide' => '隐藏内容',
		'editor/pasteword' => '从 Word 粘贴内容',
		'editor/downimage' => '下载远程图片',
		'editor/page' => '分页',
		'editor/pindex' => '目录',
		'editor/magic' => '道具',
		'editor/password' => '帖子密码',
		'editor/postbg' => '帖子背景',
		'editor/beginning' => '起始动画',
		'editor/custom' => '自定义',
		'editor/hook_1' => '插件嵌入点1',
		'editor/hook_2' => '插件嵌入点2',
		'editor/hook_3' => '插件嵌入点3',
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