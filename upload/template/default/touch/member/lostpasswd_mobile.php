<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{if !$_GET['infloat']}-->
<div class="header cl">
    <div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div><h2></h2><div class="my"></div>
</div>
<div class="header_toplogo">{$_G['style']['touchlogo']}<p>{lang getpassword}</p></div>
<!--{/if}-->
<!--{hook/logging_top_mobile}-->
<!--{eval $loginhash = 'L'.random(4);}-->
<div class="loginbox cl">
    <form id="lostpwform" method="post" action="member.php?mod=lostpasswd_mobile&lostpwsubmit=yes&infloat=yes&mobile=2" >
        <input type="hidden" name="formhash" id="formhash" value='{FORMHASH}' />
        <input type="hidden" name="referer" id="referer" value="<!--{if dreferer()}-->{echo dreferer()}<!--{else}-->forum.php?mobile=2<!--{/if}-->" />
        <div class="login_from">
            <ul>
                <li>
                    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
                    <!--{eval $smssupportedccarr = $_G['setting']['smssupportedcc'] ? explode("\r\n",$_G['setting']['smssupportedcc']) : array();}-->
                    <select id="secmobicc" name="secmobicc" class="sel_list border_right" style="width: 80px;">
                        <!--{if !empty($smssupportedccarr)}-->
                        <!--{loop $smssupportedccarr $cc_value}-->
                        <!--{eval $cc_value_arr = explode("=",$cc_value);}-->
                        <option value="$cc_value_arr[0]" <!--{if $cc_value_arr[0] == $_G['setting']['smsdefaultcc']}-->selected="selected"<!--{/if}-->>+{$cc_value_arr[0]} {$cc_value_arr[1]}</option>
                        <!--{/loop}-->
                        <!--{else}-->
                        <option value="{$_G['setting']['smsdefaultcc']}" selected="selected">+{$_G['setting']['smsdefaultcc']}</option>
                        <!--{/if}-->
                    </select>
                    <input type="text" name="secmobile" id="secmobile" class="px" style="width: 135px;" placeholder="{lang secmobile}" fwin="secmobile"/>
                </li>
                <li style="display: flex;">
                    <input type="text" name="secmobseccode" id="secmobseccode" value="" class="px"  placeholder="{lang secmobseccode}" fwin="secmobseccode"/>
                    <button type="button" name="secmobseccodesendnew" id="secmobseccodesendnew" value="true" class="pn pnc" onclick="memcp_sendsecmobseccode_{$layerhash}();return false;" style="height: 35px; line-height: 35px;"/><strong>{lang send}</strong></button>
                </li>
            </ul>
        </div>
        <div class="btn_login"><button value="true" name="lostpwsubmit" type="submit" class="formdialog pn">{lang submit}</button></div>
    </form>
    <div class="reg_link"><a href="member.php?mod=logging&action=login_mobile">{lang login}</a></div>
    <!--{hook/logging_bottom_mobile}-->
</div>
<!--{if $_G['setting']['pwdsafety']}-->
<script type="text/javascript" src="{$_G['setting']['jspath']}md5.js?{VERHASH}" reload="1"></script>
<!--{/if}-->
<!--{eval updatesession();}-->

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
                    popup.open(s.lastChild.firstChild.nodeValue);
                    evalscript(s.lastChild.firstChild.nodeValue);
                })
                .error(function () {
                    window.location.href = obj.attr('href');
                    popup.close();
                });

    }
</script>

<!--{template common/footer}-->