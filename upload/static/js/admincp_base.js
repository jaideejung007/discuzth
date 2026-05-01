/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

function run_toggle(target, styles, source) {
	var rmstyle = styles.shift();
	if (!source) {
		source = target;
	}
	if (rmstyle) {
		if (typeof rmstyle == 'string') {
			target.classList.remove(rmstyle);
		} else {
			for (var i in rmstyle) {
				target.classList.remove(rmstyle[i]);
			}
		}
	}
	if (styles[0]) {
		if (typeof styles[0] == 'string') {
			target.classList.add(styles[0]);
		} else {
			for (var i in styles[0]) {
				target.classList.add(styles[0][i]);
			}
		}
		if (styles.length > 1) {
			function nextstep() {
				source.removeEventListener('transitionend', nextstep);
				run_toggle(target, styles, source);
			}

			source.addEventListener('transitionend', nextstep);
		}
	}
}

function init_darkmode() {
	var dmcookie = getcookie('darkmode');
	var dmdark = 0, dmauto = 1;
	document.querySelector('.darkmode').addEventListener('click', toggledarkmode);
	if (dmcookie && dmcookie.indexOf('a') == -1) {
		dmauto = 0;
		if (dmcookie.indexOf('d') != -1) {
			dmdark = 1;
		}
		switchdmvalue(dmdark, dmauto);
	} else {
		var colormedia = window.matchMedia('(prefers-color-scheme: dark)');
		switchdmvalue(colormedia.matches, dmauto);
		colormedia.addEventListener('change', function () {
			var dmlcookie = getcookie('darkmode');
			if (dmlcookie && dmlcookie.indexOf('a') != -1) {
				switchdmvalue(this.matches, 1);
			}
		});
	}
}

function toggledarkmode() {
	var dmcookie = getcookie('darkmode');
	var dmdark = 0, dmauto = 1;
	var colormedia = window.matchMedia('(prefers-color-scheme: dark)');
	if (dmcookie && dmcookie.indexOf('a') == -1) {
		dmauto = 0;
		if (dmcookie.indexOf('d') != -1) {
			dmdark = 1;
		}
	} else {
		dmdark = colormedia.matches ? 1 : 0;
	}
	if (dmauto) {
		dmauto = dmauto ? 0 : 1;
		dmdark = dmdark ? 0 : 1;
	} else if (colormedia.matches == dmdark) {
		dmauto = 1;
	} else {
		dmdark = dmdark ? 0 : 1;
	}
	switchdmvalue(dmdark, dmauto);
}

function switchdmvalue(ifdark, ifauto) {
	var dmcookie = '';
	var dmmeta = '';
	if (ifdark) {
		document.body.classList.add('st-d');
		document.body.classList.remove('st-l');
		dmcookie = 'd';
		dmmeta = 'dark';
	} else {
		document.body.classList.add('st-l');
		document.body.classList.remove('st-d');
		dmcookie = 'l';
		dmmeta = 'light';
	}
	if (ifauto) {
		document.body.classList.add('st-a');
		dmcookie += 'a';
		dmmeta = 'light dark';
	} else {
		document.body.classList.remove('st-a');
	}
	if (getcookie('darkmode') != dmcookie) {
		setcookie('darkmode', dmcookie);
	}
	if (document.querySelector('meta[name="color-scheme"]').content != dmmeta) {
		document.querySelector('meta[name="color-scheme"]').content = dmmeta;
	}
}

var _qrcodeReturnCode = '';
var qrcodeCheckSwitch = null;
var qrcodeLoaded = false;
var qrcodeCCount = 0;

function qrcodelogin(op) {
	if (op) {
		var sign = Math.floor(Math.random() * (999999 - 100000 + 1)) + 100000;
		if (!qrcodeLoaded) {
			$('qrcodeimg').innerHTML = '<img src="https://api.witframe.com/discuzlogin/qrcode?sign=' + sign + '" />';
			qrcodeLoaded = true;
		}
		$('qrcodebox').style.display = '';
		$('loginform').style.display = 'none';
		qrcodecheck(sign);
	} else {
		$('qrcodebox').style.display = 'none';
		$('loginform').style.display = '';
		clearTimeout(qrcodeCheckSwitch);
	}
}

function qrcodecheck(sign) {
	qrcodeCheckSwitch = setTimeout(function () {
		if ($('qrcodecheck')) {
			$('qrcodecheck').parentNode.removeChild($('qrcodecheck'));
		}
		var scriptNode = document.createElement("script");
		scriptNode.type = "text/javascript";
		scriptNode.id = 'qrcodecheck';
		scriptNode.src = 'https://api.witframe.com/discuzlogin/check?sign=' + sign;
		document.getElementsByTagName('head')[0].appendChild(scriptNode);
		if (_qrcodeReturnCode) {
			$('qrcodeReturnCode').value = _qrcodeReturnCode;
			$('loginform').submit();
			return;
		}
		qrcodeCCount++;
		if (qrcodeCCount > 30) {
			$('qrcodeimg').style.filter = 'opacity(0.2) blur(4px)';
			$('qrcodeimg').addEventListener('click', function (e) {
				var sign = Math.floor(Math.random() * (999999 - 100000 + 1)) + 100000;
				qrcodeLoaded = false;
				qrcodelogin(1);
				$('qrcodeimg').style.filter = '';
			});
		} else {
			qrcodecheck(sign);
		}
	}, 2500);
}