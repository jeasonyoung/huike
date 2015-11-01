<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_CHARSET'       =>  'utf-8', // 默认输出编码
	'URL_MODEL'		=>  1,		 // URL访问模式: 2 采用Rewrite模式 IIS下需配置webconfig文件
	
	/* 数据库设置 */
	'DB_TYPE'               =>  'mysql',     // 数据库类型
	'DB_HOST'               =>  '124.232.146.111', // 服务器地址
	'DB_NAME'               =>  'huike',          // 数据库名
	'DB_USER'               =>  'D9xf1S600K3Op2w',      // 用户名
	'DB_PWD'                =>  'G8i2e0J5q3v1X7zLn9MM',          // 密码
	'DB_PORT'               =>  '6033',        // 端口
	'DB_PREFIX'             =>  'hk_',    // 数据库表前缀
	'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
	
	/* SQL解析缓存*/
	'DB_SQL_BUILD_CACHE'    => true,
	'DB_SQL_BUILD_QUEUE'    => 'xcache',
	'DB_SQL_BUILD_LENGTH'   => 20,
	
	/*布局设置*/
       // 'VIEW_PATH'             =>  './Theme/',
	'TMPL_ENGINE_TYPE'      =>  'Think',
	
	/*权限认证配置*/
	'AUTH_CONFIG'		=> array(
            'AUTH_ON' => true, //认证开关
            'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证
            'AUTH_GROUP' => 'hk_admin_group', //用户组数据表名
            'AUTH_GROUP_ACCESS' => 'hk_admin_group_access', //用户组明细表
            'AUTH_RULE' => 'hk_admin_rule', //权限规则表
            'AUTH_USER' => 'hk_admin'//用户信息表
	),
        'URL_CASE_INSENSITIVE' =>true,
        //密码KEY
        'md5_key'               => 'H9x5Mo3cT7Lr1AP',
);