<?php exit('Access Denied');?>
<!--{if $value['msgfromid'] != $_G['uid']}-->
<div class="friend_msg cl">
	<div class="avat z"><!--{avatar($value['msgfromid'],'small',false, false, false, '', 'vm')}--></div>
	<div class="dialog_green z">
		<div class="dialog_c">$value['message']</div>
		<div class="date"><!--{date($value['dateline'], 'u')}--></div>
	</div>
</div>
<!--{else}-->
<div class="self_msg cl">
	<div class="avat y"><!--{avatar($value['msgfromid'],'small',false, false, false, '', 'vm')}--></div>
	<div class="dialog_white y">			
		<div class="dialog_c">$value['message']</div>
		<div class="date"><!--{date($value['dateline'], 'u')}--></div>
	</div>
</div>
<!--{/if}-->