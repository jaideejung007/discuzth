<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-c-left"></i></a></div>
    <h2>{lang chgquestion_title}</h2>
</div>

<div id="ct" class="bodybox p10 cl" style="padding-top: 20px !important;">
    <!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
    <form method="post" autocomplete="off" name="security_verify" id="layerform_$layerhash" class="cl" onsubmit="ajaxpost('layerform_$layerhash', 'returnmessage_$layerhash', 'returnmessage_$layerhash', 'onerror');return false;" action="home.php?mod=spacecp&ac=account&op=verify&method=$method&idstring=$idstring&sign=$sign&submit=yes&infloat=yes&formhash={FORMHASH}&layerhash=$layerhash">
        <table cellspacing="0" cellpadding="0" class="tfm">
            <tr>
                <th><span class="rq">*</span><label for="questionidnew">{lang security_question}</th>
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
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th><span class="rq">*</span><label for="answernew">{lang security_answer}</th>
                <td>
                    <input type="text" name="answernew" id="answernew" class="px" />
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