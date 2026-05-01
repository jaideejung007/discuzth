<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-c-left"></i></a></div>
    <h2>{lang action_account_security_type_freeze}</h2>
</div>

<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&idstring=$idstring&sign=$sign&submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <table cellspacing="0" cellpadding="0" class="tfm">
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
                    $validate[remark] <p class="xg1">{lang follow_post_by_time} {date($validate['moddate'], 'u')}</p>
                </td>
            </tr>
            <!--{/if}-->
            <tr>
                <td colspan="2">
                    <button class="formdialog pn pnc" type="submit" name="submit" value="true"><strong>{lang action_account_security_submit}</strong></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<!--{template common/footer}-->