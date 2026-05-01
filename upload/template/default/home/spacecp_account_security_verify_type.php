<?php exit('Access Denied'); ?>
<!--{template common/header}-->
<div class="tm_c">
	<!--{eval $layerhash = 'L'.rand(100000, 999999);}-->
	<h3 class="flb">
		<em id="return_$handlekey">{lang action_account_security_verify_type}</em>
		<span>
			<a href="javascript:;" class="flbc" onclick="hideWindow('$handlekey')" title="{lang close}">{lang close}</a>
		</span>
	</h3>
	<div class="c cl">
		<div class="security_verify">
			<table>
				<tr>
					<!--{if in_array('secmobile', $security_verify)}-->
					<td>
						<a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=secmobile&formhash={FORMHASH}"
						   onclick="showWindow('security_verify', this.href, 'get', 0);return false;"
						   style="color: green;">{lang
							action_account_security_verify_mobile}</a>
					</td>
					<!--{/if}-->
					<!--{if in_array('email', $security_verify)}-->
					<td>
						<a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=email&formhash={FORMHASH}"
						   onclick="showWindow('security_verify', this.href, 'get', 0);return false;"
						   style="color: green;">{lang action_account_security_verify_email}</a>
					</td>
					<!--{/if}-->
					<!--{if in_array('password', $security_verify)}-->
					<td>
						<a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=password&formhash={FORMHASH}"
						   onclick="showWindow('security_verify', this.href, 'get', 0);return false;"
						   style="color: green;">{lang action_account_security_verify_password}</a>
					</td>
					<!--{/if}-->
					<!--{if in_array('appeal', $security_verify)}-->
					<td>
						<a href="home.php?mod=spacecp&ac=account&op=verify&method=$method&verify=appeal&formhash={FORMHASH}"
						   onclick="showWindow('security_verify', this.href, 'get', 0);return false;"
						   style="color: green;">{lang
							action_account_security_verify_appeal}</a>
					</td>
					<!--{/if}-->
				</tr>
			</table>
			<style type="text/css">
				.security_verify a {
					padding: 15px 20px;
					height: 50px;
					line-height: 50px;
					background-color: #e6e6e6;
				}
			</style>
		</div>
	</div>
</div>
<!--{template common/footer}-->