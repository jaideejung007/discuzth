<?php exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="$charset">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="color-scheme" content="light dark">
	<title>$title</title>
	<link rel="stylesheet" href="{$staticurl}image/admincp/minireset.css?{$_G['style']['verhash']}">
	<link rel="stylesheet" href="{$staticurl}image/admincp/admincplogin.css?{$_G['style']['verhash']}">
	<meta content="Discuz! Team" name="Copyright">
	<script src="{$staticurl}js/common.js"></script>
	<script src="{$staticurl}js/admincp_base.js"></script>
</head>
<body>
<div class="darkmode" title="$light_mode">
	<div>
		<div class="dk-light">
			<!--{subtemplate admin/svg/light}-->
		</div>
		<div class="dk-dark">
			<!--{subtemplate admin/svg/dark}-->
		</div>
	</div>
	<ul id="dkm_menu" style="display: none;"><li class="current">$by_system</li><li>$normal_mode</li><li>$dark_mode</li></ul>
</div>
<!--{if $cpaccess != -2 && $cpaccess != -3}-->
<div class="container">

<!--{/if}-->

<!--{if $cpaccess == -5}-->
	<div class="loginbox">{echo lang('admincp_login', 'login_cp_guest');}</div>
<!--{elseif $cpaccess == -2 || $cpaccess == -3}-->
	<div class="container loginbox"><span>{echo lang('admincp_login', 'login_cp_noaccess');}</span></div>
<!--{elseif $cpaccess == -1}-->
	{eval $ltime = $this->sessionlife - (TIMESTAMP - $this->adminsession['dateline']);}
	<div class="loginbox"><span>{echo lang('admincp_login', 'login_cplock', array('ltime' => $ltime));}</span></div>
<!--{elseif $cpaccess == -4}-->
	{eval $ltime = $this->sessionlife - (TIMESTAMP - $this->adminsession['dateline']);}
	<div class="loginbox"><span>{echo lang('admincp_login', 'login_user_lock');}</span></div>
<!--{else}-->
	<form method="post" autocomplete="off" name="login" id="loginform" action="$extra" class="loginbox">
		<input type="hidden" name="sid" value="$sid">
		<input type="hidden" name="frames" value="yes">
		<input type="hidden" name="formhash" value="$formhash">
		
		{$_G['style']['boardlogo']}
		<h3 style="margin-bottom: 30px;text-align: center;font-size: 20px;">$cptitle</h3>
		<!--{if $uid}-->
			<!--{if !$mustlogin}-->
			<select name="admin_type" onchange="$('admin_username').style.display = this.value > 0 ? 'none' : ''">
				<option value="$uid">{$username}</option>
				<option value="0">{$lang['other_loginname']}</option>
			</select>
			<!--{else}-->
				<h1>{$username}</h1>
			<!--{/if}-->
		<!--{/if}-->
		<input type="text" id="admin_username" name="admin_username" placeholder="{$lang['login_username']}" autofocus autocomplete="off"<!--{if $uid}--> style="display: none"<!--{/if}-->>
		<input type="password" name="admin_password" placeholder="{$lang['login_password']}" autocomplete="off"<!--{if !$isguest}--> autofocus<!--{/if}-->>
		<p onclick="document.querySelectorAll('.loginqa').forEach(vf=>{vf.className=''});this.style.display='none';"><span tabindex="0" onkeydown="window.event.key!='Tab'&&this.parentNode.click()"></span>{$lang['security_question']}</p>
		<select id="questionid" name="admin_questionid" class="loginqa">
			$forcesecques
			<option value="1">{$lang['security_question_1']}</option>
			<option value="2">{$lang['security_question_2']}</option>
			<option value="3">{$lang['security_question_3']}</option>
			<option value="4">{$lang['security_question_4']}</option>
			<option value="5">{$lang['security_question_5']}</option>
			<option value="6">{$lang['security_question_6']}</option>
			<option value="7">{$lang['security_question_7']}</option>
		</select>
		<input type="text" name="admin_answer" class="loginqa" placeholder="{$lang['security_answer']}" autocomplete="off">
		<button type="submit">{$lang['submit']}</button>

		<!--{if !empty($_G['admincp_checkip_noaccess'])}-->
			<br><span>{echo lang('admincp_login', 'login_ip_noaccess');}</span>
		<!--{/if}-->
	</form>
<!--{/if}-->

<!--{if $cpaccess != -2 && $cpaccess != -3}-->
</div>
<!--{/if}-->

<footer>{cells common/footer/copyright} {lang copyright}</footer>
<script>
	var cookiepre = '{$cookiepre}';
	if(self.parent.frames.length != 0) {
		self.parent.location=document.location;
	}
	init_darkmode();
</script>

</body>
</html>
