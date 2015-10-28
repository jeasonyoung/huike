<?php
return array(
    //'配置项'=>'配置值'
    'agencyRules'   =>  array(
        array('Company','require','公司名称不能为空'),
        array('Company','','此公司已存在',0,'unique',3),
        array('abbr_cn','require','对外名称不能为空',1),
        array('abbr_en','require','英文简称不能为空',1),
        array('login_icon','require','登陆logo不能为空',1),
        array('logo_icon','require','登陆后logo不能为空',1),
        array('video_icon','require','视频logo不能为空',1),
        array('video_icon','require','视频logo不能为空',1),
        array('domain','require','机构合作域名不能为空',1),
        array('AllExams','chk_arrEmpty','包含考试不能为空',0,'function'),
        array('Province','require','请选择所在省',1),
        array('City','require','请选择所在市',1),
        array('County','require','请选择所在区县',1),
        array('Address','require','详细地址不能为空',1),
        array('Contact','require','联系人不能为空',1),
        array('HZTel','tel_validate','合作电话不正确',0,'function'),
        array('StuTel','tel_validate','学员联系电话不正确',0,'function'),
        array('Introduce','require','机构简介不能为空',1),
        array('Md5Key','require','机构秘钥不能为空',1),
        array('APPKey','require','手机端秘钥不能为空',1),
    ),
);