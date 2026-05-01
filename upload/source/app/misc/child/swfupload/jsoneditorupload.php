<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


$isMultiUpload = false;
if(isset($_FILES['Filedata']) && is_array($_FILES['Filedata']) && isset($_FILES['Filedata']['name']) && is_array($_FILES['Filedata']['name'])) {
	$isMultiUpload = true;
} else if(isset($_FILES['Filedata'])) {
	
	$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
}

$base64Image = daddslashes(getgpc('thumbbase64'));
if(empty($base64Image)) {
	if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
		
		$thumbnail = $_FILES['thumbnail'];
		$fileType = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
		$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

		
		if (!in_array($fileType, $allowedTypes)) {
			$code = -1;
			
			$ret = [
				'success' => -1,
				
			];
			echo json_encode($ret);
			exit();
		}

		
		$mime = get_file_mime_type($thumbnail['tmp_name']);

		if (!str_starts_with($mime, 'image/')) {
			$code = -1;
			$ret = [
				'success' => -2,
				
			];
			echo json_encode($ret);
			exit();
		}

		
		$imageData = file_get_contents($thumbnail['tmp_name']);
		$base64Image = 'data:' . $mime . ';base64,' . base64_encode($imageData);
	}
}

$forumattachextensions = '';
$fid = intval($_GET['fid']);
if($fid) {
	$forum = $fid != $_G['fid'] || !$_G['forum'] ? table_forum_forum::t()->fetch_info_by_fid($fid) : $_G['forum'];
	if(empty($_G['setting']['editormodetype']) && $forum['editormode'] != 2) {
		$ret = [
			'success' => 0
		];
		echo json_encode($ret);
		exit();
	}

	if($forum['status'] == 3 && $forum['level']) {
		$levelinfo = table_forum_grouplevel::t()->fetch($forum['level']);
		if($postpolicy = $levelinfo['postpolicy']) {
			$postpolicy = dunserialize($postpolicy);
			$forumattachextensions = $postpolicy['attachextensions'];
		}
	} else {
		$forumattachextensions = $forum['attachextensions'];
	}
	if($forumattachextensions) {
		$_G['group']['attachextensions'] = $forumattachextensions;
	}
}


if($isMultiUpload) {
	$files = [];
	$successCount = 0;

	
	$fileCount = count($_FILES['Filedata']['name']);
	for($i = 0; $i < $fileCount; $i++) {
		if(empty($_FILES['Filedata']['name'][$i])) continue;

		
		$tempFile = [
			'name' => addslashes(diconv(urldecode($_FILES['Filedata']['name'][$i]), 'UTF-8')),
			'type' => $_FILES['Filedata']['type'][$i],
			'tmp_name' => $_FILES['Filedata']['tmp_name'][$i],
			'error' => $_FILES['Filedata']['error'][$i],
			'size' => $_FILES['Filedata']['size'][$i]
		];

		
		$originalFiledata = $_FILES['Filedata'];
		
		$_FILES['Filedata'] = $tempFile;

		
		$upload = new forum_upload(1, thumbBase64: $base64Image);
		if($upload) {
			$aid = $upload->getaid;
			if($aid >= 0) {
				$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
				
				$files[] = [
					'aid' => $upload->aid,
					'remote' => $attach['remote'],
					'directory' => 'forum',
					'url' => $attach['attachment'],
					'thumb' => $attach['isimage'] >= 2 ? $attach['attachment'].'.thumb.jpg' : '',
				];
				$successCount++;
			}
		}

		
		$_FILES['Filedata'] = $originalFiledata;
	}

	
	$ret = [
		'success' => $successCount > 0 ? 1 : 0,
		'files' => $files
	];
	echo json_encode($ret);
	exit();
} else {
	
	$upload = new forum_upload(1, thumbBase64: $base64Image);
	if($upload) {
		$aid = $upload->getaid;
		if($aid < 0) {
			$ret = [
				'success' => 0,
				'statusid' => $aid,
				'sizelimit' => $upload->error_sizelimit,
			];
			echo json_encode($ret);
			exit();
		}
		$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
		
		$ret = [
			'success' => 1,
			'file' => [
				'aid' => $upload->aid,
				'remote' => $attach['remote'],
				'directory' => 'forum',
				'url' => $attach['attachment'],
				'thumb' => $attach['isimage'] >= 2 ? $attach['attachment'].'.thumb.jpg' : '',
			]
		];
		echo json_encode($ret);
		exit();
	} else {
		$ret = [
			'success' => 0
		];
		echo json_encode($ret);
		exit();
	}
}

function get_file_mime_type($file) {
	
	if(!file_exists($file)) {
		return 'application/octet-stream';
	}

	
	if(class_exists('finfo')) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file);
		finfo_close($finfo);
		return $mime;
	}

	
	if(function_exists('mime_content_type')) {
		$mime = mime_content_type($file);
		if($mime) {
			return $mime;
		}
	}

	
	$image_info = getimagesize($file);
	if($image_info && isset($image_info['mime'])) {
		return $image_info['mime'];
	}

	
	$handle = fopen($file, 'rb');
	if($handle) {
		$bytes = fread($handle, 12); 
		fclose($handle);

		
		if(substr($bytes, 0, 3) === 'GIF') {
			return 'image/gif';
		} elseif(substr($bytes, 0, 2) === "\xff\xd8") {
			return 'image/jpeg';
		} elseif(substr($bytes, 0, 8) === "\x89PNG\r\n\x1a\n") {
			return 'image/png';
		} elseif(substr($bytes, 0, 4) === "\x52\x49\x46\x46") {
			return 'audio/wav';
		} elseif(substr($bytes, 0, 4) === "\x00\x00\x00\x18") {
			return 'video/mp4';
		} elseif(substr($bytes, 0, 4) === "\x1a\x45\xdf\xa3") {
			return 'video/webm';
		} elseif(substr($bytes, 0, 4) === "\x25\x50\x44\x46") {
			return 'application/pdf';
		} elseif(substr($bytes, 0, 4) === "\x50\x4B\x03\x04") {
			return 'application/zip';
		} elseif(substr($bytes, 0, 4) === "\x7B\x0A\x22\x76") {
			return 'application/json';
		}
	}

	
	$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	$mime_types = array(
		
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png' => 'image/png',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'webp' => 'image/webp',
		'svg' => 'image/svg+xml',
		'tiff' => 'image/tiff',
		'ico' => 'image/x-icon',
		
		'mp3' => 'audio/mpeg',
		'wav' => 'audio/wav',
		'ogg' => 'audio/ogg',
		'flac' => 'audio/flac',
		'aac' => 'audio/aac',
		'm4a' => 'audio/mp4',
		
		'mp4' => 'video/mp4',
		'webm' => 'video/webm',
		'flv' => 'video/x-flv',
		'mov' => 'video/quicktime',
		'avi' => 'video/x-msvideo',
		'mkv' => 'video/x-matroska',
		
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'txt' => 'text/plain',
		'csv' => 'text/csv',
		
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'tar' => 'application/x-tar',
		'gz' => 'application/gzip',
		'7z' => 'application/x-7z-compressed',
		
		'html' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'php' => 'application/x-httpd-php',
		'xml' => 'application/xml',
		'json' => 'application/json'
	);
	return isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
}