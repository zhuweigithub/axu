<?php
return array(
	//'配置项'=>'配置值'
    "LOAD_EXT_FILE"=>"fb",
    'APP_GROUP_LIST'=>'Home,Admin',
    'DEFAULT_GROUP'=>'Home',//默认分组

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
<<<<<<< HEAD
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__IMG__'    => __ROOT__ . '/Public/images',
        '__CSS__'    => __ROOT__ . '/Public/css',
        '__JS__'     => __ROOT__ . '/Public/js',
    ),
=======
>>>>>>> 1fb7570049b95464eece187277ad75a74607bd50

    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '120.26.231.149', // 服务器地址
<<<<<<< HEAD
    'DB_NAME'   => 'ax', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'zw2732.com',  // 密码fe9a28f01e
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'oa_', // 数据库表前缀
=======
    'DB_NAME'   => 'aixiu', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'fe9a28f01e',  // 密码fe9a28f01e
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'ax_', // 数据库表前缀

    /*加密字符串*/
    "PASSWORD_SUFFIX" => 'zw_info'
>>>>>>> 1fb7570049b95464eece187277ad75a74607bd50
);