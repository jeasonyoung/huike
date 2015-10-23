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
    //protected $allowType   = array('json');

    /**
     * 默认入口。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('0.默认入口...');
        $this->send_callback(true,null,'接口测试!');
    }

    /**
     * 1.验证学员登录
     * @return void
     */
    public function login(){
        if(APP_DEBUG) trace('1.验证学员登录...');
        $_model = D('Student');
        $_callback = $_model->verifyStudentUser(I('agencyId'),I('username'),I('pwd'));
        $this->response($_callback);
    }

    /**
     * 2.学员用户订单下的套餐／班级集合
     * @return void
     */
    public function courses(){
        if(APP_DEBUG) trace('2.加载学员用户订单下的套餐/班级集合...');

        //$userId
        echo "学员用户订单下的套餐／班级集合...";
    }

    /**
     * 3.班级下课程资源集合
     * @return void
     */
    public function lessons(){
        if(APP_DEBUG) trace('3.班级下课程资源集合...');
        //$classId
        echo "班级下课程资源集合...";
    }

    /**
     * 4.班级下免费课程资源集合
     * @return void
     */
    public function lessons_free(){
        if(APP_DEBUG) trace('4.班级下免费课程资源集合...');
        //$classId
        echo "班级下免费课程资源集合...";
    }

    /**
     * 5.考试类别集合
     * @return void
     */
    public function categories(){
        if(APP_DEBUG) trace('5.考试类别集合...');
        echo "考试类别集合...";
    }

    /**
     * 6.考试类别下的考试集合
     * @return void
     */
    public function exams(){
        if(APP_DEBUG) trace('6.考试类别下的考试集合...');
        //$categoryId
        echo "考试类别下的考试集合...";
    }

    /**
     * 7.机构考试下的套餐/班级集合。
     * @return void
     */
    public function packages(){
        if(APP_DEBUG) trace('7.机构考试下的套餐/班级集合...');
        //$agencyId,$examId
        echo "机构考试下的套餐/班级集合...";
    }

    /**
     * 8.更新学习记录
     * curl -l -H "Content-type: application/json" -X POST -d '{"agencyId":"13521389587","lessonId":123,"studentId":456,"position":40,"status":0}' http://php_examw.com/huike/api.php/api/m/learning
     * @return void
     */
    public function learning(){
        if(APP_DEBUG) trace('8.更新学习记录...');
        $_req = load_json_request();
        if(!$_req){
            if(APP_DEBUG) trace('未提交JSON格式数据!');
            $this->send_callback(false,null,'未提交JSON格式数据!');
        }
        //检查所属机构ID
        if(!isset($_req['agencyId']) || empty($_req['agencyId'])){
            if(APP_DEBUG) trace('未提交所属机构ID(agencyId)!');
            $this->send_callback(false,null,'未提交所属机构ID(agencyId)!');
        }
        //检查课程资源ID
        if(!isset($_req['lessonId']) || empty($_req['lessonId'])){
            if(APP_DEBUG) trace('未提交课程资源ID(lessonId)!');
            $this->send_callback(false,null,'未提交课程资源ID(lessonId)!');
        }
        //检查学员用户ID
        if(!isset($_req['studentId']) || empty($_req['studentId'])){
            if(APP_DEBUG) trace('未提交学员用户ID(studentId)!');
            $this->send_callback(false,null,'未提交学员用户ID(studentId)!');
        }
        //检查播放位置
        if(!isset($_req['position'])){
            if(APP_DEBUG) trace('未提交学习进度(position)!');
            $this->send_callback(false,null,'未提交学习进度(position)!');
        }
        //检查进度状态
        if(!isset($_req['status'])){
            if(APP_DEBUG) trace('未提交进度状态(status)!'.$_req['status']);
            $this->send_callback(false,null,'未提交进度状态(status)!');
        }
        //初始化模型
        $_model = D('LearnLog');
        //保存数据
        $this->response($_model->saveLearning($_req));
    }

    /**
     * 9.学员答疑主题
     * @return void
     */
    public function load_topics(){
        if(APP_DEBUG) trace('9.学员答疑主题...');
        //$agencyId,$userId
        echo "加载学员答疑主题...";
    }

    /**
     * 10.新增答疑主题
     * @return void
     */
    public function add_topic(){
        if(APP_DEBUG) trace('10.新增答疑主题...');
        echo "新增答疑主题...";
    }

    /**
     * 11.学员答疑明细
     * @return void
     */
    public function load_details(){
        if(APP_DEBUG) trace('11.学员答疑明细...');
        //$topicId
        echo "加载学员答疑明细...";
    }

    /**
     * 12.新增学员答疑明细
     * @return void
     */
    public function add_detail(){
        if(APP_DEBUG) trace('12.新增学员答疑明细...');
        echo "新增学员答疑明细...";
    }

    /**
     * 13.新增学员建议
     * @return void
     */
    public function add_suggest(){
        if(APP_DEBUG) trace('13.新增学员建议...');
        echo "新增学员建议...";
    }


    /**
     * 发送反馈消息
     * @param  boolean $success 请求状态。
     * @param  mixed   $data    反馈数据。
     * @param  string  $msg     错误消息。
     * @return void
     */
    protected function send_callback($success=false,$data=null,$msg=''){
        $this->response(build_callback_data($success,$data,$msg));
    }

    /**
     * 重载发送反馈数据对象。
     * @param  mixed   $data 反馈数据对象。
     * @param  string  $type 反馈数据类型。
     * @param  integer $code HTTP状态码
     * @return void        
     */
    protected function response($data,$type='json',$code=200) {
        if(APP_DEBUG) trace('发送反馈数据=>'.serialize($data));
        parent::response($data,$type,$code);
    }

    /**
     * 重载覆盖编码数据函数。
     * @param  mixed  $data 数据
     * @param  string $type 数据格式
     * @return string       编码后的数据
     */
    protected function encodeData($data,$type=''){
        if(APP_DEBUG) trace('重载覆盖编码数据函数...');
        if(empty($data))  return '';
        if('json' == $type){
            $this->setContentType($type);
            return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }
        return parent::encodeData($data,$type);
    }
}