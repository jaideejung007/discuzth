<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

$file = childfile('tools/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function jsinsertunit() {

	?>
	<script type="text/JavaScript">
            function isUndefined(variable) {
                return typeof variable == 'undefined' ? true : false;
            }

            function insertunit(text, obj) {
                if (!obj) {
                    obj = 'jstemplate';
                }
                $(obj).focus();
                if (!isUndefined($(obj).selectionStart)) {
                    var opn = $(obj).selectionStart + 0;
                    $(obj).value = $(obj).value.substr(0, $(obj).selectionStart) + text + $(obj).value.substr($(obj).selectionEnd);
                } else if (document.selection && document.selection.createRange) {
                    var sel = document.selection.createRange();
                    sel.text = text.replace(/\r?\n/g, '\r\n');
                    sel.moveStart('character', -strlen(text));
                } else {
                    $(obj).value += text;
                }
            }
	</script>
	<?php

}

?>