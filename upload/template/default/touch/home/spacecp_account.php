<?php exit('Access Denied');?>
<!--{template common/header}-->
    <script type="text/javascript" src="{$_G[setting][iconfont]}?{VERHASH}"></script>
	<!--{subtemplate home/spacecp_header}-->

            <caption>
                <h2 class="mbm xs3 xw1 pt10">
                    {lang action_account_title_security}
                </h2>
            </caption>

            <table cellspacing="0" cellpadding="0" class="tfm b_b">
                <!--{if $_G['setting']['security_rename']}-->
                <tr>
                    <th>{lang action_account_security_type_rename}</th>
                    <td>
                        <p class="y">
                            <!--{if getuserprofile('extcredits'.$creditExtra) < $_G['setting']['chgusername']['credits_pay'] || ($_G['member']['credits'] < $_G['setting']['chgusername']['credits_threshold'] && !in_array($_G['member']['groupid'], (array)$_G['setting']['chgusername']['credits_unlimit_group']))}-->
                            <span style="color: #646464;">{lang action_account_operate_chg}</span>
                            <!--{else}-->
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=chgusername&formhash={FORMHASH}"  style="color: green;" class="dialog">{lang action_account_operate_chg}</a>
                            <!--{/if}-->
                        </p>
                        {$_G['member']['username']}
                        <p class="d xs1 xg1">
                            {lang action_account_security_type_rename_comment}
                            <!--{if $_G['setting']['chgusername']['max_times'] > 0}-->
                                , {lang action_account_security_rename_numberoftimes1} <!--{echo ($_G['setting']['chgusername']['max_times'] - table_common_member_username_history::t()->count_by_uid($_G['uid']));}--> {lang action_account_security_rename_numberoftimes2}
                            <!--{/if}-->
                            <!--{if $_G['setting']['chgusername']['credits_threshold'] > 0}-->
                                , {lang action_account_security_rename_credits_low1} {$_G['setting']['chgusername']['credits_threshold']} {lang action_account_security_rename_credits_low2}
                            <!--{/if}-->
                            <!--{if $_G['setting']['chgusername']['credits_pay'] > 0}-->
                                ,
                                {lang action_account_security_rename_credits_pay_low} {$_G['setting']['chgusername']['credits_pay']} {$extcredit['unit']} {$extcredit['title']}
                            <!--{/if}-->
                        </p>
                    </td>
				</tr>
                <!--{/if}-->
                <!--{if $_G[member][loginname] != $_G[member][username]}-->
                <tr>
                    <th>{lang loginname}</th>
                    <td>{$_G['member']['loginname']}</td>
                </tr>
                <!--{/if}-->
                <!--{if $_G['setting']['security_password']}-->
                <tr>
                    <th>{lang action_account_security_type_password}</th>
                    <td>
                        <p class="y">
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=chgpassword&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_password_modify}</a>
                        </p>
                        ******
                        <p class="d xs1 xg1">{lang action_account_security_type_password_comment}</p>
                        <!--{if $_G['member']['freeze'] == 1}-->
                        <strong class="xi1">{lang freeze_pw_tips}</strong>
                        <!--{/if}-->
                    </td>
                </tr>
                <!--{/if}-->
                <!--{if $_G['setting']['security_question']}-->
                <tr>
                    <th>{lang action_account_security_type_question}</th>
                    <td>
                        <p class="y">
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=chgquestion&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_chg}</a>
                        </p>
                        <span class="d xs1 xg1">{lang action_account_security_type_question_comment}</span>
                    </td>
                </tr>
                <!--{/if}-->
                <!--{if $_G['setting']['security_email']}-->
                <tr>
                    <th>{lang action_account_security_type_email}</th>
                    <td>
                        <p class="y">
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=chgemail&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_chg}</a>
                        </p>
                        <!--{if $_G['member']['email']}-->
                            <!--{eval $email_arr = explode('@', $_G['member']['email']);}-->
                            <!--{eval echo substr($email_arr[0], 0, 3).'****' . '@' . $email_arr[1]}-->
                            <!--{if $_G['member']['emailstatus']}-->
                                <span style="color: green;">({lang action_account_status_active})</span>
                            <!--{else}-->
                                <a href="home.php?mod=spacecp&ac=account&op=verifyemail&method=resend&formhash={FORMHASH}" style="color: red;">({lang action_account_operate_email_active})</a>
                            <!--{/if}-->
                        <!--{else}-->
                            {lang action_account_security_type_data_empty}
                        <!--{/if}-->
                        <p class="d xs1 xg1">{lang action_account_security_type_email_comment}</p>
                        <!--{if $_G['member']['freeze'] == 2}-->
                        <strong class="xi1 xs1">{lang freeze_email_tips}</strong>
                        <!--{/if}-->
                        </p>
                    </td>
                </tr>
                <!--{/if}-->
                <!--{if $_G['setting']['security_mobile']}-->
                <tr>
                    <th>{lang action_account_security_type_mobile}</th>
                    <td>
                        <p class="y">
                            <!--{if !$_G['member']['secmobile']}-->
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=bindmobile&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_bind}</a>
                            <!--{else}-->
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=unbindmobile&formhash={FORMHASH}" style="color: red;" class="dialog">{lang action_account_operate_unbind}</a>
                            <!--{/if}-->
                        </p>
                        <!--{if $_G['member']['secmobile']}-->
                        <!--{if $_G['member']['secmobicc']}-->+{$_G['member']['secmobicc']} <!--{/if}-->
                        <!--{eval echo substr($_G['member']['secmobile'], 0, 3).'****'.substr($_G['member']['secmobile'], -3);}-->
                        <!--{else}-->
                        {lang action_account_security_type_data_empty}
                        <!--{/if}-->
                        <p class="d xs1 xg1">{lang action_account_security_type_mobile_comment}</p>
                    </td>
                </tr>
                <!--{/if}-->
                <!--{if $_G['setting']['security_logoff']}-->
                <tr>
                    <th>{lang action_account_security_type_logoff}</th>
                    <td>
                        <p class="y">
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=logoff&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_logoff_set}</a>
                        </p>
                    </td>
                </tr>
                <!--{/if}-->
                <!--{if $_G['member']['freeze'] == 2 || $_G['member']['freeze'] == -1}-->
                <tr>
                    <th>{lang action_account_security_type_freeze}</th>
                    <td>
                        <p class="y">
                            <a href="home.php?mod=spacecp&ac=account&op=verify&method=freeze&formhash={FORMHASH}" onclick="showWindow('security_verify', this.href, 'get', 0);return false;" style="color: green;" class="dialog">{lang action_account_security_type_freeze_reason_submit}</a>
                        </p>
                        <!--{if $_G['member']['freeze'] == 2}--><span class="d xs1 xg1">{lang freeze_reason_comment}</span>
                        <!--{elseif $_G['member']['freeze'] == -1}-->
                        <p class="xs1"><strong class="xi1">{lang freeze_admincp_tips}</strong></p>
                        <!--{/if}-->
                    </td>
                </tr>
                <!--{/if}-->
            </table>
            <!--{if $list}-->
            <caption>
                <h2 class="mbm xs3 xw1 pt10">
                    {lang action_account_title_third_login_method}
                </h2>
            </caption>
            <table cellspacing="0" cellpadding="0" class="tfm">
                <tr>
                    <th>{lang action_account_type}</th>
                    <th>{lang action_account_bindname}</th>
                    <th>{lang action_account_operate}</th>
                </tr>
                <!--{eval $i = 0;}-->
                <!--{loop $list $key $value}-->
                <!--{eval $i++;}-->
                <tr{if $i % 2 == 0} class="alt"{/if}>
                <td>{cell account/icon} {$value[1]}</td>
                <!--{if !empty($account_list[$value[0]]['account'])}-->
                    <td>{$account_list[$value[0]]['bindname']}</td>
                    <td><a href="home.php?mod=spacecp&ac=account&op=unbind&method={$value[0]}&formhash={FORMHASH}" style="color: red;">{lang action_account_operate_unbind}</a></td>
                <!--{else}-->
                    <td></td>
                    <td><a href="login.php?method={$value[0]}&formhash={FORMHASH}" style="color: green;">{lang action_account_operate_bind}</a></td>
                <!--{/if}-->
                </tr>
                <!--{/loop}-->
            </table>
            <!--{/if}-->
</div>
<!--{template common/footer}-->