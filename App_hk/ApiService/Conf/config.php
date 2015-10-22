<?php
//模块自定义配置(可覆盖全局配置)
return array(
	//'配置项'=>'配置值'
    'DEFAULT_CHARSET'      => 'utf-8',//默认输出编码
    'URL_MODEL'            => 1,//URL模式(0:普通模式,1:PATHINFO模式,2:REWRITE模式,3:兼容模式)
    'URL_CASE_INSENSITIVE' => true,//不区分URL大小写
    'DIGEST_REALM'         => 'huike',//摘要认证realm
);