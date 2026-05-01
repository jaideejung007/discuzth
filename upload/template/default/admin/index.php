<?php exit('Access Denied'); ?>
<style>
	*, *::before, *::after {
		box-sizing: inherit;
	}
	.dragObj.dragging {
		opacity: 0.5;
	}
</style>

<!--{if !empty($_GET['edit'])}-->
<form method="post" autocomplete="off" name="login" id="loginform" action="{ADMINSCRIPT}?action=misc&operation=setwidget" class="loginbox">
{echo show_edittips();}
<!--{else}-->
{echo show_releasetips();}
<!--{/if}-->

<div class="drow">
	<div id="show_widgets_left" class="dcol d-23">{echo show_widgets('left')}</div>
	<div id="show_widgets_right" class="dcol d-13">{echo show_widgets('right')}</div>
</div>

<!--{if !empty($_GET['edit'])}-->
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<input name="submit" type="submit" class="btn" value="{lang submit}" />
	<input name="resetsubmit" type="submit"class="btn" value="{lang reset}" />
</form>

<script src="{$_G['setting']['jspath']}admincp_index.js?{$_G['style']['verhash']}" type="text/javascript"></script>
<!--{elseif $_G['adminid'] == 1}-->
<a href="{ADMINSCRIPT}?action=index&edit=yes" style="float: right">{lang edit}</a>
<br /><br />
<!--{/if}-->

<div class="copyright">
	<p>Based on MitFrame<sup>&reg;</sup>, Powered by <a href="https://www.discuz.vip/" target="_blank" class="lightlink2">Discuz! {DISCUZ_VERSION}</a>, Cloud services by <a href="https://www.witframe.com/" target="_blank" class="lightlink2">WitFrame<sup>&reg;</sup></a>, <a href="https://license.discuz.vip?v=X5" target="_blank">License</a></p>
	<p>{lang copyright}</p>
</div>