<?php exit('Access Denied');?>
<!--{if $list}-->
<ul>
<!--{loop $list $value}-->
	<li id="comment_{$value['id']}_li" class="doing_comment_list bbda cl{$value['class']}" style="$value['style']">
		<!--{if $value['layer'] == 0}-->
		<div class="cl">
		<div class="m avt"><a href="home.php?mod=space&uid={$value['uid']}" c="1"><!--{avatar($value['uid'], 'small')}--></a></div>
		<div class="ptm">
		<!--{else}-->
		<div class="ptm" style="margin-left: 42px;">
		<!--{/if}-->
			<div>
				<p>
				<a href="home.php?mod=space&uid={$value['uid']}" class="lit">{$value['username']}</a>
				<!--{if $value['reply_to_user']}-->
				<span class="">{lang reply} <a href="home.php?mod=space&uid={$value['reply_uid']}">{$value['reply_to_user']}</a> </span>
				<!--{/if}-->
				: {$value['message']}
				</p>
				<p>
				<span class="xg1">{lang comefrom} {$value['iplocation']}</span>
				<span class="pipe">|</span>
				<span class="xg1"><!--{date($value['dateline'], 'Y-n-j H:i')}--></span>
				<span class="y">
				<!--{if $_G['uid'] && helper_access::check_module('doing')}-->
				<a href="javascript:;" onclick="docomment_form({$value['doid']}, {$value['id']}, '{$_GET['key']}');">{lang reply}</a>
				<!--{/if}-->
				<!--{if $value['uid'] == $_G['uid'] || $dv['uid'] == $_G['uid'] || checkperm('managedoing')}-->
				<span class="pipe">|</span><a href="home.php?mod=spacecp&ac=doing&op=delete&doid={$value['doid']}&docid={$value['id']}&handlekey=doinghk_{$value['doid']}_{$value['id']}" id="{$_GET['key']}_doing_delete_{$value['doid']}_{$value['id']}" onclick="showWindow(this.id, this.href, 'get', 0);">{lang delete}</a>
				<!--{/if}-->
				</span>
				</p>
				<div id="{$_GET['key']}_form_{$value['doid']}_{$value['id']}"></div>
			</div>
		</div>
		<!--{if $value['layer'] == 0}-->
		</div>
		<!--{/if}-->
	</li>
<!--{/loop}-->
</ul>
<!--{else}-->
<p>没有评论数据</p>
<!--{/if}-->
<div class="tri"></div>