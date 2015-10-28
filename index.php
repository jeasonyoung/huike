<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

function show_bug($msg){
  echo "<pre>";
  var_dump($msg);
  echo "</pre>";
}
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 绑定默认模块
//define('BIND_MODULE', 'Home');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','./Application/');

// 定义图片存放位置
//define('APP_IMG',APP_PATH.'/public/img/');

// 定义JS存放位置
//define('APP_JS', APP_PATH.'/public/js/');

//视频播放器
//define('APP_BFQ',APP_PATH.'/public/player/');


// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单