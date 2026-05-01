<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tm_c">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <h3 class="flb">
        <em id="return_$handlekey"><!--{if $method == 'bindmobile'}-->{lang action_account_security_set}<!--{else}-->{lang action_account_security_verify}<!--{/if}-->{lang action_account_security_type_mobile}</em>
        <span>
			<a href="javascript:;" class="flbc" onclick="hideWindow('$handlekey')" title="{lang close}">{lang close}</a>
		</span>
    </h3>
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=secmobile&security_submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <div class="c cl">
            <input type="hidden" name="formhash" value="{FORMHASH}" />
            <input type="hidden" name="referer" value="{echo dreferer()}" />

            <div class="rfm">
                <table>
                    <tr>
                        <th><span class="rq">*</span>{lang secmobile}:</th>
                        <td>
                            <!--{if $method == 'bindmobile' && empty($_G['member']['secmobile'])}-->
                                <!--{eval $smssupportedccarr = $_G['setting']['smssupportedcc'] ? explode("\r\n",$_G['setting']['smssupportedcc']) : array();}-->
                                <select id="secmobicc" name="secmobicc" class="sel_list" style="width: 80px;">
                                    <!--{if !empty($smssupportedccarr)}-->
                                    <!--{loop $smssupportedccarr $cc_value}-->
                                    <!--{eval $cc_value_arr = explode("=",$cc_value);}-->
                                    <option value="$cc_value_arr[0]" <!--{if $cc_value_arr[0] == $_G['setting']['smsdefaultcc']}-->selected="selected"<!--{/if}-->>+{$cc_value_arr[0]} {$cc_value_arr[1]}</option>
                                    <!--{/loop}-->
                                    <!--{else}-->
                                    <option value="{$_G['setting']['smsdefaultcc']}" selected="selected">+{$_G['setting']['smsdefaultcc']}</option>
                                    <!--{/if}-->
                                </select>
                                <input type="text" name="secmobile" id="secmobile" class="px" style="width: 135px;"/>
                                <input type="hidden" id="idstring_v" name="idstring_v" value="{$idstring_v}" />
                                <input type="hidden" id="sign_v" name="sign_v" value="{$sign_v}" />
                            <!--{else}-->
                            +{$_G['member']['secmobicc']} <!--{eval echo substr($_G['member']['secmobile'], 0, 3).'****'.substr($_G['member']['secmobile'], -3);}-->
                            <input type="hidden" id="secmobicc" name="secmobicc" value="{$_G['member']['secmobicc']}" />
                            <input type="hidden" id="secmobile" name="secmobile" value="{$_G['member']['secmobile']}" />
                            <!--{/if}-->
                        </td>
                    </tr>
                    <tr>
                        <th><span class="rq">*</span><label for="secmobseccode">{lang secmobseccode}:</label></th>
                        <td>
                            <input type="text" name="secmobseccode" id="secmobseccode" value="" class="px" />
                            <div class="sendsec" id="sendsec" style="display: inline-block;">
                                <button type="button" name="secmobseccodesendnew" id="secmobseccodesendnew" value="true" class="pn pnc" onclick="memcp_sendsecmobseccode();" /><strong>{lang send}</strong></button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="rfm mbw bw0">
                <table width="100%">
                    <tr>
                        <th>&nbsp;</th>
                        <td>
                            <button class="pn pnc" type="submit" name="security_submit" value="true"><strong>{lang action_account_security_submit}</strong></button>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

    </form>
    <script type="text/javascript">
        function memcp_sendsecmobseccode() {
            if (!document.getElementById("secmobicc").value) {
                document.getElementById("secmobicc").value = $_G['setting']['smsdefaultcc'];
            }
            memcp_svctype = 1;
            memcp_secmobicc = document.getElementById("secmobicc").value;
            memcp_secmobile = document.getElementById("secmobile").value;
            return sendsecmobseccode(memcp_svctype, memcp_secmobicc, memcp_secmobile);
        }

        var leftseconds = 60;
        var timeout_sendsec = null;
        function disable_sendsecbtn() {
            var dom = document.getElementById('sendsec');
            dom.disabled=true;
            dom.innerHTML = leftseconds+'s';
            --leftseconds;
            if (leftseconds<=0) {
                clearTimeout('timeout_sendsec');
                leftseconds = 60;
                dom.innerHTML = '<button type="button" name="secmobseccodesendnew" id="secmobseccodesendnew" value="true" class="pn pnc" onclick="memcp_sendsecmobseccode();" /><strong>{lang send}</strong></button>';
                return;
            }
            timeout_sendsec = setTimeout(disable_sendsecbtn, 1000);
        }
    </script>
</div>
<!--{template common/footer}-->