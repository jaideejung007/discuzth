<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-c-left"></i></a></div>
    <h2>{lang logoff_title}</h2>
</div>

<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&idstring=$idstring&sign=$sign&submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
         <table cellspacing="0" cellpadding="0" class="tfm">
            <!--{if {$_G['setting']['security_logoff_tips']}}-->
            <tr>
                <td  colspan="2">{$_G['setting']['security_logoff_tips']}</td>
                <td>&nbsp;</td>
            </tr>
            <!--{/if}-->
            <tr>
                <td colspan="2"><strong>{lang logoff_confirm_tip}</strong></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th><span class="rq">*</span><label for="logoff_enter">{lang please_enter}:</label></th>
                <td>
                    <input type="text" name="logoff_enter" id="logoff_enter" value="" class="px" />
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




<!--{template common/header}-->
<div class="tm_c">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <h3 class="flb">
        <em id="return_$handlekey">{lang logoff_title}</em>
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
                    <!--{if {$_G['setting']['security_logoff_tips']}}-->
                        <tr>
                            <td></td>
                            <td>{$_G['setting']['security_logoff_tips']}</td>
                        </tr>
                    <!--{/if}-->
                    <tr>
                        <td></td>
                        <td><strong>{lang logoff_confirm_tip}</strong></td>
                    </tr>
                    <tr>
                        <th><span class="rq">*</span><label for="logoff_enter">{lang please_enter}:</label></th>
                        <td>
                            <input type="text" name="logoff_enter" id="logoff_enter" value="" class="px" />
                        </td>
                    </tr>
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