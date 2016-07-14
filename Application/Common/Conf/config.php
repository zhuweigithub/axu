<?php
return array(
	//'配置项'=>'配置值'
    "LOAD_EXT_FILE"=>"fb",
    'APP_GROUP_LIST'=>'Home,Admin',
    'DEFAULT_GROUP'=>'Home',//默认分组
    $config=array(
        "appId"=>'wx8e44d721c760cf5c',
        "token"=>'ybzf_weixin_token',
        "appSecret"=>'7857a1c0ae6a07ce8a256ca8e155a91e',
        "returnUrl"=>"weixin.aweb.ybzf.com"
    ),
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
);