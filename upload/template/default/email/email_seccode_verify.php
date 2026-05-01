<?php exit('Access Denied');?>
<!--{subtemplate email/header}-->
<br />
{lang hello}
<br />
{lang email_seccode_code}
<br /><br />

<p style="background-color: #c9c9c9;width: max-content;padding: 10px 20px;"><strong>{$var['seccode']}</strong></p>

<br />
<p>{lang email_seccode_verify_msg}</p>

<br /><br />
</body>
</html>