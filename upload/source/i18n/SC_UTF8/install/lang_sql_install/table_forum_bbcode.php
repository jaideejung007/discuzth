<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

$data = [[
    'id' => '1',
    'available' => '0',
    'tag' => 'fly',
    'icon' => 'bb_fly.gif',
    'replacement' => '<marquee width="90%" scrollamount="3">{1}</marquee>',
    'example' => '[fly]This is sample text[/fly]',
    'explanation' => '使内容横向滚动，这个效果类似 HTML 的 marquee 标签，注意：这个效果只在 Internet Explorer 浏览器下有效。',
    'params' => '1',
    'prompt' => '请输入滚动显示的文字:',
    'nest' => '1',
    'displayorder' => '19',
    'perm' => '1	2	3	12	13	14	15	16	17	18	19',
  ],[
    'id' => '2',
    'available' => '2',
    'tag' => 'qq',
    'icon' => 'bb_qq.gif',
    'replacement' => '<a href="https://wpa.qq.com/msgrd?v=3&uin={1}&amp;site=[Discuz!]&amp;from=discuz&amp;menu=yes" target="_blank"><img src="static/image/common/qq_big.gif" border="0"></a>',
    'example' => '[qq]688888[/qq]',
    'explanation' => '显示 QQ 在线状态，点这个图标可以和他（她）聊天',
    'params' => '1',
    'prompt' => '请输入 QQ 号码:<a href="" class="xi2" onclick="this.href=\'https://wp.qq.com/set.html?from=discuz&uin=\'+$(\'e_cst1_qq_param_1\').value" target="_blank" style="float:right;">设置QQ在线状态&nbsp;&nbsp;</a>',
    'nest' => '1',
    'displayorder' => '21',
    'perm' => '1	2	3	10	11	12	13	14	15	16	17	18	19',
  ],[
    'id' => '3',
    'available' => '0',
    'tag' => 'sup',
    'icon' => 'bb_sup.gif',
    'replacement' => '<sup>{1}</sup>',
    'example' => 'X[sup]2[/sup]',
    'explanation' => '上标',
    'params' => '1',
    'prompt' => '请输入上标文字：',
    'nest' => '1',
    'displayorder' => '22',
    'perm' => '1	2	3	12	13	14	15	16	17	18	19',
  ],[
    'id' => '4',
    'available' => '0',
    'tag' => 'sub',
    'icon' => 'bb_sub.gif',
    'replacement' => '<sub>{1}</sub>',
    'example' => 'X[sub]2[/sub]',
    'explanation' => '下标',
    'params' => '1',
    'prompt' => '请输入下标文字：',
    'nest' => '1',
    'displayorder' => '23',
    'perm' => '1	2	3	12	13	14	15	16	17	18	19',
  ],
];
