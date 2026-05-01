function drawstatchart(url, height, titleOption, obj) {
	obj = obj || $('statchart');


	height = height || 400;

	var x = new Ajax();
	x.recvType = 'HTML';
	obj.style.width = '100%';
	obj.style.height = height + 'px';
	x.get(url, function (s, x) {
		var myChart = echarts.init(obj);
		option = {
			grid: { left: 60, right: 20, top: 20 },
			xAxis: { type: 'category', data: [] },
			yAxis: { type: 'value' },
			tooltip: { trigger: 'axis', textStyle: { fontSize: 12 } },
			series: [],
			legend: { type: 'scroll', data: [], left: 60, bottom: 10 },
		};
		if(titleOption) {
			option.title = titleOption;
		}
		var rex = x.XMLHttpRequest.responseXML, reax = rex.getElementsByTagName('xaxis')[0].childNodes;
		if (!reax.length) {
			option['title'] = {
				text: 'There is no data for selected period', padding: [10, 50],
				textAlign: 'center', textVerticalAlign: 'center',
				left: '50%', top: '50%', backgroundColor: '#e8f0f7'
			};
		}
		for (var i = 0; i < reax.length; i++) {
			option.xAxis.data.push(reax[i].firstChild.nodeValue);
		}
		for (var i = 0, q = rex.getElementsByTagName('graphs')[0].childNodes; i < q.length; i++) {
			qttl = q[i].getAttribute('title');
			option.legend.data.push(qttl);
			qdata = {
				type: 'line',
				smooth: true,
				name: qttl,
				data: []
			};
			for (var j = 0; j < q[i].childNodes.length; j++) {
				qdata.data.push(parseInt(q[i].childNodes[j].firstChild.nodeValue));
			}
			option.series.push(qdata);

		}
		myChart.setOption(option);
	});
}