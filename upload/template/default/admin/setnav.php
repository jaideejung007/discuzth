<?php exit('Access Denied');?>
<div class="tm_c">
	<h3 class="flb">
		<em id="return_$_GET['handlekey']">$navtitle</em>
		<span>
				<a href="javascript:;" class="flbc" onclick="hideWindow('$_GET['handlekey']')" title="{lang close}">{lang close}</a>
			</span>
	</h3>
	<form id="setnav" method="post" autocomplete="off" action="{ADMINSCRIPT}?action=misc&operation=setnav&type=$type&do=$do" onsubmit="ajaxpost('setnav', 'message_setnav', 'message_setnav');return false;">
		<input type="hidden" name="formhash" value="{FORMHASH}" />
		<input type="hidden" name="type" value="$type" />
		<input type="hidden" name="funcsubmit" value="1" />
		<input type="hidden" name="handlekey" value="$_GET['handlekey']" />
		<div class="c" id="message_setnav">
			<!--{if $do == 'open'}-->
			<ul>
				<!--{if !in_array($type, array('wall', 'friend', 'follower', 'medal', 'magic', 'favorite'))}-->
				<li><label><input type="checkbox" name="location[header]" class="pc" value="1" />{lang main_nav}</label></li>
				<!--{/if}-->
				<!--{if  !in_array($type, array('forum', 'follower'))}-->
				<li><label><input type="checkbox" name="location[quick]" class="pc" value="1" />{lang quick_nav}</label></li>
				<!--{/if}-->
			</ul>
			<!--{else}-->
			$closeprompt
			<!--{/if}-->
		</div>
		<p class="o pns">
			<input type="submit" name="funcsubmit_btn" class="btn" value="{lang confirms}">
		</p>
	</form>
</div>