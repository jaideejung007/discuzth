<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tm_c">
	<h3 class="flb">
		<em id="return_$handlekey">{lang secmobseccode}</em>
		<span>
			<a href="javascript:;" class="flbc" onclick="hideWindow('$handlekey')" title="{lang close}">{lang close}</a>
		</span>
	</h3>
	<form id="confirmform" method="post" autocomplete="off" action="misc.php?mod=secmobseccode&action=send&svctype=$svctype&secmobicc=$secmobicc&secmobile=$secmobile" onsubmit="ajaxpost('confirmform', 'return_$handlekey', 'return_$handlekey');return false;">
		<input type="hidden" name="formhash" value="{FORMHASH}" />
		<input type="hidden" name="seccodesubmit" value="true" />
		<input type="hidden" name="handlekey" value="$handlekey" />
		<!--{block sectpl}--><div class="rfm"><table><tr><th><sec>: </th><td><sec><br /><sec></td></tr></table></div><!--{/block}-->
		<!--{subtemplate common/seccheck}-->
		<p class="o pns">
            <button type="submit" name="funcsubmit_btn" class="btn pn pnc" ><strong>{lang confirms}</strong></button>
		</p>
	</form>
	<script type="text/javascript">
		function succeedhandle_$_GET['handlekey'](url, msg, values) {
			disable_sendsecbtn();
		}
	</script>
</div>
<!--{template common/footer}-->