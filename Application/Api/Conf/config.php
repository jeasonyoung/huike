<?php
//模块自定义配置(可覆盖全局配置)
return array(
	//'配置项'=>'配置值'
    'DEFAULT_CHARSET'      => 'utf-8',//默认输出编码
    'URL_MODEL'            => 1,//URL模式(0:普通模式,1:PATHINFO模式,2:REWRITE模式,3:兼容模式)
    'URL_CASE_INSENSITIVE' => true,//不区分URL大小写
    'URL_PARAMS_BIND'      => true,//开启URL参数绑定
    'URL_ROUTER_ON'        => true,
    'URL_ROUTE_RULES'      => array(
        'api/m/register' => 'index/register',//0.学员注册
        'api/m/login'    => 'index/login',//1.验证学员登录
        'api/m/courses'  => 'index/courses',//2.我的课程(套餐/班级集合)
        'api/m/lessons'  => 'index/lessons',//3.课程资源(视频)集合
        'api/m/exams'    => 'index/exams',//4.考试集合[用于免费体验]
        'api/m/packages' => 'index/packages',//5.机构考试下套餐/班级集合[用于免费体验]
        'api/m/learning/add' => 'index/learning',//6.上传学习记录(POST)
        'api/m/aq/topics'    => 'index/load_topics',//7.获取答疑主题数据
        'api/m/aq/topic/add' => 'index/add_topic',//8.新增答疑主题(POST)
        'api/m/aq/details'   => 'index/load_details',//9.答疑主题明细
        'api/m/aq/detail/add'=> 'index/add_detail',//10.新增答疑明细(POST)
        'api/m/aq/suggest/add' =>  'index/add_suggest',//11.新增学员建议(POST)
    ),
    'QUERY_LIMIT_TOP'      =>  200,
    'DB_PARAMS'            => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
	'PLAYHOST'					   => 'http://mms.52huike.com:8080',
);