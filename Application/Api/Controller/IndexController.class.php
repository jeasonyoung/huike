<?php
/**
 * Api接口控制器
 */
namespace Api\Controller;

class IndexController extends AuthController {
    /**
     * 0.默认入口。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('0.默认入口...');
        $this->send_callback_success(array('ok' => '接口测试!'));
    }

    /**
     * 1.验证学员登录。
     * @param  string $token    令牌
     * @param  string $username 用户名
     * @param  string $pwd      密码:md5(学员用户账号+学员用户输入明文密码)
     * @param  int    $terminal 终端(2:苹果,3-安卓)
     * @param  string $sign     签名
     * @return json
     */
    public function login($token=null,$username=null,$pwd=null,$terminal=2,$sign=null){
        if(APP_DEBUG) trace('1.验证学员登录...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'    => $token,
            'username' => $username,
            'pwd'      => $pwd,
            'terminal' => $terminal,
            'sign'     => $sign,
        ));
        //验证签名成功
        if($_agencyId){
            //初始化模型
            $_model = D('User');
            //验证数据
            $_callback = $_model->login($_agencyId,$username,$pwd,$terminal);
            //
            $this->response($_callback);
        }
    }

    /**
     * 根据随机用户ID获取真实用户ID。
     * @param  string $randUserId  随机用户ID
     * @return string              真实用户ID
     */
    protected function getRealUserId($randUserId){
        if(APP_DEBUG) trace("根据随机用户ID[$randUserId]获取真实用户ID...");
        if(!isset($randUserId) || empty($randUserId)){
            if(APP_DEBUG)trace("随机用户ID为空!");
            $this->send_callback_error(-8,'用户ID为空!');
            return false;
        }else{
            $_model = D('User');
            $_userId = $_model->loadRealUserIdByRandUserId($randUserId);
            if($_userId){
                if(APP_DEBUG)trace("真实用户ID:$_userId");
                return $_userId;
            }else{
                if(APP_DEBUG)trace("用户已在其他设备上登录!");
                $this->send_callback_error(-9,'用户已在其他设备上登录');
                return false;
            }
        }
    }

    /**
     * 2.学员用户订单下的套餐／班级集合
     * @param  string $token        令牌
     * @param  string $randUserId   随机用户ID
     * @param  string $sign         签名
     * @return json                 返回数据
     */
    public function courses($token=null,$randUserId=null,$sign=null){
        if(APP_DEBUG) trace('2.加载学员用户订单下的套餐/班级集合...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'      => $token,
            'randUserId' => $randUserId,
            'sign'       => $sign,
        ));
        //验证签名成功
        if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
            $_model = M('AppCoursesView');
            $_model = $_model->field(array('pid','id','name','type'))
                             ->where("`userId` = '%s'",array($_userId))
                             ->limit(C('QUERY_LIMIT_TOP'))
                             ->order("`orderno` desc")
                             ->select();
            if($_model){
                $this->send_callback_success($_model);
            }else{
                if(APP_DEBUG) trace('无数据!');
                $this->send_callback_error(0,'无数据!');
            }
        }
    }

    /**
     * 3.班级下课程资源集合
     * @param  string $token   令牌
     * @param  string $randUserId   随机用户ID
     * @param  string $classId 班级ID
     * @param  bool   $free    是否免费
     * @param  string $sign    签名
     * @return json            返回数据
     */
    public function lessons($token=null,$randUserId=null,$classId=null,$free=false,$sign=null){
        if(APP_DEBUG) trace('3.班级下课程资源集合...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'         => $token,
            'randUserId'    => $randUserId,
            'classId'       => $classId,
            'free'          => $free,
            'sign'          => $sign,
        ));
        //验证签名成功
        if($_agencyId){
            //字段定义
            $_fields = array('id','name','videoUrl'=>'videoUrl',
                             'highVideoUrl'=>'highVideoUrl',
                             'superVideoUrl'=>'superVideoUrl',
                             'time','orderNo'=>'orderNo');
            if($free){//免费试听
                //初始化免费试听数据模型
                $_model = M('AppFreeLessonsView');
                //设置字段
                $_model = $_model->field($_fields);
            }else{//班级课程
                ///TODO:须判断班级是在订单中存在

                //初始化免费试听数据模型
                $_model = M('AppLessonsView');
                //设置字段
                $_model = $_model->field($_fields);
            }
            //查询条件
            $_model = $_model->where("`classId` = '%s'", array($classId))
                             ->limit(C('QUERY_LIMIT_TOP'))
                             ->order("`orderNo` desc")
                             ->select();
            if($_model){
                //机构播放服务器地址
                $_playHost = M('Jigou')->field('FlvDomain')
                                       ->where("`JGID` = '%s'", array($_agencyId))
                                       ->find();
                if($_playHost && isset($_playHost['FlvDomain'])){
                    $_host = $_playHost['FlvDomain'];
                    foreach ($_model as &$_item) {
                        $_item['videoUrl']      = $_host.$_item['videoUrl'];
                        $_item['highVideoUrl']  = $_host.$_item['highVideoUrl'];
                        $_item['superVideoUrl'] = $_host.$_item['superVideoUrl'];
                    }

                    $this->send_callback_success($_model);
                }else{
                    $this->send_callback_error(-30,'未配置流媒体服务器地址，请联系管理员!');
                }
            }else{
                if(APP_DEBUG) trace('无数据!');
                $this->send_callback_error(0,'无数据!');
            }
       }
    }

    /**
     * 4.考试集合。
     * @param  string $token 令牌
     * @param  string $sign  签名
     * @return json          返回数据
     */
    public function exams($token=null,$sign=null){
        if(APP_DEBUG) trace('4.考试类别下的考试集合...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token' => $token,
            'sign'  => $sign,
        ));
        //验证签名成功
        if($_agencyId){
            //初始化机构数据模型
            $_model = M('Jigou')->field('allExams')
                                ->where("`JGID` = '%s'", array($_agencyId))
                                ->find();
            if($_model && isset($_model['allExams'])){
                if(APP_DEBUG) trace('考试ID集合:'.$_model['allExams']);
                //初始化考试数据模型
                $_result = M('Examclass')->field(array(
                    'examId' => 'id',
                    'CnName' => 'name',
                    'EnName' => 'abbr',
                    'SortID' => 'orderNo',
                ))->where(array('examId' => array('in', $_model['allexams'])))
                  ->limit(C('QUERY_LIMIT_TOP'))
                  ->order("SortID")
                  ->select();

                if($_result){
                    $this->send_callback_success($_result);
                }else{
                    if(APP_DEBUG) trace('无数据!');
                    $this->send_callback_error(0,'无数据!');
                }
            }else{
                if(APP_DEBUG) trace('无数据，机构未设置考试!');
                $this->send_callback_error(-40,'无数据，机构未设置考试!');
            }
        }
    }

    /**
     * 5.机构考试下的套餐/班级集合。
     * @param  string $token   令牌
     * @param  string $examId  考试ID
     * @param  string $sign    签名
     * @return json            返回数据
     */
    public function packages($token=null,$examId=null,$sign=null){
        if(APP_DEBUG) trace('5.机构考试下的套餐/班级集合...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'  => $token,
            'examId' => $examId,
            'sign'   => $sign,
        ));
        //验证签名成功
        if($_agencyId){
            //初始化数据模型
            $_model = M('AppGroupsView')->field(array(
                'pid','id','name','type','orderNo'=>'orderNo'
            ))->where("`agencyId` = '%s' and `examId` = '%s'", array($_agencyId,$examId))
              ->limit(C('QUERY_LIMIT_TOP'))
              ->order("`orderNo` desc")
              ->select();

            if($_model){
                $this->send_callback_success($_model);
            }else{
                if(APP_DEBUG) trace('无数据!');
                $this->send_callback_error(0,'无数据!');
            }
        }
    }

    /**
     * 6.提交学习记录。
     * @param  string $token        令牌
     * @param  string $randUserId   随机用户ID
     * @param  string $lessonId     课程资源ID
     * @param  int    $pos          播放位置(单位:秒)
     * @param  bool   $status       状态(true:完成,false:未完成)
     * @param  string $sign         签名
     * @return json                 返回数据
     */
    public function learning($token=null,$randUserId=null,$lessonId=null,$pos=0,$status=false,$sign=null){
        if(APP_DEBUG) trace('6.更新学习记录...');
        if(IS_POST){
            //验证签名
            $_agencyId = $this->verificationSignature(array(
                'token'         => $token,
                'randUserId'    => $randUserId,
                'lessonId'      => $lessonId,
                'pos'           => $pos,
                'status'        => $status,
                'sign'          => $sign,
            ));
            //验证签名成功
            if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
                //检查课程资源ID
                if(!isset($lessonId) || empty($lessonId)){
                    if(APP_DEBUG) trace('课程资源ID为空!');
                    $this->send_callback_error(-60,'课程资源ID为空!');
                }else{
                    //字段数据
                    $_data = array(
                        'pos'      => intval($pos),
                        'status'   => ($status ? 1 : 0),
                        'last_time'=> date('Y-m-d H:i:s', time()),
                    );
                    //初始化数据模型
                    $_model = M('LearnLog');
                    //判断用户课程资源是否存在
                    $_result = $_model->field('id')
                                      ->where("`UID` = '%s' and `LessonID` = '%s'",array($_userId,$lessonId))
                                      ->find();

                    if($_result && isset($_result['id']) &&($_id = $_result['id'])){
                        if(APP_DEBUG) trace('更新...'.$_result['id']);
                        //更新
                        $_result = $_model->where("`id` = '%s'", array($_result['id']))
                                          ->save($_data);
                        if($_result){
                            $_result = $_id;
                        }
                    }else{
                        if(APP_DEBUG) trace('新增...');
                        $_data['UID'] = $_userId;
                        $_data['LessonID'] = $lessonId;
                        $_data['create_time'] = date('Y-m-d H:i:s',time());
                        //新增
                        $_result = $_model->add($_data);
                    }

                    //结果
                    if($_result){
                        $this->send_callback_success($_result);
                    }else{
                        $this->send_callback_error(-61,'提交失败，未知错误!');
                    }
                }

            }
        }else{
            if(APP_DEBUG)trace('须POST提交!');
            $this->send_callback_error(-1,'须POST提交!');
        }
    }

    /**
     * 7.加载学员答疑主题
     * @param  string $token      令牌
     * @param  string $randUserId 随机用户ID
     * @param  string $sign       签名
     * @return json               返回数据
     */
    public function load_topics($token=null,$randUserId=null,$sign=null){
        if(APP_DEBUG) trace('7.学员答疑主题...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'      => $token,
            'randUserId' => $randUserId,
            'sign'       => $sign,
        ));
        //验证签名成功
        if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
            //初始化数据模型
            $_model = M('AppTopicsView')->field(array(
                'id','title','content',
                'lessonID'  =>'lessonId',
                'lessonName'=>'lessonName',
                'lastTime'  =>'lastTime',
            ))->where("`userId` = '%s'",array($_userId))
              ->limit(C('QUERY_LIMIT_TOP'))
              ->order("`lastTime` desc")
              ->select();

            if($_model){
                $this->send_callback_success($_model);
            }else{
                if(APP_DEBUG) trace('无数据!');
                $this->send_callback_error(0,'无数据!');
            }
        }
    }

    /**
     * 8.新增答疑主题
     * @param  string $token      令牌
     * @param  string $randUserId 随机用户ID
     * @param  string $lessonId   课程资源ID
     * @param  string $title      标题
     * @param  string $content    内容
     * @param  string $sign       签名
     * @return json               返回数据
     */
    public function add_topic($token=null,$randUserId=null,$lessonId=null,$title=null,$content=null,$sign=null){
        if(APP_DEBUG) trace('8.新增答疑主题...');
        if(IS_POST){
            //验证签名
            $_agencyId = $this->verificationSignature(array(
                'token'         => $token,
                'randUserId'    => $randUserId,
                'lessonId'      => $lessonId,
                'title'         => $title,
                'content'       => $content,
                'sign'          => $sign,
            ));
            //验证签名成功
            if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
                //检查课程资源ID
                if(!isset($lessonId) || empty($lessonId)){
                    if(APP_DEBUG) trace('课程资源ID为空!');
                    $this->send_callback_error(-80,'课程资源ID为空!');
                }else if(!isset($title) || empty($title)){
                    if(APP_DEBUG) trace('主题标题为空!');
                    $this->send_callback_error(-81,'主题标题为空!');
                }else{
                    //初始化数据模型
                    $_model = M('LearnAsk')->add(array(
                        'UID'      => $_userId,
                        'LessonID' => $lessonId,
                        'Title'    => $title,
                        'Content'  => $content,
                        'create_time' => date('Y-m-d H:i:s',time()),
                        'last_time'   => date('Y-m-d H:i:s',time()),
                    ));
                    //
                    if($_model){
                        if(APP_DEBUG)trace("新增主题成功:$_model...");
                        $this->send_callback_success($_model);
                    }else{
                        if(APP_DEBUG)trace('新增主题失败!');
                        $this->send_callback_error(-82,'提交失败，未知错误!');
                    }
                }
            }
        }else{
            if(APP_DEBUG)trace('须POST提交!');
            $this->send_callback_success(-1,'须POST提交!');
        }
    }

    /**
     * 9.获取答疑明细
     * @param  string $token    令牌
     * @param  string $topicId  主题ID
     * @param  string $sign     签名
     * @return json             返回数据
     */
    public function load_details($token=null,$topicId=null,$sign=null){
        if(APP_DEBUG) trace('9.学员答疑明细...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'     => $token,
            'topicId'   => $topicId,
            'sign'      => $sign,
        ));
        //验证签名成功
        if($_agencyId){
            //初始化数据模型
            $_model = M('AppDetailsView')->field(array(
                'id','content',`userId`=>'userId',
                'userName'=>'userName','createTime'=>'createTime',
            ))->where("`topicId` = '%s'",array($topicId))
              ->limit(C('QUERY_LIMIT_TOP'))
              ->order("`createTime` desc")
              ->select();
            //
            if($_model){
                $this->send_callback_success($_model);
            }else{
                if(APP_DEBUG) trace('无数据!');
                $this->send_callback_error(0,'无数据!');
            }
        }
    }

    /**
     * 10.新增答疑明细
     * @param  string $token      令牌
     * @param  string $randUserId 随机用户ID
     * @param  string $topicId  主题ID
     * @param  string $content  明细内容
     * @param  string $sign     签名
     * @return json             返回数据
     */
    public function add_detail($token=null,$randUserId=null,$topicId=null,$content=null,$sign=null){
        if(APP_DEBUG) trace('12.新增学员答疑明细...');
        if(IS_POST){
            //验证签名
            $_agencyId = $this->verificationSignature(array(
                'token'     => $token,
                'randUserId'=> $randUserId,
                'topicId'   => $topicId,
                'content'   => $content,
                'sign'      => $sign,
            ));
            //验证签名成功
            if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
                //检查主题ID
                if(!isset($topicId) || empty($topicId)){
                    if(APP_DEBUG) trace('主题ID为空!');
                    $this->send_callback_success(-100,'主题ID为空!');
                }else{
                    //初始化数据模型
                    $_model = M('LearnAnswer')->add(array(
                        'AskID' => $topicId,
                        'UID'   => $_userId,
                        'Content' => $content,
                        'create_time' => date('Y-m-d H:i:s',time()),
                    ));
                    //
                    if($_model){
                        if(APP_DEBUG)trace("新增主题明细成功:$_model...");
                        $this->send_callback_success($_model);
                    }else{
                        if(APP_DEBUG)trace('新增主题明细失败!');
                        $this->send_callback_error(-101,'提交失败，未知错误!');
                    }
                }
            }
        }else{
            if(APP_DEBUG)trace('须POST提交!');
            $this->send_callback_error(-1,'须POST提交!');
        }
    }

    /**
     * 11.新增学员建议
     * @param  string $token      令牌
     * @param  string $randUserId 随机用户ID
     * @param  string $content  建议内容
     * @param  string $sign     签名
     * @return json             返回数据
     */
    public function add_suggest($token=null,$randUserId=null,$content=null,$sign=null){
        if(APP_DEBUG) trace('13.新增学员建议...');
        if(IS_POST){
            //验证签名
            $_agencyId = $this->verificationSignature(array(
                'token'     => $token,
                'randUserId'=> $randUserId,
                'content'   => $content,
                'sign'      => $sign,
            ));
            //验证签名成功
            if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
                //检查建议内容
                if(!isset($content) || empty($content)){
                    if(APP_DEBUG) trace('建议内容为空!');
                    $this->send_callback_success(-110,'建议内容为空!');
                }else{
                    //初始化数据模型
                    $_model = M('LearnAdvice')->add(array(
                        'UID'     => $_userId,
                        'Content' => $content,
                        'create_time' => date('Y-m-d H:i:s',time()),
                    ));
                    //
                    if($_model){
                        if(APP_DEBUG)trace("新增学员建议成功:$_model...");
                        $this->send_callback_success($_model);
                    }else{
                        if(APP_DEBUG)trace('新增学员建议失败!');
                        $this->send_callback_error(-111,'提交失败，未知错误!');
                    }
                }
            }
        }else{
            if(APP_DEBUG)trace('须POST提交!');
            $this->send_callback_error(-1,'须POST提交!');
        }
    }
}