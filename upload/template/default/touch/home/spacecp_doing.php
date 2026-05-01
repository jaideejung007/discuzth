<?php exit('Access Denied');?>
<!--{template common/header}-->

<!--{if $_GET['op'] == 'delete'}-->
	<div class="tip">
		<form method="post" autocomplete="off" id="doingform_{$doid}_{$id}" name="doingform" action="home.php?mod=spacecp&ac=doing&op=delete&doid=$doid&id=$id">
			<!--{if $_G[inajax]}--><input type="hidden" name="handlekey" value="$_GET['handlekey']" /><!--{/if}-->
			<input type="hidden" name="referer" value="{echo dreferer()}" />
			<input type="hidden" name="formhash" value="{FORMHASH}" />
			<dt>
				<p>{lang determine_delete_doing}</p>
			</dt>
			<dd><button type="submit" name="deletesubmit" value="true" class="button z">{lang determine}</button><a href="javascript:;" onclick="popup.close();" class="button y">{lang cancel}</a></dd>
		</form>
	</div>
<!--{elseif $_GET['op'] == 'spacenote'}-->
	<!--{if $space['spacenote']}-->$space['spacenote']<!--{/if}-->
<!--{elseif $_GET['op'] == 'docomment' || $_GET['op'] == 'getcomment'}-->
	<!--{if helper_access::check_module('doing')}-->
		<div class="tip p5">
			<div id="{$_GET['key']}_form_{$doid}_{$docid}" class="moodfm_post">
				<form id="{$_GET['key']}_docommform_{$doid}_{$docid}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=doing&op=comment&doid=$doid&docid=$docid">
					<input type="hidden" name="commentsubmit" value="true" />
					<input type="hidden" name="formhash" value="{FORMHASH}" />
					<div class="moodfm_text task_viewnr">
						<textarea name="message" id="{$_GET['key']}_form_{$doid}_{$docid}_t" rows="3" class="pts" placeholder="{lang spacecp_doing_message1} 200 {lang spacecp_doing_message2}"></textarea>
					</div>
					<dd><button type="submit" name="do_button" id="{$_GET['key']}_replybtn_{$doid}_{$docid}" value="true" class="button z">{lang reply}</button><a href="javascript:;" onclick="popup.close();" class="button y">{lang cancel}</a></dd>
				</form>
				<span id="return_$_GET['handlekey']"></span>
			</div>
		</div>
	<!--{/if}-->
	<!--{if $_GET['op'] == 'getcomment'}-->
		<!--{template home/space_doing_li}-->
	<!--{/if}-->
<!--{else}-->
<!--{if $_G[inajax]}-->
<!--{else}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang doing}</h2>
</div>
<!--{/if}-->
<!--{if $_G[inajax]}--><div class="tip loginbox loginpop p5"><!--{/if}-->
<div id="content">
	<!--{if helper_access::check_module('doing')}-->
	<!--{template home/space_doing_form}-->
	<!--{/if}-->
</div>
<!--{if $_G[inajax]}--></div><!--{/if}-->
<!--{/if}-->
<!--{eval $nofooter = true;}-->
<!--{template common/footer}-->