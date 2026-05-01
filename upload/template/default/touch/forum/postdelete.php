<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="tip loginbox loginpop p5" id="floatlayout_topicadmin">
	<form id="postdeleteform" method="post" autocomplete="off" action="forum.php?mod=misc&action=postdelete&tid=$post[tid]&pid=$_GET[pid]&extra=$extra{if !empty($_GET[page])}&page=$_GET[page]{/if}&postdeletesubmit=yes&infloat=yes&mobile=2" >
		<input type="hidden" name="formhash" id="formhash" value="{FORMHASH}" />
		<input type="hidden" name="handlekey" value="$_GET['handlekey']" />
		<dt>
			<p>{lang postdelete_tip}</p>
		</dt>
		<dd><input type="submit" name="postdeletesubmit" id="postdeletesubmit"  value="{lang confirms}" class="formdialog button z"><a href="javascript:;" onclick="popup.close();" class="button y">{lang cancel}</a></dd>
	</form>
</div>
<!--{template common/footer}-->