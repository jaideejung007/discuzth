<?php exit('Access Denied');?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><!--{if !empty($navtitle)}-->$navtitle - <!--{/if}--><!--{if empty($nobbname)}--> $_G['setting']['bbname'] - <!--{/if}--> Powered by Discuz!</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: fangsong;
            height: 60vh;
            margin: 0;
            background-color: #ffffff;
        }
        .container {
            text-align: center;
            padding: 20px;
        }
        .footer-tip {
            position: absolute;
            width: 200px;
            height: auto;
            bottom: 20px;
            right: 30px;
            animation: shake 2s infinite;
        }

        @keyframes shake {
            0% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="container">
    <div>
        <img src="<!--{if $_G['style']['boardimg']}-->{$_G['style']['boardimg']}<!--{else}-->static/image/common/logo.png<!--{/if}-->" alt="Discuz！" style="width: 150px;height: auto;">
        <h2>温馨提示</h2>
        <p>为保证您的使用体验</p>
        <p>请您点击下方"<span style="color: green;"><strong>使用完整服务</strong></span>"进行授权</p>
        <p>本次授权将获取您的<span style="color: blue;">微信昵称、头像</span></p>
    </div>
    <div>
        <img src="static/image/wechat/tip.png" alt="Discuz！" class="footer-tip">
    </div>
</div>
</body>
</html>
