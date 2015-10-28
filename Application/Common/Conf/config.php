<?php
return array(
    //'配置项'=>'配置值'
    'DEFAULT_CHARSET'       =>  'utf-8', // 默认输出编码
    'URL_MODEL'             =>   URL_PATHINFO,       // URL访问模式: 2 采用Rewrite模式 IIS下需配置webconfig文件
    
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '124.232.146.111', // 服务器地址
    'DB_NAME'               =>  'huike',          // 数据库名
    'DB_USER'               =>  'D9xf1S600K3Op2w',      // 用户名
    'DB_PWD'                =>  'G8i2e0J5q3v1X7zLn9MM',          // 密码
    'DB_PORT'               =>  '6033',        // 端口
    'DB_PREFIX'             =>  'hk_',    // 数据库表前缀
    'DB_PARAMS'             =>  array(), // 数据库连接参数    
    'DB_DEBUG'  	    =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
    'SHOW_PAGE_TRACE'       =>  0,  //开启页面Trace信息
    
    /*SQL解析缓存*/
    'DB_SQL_BUILD_CACHE'    => true,
    'DB_SQL_BUILD_QUEUE' => 'xcache',
    'DB_SQL_BUILD_LENGTH' => 20,
    
    /*默认模板引擎*/
    'TMPL_ENGINE_TYPE'      =>  'Think',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
    /*自定义生成静态页后缀*/
    'HTML_FILE_SUFFIX'      =>  '.html',

    'TMPL_PARSE_STRING'     => array(
        '__APP_JS__'    =>  __ROOT__.'/Public/js',
        '__APP_IMG__'   =>  __ROOT__.'/Public/img',
        '__APP_BFQ__'   =>  __ROOT__.'/Public/player'
    ),


    'APP_BIND_DOMAIN'       => 'http://www.52huike.com',
    //'APP_SUB_DOMAIN_DEPLOY' =>  1,   // 是否开启子域名部署
    // 子域名部署规则
    //'APP_SUB_DOMAIN_RULES'  =>  array(
    //    'http://www.wx.com/' => array('Home')
    //), 
);