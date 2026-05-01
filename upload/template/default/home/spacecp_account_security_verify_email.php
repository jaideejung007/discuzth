<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tm_c">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <h3 class="flb">
        <em id="return_$handlekey"><!--{if $method == 'chgemail'}-->{lang action_account_security_set}<!--{else}-->{lang action_account_security_verify}<!--{/if}-->{lang action_account_security_type_email}</em>
        <span>
			<a href="javascript:;" class="flbc" onclick="hideWindow('$handlekey')" title="{lang close}">{lang close}</a>
		</span>
    </h3>
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=email&security_submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <div class="c cl">
            <input type="hidden" name="formhash" value="{FORMHASH}" />
            <input type="hidden" name="referer" value="{echo dreferer()}" />

            <div class="rfm">
                <table>
                    <tr>
                        <th><span class="rq">*</span>{lang email}:</th>
                        <td>
                            <!--{if $method == 'chgemail' && empty($_G['member']['email'])}-->
                                <input type="text" name="email" id="email" class="px"/>
                            <!--{else}-->
                            <!--{eval $email_arr = explode('@', $_G['member']['email']);}-->
                            <!--{eval echo substr($email_arr[0], 0, 3).'****' . '@' . $email_arr[1]}-->
                            <input type="hidden" id="email" name="email" value="{$_G['member']['email']}" />
                            <!--{/if}-->
                        </td>
                    </tr>
                    <tr>
                        <th><span class="rq">*</span><label for="seccode">{lang seccode}:</label></th>
                        <td>
                            <input type="text" name="seccode" id="seccode" value="" class="px" />
                            <div class="sendsec" id="sendsec" style="display: inline-block;">
                                <button type="button" name="seccodesendnew" id="seccodesendnew" value="true" class="pn pnc" onclick="memcp_sendseccode();" /><strong>{lang send}</strong></button>
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
        function memcp_sendseccode() {
            memcp_svctype = 1;
            memcp_email = document.getElementById("email").value;
            return sendemailseccode(memcp_svctype, memcp_email);
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
                dom.innerHTML = '<button type="button" name="seccodesendnew" id="seccodesendnew" value="true" class="pn pnc" onclick="memcp_sendseccode();" /><strong>{lang send}</strong></button>';
                return;
            }
            timeout_sendsec = setTimeout(disable_sendsecbtn, 1000);
        }
    </script>
</div>
<!--{template common/footer}-->