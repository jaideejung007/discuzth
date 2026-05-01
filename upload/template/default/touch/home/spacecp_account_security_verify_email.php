<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-x"></i></a></div>
    <h2><!--{if $method == 'bindmobile'}-->{lang action_account_security_set}<!--{else}-->{lang action_account_security_verify}<!--{/if}-->{lang action_account_security_type_email}</h2>
</div>
<!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=email&security_submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <table cellspacing="0" cellpadding="0" class="tfm">
            <tr>
                <th style="width: 10px;"><span class="rq">*</span></th>
                <td colspan="2" style="display: flex;">
                    <!--{if $method == 'chgemail' && empty($_G['member']['email'])}-->
                    <input type="text" name="email" id="secemail" class="px" placeholder="{lang email}"/>
                    <!--{else}-->
                    <!--{eval $email_arr = explode('@', $_G['member']['email']);}-->
                    <!--{eval echo substr($email_arr[0], 0, 3).'****' . '@' . $email_arr[1]}-->
                    <input type="hidden" id="secemail" name="email" value="{$_G['member']['email']}" />
                    <!--{/if}-->
                </td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <th style="width: 10px;"><span class="rq">*</span></th>
                <td colspan="2" style="display: flex;">
                    <input type="text" name="seccode" id="seccode" value="" class="px" placeholder="{lang seccode}"/>
                    <a href="javascript:void(0);" onclick="memcp_sendsecemailseccode_{$layerhash}();return false;" class="pn pnc" style="width: 80px; height: 30px;line-height: 30px; margin-left: 5px; color: #ffffff; padding: 3px 10px;"><strong>{lang send}</strong></a>
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

<script type="text/javascript">
    function memcp_sendsecemailseccode_{$layerhash}(url, msg, values) {
        memcp_svctype = 1;
        memcp_secemail = document.getElementById("secemail").value;

        if(!memcp_secemail) {
            document.getElementById("secemail").focus();
            return false;
        }
        sendurl = 'misc.php?mod=secemailseccode&action=send&svctype=' + memcp_svctype + '&email=' + memcp_secemail;
        popup.open('<img src="' + IMGDIR + '/imageloading.gif">');
        $.ajax({
            type: 'GET',
            url: sendurl + '&inajax=1',
            dataType: 'xml'
        })
                .success(function (s) {
                    popup.open(s.lastChild.firstChild.nodeValue, null, null, false);
                    evalscript(s.lastChild.firstChild.nodeValue);
                })
                .error(function () {
                    window.location.href = obj.attr('href');
                    popup.close();
                });
    }
    function hideWindow_{$layerhash}(url, msg, values) {
        $('#mask_popup').hide();
        $('#secemailseccode_popup').hide();
    }
    (function() {
        $('#mask_popup').on('click', function() {
            $('#mask_popup').hide();
            $('#secemailseccode_popup').hide();
        });
    })();
</script>
<!--{template common/footer}-->