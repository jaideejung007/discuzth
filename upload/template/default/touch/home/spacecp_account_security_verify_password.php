<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-x"></i></a></div>
    <h2>{lang action_account_security_verify}{lang action_account_security_type_password}</h2>
</div>
<!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=password&security_submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <table cellspacing="0" cellpadding="0" class="tfm">
            <tr>
                <th style="width: 10px;"><span class="rq">*</span></th>
                <td colspan="2" style="display: flex;">
	                {$_G['member']['username']}
                </td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <th style="width: 10px;"><span class="rq">*</span></th>
                <td colspan="2" style="display: flex;">
                    <input type="password" name="password" id="password" value="" class="px" placeholder="{lang action_account_security_type_password}"/>
                </td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">
                    <button class="formdialog pn pnc" type="submit" name="security_submit" value="true"><strong>{lang action_account_security_submit}</strong></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<!--{template common/footer}-->