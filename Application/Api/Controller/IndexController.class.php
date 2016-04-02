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
     * 学员注册。
     * @param  string  $token    令牌
     * @param  string  $username 用户名
     * @param  string  $pwd      密码
     * @param  string  $realname 真实姓名
     * @param  string  $phone    手机号码
     * @param  string  $email    电子邮箱
     * @param  integer $terminal 终端类型(2:苹果,3-安卓)
     * @param  string  $sign     签名
     * @return json              返回数据。
     */
    public function register($token=null,$username=null,$pwd=null,$realname=null,$phone='',$email='',$terminal=0,$sign=null){
        if(APP_DEBUG) trace('0.学员注册...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
                'token'     => $token,
                'username'  => $username,
                'pwd'       => $pwd,
                'realname'  => $realname,
                'phone'     => $phone,
                'email'     => $email,
                'terminal'  => $terminal,
                'sign'      => $sign,
            ));
        //验证签名成功
        if($_agencyId){
            //注册学员
            $_callback = D('User')->register($_agencyId,
                $username,$pwd,$realname,$phone,$email,$terminal);
            //反馈数据
            $this->response($_callback);
        }
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
     * @param  string $terminal     终端(0:默认值[不传值],2:苹果,3-安卓)
     * @param  string $sign         签名
     * @return json                 返回数据
     */
    public function courses($token=null,$randUserId=null,$terminal=0,$sign=null){
        if(APP_DEBUG) trace('2.加载学员用户订单下的套餐/班级集合...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'      => $token,
            'randUserId' => $randUserId,
            'terminal'   => $terminal,
            'sign'       => $sign,
        ));
        //验证签名成功
        if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){

            /*  if($terminal == 2){
                 $_model = M('AppGroupsView');
                 $_model = $_model->field(array('pid','id','name','type'))
                                  ->where("`agencyId` = '%s'", array($_agencyId))
                                  ->limit(C('QUERY_LIMIT_TOP'))
                                  ->order("`orderno` Asc")
                                  ->select();
             }else{ */
                $_model = M('AppCoursesView');
                $_model = $_model->field(array('pid','id','name','type'))
                                 ->where("`userId` = '%s'",array($_userId))
                                 ->limit(C('QUERY_LIMIT_TOP'))
                                 ->order("`orderno` Asc")
                                 ->select();
            // }

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
                //$_playHost = M('Jigou')->field('FlvDomain')
                //                      ->where("`JGID` = '%s'", array($_agencyId))
                //                       ->find();
                //if($_playHost && isset($_playHost['FlvDomain'])){
                    //$_host = $_playHost['FlvDomain'];
					$_host = C("PLAYHOST");
                    foreach ($_model as &$_item) {
                        $_item['videoUrl']      = $_host.$_item['videoUrl'];
                        //$_item['highVideoUrl']  = $_host.$_item['highVideoUrl'];
                        //$_item['superVideoUrl'] = $_host.$_item['superVideoUrl'];
                    }

                    $this->send_callback_success($_model);
                //}else{
                //    $this->send_callback_error(-30,'未配置流媒体服务器地址，请联系管理员!');
                //}
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
                    'ExamID' => 'id',
                    'CnName' => 'name',
                    'EnName' => 'abbr',
                    'SortID' => 'orderNo',
                ))->where(array('ExamID' => array('in', $_model['allExams'])))
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
            ))->where(array('agencyId'=>$_agencyId,'examId'=>$examId))
              ->limit(C('QUERY_LIMIT_TOP'))
              ->order("`orderNo` asc")
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

	/**
     * 12.加载产品集合。
     * @param  string $token  机构令牌
     * @param  string $examId 所属考试
     * @param  string $sign   签名数据
     * @return mixed         返回产品数据
     */
    public function load_products($token=null,$examId=null,$sign=null){
        if(APP_DEBUG) trace('12.加载产品集合...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'  => $token,
            'examId' => $examId,
            'sign'   => $sign,
        ));
        //验证签名成功
        if($_agencyId){
            //初始化数据模型
            $_model = M('AppProductsView')->field(array(
                'id','name','type','useYear','img','content',
                'teacherName','classNum','oldPrice','price'
            ))->where("`agencyId` = '%s' and `examId` = '%s' ",array($_agencyId,$examId))
              ->limit(C('QUERY_LIMIT_TOP'))
              ->order("`orderNo` asc")
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
     * 13.获取用户帐户余额。
     * @param  string  $token      机构令牌
     * @param  string  $randUserId 随机用户ID
     * @param  integer $terminal   终端(0:默认值[不传值],2:苹果,3-安卓)
     * @param  string  $sign       签名数据
     * @return mixed               返回余额数据
     */
    public function load_balance($token=null,$randUserId=null,$terminal=0,$sign=null){
        if(APP_DEBUG) trace('13.获取用户帐户余额...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'      => $token,
            'randUserId' => $randUserId,
            'terminal'   => $terminal,
            'sign'       => $sign,
        ));
        //验证签名成功
        if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
			 //查询是否有未被验证的充值记录
            $recharge = M('MobileRecharges')->where(array('userId'=>$_userId,'terminal'=>$terminal,'isValid'=>0))->find();
            if(!empty($recharge)){
                //二次验证充值
                $this->valid_charge_ticket($recharge['id'],$recharge['receipt'],false);
            }
            //查询数据
            $_model = M('user')->where(array('JGID'=>$_agencyId,'UserID'=>$_userId));
            $balance = $_model->getField('Money');
            if(!$balance) $balance = 0.0;

            //返回数据
            $this->send_callback_success(array('balance'=> $balance));
        }
    }

    /**
     * 14.验证充值
     * @param  string  $token      机构令牌
     * @param  string  $randUserId 随机用户ID
     * @param  string  $chargeId   充值项目ID
     * @param  float   $price      充值金额
     * @param  string  $receipt    充值票据
     * @param  integer $terminal   终端(0:默认值[不传值],2:苹果,3-安卓)
     * @param  string  $sign       签名数据
     * @return mixed               返回结果
     */
    public function verify_charge($token=null,$randUserId=null,$chargeId=null,$price=0.0,$receipt=null,$terminal=0,$sign=null){
        if(APP_DEBUG) trace('14.验证充值...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'      => $token,
            'randUserId' => $randUserId,
            'chargeId'   => $chargeId,
            'price'      => $price,
            'receipt'    => $receipt,
            'terminal'   => $terminal,
            'sign'       => $sign,
        ));
        //验证签名成功
        if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
			if(APP_DEBUG) trace("receipt-hex:$receipt");
			$receiptBase64 = '';
			$receiptBin = hex2bin($receipt);
			if($receiptBin){
				$receiptBase64 = base64_encode($receiptBin);
			}
			if(APP_DEBUG) trace("receipt-base64:$receiptBase64");
            //插入到数据库
            $result = M('MobileRecharges')->add(array(
                    'agencyId' => $_agencyId,
                    'userId'   => $_userId,
                    'terminal' => $terminal,
                    'chargeId' => $chargeId,
                    'price'    => $price,
                    'receipt'  => $receiptBase64,
                ));
            if($result){//插入数据库成功
                //二次验证票据
                $this->valid_charge_ticket($result,$receiptBase64);
            }else{
                $this->send_callback_error(0,'插入数据库失败!');
            }
        }
    }
    //14.1验证充值票据
    private function valid_charge_ticket($id,$receipt,$response=true){
        if(APP_DEBUG)trace("验证充值票据:$id");
		//sandbox
        $url = 'https://sandbox.itunes.apple.com/verifyReceipt/';
        //stand
        //$url = 'https://buy.itunes.apple.com/verifyReceipt/';
        //
        $data = '{"receipt-data":"'.$receipt.'"}';
        if(APP_DEBUG)trace("url=>$url");
		if(APP_DEBUG)trace("POST=>$data");
        $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); // 跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//$data JSON类型字符串
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type:application/json','Content-Length:'.strlen($data)));
		$data = curl_exec($ch);
        //curl_close($ch);
        if(APP_DEBUG)trace("反馈数据=>$data");
        $result = json_decode($data,true);
        if($result){
            if($result['status'] == 0){
                $data = M('MobileRecharges')->where(array('id'=>$id))->find();
                if(!$data || empty($data)){
                    if($response)$this->send_callback_error(0,'二次验证失败(未找到充值记录)!');
                }else{
                    //新增充值流水
                    $accId = M('UserAccount')->add(array(
                            'JGID' => $data['agencyId'],
                            'UID'  => $data['userId'],
                            'Channel' => 3,
                            'MoneyFlow' => 0,
                            'TradeType' => 0,
                            'StateTF' => 1,
                            'Money' => $data['price'],
                            'Note' => 'ios充值(资金渠道:3)',
                            'AddTime' => date('Y-m-d h:i:s'),
                        ));
                    if($accId){
                        //更新用户余额
                        M('user')->where(array('JGID'=>$data['agencyId'],'UserID'=>$data['userId']))
                                 ->setInc('Money',$data['price']);
                        //更新充值记录状态
                        M('MobileRecharges')->where(array('id'=>$id))
                                            ->setField(array('isValid'=>1));
                        //充值到帐成功
                        if($response)$this->send_callback_success();
                    }else{
                        if($response)$this->send_callback_error(0,'记录充值流水失败,请联系客户!');
                    }
                }
            }else{
                if($response)$this->send_callback_error(0,'二次验证充值失败['.$result['status'].'],请联系客服!');
            }
        }else{
            if($response)$this->send_callback_error(0,'充值验证失败(服务器没有反馈),请联系客服!');
        }
    }

    /**
     * 15.购买产品
     * @param  string  $token      机构令牌
     * @param  string  $randUserId 随机用户ID
     * @param  string  $productId  产品ID
     * @param  string  $type       产品类型(class:班级,package:套餐)
     * @param  integer $terminal   终端(0:默认值[不传值],2:苹果,3-安卓)
     * @param  string  $sign       签名数据
     * @return mixed               返回结果
     */
    public function buy($token=null,$randUserId=null,$productId=null,$type=null,$terminal=0,$sign=null){
        if(APP_DEBUG) trace('15.购买产品...');
        //验证签名
        $_agencyId = $this->verificationSignature(array(
            'token'      => $token,
            'randUserId' => $randUserId,
            'productId'  => $productId,
            'type'       => $type,
            'terminal'   => $terminal,
            'sign'       => $sign,
        ));
        //验证签名成功
        if($_agencyId && ($_userId = $this->getRealUserId($randUserId))){
			//验证是否重复购买
			$model = M('OrderDetail')->where(array('UID'=>$_userId,'JG_ProID'=>$productId))
									 ->count();
			if($model > 0){
				$this->send_callback_error(0,"已购买！");
				return;
			}

			//创建购买订单
			$orderModel = D('UserOrder');
            $order_no = $orderModel->createPayOrder($_agencyId,$_userId,$productId,$type);
            if($order_no){
				if(APP_DEBUG)trace("创建订单[$order_no]成功...");
				//加载订单
				$order = M('Orders')->where(array('OrderID'=>$order_no))->find();
				if(empty($order)){
					$this->send_callback_error(-1,"创建订单[$order_no]失败!");
					return;
				}
				//查询机构余额
				$agency_money = M('Jigou')->where(array('JGID'=>$_agencyId))->getField('Money');
				if(APP_DEBUG)trace("查询机构[$_agencyId]余额:$agency_money=>".$order['CostPrice']);
				if(empty($agency_money)){
					$this->send_callback_error(-10,'机构余额为0，请联系客服!');
					return;
				}
				if($agency_money < $order['CostPrice']){
					$this->send_callback_error(-2,"开通订单[$order_no]失败，请联系客服!");
					return;
				}
				//创建机构流水
				$accId = M('JigouAccout')->add(array(
					'JGID' => $_agencyId,
					'OrderID' => $order_no,
					'StuUserName' => $order['UserName'],
					'Money' => $order['CostPrice'],
					'AccMoney' => ($agency_money - $order['CostPrice']),
					'Channel' => 0,//0:余额
					'MoneyFlow' => 1,//0:收入,1:支出
					'TradeType' => 1,//付款
					'StateTF' => 1,//有效
					'Note' => '移动支付(ios)',
					'AddTime' => date('Y-m-d H:i:s'),
				));
				if($accId){//更新机构余额
					M('Jigou')->where(array('JGID'=>$_agencyId))
							  ->setDec('Money',$order['CostPrice']);
					//开通订单
					M('Orders')->where(array('OrderID'=>$order_no))
								  ->save(array(
									'OrderState'=>2,//开通
									'OpenTime'=>date('Y-m-d H:i:s'),
								  ));
					//开通订单明细
					//有效期
					$validity = $orderModel->loadValidity($type,$productId);
					if(empty($validity))$validity = 12;
					M('OrderDetail')->where(array('OrderID'=>$order_no))
									->save(array(
										'OrderState'=>1,//0:未开通,1:已开通,
										'OpenDate'=>date("Y-m-d H:i:s"),
										'EndDate'=>date('Y-m-d H:i:s',strtotime("+".$validity." month")),
									));
					//完成购买
					$this->send_callback_success();
				}else{
					$this->send_callback_error(-3,"创建机构订单[$order_no]流水失败，请联系客服!");
				}
            }else{
                $this->send_callback_error(0,"您选择的产品已下架[$productId]!");
            }
        }
    }
}
