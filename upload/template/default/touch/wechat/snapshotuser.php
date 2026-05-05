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
        <h2>ข้อแนะนำ</h2>
        <p>เพื่อประสบการณ์การใช้งานที่ดีที่สุดของคุณ</p>
        <p>กรุณาคลิกที่ "<span style="color: green;"><strong>ใช้งานบริการเต็มรูปแบบ</strong></span>" ด้านล่างเพื่ออนุญาตสิทธิ์</p>
        <p>การอนุญาตสิทธิ์นี้จะเข้าถึง <span style="color: blue;">ชื่อเล่นและรูปโปรไฟล์ WeChat</span> ของคุณ</p>
    </div>
    <div>
        <img src="static/image/wechat/tip.png" alt="Discuz！" class="footer-tip">
    </div>
</div>
</body>
</html>
