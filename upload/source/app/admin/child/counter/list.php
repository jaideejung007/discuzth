<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('tools', 'nav_updatecounters');
showsubmenu('nav_updatecounters');
/*search={"nav_updatecounters":"action=counter"}*/
showtips('counter_tips');
/*search*/
showformheader('counter');
showtableheader();
showsubtitle(['', 'counter_amount']);
showhiddenfields(['pertask' => '']);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_forum']}:",
	'<input name="pertask1" type="text" class="txt" value="15" /><input type="submit" class="btn" name="forumsubmit" onclick="this.form.pertask.value=this.form.pertask1.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_digest']}:",
	'<input name="pertask2" type="text" class="txt" value="1000" /><input type="submit" class="btn" name="digestsubmit" onclick="this.form.pertask.value=this.form.pertask2.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_member']}:",
	'<input name="pertask3" type="text" class="txt" value="1000" /><input type="submit" class="btn" name="membersubmit" onclick="this.form.pertask.value=this.form.pertask3.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_thread']}:",
	'<input name="pertask4" type="text" class="txt" value="500" /><input type="submit" class="btn" name="threadsubmit" onclick="this.form.pertask.value=this.form.pertask4.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_special']}:",
	'<input name="pertask7" type="text" class="txt" value="1" disabled/><input type="submit" class="btn" name="specialarrange" onclick="this.form.pertask.value=this.form.pertask7.value" value="'.$lang['submit'].'" />'
]);

showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_groupnum']}:",
	'<input name="pertask8" type="text" class="txt" value="10" /><input type="submit" class="btn" name="groupnum" onclick="this.form.pertask.value=this.form.pertask8.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_groupmember_num']}:",
	'<input name="pertask9" type="text" class="txt" value="100" /><input type="submit" class="btn" name="groupmembernum" onclick="this.form.pertask.value=this.form.pertask9.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_groupmember_post']}:",
	'<input name="pertask10" type="text" class="txt" value="100" /><input type="submit" class="btn" name="groupmemberpost" onclick="this.form.pertask.value=this.form.pertask10.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_blog_replynum']}:",
	'<input name="pertask11" type="text" class="txt" value="100" /><input type="submit" class="btn" name="blogreplynum" onclick="this.form.pertask.value=this.form.pertask11.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_friendnum']}:",
	'<input name="pertask12" type="text" class="txt" value="100" /><input type="submit" class="btn" name="friendnum" onclick="this.form.pertask.value=this.form.pertask12.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_album_picnum']}:",
	'<input name="pertask13" type="text" class="txt" value="100" /><input type="submit" class="btn" name="albumpicnum" onclick="this.form.pertask.value=this.form.pertask13.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_tagitemnum']}:",
	'<input name="pertask14" type="text" class="txt" value="100" /><input type="submit" class="btn" name="tagitemnum" onclick="this.form.pertask.value=this.form.pertask14.value" value="'.$lang['submit'].'" />'
]);
showtablerow('', ['class="td31 bold"'], [
	"{$lang['counter_thread_cover']}:",
	'<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script><input name="pertask99" type="text" class="txt" value="100" /> '.$lang['counter_forumid'].': <input type="text" class="txt marginleft10" name="fid" value="" size="10">&nbsp;<input type="checkbox" class="checkbox" value="1" name="allthread">'.$lang['counter_have_cover'].'<br><input type="text" onclick="showcalendar(event, this)" value="" name="starttime" class="txt"> -- <input type="text" onclick="showcalendar(event, this)" value="" name="endtime" class="txt marginleft10">('.$lang['counter_thread_cover_settime'].')  &nbsp;&nbsp;<input type="submit" class="btn" name="setthreadcover" onclick="this.form.pertask.value=this.form.pertask99.value" value="'.$lang['submit'].'" />'
]);
showtablefooter();
showformfooter();
	