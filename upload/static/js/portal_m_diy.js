/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

var drag = new Drag();
drag.extend({
	'getBlocksTimer' : '',
	'blocks' : [],
	'blockDefaultClass' : [{'key':$L('diy_style_select'),'value':''},{'key':$L('diy_noborder'),'value':'cl_block_bm'},{'key':$L('diy_style') + '1','value':'xbs_1'},{'key':$L('diy_style') + '2','value':'xbs xbs_2'},{'key':$L('diy_style') + '3','value':'xbs xbs_3'},{'key':$L('diy_style') + '4','value':'xbs xbs_4'},{'key':$L('diy_style') + '5','value':'xbs xbs_5'},{'key':$L('diy_style') + '6','value':'xbs xbs_6'},{'key':$L('diy_style') + '7','value':'xbs xbs_7'}],
	'frameDefaultClass' : [{'key':$L('diy_style_select'),'value':''},{'key':$L('diy_noborder'),'value':'cl_frame_bm'},{'key':$L('diy_noborder_frame'),'value':'xfs xfs_nbd'},{'key':$L('diy_style') + '1','value':'xfs xfs_1'},{'key':$L('diy_style') + '2','value':'xfs xfs_2'},{'key':$L('diy_style') + '3','value':'xfs xfs_3'},{'key':$L('diy_style') + '4','value':'xfs xfs_4'},{'key':$L('diy_style') + '5','value':'xfs xfs_5'}],
	setDefalutMenu : function () {
		this.addMenu('default',$L('title'),'drag.openTitleEdit(event)');
		this.addMenu('default', $L('delete'), 'drag.removeBlock(event)');
		this.addMenu('block', $L('property'), 'drag.openBlockEdit(event)');
		this.addMenu('block', $L('data'), 'drag.openBlockEdit(event,"data")');
		this.addMenu('block', $L('update'), 'drag.blockForceUpdate(event)');
		this.addMenu('frame', $L('export'), 'drag.frameExport(event)');
		this.addMenu('default', $L('diy_moveup'), 'drag.moveUp(event)');
		this.addMenu('default', $L('diy_movedown'), 'drag.moveDown(event)');
		this.addMenu('tab', $L('export'), 'drag.frameExport(event)');
	},
	setSampleMenu : function () {
		this.addMenu('block', $L('property'), 'drag.openBlockEdit(event)');
		this.addMenu('block', $L('data'), 'drag.openBlockEdit(event,"data")');
		this.addMenu('block', $L('update'), 'drag.blockForceUpdate(event)');
	},
	openBlockEdit : function (e,op) {
		e = Util.event(e);
		op = (op=='data') ? 'data' : 'block';
		var bid = e.aim.id.replace('cmd_portal_block_','');
		this.removeMenu();
		showWindow('showblock', 'portal.php?mod=portalcp&ac=block&op='+op+'&bid='+bid+'&tpl='+document.diyform.template.value, 'get', -1);
	},
	getDiyClassName : function (id,index) {
		var obj = this.getObjByName(id);
		var ele = $(id);
		var eleClassName = ele.className.replace(/ {2,}/g,' ');
		var className = '',srcClassName = '';
		if (obj instanceof Block) {
			className = eleClassName.split(this.blockClass+' ');
			srcClassName = this.blockClass;
		} else if(obj instanceof Tab) {
			className = eleClassName.split(this.tabClass+' ');
			srcClassName = this.tabClass;
		} else if(obj instanceof Frame) {
			className = eleClassName.split(this.frameClass+' ');
			srcClassName = this.frameClass;
		}
		if (index != null && index<className.length) {
			className = className[index].replace(/^ | $/g,'');
		} else {
			className.push(srcClassName);
		}
		return className;
	},
	getOption : function (arr,value) {
		var html = '';
		for (var i in arr) {
			if (typeof arr[i] == 'function') continue;
			var selected = arr[i]['value'] == value ? ' selected="selected"' : '';
			html += '<option value="'+arr[i]['value']+'"'+selected+'>'+arr[i]['key']+'</option>';
		}
		return html;
	},
	getRule : function (selector,attr) {
		selector = spaceDiy.checkSelector(selector);
		var value = (!selector || !attr) ? '' : spaceDiy.styleSheet.getRule(selector, attr);
		return value;
	},
	moveUp: function (e) {
		e = Util.event(e);
		var id = e.aim.id.replace('cmd_', '');
		var _row = $(id);
		//如果不是第一行，则与上一行交换顺序
		var _node = _row.previousSibling;
		while (_node && _node.nodeType != 1) {
			_node = _node.previousSibling;
		}
		if (_node) {
			this.swapNode(_row, _node);
		}

		//console.log($('frameJdxXD6').parentNode)
	},
	moveDown: function (e) {
		e = Util.event(e);
		var id = e.aim.id.replace('cmd_', '');
		var _row = $(id);
		//如果不是最后一行，则与下一行交换顺序
		var _node = _row.nextSibling;
		while (_node && _node.nodeType != 1) {
			_node = _node.nextSibling;
		}
		if (_node) {
			this.swapNode(_row, _node);
		}
	},
	swapNode: function (node1, node2) {
		//获取父结点
		var _parent = node1.parentNode;
		//获取两个结点的相对位置
		var _t1 = node1.nextSibling;
		var _t2 = node2.nextSibling;
		//将node2插入到原来node1的位置
		if (_t1) _parent.insertBefore(node2, _t1);
		else _parent.appendChild(node2);
		//将node1插入到原来node2的位置
		if (_t2) _parent.insertBefore(node1, _t2);
		else _parent.appendChild(node1);
	},
	formatValue : function(id) {
		var value = '';
		if ($(id)) {
			value = parseInt($(id).value);
			value = isNaN(value) ? '' : value+'px';
		}
		return value;
	},
	saveBlockClassName : function(id,className){
		if (!$('saveblockclassname')){
			var dom  = document.createElement('div');
			dom.innerHTML = '<form id="saveblockclassname" method="post" action=""><input type="hidden" name="classname" value="" />\n\
				<input type="hidden" name="formhash" value="'+document.diyform.formhash.value+'" /><input type="hidden" name="saveclassnamesubmit" value="true"/></form>';
			$('append_parent').appendChild(dom.childNodes[0]);
		}
		$('saveblockclassname').action = 'portal.php?mod=portalcp&ac=block&op=saveblockclassname&bid='+id.replace('portal_block_','');
		document.forms.saveblockclassname.classname.value = className;
		ajaxpost('saveblockclassname','ajaxwaitid');
	},
	closeTitleEdit : function (fid) {
		this.deleteFrame(fid+'bgPalette_0');
		for (var i = 0 ; i<=10; i++) {
			this.deleteFrame(fid+'Palette_'+i);
		}
		hideWindow('frameTitle');
	},
	openTitleEdit : function (e) {
		if (typeof e == 'object') {
			e = Util.event(e);
			var fid = e.aim.id.replace('cmd_','');
		} else {
			fid = e;
		}
		var obj = this.getObjByName(fid);
		var titlename = obj instanceof Block ? $L('diy_module') : $L('diy_frame');
		var repeatarr = [{'key':$L('diy_tile'),'value':'repeat'},{'key':$L('diy_notile'),'value':'no-repeat'},{'key':$L('diy_tile_x'),'value':'repeat-x'},{'key':$L('diy_tile_y'),'value':'repeat-y'}];

		var len = obj.titles.length;
		var bgimage = obj.titles.style && obj.titles.style['background-image'] ? obj.titles.style['background-image'] : '';
		bgimage = bgimage != 'none' ? Util.trimUrl(bgimage) : '';
		var bgcolor = obj.titles.style && obj.titles.style['background-color'] ? obj.titles.style['background-color'] : '';
		bgcolor = Util.formatColor(bgcolor);
		var bgrepeat = obj.titles.style && obj.titles.style['background-repeat'] ? obj.titles.style['background-repeat'] : '';

		var common = '<table class="tfm">';
		common += '<tr><th>' + $L('diy_bgimg') + ':</th><td><input type="text" id="titleBgImage" class="px p_fre" value="'+bgimage+'" /> <select class="ps vm" id="titleBgRepeat" >'+this.getOption(repeatarr,bgrepeat)+'</select></td></tr>';
		common += '<tr><th>' + $L('table_bgcolor') + ':</th><td><input type="text" id="titleBgColor" class="px p_fre" value="'+bgcolor+'" size="7" />';
		common += getColorPalette(fid+'bgPalette_0', 'titleBgColor' ,bgcolor)+'</td></tr>';
		if (obj instanceof Tab) {
			var switchArr = [{'key':$L('diy_click'),'value':'click'},{'key':$L('diy_mouseover'),'value':'mouseover'}];
			var switchType = obj.titles['switchType'] ? obj.titles['switchType'][0] : 'click';
			common += '<tr><th>' + $L('diy_switch_type') + ':</th><td><select class="ps" id="switchType" >'+this.getOption(switchArr,switchType)+'</select></td></tr>';
		}
		common += '</table><hr class="l">';
		var li = '';
		li += '<div id="titleInput_0"><table class="tfm"><tr><th>'+titlename+$L('title')+':</th><td><input type="text" id="titleText_0" class="px p_fre" value="`title`" /></td></tr>';
		li += '<tr><th>' + $L('diy_link') + ':</th><td><input type="text" id="titleLink_0" class="px p_fre" value="`link`" /></td></tr>';
		li += '<tr><th>' + $L('diy_img') + ':</th><td><input type="text" id="titleSrc_0" class="px p_fre" value="`src`" /></td></tr>';
		li += '<tr><th>' + $L('diy_float') + ':</th><td><select id="titleFloat_0" class="ps vm"><option value="" `left`>' + $L('diy_fleft') + '</option><option value="right" `right`>' + $L('diy_fright') + '</option></select>';
		li += '&nbsp;&nbsp;' + $L('diy_offset') + ':<input type="text" id="titleMargin_0" class="px p_fre vm" value="`margin`" size="2" />px</td></tr>';
		li += '<tr><th>' + $L('font_family') + ':</th><td><select class="ps vm" id="titleSize_0" ><option value="">' + $L('size') + '</option>`size`</select>';
		li += '&nbsp;&nbsp;' + $L('color') + ': <input type="text" id="titleColor_0" class="px p_fre vm" value="`color`" size="4" />';
		li += getColorPalette(fid+'Palette_0', 'titleColor_0' ,'`color`');
		li += '</td></tr><tr><td colspan="2"><hr class="l"></td></tr></table></div>';
		var html = '';
		if (obj.titles['first']) {
			html = this.getTitleHtml(obj, 'first', li);
		}
		for (var i = 0; i < len; i++ ) {
			html += this.getTitleHtml(obj, i, li);
		}
		if (!html) {
			var bigarr = [];
			for (var k=7;k<27;k++) {
				var key = k+'px';
				bigarr.push({'key':key,'value':key});
			}
			var ssize = this.getOption(bigarr,ssize);
			html = li.replace('`size`', ssize).replace(/`\w+`/g, '');
		}

		var c = len + 1;
			html = '<div class="c diywin" style="width:450px;height:400px; overflow:auto;"><table cellspacing="0" cellpadding="0" class="tfm pns"><tr><th></th><td><button type="button" id="addTitleInput" class="pn" onclick="drag.addTitleInput('+c+');"><em>' + $L('diy_add_title') + '</em></button></td></tr></table><div id="titleEdit">'+html+common+'</div></div>';
		var h = '<h3 class="flb"><em>'+$L('edit')+titlename+$L('title')+'</em><span><a href="javascript:;" class="flbc" onclick="drag.closeTitleEdit(\''+fid+'\');return false;" title="' + $L('close') + '">' + $L('close') + '</a></span></h3>';
		var f = '<p class="o pns"><button onclick="drag.saveTitleEdit(\''+fid+'\');drag.closeTitleEdit(\''+fid+'\');" class="pn pnc" value="true">\n\
			<strong>' + $L('confirm') + '</strong></button><button onclick="drag.closeTitleEdit(\''+fid+'\')" class="pn" value="true"><strong>' + $L('cancel') + '</strong></button></p>';
		this.removeMenu(e);
		showWindow('frameTitle',h + html + f, 'html', 0);
	},
	getTitleHtml : function (obj, i, li) {
		var shtml = '',stitle = '',slink = '',sfloat = '',ssize = '',scolor = '',margin = '',src = '';
		var c = i == 'first' ? '0' : i+1;
		stitle = obj.titles[i]['text'] ? obj.titles[i]['text'] : '';
		slink = obj.titles[i]['href'] ? obj.titles[i]['href'] : '';
		sfloat = obj.titles[i]['float'] ? obj.titles[i]['float'] : '';
		margin = obj.titles[i]['margin'] ? obj.titles[i]['margin'] : '';
		ssize = obj.titles[i]['font-size'] ? obj.titles[i]['font-size']+'px' : '';
		scolor = obj.titles[i]['color'] ? obj.titles[i]['color'] : '';
		src = obj.titles[i]['src'] ? obj.titles[i]['src'] : '';

		var bigarr = [];
		for (var k=7;k<27;k++) {
			var key = k+'px';
			bigarr.push({'key':key,'value':key});
		}
		ssize = this.getOption(bigarr,ssize);

		shtml = li.replace(/_0/g, '_' + c).replace('`title`', stitle).replace('`link`', slink).replace('`size`', ssize).replace('`src`',src);
		var left = sfloat == '' ? 'selected' : '';
		var right = sfloat == 'right' ? 'selected' : '';
		scolor = Util.formatColor(scolor);
		shtml = shtml.replace(/`color`/g, scolor).replace('`left`', left).replace('`right`', right).replace('`margin`', margin);
		return shtml;
	},
	addTitleInput : function (c) {
		if (c  > 10) return false;
		var pre = $('titleInput_'+(c-1));
		var dom = document.createElement('div');
		dom.className = 'tfm';
		var exp = new RegExp('_'+(c-1), 'g');
		dom.id = 'titleInput_'+c;
		dom.innerHTML = pre.innerHTML.replace(exp, '_'+c);
		Util.insertAfter(dom, pre);
		$('addTitleInput').onclick = function () {drag.addTitleInput(c+1)};
	},
	saveTitleEdit : function (fid) {
		var obj = this.getObjByName(fid);
		var ele  = $(fid);
		var children = ele.childNodes;
		var title = first = '';
		var hastitle = 0;
		var c = 0;
		for (var i in children) {
			if (typeof children[i] == 'object' && Util.hasClass(children[i], this.titleClass)) {
				title = children[i];
				break;
			}
		}
		if (title) {
			var arrDel = [];
			for (var i in title.childNodes) {
				if (typeof title.childNodes[i] == 'object' && Util.hasClass(title.childNodes[i], this.titleTextClass)) {
					first = title.childNodes[i];
					this._createTitleHtml(first, c);
					if (first.innerHTML != '') hastitle = 1;
				} else if (typeof title.childNodes[i] == 'object' && !Util.hasClass(title.childNodes[i], this.moveableObject)) {
					arrDel.push(title.childNodes[i]);
				}
			}
			for (var i = 0; i < arrDel.length; i++) {
				title.removeChild(arrDel[i]);
			}
		} else {
			var titleClassName = '';
			if(obj instanceof Tab) {
				titleClassName = 'tab-';
			} else if(obj instanceof Frame) {
				titleClassName = 'frame-';
			} else if(obj instanceof Block) {
				titleClassName = 'block';
			}
			title = document.createElement('div');
			title.className = titleClassName + 'title' + ' '+ this.titleClass;
			ele.insertBefore(title,ele.firstChild);
		}
		if (!first) {
			var first = document.createElement('span');
			first.className = this.titleTextClass;
			this._createTitleHtml(first, c);
			if (first.innerHTML != '') {
				title.insertBefore(first, title.firstChild);
				hastitle = 1;
			}
		}
		while ($('titleText_'+(++c))) {
			var dom = document.createElement('span');
			dom.className = 'subtitle';
			this._createTitleHtml(dom, c);
			if (dom.innerHTML != '') {
				if (dom.innerHTML) Util.insertAfter(dom, first);
				first = dom;
				hastitle = 1;
			}
		}

		var titleBgImage = $('titleBgImage').value;
		titleBgImage = titleBgImage && titleBgImage != 'none' ? Util.url(titleBgImage) : '';
		if ($('titleBgColor').value != '' && titleBgImage == '') titleBgImage = 'none';
		title.style['backgroundImage'] = titleBgImage;
		if (titleBgImage) {
			title.style['backgroundRepeat'] = $('titleBgRepeat').value;
		}
		title.style['backgroundColor'] = $('titleBgColor').value;
		if ($('switchType')) {
			title.switchType = [];
			title.switchType[0] = $('switchType').value ? $('switchType').value : 'click';
			title.setAttribute('switchtype',title.switchType[0]);
		}

		obj.titles = [];
		if (hastitle == 1) {
			this._initTitle(obj,title);
		} else {
			if (!(obj instanceof Tab)) title.parentNode.removeChild(title);
			title = '';
			this.initPosition();
		}
		if (obj instanceof Block) this.saveBlockTitle(fid,title);
		this.setClose();

	},
	_createTitleHtml : function (ele,tid) {
		var html = '',img = '';
		tid = '_' + tid ;
		var ttext = $('titleText'+tid).value;
		var tlink = $('titleLink'+tid).value;
		var tfloat = $('titleFloat'+tid).value;
		var tmargin_ = tfloat != '' ? tfloat : 'left';
		var tmargin = $('titleMargin'+tid).value;
		var tsize = $('titleSize'+tid).value;
		var tcolor = $('titleColor'+tid).value;
		var src = $('titleSrc'+tid).value;
		var divStyle = 'float:'+tfloat+';margin-'+tmargin_+':'+tmargin+'px;font-size:'+tsize;
		var aStyle = 'color:'+tcolor+' !important;';
		if (src) {
			img = '<img class="vm" src="'+src+'" alt="'+ttext+'" />';
		}
		if (ttext || img) {
			if (tlink) {
				Util.setStyle(ele, divStyle);
				html = '<a href='+tlink+' target="_blank" style="'+aStyle+'">'+img+ttext+'</a>';
			} else {
				Util.setStyle(ele, divStyle+';'+aStyle);
				html = img+ttext;
			}
		}
		ele.innerHTML = html;
		return true;
	},
	saveBlockTitle : function (id,title) {
		if (!$('saveblocktitle')){
			var dom  = document.createElement('div');
			dom.innerHTML = '<form id="saveblocktitle" method="post" action=""><input type="hidden" name="title" value="" />\n\
				<input type="hidden" name="formhash" value="'+document.diyform.formhash.value+'" /><input type="hidden" name="savetitlesubmit" value="true"/></form>';
			$('append_parent').appendChild(dom.childNodes[0]);
		}
		$('saveblocktitle').action = 'portal.php?mod=portalcp&ac=block&op=saveblocktitle&bid='+id.replace('portal_block_','');
		var html = !title ? '' : title.outerHTML;
		document.forms.saveblocktitle.title.value = html;
		ajaxpost('saveblocktitle','ajaxwaitid');
	},
	removeBlock : function (e, flag) {
		if ( typeof e !== 'string') {
			e = Util.event(e);
			var id = e.aim.id.replace('cmd_','');
		} else {
			var id = e;
		}
		if ($(id) == null) return false;
		var obj = this.getObjByName(id);
		if (!flag) {
			if (!confirm($L('delete_confirm_2'))) return false;
		}
		if (obj instanceof Block) {
			this.delBlock(id);
		} else if (obj instanceof Frame) {
			this.delFrame(obj);
		}
		$(id).parentNode.removeChild($(id));
		var content = $(id+'_content');
		if(content) {
			content.parentNode.removeChild(content);
		}
		this.setClose();
		this.initPosition();
		this.initChkBlock();
	},
	delBlock : function (bid) {
		spaceDiy.removeCssSelector('#'+bid);
		this.stopSlide(bid);
	},
	delFrame : function (frame) {
		spaceDiy.removeCssSelector('#'+frame.name);
		for (var i in frame['columns']) {
			if (frame['columns'][i] instanceof Column) {
				var children = frame['columns'][i]['children'];
				for (var j in children) {
					if (children[j] instanceof Frame) {
						this.delFrame(children[j]);
					} else if (children[j] instanceof Block) {
						this.delBlock(children[j]['name']);
					}
				}
			}
		}
		this.setClose();
	},
	initChkBlock : function (data) {
		if (typeof name == 'undefined' || data == null ) data = this.data;
		if ( data instanceof Frame) {
			this.initChkBlock(data['columns']);
		} else if (data instanceof Block) {
			var el = $('chk'+data.name);
			if (el != null) el.checked = true;
		} else if (typeof data == 'object') {
			for (var i in data) {
				this.initChkBlock(data[i]);
			}
		}
	},
	getBlockData : function (blockname) {
		var bid = this.dragObj.id;
		var eleid = bid;
		if (bid.indexOf('portal_block_') != -1) {
			eleid = 0;
		}else {
			bid = 0;
		}
		showWindow('showblock', 'portal.php?mod=portalcp&ac=block&op=block&classname='+blockname+'&bid='+bid+'&eleid='+eleid+'&tpl='+document.diyform.template.value,'get',-1);
		drag.initPosition();
		this.fn = '';
		return true;
	},
	stopSlide : function (id) {
		if (typeof slideshow == 'undefined' || typeof slideshow.entities == 'undefined') return false;
		var slidebox = $C('slidebox',$(id));
		if(slidebox && slidebox.length > 0) {
			if(slidebox[0].id) {
				var timer = slideshow.entities[slidebox[0].id].timer;
				if(timer) clearTimeout(timer);
				slideshow.entities[slidebox[0].id] = '';
			}
		}
	},
	blockForceUpdate : function (e,all) {
		if ( typeof e !== 'string') {
			e = Util.event(e);
			var id = e.aim.id.replace('cmd_','');
		} else {
			var id = e;
		}
		if ($(id) == null) return false;
		var bid = id.replace('portal_block_', '');
		var bcontent = $(id+'_content');
		if (!bcontent) {
			bcontent = document.createElement('div');
			bcontent.id = id+'_content';
			bcontent.className = this.contentClass;
		}
		this.stopSlide(id);

		var height = Util.getFinallyStyle(bcontent, 'height');
		bcontent.style.lineHeight = height == 'auto' ? '' : (height == '0px' ? '20px' : height);
		var boldcontent = bcontent.innerHTML;
		bcontent.innerHTML = '<center>' + $L('diy_loading') + '</center>';
		var x = new Ajax();
		x.get('portal.php?mod=portalcp&ac=block&op=getblock&forceupdate=1&inajax=1&bid='+bid+'&tpl='+document.diyform.template.value, function(s) {
			if(s.indexOf('errorhandle_') != -1) {
				bcontent.innerHTML = boldcontent;
				runslideshow();
				showDialog($L('diy_no_permission'), 'alert');
				doane();
			} else {
				var obj = document.createElement('div');
				obj.innerHTML = s;
				bcontent.parentNode.removeChild(bcontent);
				$(id).innerHTML = obj.childNodes[0].innerHTML;
				evalscript(s);
				if(s.indexOf('runslideshow()') != -1) {runslideshow();}
				drag.initPosition();
				if (all) {drag.getBlocks();}
			}
		});
	},
	frameExport : function (e) {
		var flag = true;
		if (drag.isChange) {
			flag = confirm($L('diy_modify_notice'));
		}
		if (flag) {
			if ( typeof e == 'object') {
				e = Util.event(e);
				var frame = e.aim.id.replace('cmd_','');
			} else {
				frame = e == undefined ? '' : e;
			}
			if (!$('frameexport')){
				var dom  = document.createElement('div');
				dom.innerHTML = '<form id="frameexport" method="post" action="" target="_blank"><input type="hidden" name="frame" value="" />\n\
					<input type="hidden" name="tpl" value="'+document.diyform.template.value+'" />\n\
					<input type="hidden" name="tpldirectory" value="'+document.diyform.tpldirectory.value+'" />\n\
					<input type="hidden" name="diysign" value="'+document.diyform.diysign.value+'" />\n\
					<input type="hidden" name="formhash" value="'+document.diyform.formhash.value+'" /><input type="hidden" name="exportsubmit" value="true"/></form>';
				$('append_parent').appendChild(dom.childNodes[0]);
			}
			$('frameexport').action = 'portal.php?mod=portalcp&ac=diy&op=export';
			document.forms.frameexport.frame.value = frame;
			document.forms.frameexport.submit();
		}
		doane();
	},
	openFrameImport : function (type) {
		type = type || 0;
		showWindow('showimport','portal.php?mod=portalcp&ac=diy&op=import&tpl='+document.diyform.template.value+'&tpldirectory='+document.diyform.tpldirectory.value+'&diysign='+document.diyform.diysign.value+'&type='+type, 'get');
	},
	endBlockForceUpdateBatch : function () {
		if($('allupdate')) {
			$('allupdate').innerHTML = $L('diy_operate_complete');
			$('fwin_dialog_submit').style.display = '';
			$('fwin_dialog_cancel').style.display = 'none';
		}
		this.initPosition();
	},
	getBlocks : function () {
		if (this.blocks.length == 0) {
			this.endBlockForceUpdateBatch();
		}
		if (this.blocks.length > 0) {
			var cur = this.blocksLen - this.blocks.length;
			if($('allupdate')) {
				$('allupdate').innerHTML = $L('diy_update_info', [
                    '<span style="color:blue">'+this.blocksLen+'</span>',
                    '<span style="color:red">'+cur+'</span>',
                    '<span style="color:red">'+(parseInt(cur / this.blocksLen * 100)) + '%']
                );
				var bid = 'portal_block_'+this.blocks.pop();
				this.blockForceUpdate(bid,true);
			}
		}
	},
	blockForceUpdateBatch : function (blocks) {
		if (blocks) {
			this.blocks = blocks;
		} else {
			this.initPosition();
			this.blocks = this.allBlocks;
		}
		this.blocksLen = this.blocks.length;
		showDialog('<div id="allupdate" style="width:350px;line-height:28px;">' + $L('diy_updating') + '</div>','confirm',$L('diy_update_data'), '', true, 'drag.endBlockForceUpdateBatch()');
		var wait = function() {
			if($('fwin_dialog_submit')) {
				$('fwin_dialog_submit').style.display = 'none';
				$('fwin_dialog_cancel').className = 'pn pnc';
				setTimeout(function(){drag.getBlocks()},500);
			} else {
				setTimeout(wait,100);
			}
		};
		wait();
		doane();
	},
	clearAll : function () {
		if (confirm($L('diy_clear_confirm'))) {
			for (var i in this.data) {
				for (var j in this.data[i]) {
					if (typeof(this.data[i][j]) == 'object' && this.data[i][j].name.indexOf('_temp')<0) {
						this.delFrame(this.data[i][j]);
						$(this.data[i][j].name).parentNode.removeChild($(this.data[i][j].name));
					}
				}
			}
			this.initPosition();
			this.setClose();
		}
		doane();
	},
	createObj : function (e,objType,contentType) {
		if (objType == 'block' && !this.checkHasFrame()) {alert($L('diy_noframe_notice'));spaceDiy.getdiy('frame');return false;}
		e = Util.event(e);
		if(e.which != 1 ) {return false;}
		var html = '',offWidth = 0;
		if (objType == 'frame') {
			html =  this.getFrameHtml(contentType);
			offWidth = 600;
		} else if (objType == 'block') {
			html =  this.getBlockHtml(contentType);
			offWidth = 200;
			this.fn = function (e) {drag.getBlockData(contentType);};
		} else if (objType == 'tab') {
			html = this.getTabHtml(contentType);
			offWidth = 300;
		}
		var ele = document.createElement('div');
		ele.innerHTML = html;
		ele = ele.childNodes[0];
		document.body.appendChild(ele);
		this.dragObj = this.overObj = ele;
		if (!this.getTmpBoxElement()) return false;
		var scroll = Util.getScroll();
		this.dragObj.style.position = 'absolute';
		this.dragObj.style.left = e.clientX + scroll.l - 60 + "px";
		this.dragObj.style.top = e.clientY + scroll.t - 10 + "px";
		this.dragObj.style.width = offWidth + 'px';
		this.dragObj.style.cursor = 'move';
		this.dragObj.lastMouseX = e.clientX;
		this.dragObj.lastMouseY = e.clientY;
		Util.insertBefore(this.tmpBoxElement,this.overObj);
		Util.addClass(this.dragObj,this.moving);
		this.dragObj.style.zIndex = 500 ;
		this.scroll = Util.getScroll();
		this.newFlag = true;
		var _method = this;
		document.onscroll = function(){Drag.prototype.resetObj.call(_method, e);};
		window.onscroll = function(){Drag.prototype.resetObj.call(_method, e);};
		document.onmousemove = function (e){Drag.prototype.drag.call(_method, e);};
		document.onmouseup = function (e){Drag.prototype.dragEnd.call(_method, e);};
	},
	getFrameHtml : function (type) {
		var id = 'frame'+Util.getRandom(6);
		var className = [this.frameClass,this.moveableObject].join(' ');
		className = className + ' cl frame-' + type;
		var str = '<div id="'+id+'" class="'+className+'">';
		str += '<div id="'+id+'_title" class="'+this.titleClass+' '+this.frameTitleClass+'"><span class="'+this.titleTextClass+'">'+type+$L('diy_frame')+'</span></div>';
		var cols = type.split('-');
		var clsl='',clsc='',clsr='';
		clsl = ' frame-'+type+'-l';
		clsc = ' frame-'+type+'-c';
		clsr = ' frame-'+type+'-r';
		var len = cols.length;
		if (len == 1) {
			str += '<div id="'+id+'_left" class="'+this.moveableColumn+clsc+'"></div>';
		} else if (len == 2) {
			str += '<div id="'+id+'_left" class="'+this.moveableColumn+clsl+ '"></div>';
			str += '<div id="'+id+'_center" class="'+this.moveableColumn+clsr+ '"></div>';
		} else if (len == 3) {
			str += '<div id="'+id+'_left" class="'+this.moveableColumn+clsl+'"></div>';
			str += '<div id="'+id+'_center" class="'+this.moveableColumn+clsc+'"></div>';
			str += '<div id="'+id+'_right" class="'+this.moveableColumn+clsr+'"></div>';
		}
		str += '</div>';
		return str;
	},
	getTabHtml : function () {
		var id = 'tab'+Util.getRandom(6);
		var className = [this.tabClass,this.moveableObject].join(' ');
		className = className + ' cl';
		var titleClassName = [this.tabTitleClass, this.titleClass, this.moveableColumn, 'cl'].join(' ');
		var str = '<div id="'+id+'" class="'+className+'">';
		str += '<div id="'+id+'_title" class="'+titleClassName+'"><span class="'+this.titleTextClass+'">' + $L('diy_tab') + '</span></div>';
		str += '<div id="'+id+'_content" class="'+this.tabContentClass+'"></div>';
		str += '</div>';
		return str;
	},
	getBlockHtml : function () {
		var id = 'block'+Util.getRandom(6);
		var str = '<div id="'+id+'" class="block move-span"></div>';
		str += '</div>';
		return str;
	},
	setClose : function () {
		if(this.sampleMode) {
			return true;
		} else {
			if (!this.isChange) {
				window.onbeforeunload = function() {
					return $L('diy_exit_notice');
				};
			}
			this.isChange = true;
			spaceDiy.enablePreviewButton();
		}
	},
	clearClose : function () {
		this.isChange = false;
		this.isClearClose = true;
		window.onbeforeunload = function () {};
	},
	goonDIY : function () {
		if ($('prefile').value == '1') {
			showDialog('<div style="line-height:28px;">' + $L('diy_goon_notice') + '</div>','confirm',$L('diy_continue_notice'), function(){location.replace(location.href+'&preview=yes');}, true, 'spaceDiy.cancelDIY()', '', $L('continue'), $L('delete'));
		} else if (location.search.indexOf('preview=yes') > -1) {
			spaceDiy.enablePreviewButton();
		} else {
			spaceDiy.disablePreviewButton();
		}
		setInterval(function(){spaceDiy.save('savecache', 1);},180000);
	}
});

var spaceDiy = new DIY();
spaceDiy.extend({
	save : function (optype,rejs) {
		optype = typeof optype == 'undefined' ? '' : optype;
		if (optype == 'savecache' && !drag.isChange) {return false;}
		var tplpre = document.diyform.template.value.split(':');
		if (!optype) {
			if (['portal/portal_topic_content', 'portal/list', 'portal/view'].indexOf(tplpre[0]) == -1) {
				if (document.diyform.template.value.indexOf(':') > -1 && !document.selectsave) {
					var schecked = '',dchecked = '';
					if (document.diyform.savemod.value == '1') {
						dchecked = ' checked';
					} else {
						schecked = ' checked';
					}
					showDialog('<form name="selectsave" action="" method="get">' +
						'<label><input type="radio" value="0" name="savemod"'+schecked+' />' + $L('diy_save_all') + '</label>' +
						'<label><input type="radio" value="1" name="savemod"'+dchecked+' />' + $L('diy_save_this') + '</label>' +
						'</form>','notice', '', spaceDiy.save);
					return false;
				}
				if (document.selectsave) {
					if (document.selectsave.savemod[0].checked) {
						document.diyform.savemod.value = document.selectsave.savemod[0].value;
					} else {
						document.diyform.savemod.value = document.selectsave.savemod[1].value;
					}
				}
			} else {
				document.diyform.savemod.value = 1;
			}
		} else if (optype == 'savecache') {
			if (!drag.isChange) return false;
			this.checkPreview_form();
			document.diyform.rejs.value = rejs ? 0 : 1;
		} else if (optype =='preview') {
			if (drag.isChange) {
				optype = 'savecache';
			} else {
				this.checkPreview_form();
				$('preview_form').submit();
				return false;
			}
		}
		document.diyform.action = document.diyform.action.replace(/[&|\?]inajax=1/, '');
		document.diyform.optype.value = optype;
		document.diyform.spacecss.value = spaceDiy.getSpacecssStr();
		document.diyform.style.value = spaceDiy.style;
		document.diyform.layoutdata.value = drag.getPositionStr();
		document.diyform.gobackurl.value = spaceDiy.cancelDiyUrl();
		drag.clearClose();
		if (optype == 'savecache') {
			document.diyform.handlekey.value = 'diyform';
			ajaxpost('diyform','ajaxwaitid','ajaxwaitid','onerror');
		} else {
			saveUserdata('diy_advance_mode', '');
			document.diyform.submit();
		}
	},
	checkPreview_form : function () {
		if (!$('preview_form')) {
			var dom = document.createElement('div');
			var search = '';
			var sarr = location.search.replace('?','').split('&');
			for (var i = 0;i<sarr.length;i++){
				var kv = sarr[i].split('=');
				if (kv.length>1 && kv[0] != 'diy') {
					search += '<input type="hidden" value="'+kv[1]+'" name="'+kv[0]+'" />';
				}
			}
			search +=  '<input type="hidden" value="yes" name="preview" />';
			dom.innerHTML = '<form action="'+location.href+'" target="_bloak" method="get" id="preview_form">'+search+'</form>';
			var form = dom.getElementsByTagName('form');
			$('append_parent').appendChild(form[0]);
		}
	},
	cancelDiyUrl : function () {
		return location.href.replace(/[\?|\&]diy\=yes/g,'').replace(/[\?|\&]preview=yes/,'');
	},
	cancel : function () {
		saveUserdata('diy_advance_mode', '');
		if (drag.isClearClose) {
			showDialog('<div style="line-height:28px;">' + $L('diy_save_confirm') + '</div>','confirm',$L('diy_save'), function(){location.href = spaceDiy.cancelDiyUrl();}, true, function(){window.onunload=function(){spaceDiy.cancelDIY()};location.href = spaceDiy.cancelDiyUrl();});
		} else {
			location.href = this.cancelDiyUrl();
		}

	},
	recover : function() {
		if (confirm($L('diy_restore_confirm'))) {
			drag.clearClose();
			document.diyform.recover.value = '1';
			document.diyform.gobackurl.value = location.href.replace(/(\?diy=yes)|(\&diy=yes)/,'').replace(/[\?|\&]preview=yes/,'');
			document.diyform.submit();
		}
		doane();
	},
	enablePreviewButton : function () {
		if ($('preview')){
			$('preview').className = '';
			if(drag.isChange) {
				$('diy_preview').onclick = function () {spaceDiy.save('savecache');return false;};
			} else {
				$('diy_preview').onclick = function () {spaceDiy.save('preview');return false;};
			}
			Util.show($('savecachemsg'))
		}
	},
	disablePreviewButton : function () {
		if ($('preview')) {
			$('preview').className = 'unusable';
			$('diy_preview').onclick = function () {return false;};
		}
	},
	cancelDIY : function () {
		this.disablePreviewButton();
		document.diyform.optype.value = 'canceldiy';
		var x = new Ajax();
		x.post($('diyform').action+'&inajax=1','optype=canceldiy&diysubmit=1&template='+document.diyform.template.value+'&savemod='+document.diyform.savemod.value+'&formhash='+document.diyform.formhash.value+'&tpldirectory='+document.diyform.tpldirectory.value+'&diysign='+document.diyform.diysign.value,function(s){});
	},
	switchBlockclass : function(blockclass) {
		var navs = $('contentblockclass_nav').getElementsByTagName('a');
		var contents = $('contentblockclass').getElementsByTagName('ul');
		for(var i=0; i<navs.length; i++) {
			if(navs[i].id=='bcnav_'+blockclass) {
				navs[i].className = 'a';
			} else {
				navs[i].className = '';
			}
		}
		for(var i=0; i<contents.length; i++) {
			if(contents[i].id=='contentblockclass_'+blockclass) {
				contents[i].style.display = '';
			} else {
				contents[i].style.display = 'none';
			}
		}
	},
	getdiy : function (type) {
		if (type) {
			var nav = $('controlnav').children;
			for (var i in nav) {
				if (nav[i].className == 'current') {
					nav[i].className = '';
					var contentid = 'content'+nav[i].id.replace('nav', '');
					if ($(contentid)) $(contentid).style.display = 'none';
				}
			}
			$('nav'+type).className = 'current';
			if (type == 'start' || type == 'frame') {
				$('content'+type).style.display = 'block';
				return true;
			}
			if(type == 'blockclass' && $('content'+type).innerHTML !='') {
				$('content'+type).style.display = 'block';
				return true;
			}
			var para = '&op='+type;
			if (arguments.length > 1) {
				for (var i = 1; i < arguments.length; i++) {
					para += '&' + arguments[i] + '=' + arguments[++i];
				}
			}
			var ajaxtarget = type == 'diy' ? 'diyimages' : '';
			var x = new Ajax();
			x.showId = ajaxtarget;
			x.get('portal.php?mod=portalcp&ac=diy'+para+'&inajax=1&ajaxtarget='+ajaxtarget,function(s, x) {
				if (s) {
					if (typeof cpb_frame == 'object') {delete cpb_frame;}
					if (!$('content'+type)) {
						var dom = document.createElement('div');
						dom.id = 'content'+type;
						$('controlcontent').appendChild(dom);
					}
					$('content'+type).innerHTML = s;
					$('content'+type).style.display = 'block';
					if (type == 'diy') {
						spaceDiy.setCurrentDiy(spaceDiy.currentDiy);
						if (spaceDiy.styleSheet.rules.length > 0) {
							Util.show('recover_button');
						}
					}

					var evaled = false;
					if(s.indexOf('ajaxerror') != -1) {
						evalscript(s);
						evaled = true;
					}
					if(!evaled && (typeof ajaxerror == 'undefined' || !ajaxerror)) {
						if(x.showId) {
							ajaxupdateevents($(x.showId));
						}
					}
					if(!evaled) evalscript(s);
				}
			});
		}
	}
});

spaceDiy.init(1);

function succeedhandle_diyform (url, message, values) {
	if (values['rejs'] == '1') {
		document.diyform.rejs.value = '';
		parent.$('preview_form').submit();
	}
	spaceDiy.enablePreviewButton();
	return false;
}