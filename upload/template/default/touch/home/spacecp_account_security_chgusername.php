<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-c-left"></i></a></div>
    <h2>{lang chgusername_title}</h2>
</div>

<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&idstring=$idstring&sign=$sign&submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <table cellspacing="0" cellpadding="0" class="tfm">
            <tr>
                <th><span class="rq">*</span><label for="username">{lang newusername}:</label></th>
                <td>
                    <input type="text" name="username" id="username" value="" class="px" />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <button class="formdialog pn pnc" type="submit" name="submit" value="true"><strong>{lang action_account_security_submit}</strong></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<!--{template common/footer}-->