/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

function redirect(url) {
	window.location.replace(url);
}

function scrollTopBody() {
	return Math.max(document.documentElement.scrollTop, document.body.scrollTop);
}

function checkAll(type, form, value, checkall, changestyle) {
	var checkall = checkall ? checkall : 'chkall';
	for (var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if (type == 'option' && e.type == 'radio' && e.value == value && e.disabled != true) {
			e.checked = true;
		} else if (type == 'value' && e.type == 'checkbox' && e.getAttribute('chkvalue') == value) {
			e.checked = form.elements[checkall].checked;
			if (changestyle) {
				multiupdate(e);
			}
		} else if (type == 'prefix' && e.name && e.name != checkall && (!value || (value && e.name.match(value)))) {
			e.checked = form.elements[checkall].checked;
			if (changestyle) {
				if (e.parentNode && e.parentNode.tagName.toLowerCase() == 'li') {
					e.parentNode.className = e.checked ? 'checked' : '';
				}
				if (e.parentNode.parentNode && e.parentNode.parentNode.tagName.toLowerCase() == 'div') {
					e.parentNode.parentNode.className = e.checked ? 'item checked' : 'item';
				}
			}
		}
	}
}

function altStyle(obj, disabled) {
	function altStyleClear(obj) {
		var input, lis, i;
		lis = obj.parentNode.getElementsByTagName('li');
		for (i = 0; i < lis.length; i++) {
			lis[i].className = '';
		}
	}

	var disabled = !disabled ? 0 : disabled;
	if (disabled) {
		return;
	}

	var input, lis, i, cc, o;
	cc = 0;
	lis = obj.getElementsByTagName('li');
	for (i = 0; i < lis.length; i++) {
		lis[i].onclick = function (e) {
			o = e.target.tagName;
			altKey = e.altKey;
			if (cc) {
				return;
			}
			cc = 1;
			input = this.getElementsByTagName('input')[0];
			if (input.getAttribute('type') == 'checkbox' || input.getAttribute('type') == 'radio') {
				if (input.getAttribute('type') == 'radio') {
					altStyleClear(this);
				}

				if (o != 'INPUT' && input.onclick) {
					input.click();
				}
				if (this.className != 'checked') {
					this.className = 'checked';
					input.checked = true;
				} else {
					this.className = '';
					input.checked = false;
				}
				if (altKey && input.name.match(/^multinew\[\d+\]/)) {
					miid = input.id.split('|');
					mi = 0;
					while ($(miid[0] + '|' + mi)) {
						$(miid[0] + '|' + mi).checked = input.checked;
						if (input.getAttribute('type') == 'radio') {
							altStyleClear($(miid[0] + '|' + mi).parentNode);
						}
						$(miid[0] + '|' + mi).parentNode.className = input.checked ? 'checked' : '';
						mi++;
					}
				}
			}
		};
		lis[i].onmouseup = function (e) {
			cc = 0;
		}
	}
}

var addrowdirect = 0;
var addrowkey = 0;

function addrow(obj, type) {
	var table = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
	if (!addrowdirect) {
		var row = table.insertRow(obj.parentNode.parentNode.parentNode.rowIndex);
	} else {
		var row = table.insertRow(obj.parentNode.parentNode.parentNode.rowIndex + 1);
	}
	var typedata = rowtypedata[type];
	for (var i = 0; i <= typedata.length - 1; i++) {
		var cell = row.insertCell(i);
		cell.colSpan = typedata[i][0];
		var tmp = typedata[i][1];
		if (typedata[i][2]) {
			cell.className = typedata[i][2];
		}
		tmp = tmp.replace(/\{(n)\}/g, function ($1) {
			return addrowkey;
		});
		tmp = tmp.replace(/\{(\d+)\}/g, function ($1, $2) {
			return addrow.arguments[parseInt($2) + 1];
		});
		cell.innerHTML = tmp;
	}
	addrowkey++;
	addrowdirect = 0;
}

function deleterow(obj) {
	var table = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
	var tr = obj.parentNode.parentNode.parentNode;
	table.deleteRow(tr.rowIndex);
}

function dropmenu(obj) {
	showMenu({'ctrlid': obj.id, 'menuid': obj.id + 'child', 'evt': 'mouseover'});
	$(obj.id + 'child').style.top = (parseInt($(obj.id + 'child').style.top) - Math.max(document.body.scrollTop, document.documentElement.scrollTop)) + 'px';
	$(obj.id + 'child').style.left = (parseInt($(obj.id + 'child').style.left) - Math.max(document.body.scrollLeft, document.documentElement.scrollLeft)) + 'px';
}

function insertunit(obj, text, textend) {
	obj.focus();
	textend = isUndefined(textend) ? '' : textend;
	if (!isUndefined(obj.selectionStart)) {
		var opn = obj.selectionStart + 0;
		if (textend != '') {
			text = text + obj.value.substring(obj.selectionStart, obj.selectionEnd) + textend;
		}
		obj.value = obj.value.substr(0, obj.selectionStart) + text + obj.value.substr(obj.selectionEnd);
		obj.selectionStart = opn + strlen(text);
		obj.selectionEnd = opn + strlen(text);
	} else if (document.selection && document.selection.createRange) {
		var sel = document.selection.createRange();
		if (textend != '') {
			text = text + sel.text + textend;
		}
		sel.text = text.replace(/\r?\n/g, '\r\n');
		sel.moveStart('character', -strlen(text));
	} else {
		obj.value += text;
	}
	obj.focus();
}

var heightag = BROWSER.chrome ? 4 : 0;

function textareakey(obj, event) {
	if (event.keyCode == 9) {
		insertunit(obj, '\t');
		doane(event);
	}
}

function textareasize(obj, op) {
	if (!op) {

	} else {
		if (obj.style.position == 'absolute') {
			obj.style.position = '';
			obj.style.width = '';
			obj.parentNode.style.height = '';
			obj.style.resize = 'none';
		} else {
			obj.parentNode.style.height = obj.parentNode.offsetHeight + 'px';
			obj.style.width = '90%';
			obj.style.position = 'absolute';
			obj.style.resize = 'vertical';
		}
	}
}

function showanchor(obj) {
	var navs = $('submenu').getElementsByTagName('li');
	for (var i = 0; i < navs.length; i++) {
		if (navs[i].id.substr(0, 4) == 'nav_' && navs[i].id != obj.id) {
			if ($(navs[i].id.substr(4))) {
				navs[i].className = '';
				$(navs[i].id.substr(4)).style.display = 'none';
				if ($(navs[i].id.substr(4) + '_tips')) $(navs[i].id.substr(4) + '_tips').style.display = 'none';
			}
		}
	}
	obj.className = 'current';
	currentAnchor = obj.id.substr(4);
	$(currentAnchor).style.display = '';
	if ($(currentAnchor + '_tips')) $(currentAnchor + '_tips').style.display = '';
	if ($(currentAnchor + 'form')) {
		$(currentAnchor + 'form').anchor.value = currentAnchor;
	} else if ($('cpform')) {
		$('cpform').anchor.value = currentAnchor;
	}
}

function updatecolorpreview(obj) {
	$(obj).style.background = $(obj + '_v').value;
}

function entersubmit(e, name) {
	if (loadUserdata('is_blindman')) {
		return false;
	}
	e = e ? e : event;
	if (e.keyCode != 13) {
		return;
	}
	var tag = e.target.tagName;
	if (tag != 'TEXTAREA') {
		doane(e);
		if ($('submit_' + name).offsetWidth) {
			$('formscrolltop').value = document.documentElement.scrollTop;
			$('submit_' + name).click();
		}
	}
}

function parsetag(tag) {
	var parse = function (tds) {
		for (var i = 0; i < tds.length; i++) {
			if (tds[i].getAttribute('s') == '1') {
				tds[i].innerHTML = tds[i].innerHTML.replace(/(^|>)([^<]+)(?=<|$)/ig, function ($1, $2, $3) {
					if (tag && $3.toLowerCase().indexOf(tag.toLowerCase()) != -1) {
						re = new RegExp(tag, "ig");
						$3 = $3.replace(re, '<font class="highlight">$&</font>');
					}
					return $2 + $3;
				});
			}
		}
	};
	parse(document.body.getElementsByTagName('td'));
	parse(document.body.getElementsByTagName('span'));
}

function sdisplay(id, obj) {
	obj.innerHTML = $(id).style.display == 'none' ? '<img src="static/image/admincp/desc.gif" style="vertical-align:middle" />' : '<img src="static/image/admincp/add.gif" style="vertical-align:middle" />';
	display(id);
}

if (ISFRAME) {
	try {
		_attachEvent(document.documentElement, 'keydown', parent.resetEscAndF5);
	} catch (e) {
	}
}

var multiids = new Array();

function multiupdate(obj) {
	v = obj.value;
	if (obj.checked) {
		multiids[v] = v;
	} else {
		multiids[v] = null;
	}
}

function getmultiids() {
	var ids = '', comma = '';
	for (i in multiids) {
		if (multiids[i] != null) {
			ids += comma + multiids[i];
			comma = ',';
		}
	}
	return ids;
}


function toggle_group(oid, obj, conf, op) {
	obj = obj ? obj : $('a_' + oid);
	op = op ? op : '';
	if (!conf) {
		var conf = {'show': '[-]', 'hide': '[+]'};
	}
	var obody = $(oid);
	if (obody.style.display == 'none' || op == 'show') {
		obody.style.display = '';
		obj.innerHTML = conf.show;
	} else {
		obody.style.display = 'none';
		obj.innerHTML = conf.hide;
	}
}

function show_all() {
	var tbodys = $("cpform").getElementsByTagName('tbody');
	for (var i = 0; i < tbodys.length; i++) {
		var re = /^group_(\d+)$/;
		var matches = re.exec(tbodys[i].id);
		if (matches != null) {
			tbodys[i].style.display = '';
			$('a_group_' + matches[1]).innerHTML = '[-]';
		}
	}
}

function hide_all() {
	var tbodys = $("cpform").getElementsByTagName('tbody');
	for (var i = 0; i < tbodys.length; i++) {
		var re = /^group_(\d+)$/;
		var matches = re.exec(tbodys[i].id);
		if (matches != null) {
			tbodys[i].style.display = 'none';
			$('a_group_' + matches[1]).innerHTML = '[+]';
		}
	}
}

function show_all_hook(prefix, tagname) {
	var tbodys = $("cpform").getElementsByTagName(tagname);
	for (var i = 0; i < tbodys.length; i++) {
		var re = new RegExp('^' + prefix + '(.+)$');
		var matches = re.exec(tbodys[i].id);
		if (matches != null) {
			tbodys[i].style.display = '';
			$('a_' + prefix + matches[1]).innerHTML = '[-]';
		}
	}
}

function hide_all_hook(prefix, tagname) {
	var tbodys = $("cpform").getElementsByTagName(tagname);
	for (var i = 0; i < tbodys.length; i++) {
		var re = new RegExp('^' + prefix + '(.+)$');
		var matches = re.exec(tbodys[i].id);
		if (matches != null) {
			tbodys[i].style.display = 'none';
			$('a_' + prefix + matches[1]).innerHTML = '[+]';
		}
	}
}

function srchforum() {
	var fname = $('srchforumipt').value.toLowerCase();
	if (!fname) return false;
	var inputs = $("cpform").getElementsByTagName('input');
	for (var i = 0; i < inputs.length; i++) {
		if (inputs[i].name.match(/^name\[\d+\]$/)) {
			if (inputs[i].value.toLowerCase().indexOf(fname) !== -1) {
				inputs[i].parentNode.parentNode.parentNode.parentNode.style.display = '';
				inputs[i].parentNode.parentNode.parentNode.style.background = '#eee';
				window.scrollTo(0, fetchOffset(inputs[i]).top - 100);
				return false;
			}
		}
	}
	return false;
}

function setfaq(obj, id) {
	if (!$(id)) {
		return;
	}
	$(id).style.display = '';
	if (!obj.onmouseout) {
		obj.onmouseout = function () {
			$(id).style.display = 'none';
		}
	}
}

function floatbottom(id) {
	if (!$(id)) {
		return;
	}
	$(id).style.position = 'fixed';
	$(id).style.bottom = '0';
	$(id).parentNode.style.paddingBottom = '15px';
	window.onscroll = function () {
		var scrollLeft = Math.max(document.documentElement.scrollLeft, document.body.scrollLeft);
		$(id).style.marginLeft = '-' + scrollLeft + 'px';
	};
	$(id).style.display = '';
}

function multisubmit(id, msg, url) {
	var max = $(id).multi.value.split(',').length;
	if (multiStep >= max) {
		showDialog('<div class=infotitle2 style="padding:5px 10px">' + msg + '</div>', 'info', $L('admincp_notice'));
		setTimeout(function () {
			location.reload();
			parent.location.reload();
		}, 3000);
		return;
	}
	showDialog('<div class=infotitle1 style=\"padding:5px 10px\">' + $L('admincp_submit_waiting') + '</div>', 'info', $L('admincp_notice'));
	var re = /multinew\[(\d+)\]/;
	var x = '';
	for (var i = 0; i < $(id).elements.length; i++) {
		var matches = re.exec($(id).elements[i].name);
		if (matches != null) {
			if (parseInt(matches[1]) != parseInt(multiStep)) {
				$(id).elements[i].disabled = true;
			} else {
				$(id).elements[i].disabled = false;
			}
		}
	}
	$(id).submit();
	return false;
}

function multiselect(id) {
	var multi = '', d = '';
	var re = /multi\[\]/;
	for (var i = 0; i < $(id).elements.length; i++) {
		var matches = re.exec($(id).elements[i].name);
		if (matches != null) {
			if ($(id).elements[i].checked) {
				multi += d + $(id).elements[i].value;
				d = ',';
			}
		}
	}
	var newlocation = location.href.replace(/multi=(.+?)$/g, 'multi=' + multi);
	if (newlocation == location.href) {
		location.href = document.location.origin + document.location.pathname + document.location.search + '&multi=' + multi + '#' + document.location.hash;
	} else {
		location.href = newlocation;
	}
}

var sethtml_id = null;

function sethtml(id) {
	$(id).className = 'txt html';
	$(id).contentEditable = true;
	$(id).onkeyup = function () {
		$(id + '_v').value = $(id).innerHTML;
	};
	var curvalue = $(id).innerHTML;

	var div = document.createElement('div');
	div.id = id + '_c_menu';
	div.style.display = 'none';
	div.innerHTML = '<iframe id="' + id + '_c_frame" src="" frameborder="0" width="210" height="148" scrolling="no"></iframe>';
	$(id).parentNode.appendChild(div);

	var btn = document.createElement('input');
	btn.id = id + '_c';
	btn.type = 'button';
	btn.className = 'htmlbtn c';
	if (curvalue.search(/<font/ig) !== -1) {
		btn.className = 'htmlbtn c current';
	}
	btn.onclick = function () {
		$(id + '_c_frame').src = 'static/image/admincp/getcolor.htm?||sethtml_color';
		showMenu({'ctrlid': id + '_c'});
		sethtml_id = id;
	};
	$(id).parentNode.appendChild(btn);

	var btn = document.createElement('input');
	btn.id = id + '_b';
	btn.type = 'button';
	btn.className = 'htmlbtn b';
	if (curvalue.search(/<b>/ig) !== -1) {
		btn.className = 'htmlbtn b current';
	}
	btn.onclick = function () {
		var oldvalue = $(id).innerHTML;
		$(id).innerHTML = preg_replace(['<b>', '</b>'], '', $(id).innerHTML);
		if (oldvalue == $(id).innerHTML) {
			$(id + '_b').className = 'htmlbtn b current';
			$(id).innerHTML = '<b>' + $(id).innerHTML + '</b>';
		} else {
			$(id + '_b').className = 'htmlbtn b';
		}
		$(id + '_v').value = $(id).innerHTML;
	};
	$(id).parentNode.appendChild(btn);

	var btn = document.createElement('input');
	btn.id = id + '_i';
	btn.type = 'button';
	btn.className = 'htmlbtn i';
	if (curvalue.search(/<i>/ig) !== -1) {
		btn.className = 'htmlbtn i current';
	}
	btn.onclick = function () {
		var oldvalue = $(id).innerHTML;
		$(id).innerHTML = preg_replace(['<i>', '</i>'], '', $(id).innerHTML);
		if (oldvalue == $(id).innerHTML) {
			$(id + '_i').className = 'htmlbtn i current';
			$(id).innerHTML = '<i>' + $(id).innerHTML + '</i>';
		} else {
			$(id + '_i').className = 'htmlbtn i';
		}
		$(id + '_v').value = $(id).innerHTML;
	};
	$(id).parentNode.appendChild(btn);

	var btn = document.createElement('input');
	btn.id = id + '_u';
	btn.type = 'button';
	btn.style.textDecoration = 'underline';
	btn.className = 'htmlbtn u';
	if (curvalue.search(/<u>/ig) !== -1) {
		btn.className = 'htmlbtn u current';
	}
	btn.onclick = function () {
		var oldvalue = $(id).innerHTML;
		$(id).innerHTML = preg_replace(['<u>', '</u>'], '', $(id).innerHTML);
		if (oldvalue == $(id).innerHTML) {
			$(id + '_u').className = 'htmlbtn u current';
			$(id).innerHTML = '<u>' + $(id).innerHTML + '</u>';
		} else {
			$(id + '_u').className = 'htmlbtn u';
		}
		$(id + '_v').value = $(id).innerHTML;
	};
	$(id).parentNode.appendChild(btn);
}

function sethtml_color(color) {
	$(sethtml_id).innerHTML = preg_replace(['<font[^>]+?>', '</font>'], '', $(sethtml_id).innerHTML);
	if (color != 'transparent') {
		$(sethtml_id + '_c').className = 'htmlbtn c current';
		$(sethtml_id).innerHTML = '<font color=' + color + '>' + $(sethtml_id).innerHTML + '</font>';
	} else {
		$(sethtml_id + '_c').className = 'htmlbtn c';
	}
	$(sethtml_id + '_v').value = $(sethtml_id).innerHTML;
}

function uploadthreadtypexml(formobj, formaction) {
	formobj.action = formaction;
	formobj.submit();
}

function showretheader(title, jsmenu) {
	if (ISFRAME && !parent.document.getElementById('leftmenu') && !parent.parent.document.getElementById('leftmenu')) {
		document.write('<div class="retheader">' +
		    '<a id="retheader" onmouseover="showMenu({\'ctrlid\':this.id})" href="' + document.location.origin + document.location.pathname + document.location.search + '&frames=yes">' +
		    '<i></i> ' + title + '</a><a class="index" href="index.php" target="_blank"></a>' +
		    '</div><div id="retheader_menu" style="display:none"></div>');
		appendscript(jsmenu);
	}
}

function perm_search(type, kw) {
	let id = 'permitem_menu';
	if (!$(id)) {
		var div = document.createElement('div');
		div.id = id;
		div.style.display = 'none';
		div.innerHTML = '';
		$('append_parent').parentNode.appendChild(div);
	}
	showMenu({'menuid': id, 'duration': 3, 'pos': '00', 'mtype': 'win'});
	ajaxget(admincpfilename + '?action=forums&mod=forum&operation=perm_get_item&itemtype=' + type + '&kw=' + ($(kw).type == 'text' ? encodeURIComponent($(kw).value) : '') + '&kwid=' + kw, id);
}

function perm_enter(event, obj) {
	let theEvent = event || window.event;
	let keyCode = theEvent.keyCode || theEvent.which || theEvent.charCode;
	if (keyCode == 13) {
		obj.nextElementSibling.click();
		doane(event);
	}
}

function perm_show_item(data) {
	var type = data['type'];
	let id = 'permitem_menu';
	$(type + '_show').innerHTML = '';


	// 窗口拖拽
	const permitemMenu = $(id);
	let isDragging = false;
	let initialX, initialY, initialOffsetX, initialOffsetY;

	if (permitemMenu) {
		permitemMenu.style.cursor = 'move';
		permitemMenu.style.position = 'absolute';

		const isInPermitemArea = (element) => {
			while (element && element !== permitemMenu) {
				if (element.classList.contains('permitem')) {
					return true;
				}
				element = element.parentElement;
			}
			return false;
		};

		permitemMenu.addEventListener('mousedown', (e) => {
			if (isInPermitemArea(e.target)) return;
			isDragging = true;
			initialX = e.clientX;
			initialY = e.clientY;
			initialOffsetX = parseInt(permitemMenu.style.left) || 0;
			initialOffsetY = parseInt(permitemMenu.style.top) || 0;
		});

		document.addEventListener('mousemove', (e) => {
			if (isDragging) {
				const deltaX = e.clientX - initialX;
				const deltaY = e.clientY - initialY;
				permitemMenu.style.left = (initialOffsetX + deltaX) + 'px';
				permitemMenu.style.top = (initialOffsetY + deltaY) + 'px';
			}
		});

		document.addEventListener('mouseup', () => {
			isDragging = false;
		});

		permitemMenu.addEventListener('mouseover', (e) => {
			e.target.style.cursor = isInPermitemArea(e.target) ? 'default' : 'move';
		});
	}

	if (data['data'].length) {
		for (i in data['data']) {
			var p = document.createElement('p');
			var a = document.createElement('a');
			let id = data['data'][i][0];
			let name = data['data'][i][1];
			let addname = data['data'][i][3] ? data['data'][i][3] : data['data'][i][1];
			let t = data['data'][i][2];
			a.onclick = function () {
				if (!data['addVariable']) {
					perm_add_item('g' + type, id, type, [addname, t + id]);
				} else {
					perm_add_item_component(addname, t, id, data['addVariable'], data['kwid']);
				}
			};
			a.href = 'javascript:;';
			a.dataid = id;
			a.innerHTML = name;
			if (data['multi']) {
				var input = document.createElement('input');
				input.type = 'checkbox';
				input.setAttribute('level', data['data'][i][4]);
				input.style.marginLeft = (data['data'][i][4] * 15) + 'px';
				p.appendChild(input);
			}
			p.appendChild(a);
			var span = document.createElement('span');
			span.innerHTML = '(id: ' + id + ')';
			p.appendChild(span);
			$(type + '_show').appendChild(p);
		}
	} else {
		$(type + '_show').innerHTML = '<a>' + $L('admincp_perm_item_no_data') + '</a>';
	}
	if (data['multi']) {
		var btn = document.createElement('button');
		btn.onclick = function () {
			perm_add_multi(type, data['addVariable'], data['kwid']);
		};
		btn.className = 'btn';
		btn.innerHTML = $L('admincp_perm_add');
		$(id + '_footer').appendChild(btn);
	}
	setMenuPosition(null, id, '00');
	document.querySelectorAll('#' + type + '_show input[type=checkbox]').forEach(function (o) {
		o.onclick = function () {
			var cItem = o;
			var cLevel = cItem.getAttribute('level');
			var cRootDataid = cItem.nextElementSibling.dataid;
			var removeRoot = false;
			if (o.checked) {
				if (!cItem.getAttribute('_byRoot')) {
					cItem.setAttribute('_root', cRootDataid);
				} else {
					cRootDataid = cItem.getAttribute('_byRoot');
				}
				cItem.removeAttribute('_disabled', cRootDataid);
			} else {
				if (cItem.getAttribute('_root')) {
					cItem.removeAttribute('_root');
					removeRoot = true;
				}
				if (cItem.getAttribute('_byRoot')) {
					cItem.setAttribute('_disabled', cRootDataid);
				}
			}
			do {
				try {
					cItem = cItem.parentNode.nextElementSibling.childNodes[0];
				} catch (e) {
					break;
				}
				if (!cItem || cItem.tagName != 'INPUT') {
					break;
				}
				if (cItem.getAttribute('level') <= cLevel) {
					break;
				}
				var cDataid = cItem.nextElementSibling.dataid;
				cItem.checked = o.checked;
				if (o.checked) {
					cItem.removeAttribute('_disabled');
					cItem.setAttribute('_byRoot', cRootDataid);
					cItem.removeAttribute('_root');
				} else if (removeRoot) {
					cItem.removeAttribute('_byRoot');
					cItem.removeAttribute('_disabled');
				} else if (cItem.getAttribute('_byRoot')) {
					cItem.setAttribute('_disabled', cDataid);
				}
			} while (true);
		};
	});
}

function perm_add_item(row, id, t, r) {
	toggle_group('g' + t, null, null, 'show');
	if (document.querySelector('input[name=chkall' + t + id + ']')) {
		return;
	}

	var row = $(row).insertRow(-1);
	row.insertCell(0).innerHTML = '<input class="checkbox" type="checkbox" name="chkall' + t + id + '" onclick="checkAll(\'value\', this.form, \'' + t + id + '\', \'chkall' + t + id + '\')" id="chkall' + t + '_' + id + '">';
	row.insertCell(1).innerHTML = '<a href="javascript:;" onclick="perm_del_item(this)">[x]</a> ' + r[0];
	row.insertCell(2).innerHTML = r[1];
	row.cells[2].className = 'lightfont';
	row.insertCell(3).innerHTML = '<input class="checkbox" type="checkbox" name="viewperm[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	row.insertCell(4).innerHTML = '<input class="checkbox" type="checkbox" name="postperm[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	row.insertCell(5).innerHTML = '<input class="checkbox" type="checkbox" name="replyperm[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	row.insertCell(6).innerHTML = '<input class="checkbox" type="checkbox" name="getattachperm[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	row.insertCell(7).innerHTML = '<input class="checkbox" type="checkbox" name="postattachperm[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	row.insertCell(8).innerHTML = '<input class="checkbox" type="checkbox" name="postimageperm[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	for (var i = 0; i < extraperms.length; i++) {
		row.insertCell(9 + i).innerHTML = '<input class="checkbox" type="checkbox" name="' + extraperms[i] + '[]" value="' + r[1] + '" chkvalue="' + t + id + '">';
	}
}

function perm_add_item_component(addname, t, id, addVariable, kwid) {
	if ($(addVariable) && $(addVariable).type == 'textarea') {
		if ($(addVariable).value === '') {
			$(addVariable).value = t + id;
		} else {
			$(addVariable).value += ' or ' + t + id;
		}
		return;
	}
	var obj = $(kwid).parentNode.parentNode;
	if (obj.querySelector('input[value="' + t + id + '"]')) {
		return;
	}

	var label = document.createElement('label');
	label.innerHTML = '<input name="' + addVariable + '[]" value="' + t + id + '" onclick="perm_preview(\'' + addVariable + '\', \'' + addname + '\', 0)" type="checkbox" checked="">' + addname;
	obj.appendChild(label);
	perm_preview(addVariable, addname, 0);
}

function perm_del_item(o) {
	var tr = o.parentNode.parentNode;
	tr.parentNode.removeChild(tr);
}

function perm_add_multi(type, addVariable, kwid) {
	var roots = [];
	var name = '';
	document.querySelectorAll('#permitem_menu input[type=checkbox]').forEach(function (o) {
		var root = o.getAttribute('_root');
		var byroot = o.getAttribute('_byroot');
		var disabled = o.getAttribute('_disabled');
		if (root) {
			roots[root] = [];
			name = o.nextElementSibling.innerHTML;
		} else if (byroot) {
			root = byroot;
		} else {
			return;
		}
		if (disabled) {
			roots[root].push(disabled);
		}
	});
	for (var i in roots) {
		var t = 'O' + i;
		var id = '[' + roots[i].join(',') + ']';

		if (!addVariable) {
			perm_add_item('g' + type, i, type, ['<i>' + name + '</i>', t + id]);
		} else {
			perm_add_item_component('<i>' + name + '</i>', t, id, addVariable, kwid);
		}
	}
}

function perm_add_formula(id, v) {
	var obj = $(id);
	if (obj.value === '') {
		obj.value = v;
	} else {
		obj.value += ' or ' + v;
	}
	perm_preview(id, obj.value, 1);
}

function perm_preview(id, value, t) {
	id = 'ppreview_' + id;
	if (!t) {
		let s = '[' + value + '] ';
		if ($(id).innerHTML.indexOf(s) !== -1) {
			$(id).innerHTML = $(id).innerHTML.replace(s, '');
		} else {
			$(id).innerHTML += s;
		}
	} else {
		$(id).innerHTML = value;
	}
}

function userselect(srcid, kw) {
	var kw = !kw ? '' : kw;
	let id = 'userselect_menu';
	if (!$(id)) {
		var div = document.createElement('div');
		div.id = id;
		div.style.display = 'none';
		div.innerHTML = '';
		$('append_parent').parentNode.appendChild(div);
		showMenu({'menuid': id, 'duration': 3, 'pos': '00', 'mtype': 'win', 'drag': true});
	}
	ajaxget(admincpfilename + '?action=misc&operation=userselect&srcid=' + srcid + '&kw=' + encodeURIComponent(kw), id, null, null, null, function() {
		$(id).style.left = '400px';
	});
}

function userselect_delay(srcid, kw) {
	clearTimeout(this.timer);
	this.timer = setTimeout(function () {
		userselect(srcid, kw);
	}, 500);
}

function userselect_click(srcid, value, text) {
	if($(srcid).querySelectorAll('input[value="' + value + '"]').length > 0) {
		return;
	}
	var obj = document.createElement('span');
	obj.innerHTML = '<input name="' + srcid + '[]" value="' + value + '" type="hidden"> ' + text + ' <a href="javascript:;" onclick="userselect_del(this)">[' + $L('delete') + ']</a>&nbsp;&nbsp;&nbsp;';
	$(srcid).appendChild(obj);
}

function userselect_del(obj) {
	obj.parentNode.parentNode.removeChild(obj.parentNode);
}