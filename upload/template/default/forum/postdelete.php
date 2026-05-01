<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{if empty($_GET['infloat'])}-->
<div id="ct" class="wp cl">
	<div class="mn">
		<div class="bm bw0">
<!--{/if}-->

<form method="post" autocomplete="off" id="postdeleteform" action="forum.php?mod=misc&action=postdelete&tid=$post[tid]&pid=$_GET[pid]&extra=$extra{if !empty($_GET[page])}&page=$_GET[page]{/if}&postdeletesubmit=yes&infloat=yes" onsubmit="{if !empty($_GET['infloat'])}ajaxpost('postdeleteform', 'return_$_GET['handlekey']', 'return_$_GET['handlekey']', 'onerror');return false;{/if}">
	<div class="f_c">
		<h3 class="flb">
			<em id="return_$_GET['handlekey']">{lang postdelete}</em>
			<span>
				<!--{if !empty($_GET['infloat'])}--><a href="javascript:;" class="flbc" onclick="hideWindow('$_GET['handlekey']')" title="{lang close}">{lang close}</a><!--{/if}-->
			</span>
		</h3>
		<input type="hidden" name="formhash" id="formhash" value="{FORMHASH}" />
		<input type="hidden" name="handlekey" value="$_GET['handlekey']" />
        <div class="c{if empty($_GET['infloat'])} mbm{/if}">
            {lang postdelete_tip}
        </div>
		<div class="o pns cl">
			<button type="submit" id="postdeletesubmit" class="pn pnc z" value="true" name="postdeletesubmit"><span>{lang confirms}</span></button>
            <button type="button" class="pn pnc z" onclick="hideWindow('$_GET['handlekey']')" title="{lang cancel}"><span>{lang cancel}</span></button>
		</div>
	</div>
</form>

<!--{if empty($_GET['infloat'])}-->
		</div>
	</div>
</div>
<!--{/if}-->
<!--{template common/footer}-->