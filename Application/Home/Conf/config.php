<?php
return array(
	'TOKEN_ON'      =>    true,  // 是否开启令牌验�?默认关闭
	'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
	'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令�?默认为true
	'URL_ROUTER_ON'   => true,  //开启路由
	'URL_HTML_SUFFIX' => 'html',
	'URL_MAP_RULES'=>array(
		'test' => 'index/test'
	),
);