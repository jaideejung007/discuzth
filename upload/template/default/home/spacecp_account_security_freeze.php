<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tm_c">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <h3 class="flb">
        <em id="return_$handlekey">{lang action_account_security_type_freeze}</em>
        <span>
			<a href="javascript:;" class="flbc" onclick="hideWindow('$handlekey')" title="{lang close}">{lang close}</a>
		</span>
    </h3>
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&idstring=$idstring&sign=$sign&submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <div class="c cl">
            <input type="hidden" name="formhash" value="{FORMHASH}" />
            <input type="hidden" name="referer" value="{echo dreferer()}" />

            <div class="rfm">
                <table>
                    <tr>
                        <th><span class="rq">*</span><label for="freezereson">{lang freeze_reason}:</label></th>
                        <td>
                            <textarea rows="3" cols="50" name="freezereson" id="freezereson" class="pt">$validate[message]</textarea>
                        </td>
                    </tr>
                    <!--{if $validate['submittimes']}-->
                    <tr>
                        <th><label for="freezereson">{lang action_account_security_type_freeze_submittime}:</label></th>
                        <td>
                            {date($validate['submitdate'], 'u')}</span>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="freezereson">{lang action_account_security_type_freeze_submittimes}:</label></th>
                        <td>
                            $validate[submittimes]
                        </td>
                    </tr>
                    <!--{/if}-->
                    <!--{if $validate['status'] == 1 && $validate['remark']}-->
                    <tr>
                        <th><label for="freezereson">{lang action_account_security_type_freeze_admin_remark}:</label></th>
                        <td>
                            $validate[remark] <span class="xg1">{lang follow_post_by_time} {date($validate['moddate'], 'u')}</span>
                        </td>
                    </tr>
                    <!--{/if}-->
                </table>
            </div>

            <div class="rfm mbw bw0">
                <table width="100%">
                    <tr>
                        <th>&nbsp;</th>
                        <td>
                            <button class="pn pnc" type="submit" name="submit" value="true"><strong>{lang action_account_security_submit}</strong></button>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

    </form>
</div>
<!--{template common/footer}-->