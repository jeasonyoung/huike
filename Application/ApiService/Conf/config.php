<?php
//模块自定义配置(可覆盖全局配置)
return array(
	//'配置项'=>'配置值'
    'DEFAULT_CHARSET'      => 'utf-8',//默认输出编码
    'URL_MODEL'            => 1,//URL模式(0:普通模式,1:PATHINFO模式,2:REWRITE模式,3:兼容模式)
    'URL_CASE_INSENSITIVE' => true,//不区分URL大小写
    'URL_ROUTER_ON'        => true,
    'URL_ROUTE_RULES'      => array(
        'api/m/login'                       =>  'index/login',//1.验证学员登录
        'api/m/courses/:userId'             =>  'index/courses',//2.我的课程(套餐/班级集合)
        'api/m/lessons/:classId'            =>  'index/lessons',//3.班级下课程资源(视频)集合
        'api/m/freelessons/:classId'        =>  'index/lessons_free',//4.班级下免费课程资(视频)源集合[用于免费体验]
        //'api/m/categories'                  =>  'index/categories',//5.考试类别集合(含有考试的考试类别)[用于免费体验]
        'api/m/exams/:agencyId'             =>  'index/exams',//6.考试类别下考试下集合[用于免费体验]
        'api/m/packages/:agencyId/:examId'  =>  'index/packages',//7.机构考试下套餐/班级集合[用于免费体验]
        'api/m/learning'                    =>  'index/learning',//8.上传学习记录(POST)
        'api/m/aq/topic/:userId'            =>  'index/load_topics',//9.获取答疑主题数据
        'api/m/aq/topic'                    =>  'index/add_topic',//10.新增答疑主题(POST)
        'api/m/aq/details/:topicId'         =>  'index/load_details',//11.答疑主题明细
        'api/m/aq/details'                  =>  'index/add_detail',//12.新增答疑明细(POST)
        'api/m/aq/suggest'                  =>  'index/add_suggest',//13.新增学员建议(POST)
    ),
    'READ_DATA_MAP'        =>  true,//开启查询字段映射
    'QUERY_LIMIT_TOP'      =>  20,
    'DIGEST_REALM'         => 'huike',//摘要认证realm
);