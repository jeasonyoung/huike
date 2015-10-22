<?php
/**
 * Api接口控制器
 */
namespace ApiService\Controller;
use Think\Controller\RestController;

class IndexController extends RestController {
    //允许的REST方法
    protected $allowMethod = array('get','post');
    //允许的请求资源类型
    protected $allowType   = array('json');

    //默认入口
    public function index(){
       echo "hello api";
    }
    //验证学员登录
    public function login($agencyId,$username,$pwd){
        echo "验证学员登录...";
    }
    //学员用户订单下的套餐／班级集合
    public function courses($userId){
        echo "学员用户订单下的套餐／班级集合...";
    }
    //班级下课程资源集合
    public function lessons($classId){
        echo "班级下课程资源集合...";
    }
    //班级下免费课程资源集合
    public function free($classId){
        echo "班级下免费课程资源集合...";
    }
    //考试类别集合
    public function categories(){
        echo "考试类别集合...";
    }
    //考试类别下的考试集合
    public function exams($categoryId){
        echo "考试类别下的考试集合...";
    }
    //机构考试下的套餐/班级集合。
    public function packages($agencyId,$examId){
        echo "机构考试下的套餐/班级集合...";
    }
    //更新学习记录
    public function learning(){
        echo "更新学校记录...";
    }
    //学员答疑主题
    public function loadTopic($agencyId,$userId){
        echo "加载学员答疑主题...";
    }
    //新增答疑主题
    public function addTopic(){
        echo "新增答疑主题...";
    }
    //学员答疑明细
    public function loadDetails($topicId){
        echo "加载学员答疑明细...";
    }
    //新增学员答疑明细
    public function addDetails(){
        echo "新增学员答疑明细...";
    }
    //新增学员建议
    public function suggest(){
        echo "新增学员建议...";
    }
}