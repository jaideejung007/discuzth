<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_jack.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'jack_name' => 'เลื่อนกระทู้ให้อยู่ที่ 1',
	'jack_desc' => 'Thread from the top can be a period of time, re-use may be extended from the top post by the time',
	'jack_expiration' => 'Duration',
	'jack_expiration_comment' => 'Set the thread can be long from the top, default is 1 hour',
	'jack_forum' => 'สามารถใช้งานได้ในเว็บบอร์ด',
	'jack_info' => '<p class="mtn xw0 mbn">Top the specified thread for <span class="xi1 xw1 xs2">{expiration}</span> hours.</p><p class="mtn xw0 mbn">You now have <span class="xi1 xw1 xs2">{magicnum}</span> jacks can be used.</p>',
	'jack_num' => 'Use this number:',
	'jack_num_not_enough' => 'Insufficient number or do not fill in the number of props to use.',
	'jack_info_nonexistence' => 'Please specify the thread for top',
	'jack_succeed' => 'The thread successfully jacked to the top',
	'jack_info_noperm' => 'ขออภัย! บอร์ดนี้ไม่อนุญาตให้ใช้ไอเท็มนี้',

	'jack_notification' => '{actor} ใช้ไอเท็ม{magicname} กับกระทู้ {subject} ของคุณ <a href="forum.php?mod=viewthread&tid={tid}">ไปดูกระทู้!</a>',
);

?>