<?php
/**
 * Api接口控制器
 */
namespace ApiService\Controller;
use Think\Controller;

class IndexController extends Controller {
    /**
     * 默认入口。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('0.默认入口...');
        $_data = array(
            array(
                'title'     =>  '1.验证学员登录',
                'url'       =>  'api/m/login',
                'method'    =>  'POST or GET',
                'params'    =>  array(
                    'agencyId'  =>  '所属机构ID',
                    'username'  =>  '学员用户账号',
                    'pwd'       =>  '密码:md5(md5(所属机构ID+学员用户账号)+md5(学员用户输入明文密码))',
                ),
                'results'    =>  array(
                    'success' => 'true or false',
                    'data'    => '验证成功后返回学员用户ID',
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title'     =>  '2.学员用户订单下的套餐／班级集合',
                'url'       =>  'api/m/courses/{userId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'userId'    =>  '学员用户ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'pid'       =>  '父级(套餐 or 班级)ID',
                            'id'        =>  '当前(套餐 or 班级)ID',
                            'name'      =>  '当前(套餐 or 班级)名称',
                            'type'      =>  '类型(package:套餐,class:班级)',
                            'orderNo'   =>  '排序号'
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '3.班级下课程资源集合',
                'url'       =>  'api/m/lessons/{classId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'classId'    =>  '机构班级ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'id'            =>  '课程资源ID',
                            'name'          =>  '课程资源名称',
                            'videoUrl'      =>  '视频URL',
                            'highVideoUrl'  =>  '高清视频URL',
                            'superVideoUrl' =>  '超清视频URL',
                            'time'          =>  '视频时长',
                            'orderNo'       => '排序号',
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '4.班级下试听课程资源集合',
                'url'       =>  'api/m/freelessons/{classId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'classId'    =>  '机构班级ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'id'            =>  '课程资源ID',
                            'name'          =>  '课程资源名称',
                            'videoUrl'      =>  '视频URL',
                            'highVideoUrl'  =>  '高清视频URL',
                            'superVideoUrl' =>  '超清视频URL',
                            'time'          =>  '视频时长',
                            'orderNo'       => '排序号',
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            // array(
            //     'title' =>  '5.考试类别集合',
            // ),
            array(
                'title' =>  '5.机构下考试集合',
                'url'       =>  'api/m/exams/{agencyId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'agencyId'    =>  '机构ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'id'        =>  '考试ID',
                            'name'      =>  '考试名称',
                            'abbr'      =>  '考试EN简称',
                            'orderNo'   =>  '排序号',
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '6.机构考试下的套餐/班级集合',
                'url'       =>  'api/m/packages/{agencyId}/{examId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'agencyId'  =>  '机构ID',
                    'examId'    =>  '考试ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'pid'       =>  '父级(套餐 or 班级)ID',
                            'id'        =>  '当前(套餐 or 班级)ID',
                            'name'      =>  '当前(套餐 or 班级)名称',
                            'type'      =>  '类型(package:套餐,class:班级)',
                            'orderNo'   =>  '排序号'
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '7.上传学习记录',
                'url'       =>  'api/m/learning',
                'method'    =>  'POST',
                'params(JSON)'    =>  array(
                    'lessonId'  =>  '课程资源ID',
                    'studentId' =>  '学员用户ID',
                    'position'  =>  '学习进度(单位:秒)',
                    'status'    =>  '状态(0:未学完,1:已学完)',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title'     =>  '8.获取学员答疑主题',
                'url'       =>  'api/m/aq/topic/{userId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'userId'    =>  '学员用户ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'id'            =>  '主题ID',
                            'lessonId'      =>  '课程资源ID',
                            'lessonName'    =>  '课程资源名称',
                            'title'         =>  '主题标题',
                            'content'       =>  '主题内容',
                            'lastTime'      =>  '更新时间'
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '9.新增答疑主题',
                'url'       =>  'api/m/aq/topic',
                'method'    =>  'POST',
                'params(JSON)'    =>  array(
                    'title'     =>  '主题标题',
                    'content'   =>  '主题内容',
                    'lessonId'  =>  '课时资源ID',
                    'studentId' =>  '学员用户ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '10.获取学员答疑明细',
                'url'       =>  'api/m/aq/details/{topicId}',
                'method'    =>  'GET',
                'params'    =>  array(
                    'topicId'    =>  '答疑主题ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'data'    => array(
                        array(
                            'id'            =>  '明细ID',
                            'topicId'       =>  '所属主题ID',
                            'content'       =>  '内容',
                            'userId'        =>  '用户ID',
                            'userName'      =>  '用户名称',
                            'createTime'    =>  '创建时间'
                        ),
                     ),
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '11.新增答疑明细',
                'url'       =>  'api/m/aq/details',
                'method'    =>  'POST',
                'params(JSON)'    =>  array(
                    'topicId'   =>  '所属主题ID',
                    'content'   =>  '明细内容',
                    'userId'    =>  '学员用户ID',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'msg'     => '错误消息',
                ),
            ),
            array(
                'title' =>  '12.新增学员建议',
                'url'       =>  'api/m/aq/suggest',
                'method'    =>  'POST',
                'params(JSON)'    =>  array(
                    'studentId' =>  '所属学员用户ID',
                    'content'   =>  '建议内容',
                ),
                'results'   => array(
                    'success' => 'true or false',
                    'msg'     => '错误消息',
                ),
            ),
        );
        $this->send_callback(true,$_data,'接口说明!');
    }

    /**
     * 1.验证学员登录
     * @return void
     */
    public function login(){
        if(APP_DEBUG) trace('1.验证学员登录...');
        $_token = I('token','');
        $_username = I('username','');
        $_pwd = I('pwd','');
        $_sign = I('sign','');

        


        //初始化模型
        $_model = D('Student');
        //验证数据
        $_callback = $_model->verifyStudentUser(I('agencyId'),I('username'),I('pwd'));
        $this->response($_callback);
    }

    /**
     * 2.学员用户订单下的套餐／班级集合
     * @return void
     */
    public function courses(){
        if(APP_DEBUG) trace('2.加载学员用户订单下的套餐/班级集合...');
        //初始化模型
        $_model = D('Course');
        //查询数据
        $this->response($_model->loadCourses(I('userId')));
    }

    /**
     * 3.班级下课程资源集合
     * @return void
     */
    public function lessons(){
        if(APP_DEBUG) trace('3.班级下课程资源集合...');
        //初始化模型
        $_model = D('Lesson');
        //查询数据
        $this->response($_model->loadLessons(I('classId')));
    }

    /**
     * 4.班级下免费课程资源集合
     * @return void
     */
    public function lessons_free(){
        if(APP_DEBUG) trace('4.班级下免费课程资源集合...');
        //初始化模型
        $_model = D('Lesson');
        //查询数据
        $this->response($_model->loadFreeLessons(I('classId')));
    }

    // /**
    //  * 5.考试类别集合
    //  * @return void
    //  */
    // public function categories(){
    //     if(APP_DEBUG) trace('5.考试类别集合...');
    //     echo "考试类别集合...";
    // }

    /**
     * 6.考试类别下的考试集合
     * @return void
     */
    public function exams(){
        if(APP_DEBUG) trace('6.考试类别下的考试集合...');
        //初始化模型
        $_model = D('Exam');
        //查询数据
        $this->response($_model->loadExams(I('agencyId')));
    }

    /**
     * 7.机构考试下的套餐/班级集合。
     * @return void
     */
    public function packages(){
        if(APP_DEBUG) trace('7.机构考试下的套餐/班级集合...');
        //初始化模型
        $_model = D('Group');
        //查询数据
        $this->response($_model->loadGroupClasses(I('agencyId'),I('examId')));
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
     * 9.加载学员答疑主题
     * @return void
     */
    public function load_topics(){
        if(APP_DEBUG) trace('9.学员答疑主题...');
        //检查用户ID
        $_userId = I("userId");
        if(!isset($_userId) || empty($_userId)){
            if(APP_DEBUG) trace('未提交用户ID(userId)!');
            $this->send_callback(false,null,'未提交用户ID(userId)!');
        }
        //初始化模型
        $_model = D('Topic');
        //反馈数据
        $this->response($_model->loadTopics($_userId));
    }

    /**
     * 10.新增答疑主题
     * curl -l -H "Content-type: application/json" -X POST -d '{"studentId":456,"lessonId":121,"title":"测试答疑主题标题","content":"测试答疑主题标题"}' http://php_examw.com/huike/api.php/api/m/aq/topic
     * @return void
     */
    public function add_topic(){
        if(APP_DEBUG) trace('10.新增答疑主题...');
        $_req = load_json_request();
        if(!$_req){
            if(APP_DEBUG) trace('未提交JSON格式数据!');
            $this->send_callback(false,null,'未提交JSON格式数据!');
        }
        //检查主题标题
        if(!isset($_req['title']) || empty($_req['title'])){
            if(APP_DEBUG) trace('未提交主题标题(title)!');
            $this->send_callback(false,null,'未提交主题标题(title)!');
        }
        //检查主题内容
        if(!isset($_req['content']) || empty($_req['content'])){
            if(APP_DEBUG) trace('未提交主题内容(content)!');
            $this->send_callback(false,null,'未提交主题内容(content)!');
        }
        //检查课时资源ID
        if(!isset($_req['lessonId']) || empty($_req['lessonId'])){
            if(APP_DEBUG) trace('未提交课时资源ID(lessonId)!');
            $this->send_callback(false,null,'未提交课时资源ID(lessonId)!');
        }
        //检查用户ID
        if(!isset($_req['studentId']) || empty($_req['studentId'])){
            if(APP_DEBUG) trace('未提交用户ID(studentId)!');
            $this->send_callback(false,null,'未提交用户ID(studentId)!');
        }
        //初始化模型
        $_model = D('Topic');
        //保存数据
        $this->response($_model->addTopic($_req));
    }

    /**
     * 11.学员答疑明细
     * @return void
     */
    public function load_details(){
        if(APP_DEBUG) trace('11.学员答疑明细...');
        //初始化模型
        $_model = D('Detail');
        //反馈数据
        $this->response($_model->loadDetails(I('topicId')));
    }

    /**
     * 12.新增学员答疑明细
     * curl -l -H "Content-type: application/json" -X POST -d '{"userId":456,"topicId":121,"content":"测试答疑明细"}' http://php_examw.com/huike/api.php/api/m/aq/details
     * @return void
     */
    public function add_detail(){
        if(APP_DEBUG) trace('12.新增学员答疑明细...');
        $_req = load_json_request();
        if(!$_req){
            if(APP_DEBUG) trace('未提交JSON格式数据!');
            $this->send_callback(false,null,'未提交JSON格式数据!');
        }
        //检查答疑主题ID
        if(!isset($_req['topicId']) || empty($_req['topicId'])){
            if(APP_DEBUG) trace('未提交答疑主题ID(topicId)!');
            $this->send_callback(false,null,'未提交答疑主题ID(topicId)!');
        }
        //检查内容
        if(!isset($_req['content']) || empty($_req['content'])){
            if(APP_DEBUG) trace('未提交内容(content)!');
            $this->send_callback(false,null,'未提交内容(content)!');
        }
        //检查用户ID
        if(!isset($_req['userId']) || empty($_req['userId'])){
            if(APP_DEBUG) trace('未提交用户ID(userId)!');
            $this->send_callback(false,null,'未提交用户ID(userId)!');
        }
        //初始化模型
        $_model = D('Detail');
        //保存数据
        $this->response($_model->addDetail($_req));
    }

    /**
     * 13.新增学员建议
     * curl -l -H "Content-type: application/json" -X POST -d '{"studentId":456,"content":"测试建议提交"}' http://php_examw.com/huike/api.php/api/m/aq/suggest
     * @return void
     */
    public function add_suggest(){
        if(APP_DEBUG) trace('13.新增学员建议...');
        $_req = load_json_request();
        if(!$_req){
            if(APP_DEBUG) trace('未提交JSON格式数据!');
            $this->send_callback(false,null,'未提交JSON格式数据!');
        }
        //检查学员用户ID
        if(!isset($_req['studentId']) || empty($_req['studentId'])){
            if(APP_DEBUG) trace('未提交学员用户ID(studentId)!');
            $this->send_callback(false,null,'未提交学员用户ID(studentId)!');
        }
        //检查建议内容
        if(!isset($_req['content']) || empty($_req['content'])){
            if(APP_DEBUG) trace('未提交建议内容(content)!');
            $this->send_callback(false,null,'未提交建议内容(content)!');
        }
        //初始化模型
        $_model = D('Suggest');
        //保存数据
        $this->response($_model->saveSuggest($_req));
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
     * @return void        
     */
    protected function response($data,$type='json') {
        if(APP_DEBUG) trace('发送反馈数据=>'.serialize($data));
        header('HTTP/1.1 200 OK');
        exit($this->encodeData($data));
    }

    /**
     * 重载覆盖编码数据函数。
     * @param  mixed  $data 数据
     * @param  string $type 数据格式
     * @return string       编码后的数据
     */
    protected function encodeData($data){
        if(APP_DEBUG) trace('编码数据函数...');
        if(headers_sent()) return;
        if(empty($charset))$charset = C('DEFAULT_CHARSET');
        header('Content-Type: application/json;charset='.$charset);
        return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}