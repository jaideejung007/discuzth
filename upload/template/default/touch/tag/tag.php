<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{if $type != 'countitem'}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2><a href="misc.php?mod=tag">{lang tag}</a></h2>
	<div class="my"><a href="index.php"><i class="dm-house"></i></a></div>
</div>

<form class="searchform" method="post" autocomplete="off" action="misc.php?mod=tag">
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="search flex-box">
		<input value="$keyword" autocomplete="off" class="mtxt flex" name="name" id="scform_srchtxt" value="" placeholder="{lang mobsearchtxt}">
		<input type="submit" value="{lang search}" class="mbtn" id="scform_submit">
	</div>
</form>

<div class="bodybox p10 cl">
	<!--{if $tagarray}-->
	<div class="wordcloud-container" id="wordcloud"></div>
	<!--{else}-->
	<div class="empty-box mt10 cl">
		<h4>{lang no_tag}</h4>
	</div>
	<!--{/if}-->
</div>

<script>
	// 关键词数据 - 包含文本和权重(影响大小)
	const keywords = [
		<!--{loop $tagarray $tag}-->
		{ text: "{$tag['tagname']}", weight: {$tag['hot_score']}, url: 'misc.php?mod=tag&id={$tag['tagid']}' },
		<!--{/loop}-->
	];

	// 颜色方案
	const colors = [
		'#0071e3', '#34c759', '#ff9500', '#ff2d55', '#5856d6',
		'#af52de', '#ffcc00', '#5ac8fa', '#ff9500', '#32ade6'
	];

	// DOM元素引用
	const wordcloudContainer = document.getElementById('wordcloud');
	let animationFrameId = null;

	// 优化的URL编码函数，确保特殊字符正确处理
	function encodeSpecialChars(url) {
		// 解析URL结构
		let urlParts = url.split('?');
		let baseUrl = urlParts[0];
		let queryString = urlParts.length > 1 ? urlParts[1] : '';

		// 处理查询参数
		if (queryString) {
			let params = queryString.split('&');
			let encodedParams = [];

			for (let i = 0; i < params.length; i++) {
				let param = params[i].split('=');
				let key = param[0];
				let value = param.length > 1 ? param[1] : '';

				// 对参数名和值分别进行编码
				encodedParams.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
			}

			// 重新组合URL
			return baseUrl + '?' + encodedParams.join('&');
		}

		return url;
	}

	// 生成字云函数
	function generateWordCloud() {
		// 清空容器
		wordcloudContainer.innerHTML = '';

		// 获取容器尺寸
		let containerWidth = wordcloudContainer.clientWidth;
		let containerHeight = wordcloudContainer.clientHeight;

		// 创建一个数组用于存储已放置的词语位置，避免重叠
		let placedWords = [];

		// 为每个关键词创建元素
		for (let i = 0; i < keywords.length; i++) {
			let keyword = keywords[i];

			// 创建链接元素
			let wordElement = document.createElement('a');
			wordElement.className = 'word';
			wordElement.textContent = keyword.text;

			// 处理URL中的特殊字符
			let encodedUrl = encodeSpecialChars(keyword.url);
			wordElement.href = encodedUrl;
			wordElement.target = "";

			// 根据权重设置字体大小
			let fontSize = 12 + (keyword.weight / 10);
			wordElement.style.fontSize = fontSize + 'px';

			// 随机颜色
			let colorIndex = Math.floor(Math.random() * colors.length);
			wordElement.style.color = colors[colorIndex];

			// 先添加到DOM再计算尺寸
			wordcloudContainer.appendChild(wordElement);

			// 获取词语尺寸
			let wordWidth = wordElement.offsetWidth;
			let wordHeight = wordElement.offsetHeight;

			// 寻找不重叠的位置
			let positionFound = false;
			let left, top;
			let attempts = 0;

			// 尝试最多50次寻找不重叠的位置
			while (!positionFound && attempts < 50) {
				// 随机位置，但确保在容器内
				left = Math.floor(Math.random() * (containerWidth - wordWidth - 40)) + 20;
				top = Math.floor(Math.random() * (containerHeight - wordHeight - 40)) + 20;

				// 检查是否与已放置的词语重叠
				let overlap = false;
				for (let j = 0; j < placedWords.length; j++) {
					let placedWord = placedWords[j];
					let distance = Math.hypot(left - placedWord.left, top - placedWord.top);

					// 距离小于两个词语尺寸之和的一半，则认为重叠
					if (distance < (wordWidth + placedWord.width) / 2) {
						overlap = true;
						break;
					}
				}

				if (!overlap) {
					positionFound = true;
					placedWords.push({
						left: left,
						top: top,
						width: wordWidth,
						height: wordHeight
					});
				}

				attempts++;
			}

			// 如果找不到不重叠的位置，仍然放置
			if (!positionFound) {
				left = Math.floor(Math.random() * (containerWidth - wordWidth - 40)) + 20;
				top = Math.floor(Math.random() * (containerHeight - wordHeight - 40)) + 20;
				placedWords.push({
					left: left,
					top: top,
					width: wordWidth,
					height: wordHeight
				});
			}

			// 设置位置（使用标准变量拼接，不使用模板字符串）
			wordElement.style.left = left + 'px';
			wordElement.style.top = top + 'px';

			// 保持水平显示
			wordElement.style.transform = 'rotate(0deg)';

			// 添加出现动画
			wordElement.style.opacity = '0';
			wordElement.style.transform = 'rotate(0deg) scale(0.8)';

			setTimeout(function() {
				wordElement.style.opacity = '1';
				wordElement.style.transform = 'rotate(0deg) scale(1)';
			}, 50 + Math.random() * 300);
		}
	}

	// 窗口大小改变时重新生成字云
	window.addEventListener('resize', function() {
		if (animationFrameId) {
			cancelAnimationFrame(animationFrameId);
		}
		animationFrameId = requestAnimationFrame(generateWordCloud);
	});

	// 初始生成字云
	window.addEventListener('load', generateWordCloud);
</script>


<!--{else}-->
$num
<!--{/if}-->

<!--{template common/footer}-->