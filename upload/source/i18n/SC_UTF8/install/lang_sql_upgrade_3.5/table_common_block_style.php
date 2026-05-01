<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

$data = [
  [
    'blockclass' => 'html_misctag',
    'name' => '[内置]标签模版',
    'template' =>
    [
      'raw' => '<!-- 热门标签模块 -->
<div class="tag-cloud-module">
	<div class="tag-cloud-container">
		[loop]
		<a href="{url}"
		   title="{title} ({related_count}篇内容)"
		   class="tag-cloud-item tag-size-{size_level} tag-color-{color_level}"
		   data-count="{related_count}"
		   data-hot="{hot_score}">
			{title}
		</a>
		[/loop]
	</div>
</div>',
      'footer' => '',
      'header' => '',
      'indexplus' =>
      [
      ],
      'index' =>
      [
      ],
      'orderplus' =>
      [
      ],
      'order' =>
      [
      ],
      'loopplus' =>
      [
      ],
      'loop' => '<a href="{url}"
		   title="{title} ({related_count}篇内容)"
		   class="tag-cloud-item tag-size-{size_level} tag-color-{color_level}"
		   data-count="{related_count}"
		   data-hot="{hot_score}">
			{title}
		</a>',
    ],
    'hash' => '391cb72a',
    'getpic' => '0',
    'getsummary' => '0',
    'makethumb' => '0',
    'settarget' => '0',
    'fields' =>
    [
      0 => 'url',
      1 => 'title',
      2 => 'related_count',
      3 => 'size_level',
      4 => 'color_level',
      5 => 'hot_score',
    ],
    'moreurl' => '0',
  ],
];
