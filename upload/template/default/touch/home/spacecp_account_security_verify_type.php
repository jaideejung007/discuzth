<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
    <div class="mz"><a href="home.php?mod=spacecp&ac=account"><i class="dm-x"></i></a></div>
    <h2>{lang action_account_security_verify_type}</h2>
</div>
<div id="ct" class="bodybox p10 cl" style="display: block;width:200px !important; padding-top: 20px !important;">
    <div class="rfm security_verify">
        <ul style="text-align: center;">
            <!--{if in_array('secmobile', $security_verify)}-->
            <li>
                <a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=secmobile&formhash={FORMHASH}" style="color: green;">{lang action_account_security_verify_mobile}</a>
            </li>
            <!--{/if}-->
            <!--{if in_array('email', $security_verify)}-->
            <li>
                <a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=email&formhash={FORMHASH}" style="color: green;">{lang action_account_security_verify_email}</a>
            </li>
            <!--{/if}-->
	        <!--{if in_array('password', $security_verify)}-->
	        <li>
		        <a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=password&formhash={FORMHASH}" style="color: green;">{lang action_account_security_verify_password}</a>
	        </li>
	        <!--{/if}-->
            <!--{if in_array('appeal', $security_verify)}-->
            <li>
                <a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=appeal&formhash={FORMHASH}" style="color: green;">{lang action_account_security_verify_appeal}</a>
            </li>
            <!--{/if}-->
        </ul>
        <style type="text/css">
            .security_verify a{
                padding: 15px 50px;
                height: 50px;
                line-height: 50px;
                background-color: #e6e6e6;
            }
        </style>
    </div>
</div>
<div id="mask_popup" style="display:none;width: 100%; height: 100%; position: fixed; top: 0px; left: 0px; background: black; opacity: 0.2; z-index: 100;"></div>
<!--{template common/footer}-->