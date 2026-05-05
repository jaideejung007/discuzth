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
    'explanation' => 'ทำให้เนื้อหาเลื่อนในแนวนอน ซึ่งมีลักษณะคล้ายกับแท็ก marquee ใน HTML หมายเหตุ: เอฟเฟกต์นี้รองรับเฉพาะเบราว์เซอร์ Internet Explorer เท่านั้น',
    'params' => '1',
    'prompt' => 'โปรดป้อนข้อความที่ต้องการให้เลื่อน:',
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
    'explanation' => 'แสดงสถานะออนไลน์ของ QQ สามารถคลิกที่ไอคอนเพื่อเริ่มการสนทนาได้',
    'params' => '1',
    'prompt' => 'โปรดป้อนหมายเลข QQ: <a href="" class="xi2" onclick="this.href=\'https://wp.qq.com/set.html?from=discuz&uin=\'+$(\'e_cst1_qq_param_1\').value" target="_blank" style="float:right;">ตั้งค่าสถานะออนไลน์ QQ&nbsp;&nbsp;</a>',
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
    'explanation' => 'ตัวยก',
    'params' => '1',
    'prompt' => 'โปรดป้อนข้อความตัวยก:',
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
    'explanation' => 'ตัวห้อย',
    'params' => '1',
    'prompt' => 'โปรดป้อนข้อความตัวห้อย:',
    'nest' => '1',
    'displayorder' => '23',
    'perm' => '1	2	3	12	13	14	15	16	17	18	19',
  ],
];
