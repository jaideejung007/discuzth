<?php exit('Access Denied');?>
<!--{template common/header}-->

<!--{if $_GET['op'] == 'delete'}-->
	<!--{if !$_G[inajax]}-->
		<div id="pt" class="bm cl">
			<div class="z"><a href="./" class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a> <em>&rsaquo;</em> <a href="home.php">$_G[setting][navs][4][navname]</a></div>
		</div>
		<div id="ct" class="ct2_a wp cl">
			<div class="mn">
				<div class="bm bw0">
	<!--{/if}-->
	<h3 class="flb">
		<em id="return_$_GET[handlekey]">{lang delete_log}</em>
		<!--{if $_G[inajax]}--><span><a href="javascript:;" onclick="hideWindow('$_GET[handlekey]');" class="flbc" title="{lang close}">{lang close}</a></span><!--{/if}-->
	</h3>
	<form method="post" autocomplete="off" id="doingform_{$doid}_{$docid}" name="doingform" action="home.php?mod=spacecp&ac=doing&op=delete&doid=$doid&docid=$docid">
		<!--{if $_G[inajax]}--><input type="hidden" name="handlekey" value="$_GET[handlekey]" /><!--{/if}-->
		<input type="hidden" name="referer" value="{echo dreferer()}" />
		<input type="hidden" name="formhash" value="{FORMHASH}" />
		<div class="c">{lang determine_delete_doing}</div>
		<p class="o pns">
			<button name="deletesubmit" type="submit" class="pn pnc" value="true"><strong>{lang determine}</strong></button>
		</p>
	</form>
	<!--{if !$_G[inajax]}-->
			</div>
		</div>
		<div class="appl"><!--{subtemplate common/userabout}--></div>
	</div>
	<!--{/if}-->
<!--{elseif $_GET['op'] == 'spacenote'}-->
	<!--{if $space[spacenote]}-->$space[spacenote]<!--{/if}-->
<!--{elseif $_GET['op'] == 'docomment' || $_GET['op'] == 'getcomment'}-->
	<!--{if helper_access::check_module('doing')}-->
	<div id="{$_GET[key]}_form_{$doid}_{$docid}">
		<form id="{$_GET[key]}_docommform_{$doid}_{$docid}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=doing&op=comment&doid=$doid&docid=$docid" {if $_G[inajax]}onsubmit="ajaxpost(this.id, 'return_{$doid}_$_GET[handlekey]');"{/if} class="comment-form-container">
			<textarea name="message" id="{$_GET[key]}_form_{$doid}_{$docid}_t" cols="40" class="" oninput="resizeTx(this);" onpropertychange="resizeTx(this);" onkeyup="dstrLenCalc(this, '{$_GET[key]}_form_{$doid}_{$docid}_limit')" onkeydown="ctrlEnter(event, '{$_GET[key]}_replybtn_{$doid}_{$docid}');"></textarea>
			<div class="comment-form-actions">
				<div class="comment-form-left">
					<span id="{$_GET[key]}_form_{$doid}_{$docid}_face" onclick="showFace(this.id, '{$_GET[key]}_form_{$doid}_{$docid}_t');return false;" class="comment-form-face"><i class="fico-emojifill fic8 vm" ></i></span>
				</div>
				<div class="comment-form-right">
					<input type="hidden" name="commentsubmit" value="true" />
					<input type="hidden" name="handlekey" value="$_GET[handlekey]" />
					<input type="hidden" name="formhash" value="{FORMHASH}" />
					<button type="submit" name="do_button" id="{$_GET[key]}_replybtn_{$doid}_{$docid}" class="pn" value="true"><em>{lang reply}</em></button>
					<button name="btncancel" href="javascript:;" onclick="docomment_form_close($doid, $docid, '$_GET[key]');" class="pn cancel-btn">{lang cancel}</button>
				</div>
			</div>
			<div id="{$_GET[key]}_form_{$doid}_{$docid}_t_limit" class="mtn" style="display: none;">{lang spacecp_doing_message1} <span id="{$_GET[key]}_form_{$doid}_{$docid}_limit">200</span> {lang spacecp_doing_message2}</div>
		</form>
		<span id="return_{$doid}_$_GET[handlekey]"></span>
	</div>
	<script type="text/javascript">
function succeedhandle_$_GET[handlekey](url, msg, values) {
	var doid = values['doid'];
	var key = '$_GET[key]';
	var docid = $docid;
	
	// 区分对动态的回复和对评论的回复
	if (docid == 0) {
		// 对动态的一级回复，发布成功后刷新到第一页，以便显示新评论
		if (typeof current_comment_pages !== 'undefined') {
			// 重置当前页码为1
			current_comment_pages[doid] = 1;
		}
		docomment_get(doid, key, 1);
	} else {
		// 对评论的回复，发布成功后保留在当前页
		docomment_get(doid, key);
	}
}
</script>
	<!--{/if}-->
	<!--{if $_GET['op'] == 'getcomment'}-->
		<!--{template home/space_doing_li}-->
	<!--{/if}-->

<!--{else}-->

<div id="content">
	<!--{if helper_access::check_module('doing')}-->
	<!--{template home/space_doing_form}-->
	<!--{/if}-->
</div>

<!--{/if}-->

<!--{template common/footer}-->