<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_CHARSET'       =>  'utf-8', // 默认输出编码
	'MODULE_ALLOW_LIST'     =>  array('Home'),
	'DEFAULT_MODULE'        =>  'Home',
	'URL_MODEL'		=>  1,		 // URL访问模式: 2 采用Rewrite模式 IIS下需配置webconfig文件
	
	
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