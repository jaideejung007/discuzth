<?php exit('Access Denied'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{lang login_confirm}</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background-color: #f5f7fa;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			min-height: 80vh;
			padding: 20px;
		}

		.icon-container {
			margin-bottom: 30px;
			text-align: center;
		}

		svg {
			width: 240px;
			height: 160px;
			fill: none;
			stroke: #d0d6dd;
			stroke-width: 2;
			stroke-linecap: round;
			stroke-linejoin: round;
		}

		.text-content {
			text-align: center;
			margin-bottom: 30px;
		}

		.text-content p {
			color: #86909c;
			line-height: 1.6;
		}

		.text-content p:first-child {
			margin-bottom: 8px;
		}

		.button-container {
			width: 100%;
			max-width: 320px;
			display: flex;
			flex-direction: column;
			gap: 12px;
		}

		button {
			padding: 12px 0;
			border: none;
			border-radius: 4px;
			font-size: 16px;
			font-weight: 500;
			cursor: pointer;
			transition: background-color 0.2s ease-in-out;
		}

		#confirm-btn {
			background-color: #2B7ACD;
			color: #fff;
		}

		#confirm-btn:hover {
			background-color: #0e66e5;
		}

		#cancel-btn {
			background-color: #fff;
			color: #333;
			border: 1px solid #d0d6dd;
		}

		#cancel-btn:hover {
			background-color: #f5f5f5;
		}
	</style>
</head>
<body>
<div class="icon-container">
	<svg t="1756724714214" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4798" width="500" height="500"><path d="M357.349269 395.476123 138.018935 395.476123c-16.650215 0-30.147624 15.002692-30.147624 33.508161l0 360.207875c0 18.507516 13.497409 33.508161 30.147624 33.508161l219.330334 0c16.650215 0 30.147624-15.000646 30.147624-33.508161L387.496893 428.984285C387.496893 410.478815 373.998461 395.476123 357.349269 395.476123zM219.491505 421.560198l55.394634 0c2.698458 0 4.885264 2.186805 4.885264 4.885264 0 2.697435-2.187829 4.88424-4.885264 4.88424l-55.394634 0c-2.698458 0-4.885264-2.186805-4.885264-4.88424C214.606241 423.747004 216.793046 421.560198 219.491505 421.560198zM246.930437 802.242384c-7.135514 0-12.919241-5.78475-12.919241-12.920264s5.783727-12.919241 12.919241-12.919241 12.919241 5.783727 12.919241 12.919241S254.064928 802.242384 246.930437 802.242384zM364.209514 769.190617l-233.825466 0 0-321.318146 233.825466 0L364.209514 769.190617z" p-id="4799" data-spm-anchor-id="a313x.search_index.0.i0.c5823a813B24gc" class="selected" fill="#e6e6e6"></path><path d="M725.955418 780.702811l-91.841731 0 0-23.408129c0-5.650697-4.582365-10.233062-10.233062-10.233062l-115.121947 0c-5.650697 0-10.233062 4.582365-10.233062 10.233062l0 23.408129-67.538209 0c-5.65172 0-10.233062 4.582365-10.233062 10.233062l0 17.140379c0 5.650697 4.581342 10.233062 10.233062 10.233062l294.968012 0c5.650697 0 10.233062-4.582365 10.233062-10.233062l0-17.140379C736.18848 785.285177 731.606115 780.702811 725.955418 780.702811z" p-id="4800" data-spm-anchor-id="a313x.search_index.0.i2.c5823a813B24gc" class="selected" fill="#e6e6e6"></path><path d="M874.590644 201.76733l-627.2867 0c-22.605857 0-40.932248 18.326391-40.932248 40.932248l0 120.238478 32.745798 0 0-125.355009c0-2.825348 2.291183-5.116531 5.116531-5.116531l637.519762 0c2.826372 0 5.116531 2.291183 5.116531 5.116531l0 385.786437c0 2.826372-2.290159 5.116531-5.116531 5.116531l-460.999442 0 0 90.050945 453.836299 0c22.606881 0 40.932248-18.325367 40.932248-40.932248l0-434.905134C915.522891 220.09372 897.197524 201.76733 874.590644 201.76733zM562.195727 683.426301c-10.008958 0-18.122753-8.113795-18.122753-18.122753s8.113795-18.122753 18.122753-18.122753 18.122753 8.113795 18.122753 18.122753S572.204685 683.426301 562.195727 683.426301z" p-id="4801" data-spm-anchor-id="a313x.search_index.0.i1.c5823a813B24gc" class="selected" fill="#e6e6e6"></path></svg>
</div>

<div class="text-content">
	<p>{lang wechat_confirm_tip1}</p>
	<p>{lang wechat_confirm_tip2}</p>
</div>

<div class="button-container">
	<button id="confirm-btn">{lang wechat_confirm_btn}</button>
	<button id="cancel-btn">{lang wechat_cancel_btn}</button>
</div>

<script>
	document.getElementById('confirm-btn').addEventListener('click', function () {
		window.location.href = 'misc.php?mod=wechat&ac=confirm_ok&authcode={$authcode}&formhash={FORMHASH}';
	});

	document.getElementById('cancel-btn').addEventListener('click', function () {
		window.location.href = '{$_G["siteurl"]}';
	});
</script>
</body>
</html>
