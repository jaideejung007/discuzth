<?php exit('Access Denied');?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<title><!--{if !empty($navtitle)}-->$navtitle - <!--{/if}--><!--{if empty($nobbname)}--> $_G['setting']['bbname'] - <!--{/if}--> Powered by Discuz!</title>
	<script type="text/javascript">var STYLEID = '{STYLEID}', STATICURL = '{STATICURL}', IMGDIR = '{IMGDIR}', VERHASH = '{VERHASH}', charset = '{CHARSET}', discuz_uid = '$_G[uid]', cookiepre = '{$_G[config][cookie][cookiepre]}', cookiedomain = '{$_G[config][cookie][cookiedomain]}', cookiepath = '{$_G[config][cookie][cookiepath]}', showusercard = '{$_G[setting][showusercard]}', attackevasive = '{$_G[config][security][attackevasive]}', disallowfloat = '{$_G[setting][disallowfloat]}', creditnotice = '<!--{if $_G['setting']['creditnotice']}-->$_G['setting']['creditnames']<!--{/if}-->', defaultstyle = '$_G[style][defaultextstyle]', REPORTURL = '$_G[currenturl_encode]', SITEURL = '$_G[siteurl]', JSPATH = '$_G[setting][jspath]', CSSPATH = '$_G[setting][csspath]', DYNAMICURL = '{$_G[dynamicurl] or ''}';</script>
	<script type="text/javascript" src="{$_G[setting][jspath]}common.js?{VERHASH}"></script>
	<script type="text/javascript">
		var wechat_checkST = null, wechat_checkCount = 0;
		function wechat_checkstart() {
			wechat_checkST = setTimeout(function () {wechat_check()}, 3000);
		}
		function wechat_check() {
			var x = new Ajax();
			x.get('misc.php?mod=wechat&ac=check&authcode={$authcode}&formhash={FORMHASH}', function(s, x) {
				s = trim(s);
				if(s == '0') {
					wechat_checkstart();
					wechat_checkCount++;
					if (wechat_checkCount >= 30) {
						clearTimeout(wechat_checkST);
						document.getElementById('login-url-qr-refresh').style.display = 'block';
					}
				}else if(s == '1'){
					clearTimeout(wechat_checkST);
					<!--{if strpos($_G['cookie']['wechat_referer'], 'wechat') === false}-->
					location.href="$_G['cookie']['wechat_referer']";
					<!--{else}-->
					location.href="$_G['siteurl']";
					<!--{/if}-->
					return false;
				}else{
					clearTimeout(wechat_checkST);
					document.getElementById('login-url-qr-refresh').style.display = 'block';
				}

			});
		}
		wechat_checkstart();

		function refreshQRCode() {
			location.reload();
		}
	</script>
</head>
<body style="background:rgb(51,51,51);padding:50px;">
<center>
	<p style="color:#fff;font-size:28px;font-family:'microsoft yahei';"><!--{if $_G['uid']}-->{lang wechat_bind}<!--{else}-->{lang wechat_login}<!--{/if}--></p>
	<div style="background:#fff;width:280px;">
		<div id="login-url-qr" style="position: relative;">
			<img src="misc.php?mod=wechat&ac=qrcode&authcode={$authcode}&formhash={FORMHASH}" width="250" height="250" style="padding:15px 0;"/>
			<div id="login-url-qr-refresh" style="display: none;">
				<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); display: flex; justify-content: center; align-items: center;">
					<div onclick="refreshQRCode()" style="width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer; display: flex; justify-content: center; align-items: center;">
						<svg t="1756732593956" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2466" width="60" height="60"><path d="M990.08 425.6a30.72 30.72 0 0 1 0 44.8l-135.68 135.68a30.72 30.72 0 0 1-44.8 0l-135.68-135.68a31.36 31.36 0 1 1 44.8-44.8L832 538.24l113.28-112.64a30.72 30.72 0 0 1 44.8 0z" fill="#ffffff" p-id="2467"></path><path d="M448 928A416 416 0 1 1 864 512a32 32 0 0 1-64 0A352 352 0 1 0 448 864a345.6 345.6 0 0 0 176-47.36 32.64 32.64 0 0 1 43.52 12.16 31.36 31.36 0 0 1-11.52 43.52A414.72 414.72 0 0 1 448 928z" fill="#ffffff" p-id="2468"></path></svg>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div style="width:280px;margin-top:15px;background-color:#232323;border-radius: 100px;color:#fff;
    -moz-border-radius: 100px;
    -webkit-border-radius: 100px;
    box-shadow: inset 0 5px 10px -5px #191919,0 1px 0 0 #444;
    -moz-box-shadow: inset 0 5px 10px -5px #191919,0 1px 0 0 #444;
    -webkit-box-shadow: inset 0 5px 10px -5px #191919,0 1px 0 0 #444;
    ">
		<p style="font-size:16px;line-height:22px;padding-top:10px;"><!--{if $_G['uid']}-->{lang wechat_bind_tip}<!--{else}-->{lang wechat_login_tip}<!--{/if}--></p>
		<p style="font-size:16px;font-family:'microsoft yahei';line-height:18px;padding:10px 0;"><!--{if !empty($navtitle)}-->$navtitle<!--{/if}--><!--{if empty($nobbname)}--> $_G['setting']['bbname']<!--{/if}--></p>
	</div>
</center>
</body>
</html>
