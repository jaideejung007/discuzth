<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tm_c">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <h3 class="flb">
        <em id="return_$handlekey">{lang chgemail_title}</em>
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
                        <th><span class="rq">*</span><label for="email">{lang newemail}:</label></th>
                        <td>
                            <input type="text" name="email" id="email" value="" class="px" />
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