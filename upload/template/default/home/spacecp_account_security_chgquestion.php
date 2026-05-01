<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tm_c">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <h3 class="flb">
        <em id="return_$handlekey">{lang chgquestion_title}</em>
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
                        <th><span class="rq">*</span><label for="questionidnew">{lang security_question}</label></th>
                        <td>
                            <select name="questionidnew" id="questionidnew">
                                <option value="" selected>{lang memcp_profile_security_keep}</option>
                                <option value="0">{lang security_question_0}</option>
                                <option value="1">{lang security_question_1}</option>
                                <option value="2">{lang security_question_2}</option>
                                <option value="3">{lang security_question_3}</option>
                                <option value="4">{lang security_question_4}</option>
                                <option value="5">{lang security_question_5}</option>
                                <option value="6">{lang security_question_6}</option>
                                <option value="7">{lang security_question_7}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="rq">*</span><label for="answernew">{lang security_answer}</label></th>
                        <td>
                            <input type="text" name="answernew" id="answernew" class="px" />
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