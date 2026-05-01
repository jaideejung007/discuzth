<?php exit('Access Denied');?>
<!--{if $list}-->
<ul>
<!--{loop $list $value}-->
	<!--{if $value[uid]}-->
	<li class="ptn pbn{$value['class']}" style="$value['style']">
		<a href="home.php?mod=space&uid=$value[uid]" class="lit">$value[username]</a>: $value[message]
		<span class="xg1"><!--{date($value['dateline'], 'n-j H:i')}--></span>
		<!--{if $_G[uid] && helper_access::check_module('doing')}-->
		<a  href="home.php?mod=spacecp&ac=doing&op=docomment&doid=$value['doid']&docid=$value['id']&handlekey=doinghk_{$value['doid']}_$value['id']" class="dialog">{lang reply}</a>
		<!--{/if}-->
		<!--{if $value[uid]==$_G[uid] || $dv['uid']==$_G[uid] || checkperm('managedoing')}-->
			 <a href="home.php?mod=spacecp&ac=doing&op=delete&doid=$value['doid']&docid=$value['id']&handlekey=doinghk_{$value['doid']}_$value['id']" id="{$_GET['key']}_doing_delete_{$value['doid']}_{$value['id']}" class="dialog">{lang delete}</a>
		<!--{/if}-->
		<div id="{$_GET['key']}_form_{$value['doid']}_{$value['id']}"></div>
	</li>
	<!--{/if}-->
<!--{/loop}-->
</ul>
<!--{/if}-->
<div class="tri"></div>