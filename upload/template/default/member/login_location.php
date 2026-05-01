<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{if !empty($_GET['infloat'])}-->
<h3 class="flb">
    <div class="loadicon"></div>
</h3>
<!--{/if}-->
<script type="text/javascript" reload="1">
	location.href = '$url';
</script>
<!--{template common/footer}-->