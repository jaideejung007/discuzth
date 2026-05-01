<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tip loginbox loginpop p5" id="floatlayout_collection">
    <h2 class="log_tit" id="return_rate"><a href="javascript:;" onclick="popup.close();"><span class="icon_close y">&nbsp;</span></a>{lang seccode_verify}</h2>
    <form id="confirmform" method="post" autocomplete="off" action="misc.php?mod=secmobseccode&action=send&svctype=$svctype&secmobicc=$secmobicc&secmobile=$secmobile" onsubmit="ajaxpost('confirmform', 'return_$handlekey', 'return_$handlekey');return false;">
        <input type="hidden" name="formhash" value="{FORMHASH}" />
        <input type="hidden" name="seccodesubmit" value="true" />
        <input type="hidden" name="handlekey" value="$handlekey" />
        <!--{block sectpl}--><div class="rfm"><table><tr><th><sec>: </th><td><sec><br /><sec></td></tr></table></div><!--{/block}-->
        <!--{subtemplate common/seccheck}-->
        <p class="o pns" style="height: auto;">
            <input type="submit" name="funcsubmit_btn" class="formdialog pn pnc" value="{lang confirms}">
        </p>
    </form>
</div>
<!--{template common/footer}-->