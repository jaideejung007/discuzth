<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-x"></i></a></div>
    <h2><!--{if $method == 'bindmobile'}-->{lang action_account_security_set}<!--{else}-->{lang action_account_security_verify}<!--{/if}-->{lang action_account_security_type_mobile}</h2>
</div>
<!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=secmobile&security_submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <table cellspacing="0" cellpadding="0" class="tfm">
            <tr>
                <th style="width: 10px;"><span class="rq">*</span></th>
                <td colspan="2" style="display: flex;">
                    <!--{if $method == 'bindmobile' && empty($_G['member']['secmobile'])}-->
                    <!--{eval $smssupportedccarr = $_G['setting']['smssupportedcc'] ? explode("\r\n",$_G['setting']['smssupportedcc']) : array();}-->
                    <select id="secmobicc" name="secmobicc" class="sel_list" style="width: 100px;">
                        <!--{if !empty($smssupportedccarr)}-->
                        <!--{loop $smssupportedccarr $cc_value}-->
                        <!--{eval $cc_value_arr = explode("=",$cc_value);}-->
                        <option value="$cc_value_arr[0]" <!--{if $cc_value_arr[0] == $_G['setting']['smsdefaultcc']}-->selected="selected"<!--{/if}-->>+{$cc_value_arr[0]} {$cc_value_arr[1]}</option>
                        <!--{/loop}-->
                        <!--{else}-->
                        <option value="{$_G['setting']['smsdefaultcc']}" selected="selected">+{$_G['setting']['smsdefaultcc']}</option>
                        <!--{/if}-->
                    </select>
                    <input type="number" name="secmobile" id="secmobile" class="px" placeholder="{lang secmobile}" style="margin-left: 15px;"/>
                    <input type="hidden" id="idstring_v" name="idstring_v" value="{$idstring_v}" />
                    <input type="hidden" id="sign_v" name="sign_v" value="{$sign_v}" />
                    <!--{else}-->
                    {lang secmobile}: +{$_G['member']['secmobicc']} <!--{eval echo substr($_G['member']['secmobile'], 0, 3).'****'.substr($_G['member']['secmobile'], -3);}-->
                    <input type="hidden" id="secmobicc" name="secmobicc" value="{$_G['member']['secmobicc']}" />
                    <input type="hidden" id="secmobile" name="secmobile" value="{$_G['member']['secmobile']}" />
                    <!--{/if}-->
                </td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <th style="width: 10px;"><span class="rq">*</span></th>
                <td colspan="2" style="display: flex;">
                    <input type="text" name="secmobseccode" id="secmobseccode" value="" placeholder="{lang secmobseccode}" class="px"/>
                    <a href="javascript:void(0);" onclick="memcp_sendsecmobseccode_{$layerhash}();return false;" class="pn pnc" style="width: 80px; height: 30px;line-height: 30px; margin-left: 5px; color: #ffffff; padding: 3px 10px;"><strong>{lang send}</strong></a>
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
    function memcp_sendsecmobseccode_{$layerhash}(url, msg, values) {
        if (!document.getElementById("secmobicc").value) {
            document.getElementById("secmobicc").value = $_G['setting']['smsdefaultcc'];
        }
        memcp_svctype = 1;
        memcp_secmobicc = document.getElementById("secmobicc").value;
        memcp_secmobile = document.getElementById("secmobile").value;

        if(!memcp_secmobile) {
            document.getElementById("secmobile").focus();
            return false;
        }
        sendurl = 'misc.php?mod=secmobseccode&action=send&svctype=' + memcp_svctype + '&secmobicc=' + memcp_secmobicc + '&secmobile=' + memcp_secmobile;
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
        $('#secmobseccode_popup').hide();
    }
    (function() {
        $('#mask_popup').on('click', function() {
            $('#mask_popup').hide();
            $('#secmobseccode_popup').hide();
        });
    })();
</script>
<!--{template common/footer}-->