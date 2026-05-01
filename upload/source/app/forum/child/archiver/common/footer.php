<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
?>
	<br/>
	<center>
		<?php echo adshow('footerbanner//1').adshow('footerbanner//2').adshow('footerbanner//3'); ?>
		<div id="footer">
			Powered by <strong><a target="_blank"
			                      href="https://www.discuz.vip">Discuz! <?php echo $_G['setting']['version']; ?>
					Archiver</a></strong> &nbsp; <?php echo lang('template', 'copyright'); ?>
			<br/>
			<br/>
		</div>
	</center>
	</body>
	</html>
<?php output(); ?>