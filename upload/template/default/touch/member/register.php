<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div><h2></h2><div class="my"></div>
</div>
<div class="header_toplogo">
	{$_G['style']['touchlogo']}
	<!--{if !$_G['cookie']['accountUDAuth']}-->
	<p>$_G['setting']['reglinkname']</p>
	<!--{else}-->
	<!--{eval $auth = $_G['cookie']['accountUDAuth'];}-->
	<!--{eval $paramBase = dunserialize(authcode($auth, 'DECODE'));}-->
	<p>{lang quickregister_bindaccount}<!--{eval echo lang('admincp_menu', 'menu_setting_'.$paramBase['type'])}--></p>
	<!--{/if}-->
</div>
<!--{hook/register_top_mobile}-->
<div class="loginbox registerbox">
	<div class="login_from post_box">
		<form method="post" autocomplete="off" name="register" id="registerform" action="member.php?mod={$_G['setting']['regname']}&mobile=2">
			<input type="hidden" name="regsubmit" value="yes" />
			<input type="hidden" name="formhash" value="{FORMHASH}" />
			<!--{eval $dreferer = str_replace('&amp;', '&', dreferer());}-->
			<input type="hidden" name="referer" value="$dreferer" />
			<input type="hidden" name="activationauth" value="{if $_GET['action'] == 'activation'}$activationauth{/if}" />
			<!--{if $_G['setting']['sendregisterurl']}-->
			<input type="hidden" name="hash" value="$_GET['hash']" />
			<!--{/if}-->
			<ul>
				<!--{if $sendurl}-->
				<li class="mli"><input type="email" class="px" autocomplete="off" value="" name="{$_G['setting']['reginput']['email']}" placeholder="{lang registeremail}" fwin="login"></li>
				<!--{else}-->
				<!--{if $invite}-->
				<!--{if $invite['uid']}-->
				<li class="mli sec_txt">
					<span>{lang register_from}:</span>
					<span><a href="home.php?mod=space&uid=$invite[uid]" target="_blank">$invite['username']</a></span>
				</li>
				<!--{else}-->
				<li class="mli sec_txt">
					<span>{lang invite_code}:</span>
					<span>$_GET[invitecode]<input type="hidden" id="invitecode" name="invitecode" value="$_GET['invitecode']" /></span>
				</li>
				<!--{eval $invitecode = 1;}-->
				<!--{/if}-->
				<!--{/if}-->
				<!--{if empty($invite) && $_G['setting']['regstatus'] == 2 && !$invitestatus}-->
				<li class="mli">
					<input type="text" class="px" autocomplete="off" value="" name="invitecode" placeholder="{lang invite_code}" fwin="login">
					<!--{if $this->setting['inviteconfig']['buyinvitecode'] && $this->setting['inviteconfig']['invitecodeprice'] && payment::enable()}-->
					<a href="misc.php?mod=buyinvitecode" class="input-append">{lang register_buyinvitecode}</a>
					<!--{/if}-->
				</li>
				<!--{/if}-->
				<li class="mli"><input type="text" class="px" autocomplete="off" value="" name="{$_G['setting']['reginput']['username']}" placeholder="{lang registerinputtip}" fwin="login"></li>
				<li class="mli"><input type="password" class="px" value="" name="{$_G['setting']['reginput']['password']}" placeholder="{lang login_password}" fwin="login"></li>
				<li class="mli"><input type="password" class="px" value="" name="{$_G['setting']['reginput']['password2']}" placeholder="{lang registerpassword2}" fwin="login"></li>
				<!--{if $this->setting['regemail']}-->
				<li class="mli"><input type="email" class="px" autocomplete="off" value="$hash[0]" name="{$_G['setting']['reginput']['email']}" placeholder="{lang registeremail}" fwin="login"></li>
				<!--{/if}-->
				<!--{if $_G['setting']['regverify'] == 2}-->
				<li class="mli"><input type="text" class="px" autocomplete="off" value="{lang register_message}" name="regmessage" placeholder="{lang register_message}" fwin="login"></li>
				<!--{/if}-->
				<!--{if empty($invite) && $_G['setting']['regstatus'] == 3}-->
				<li class="mli"><input type="text" class="px" autocomplete="off" value="" name="invitecode" placeholder="{lang invite_code}" fwin="login"></li>
				<!--{/if}-->
				<!--{loop $_G['cache']['fields_register'] $field}-->
				<!--{if $htmls[$field['fieldid']]}-->
				<!--{if $field['fieldid'] == 'gender'}-->
				<li class="flex-box mli"><div class="flex xg1">{$field['title']}</div><div class="flex-3">$htmls[$field['fieldid']]</div></li>
				<!--{elseif $field['fieldid'] == 'birthday'}-->
				<li class="flex-box mli"><div class="flex xg1">{$field['title']}</div><div class="flex-3 multisel">$htmls[$field['fieldid']]</div></li>
				<!--{else}-->
				<li class="mli">$htmls[$field['fieldid']]</li>
				<!--{/if}-->
				<!--{/if}-->
				<!--{/loop}-->

				<!--{if $this->setting['regverifymobile'] && $this->setting['smsstatus']}-->
				<li class="mli">
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
				<li class="mli" style="display: flex;">
					<input type="text" name="secmobseccode" id="secmobseccode" value="" class="px"  placeholder="{lang secmobseccode}" fwin="secmobseccode"/>
					<button type="button" name="secmobseccodesendnew" id="secmobseccodesendnew" value="true" class="pn pnc" onclick="memcp_sendsecmobseccode_{$layerhash}();return false;" style="height: 35px; line-height: 35px;"/><strong>{lang send}</strong></button>
				</li>
				<!--{/if}-->

				<!--{/if}-->
				<!--{hook/register_input_mobile}-->
			</ul>
			<!--{if $secqaacheck || $seccodecheck}-->
			<!--{subtemplate common/seccheck}-->
			<!--{/if}-->
			<!--{if $bbrules}-->
			<label for="agreebbrule"><li class="mli"><input type="checkbox" class="pc" name="agreebbrule" value="{$bbrulehash}" id="agreebbrule" checked="checked" />{lang agree}<a href="javascript:;" onclick="showBBRule()">{lang rulemessage}</a></li></label>
			<div id="layer_bbruletxt" popup="true" class="tip login_pop" style="display:none;">
				<div style="height:200px;display:block;overflow-y:scroll;">
					<div class="log_tit"><!--{echo addslashes($this->setting['bbname']);}--> {lang rulemessage}</div>
					<div class="p15">{$bbrulestxt}</div>
				</div>
			</div>
			<!--{/if}-->
	</div>
	<div class="btn_register"><button value="true" name="regsubmit" type="submit" class="formdialog pn">{lang quickregister}</button></div>
	<div class="reg_link"><a href="member.php?mod=logging&action=login&referer={echo rawurlencode($dreferer)}" class="login_now">{lang login_now}</a></div>
	</form>
</div>

<!--{if $this->setting['regverifymobile'] && $this->setting['smsstatus']}-->
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
<!--{/if}-->


<div id="mask" style="display:none;"></div>
<!--{hook/register_bottom_mobile}-->
<script type="text/javascript">
	<!--{if $sendurl}-->
	function succeedhandle_registerform(url, message, extra) {
		popup.open(message, 'confirm', url)
	}
	<!--{/if}-->
	<!--{if $bbrules && $bbrulesforce}-->
	showBBRule();
	<!--{/if}-->
	<!--{if $this->showregisterform}-->
	function showBBRule() {
		var bbruletxt = getID("layer_bbruletxt").innerHTML;
		popup.open(bbruletxt, 'alert');
	}
	<!--{/if}-->
</script>
<!--{eval updatesession();}-->
<!--{template common/footer}-->