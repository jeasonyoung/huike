<?php
/**
 * 机构套餐控制器。
 *
 * @author yangyong <jeason1914@qq.com>
 */
namespace Agency\Controller;
use Agency\Controller\AdminController;

class JigouTaocanController extends AdminController{

    //session id
    protected $session_class_id = 'class_session_id';

    /**
     * 默认入口函数。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('调用index...');
        $this->listAction();
    }

    /**
     * 机构套餐列表页面。
     * @return void
     */
    public function listAction(){
        if(APP_DEBUG) trace('调用listAction...');
        //初始化模型
        $_model = D('JigouTaocan');
        //设置查询数据
        $this->assign('data_list',$_model->loadTaocans(I('session.JGID','')));
        //显示列表
        $this->display('list');
    }

    /**
     * 选择考试。
     * @return void
     */
    public function select_exam(){
        if(APP_DEBUG) trace('调用select_exam...');
        //初始化模型
        $_model = D('JigouTaocan');
        //设置机构考试集合
        $this->assign('exams',$_model->loadExams(I('session.JGID','')));
        //显示
        $this->display();
    }

    /**
     * 选择套餐分类。
     * @return void
     */
    public function select_type(){
        if(APP_DEBUG) trace('调用select_type...');
        //初始化模型
        $_model = D('JigouTaocan');
        //设置机构考试集合
        $this->assign('taocanClasses',$_model->loadTaocanClasses(I('session.JGID',''),I('examId','')));
        //显示
        $this->display();
    }

    /**
     * 新增机构套餐。
     * @return void
     */
    public function add(){
        if(APP_DEBUG) trace('调用add...');
        //加载当前机构ID
        $_agencyId = I('session.JGID','');
        //初始化模型
        $_model = D('JigouTaocan');
        //是否提交数据
        if(IS_POST){
            if(APP_DEBUG) trace('提交新增数据...'.serialize(I()));
            //验证数据
            if(!$result = $_model->create()){
                if(APP_DEBUG) trace('验证数据错误!');
                $this->error($_model->getError());
            }else{
                $result['JGID'] = $_agencyId;
                $result['create_time'] = $result['last_time'] = date('Y-m-d H:i:s',time());

                //获取班级IDsSession
                $_classeIds = session($this->session_class_id);
                if($_classeIds){
                    //清空Session
                    session($this->session_class_id,null);
                }
                //
                if($_model->addTaocan($result,$_classeIds)){
                    $this->success('新增自定义套餐成功!',U('JigouTaocan/listAction'));
                }else{
                    $this->error('新增自定义套餐失败!');
                }
            }
        }else{
            if(APP_DEBUG)trace('加载添加页面...');
            //设置机构套餐分类集合
            $this->assign('taocanClasses',$_model->loadTaocanClasses($_agencyId));
            //设置选择的套餐分类
            $this->assign('type_id',I('get.typeId',''));
            //设置默认使用年
            $this->assign('use_year',date('Y'));
            //设置排序号
            $this->assign('order',$_model->loadMaxSort($_agencyId));

            //获取设置Session
            $_classes = session($this->session_class_id);

            //设置数据集合
            $this->assign('data_list', $_model->loadSessionAgencyClasses($_agencyId, $_classes ?: []));
            
            //显示模版
            $this->display('add');
        }
    }

    /**
     * 删除session中的班级
     * @return void
     */
    public function del_add(){
        if(APP_DEBUG) trace('调用del_add...');
        //获取ID
        $_classId = I('classId','');
        //是否存在
        if(isset($_classId) && !empty($_classId)){
            //获取session
            $_classes = session($this->session_class_id);
            if(isset($_classes) && !empty($_classes)){
                //查找删除的班级
                $_key = array_search($_classId, $_classes);
                //
                if($_key || $_key == 0){
                    //移除班级
                    unset($_classes[$_key]);
                    //添加到Session
                    session($this->session_class_id,$_classes);
                }
            }
        }
        //显示
        $this->add();
    }

    /**
     * 添加班级到自定义套餐
     * @return void
     */
    public function select_subject(){
        if(APP_DEBUG) trace('调用add_class...');
        //获取套餐ID
        $_groupId = I('get.groupId','');
        //获取套餐分类ID
        $_typeId = I('get.typeId','');
        //初始化模型
        $_model = D('JigouTaocan');
        $_type = $_model->loadTaocanClass($_typeId);
        if(!$_type){
            $this->error("加载套餐分类[$_typeId]失败,请联系技术人员!");
        }else{
            //设置套餐ID
            $this->assign('group_id',$_groupId);
            //设置套餐分类ID
            $this->assign('type_id',$_typeId);
            //设置考试名称
            $this->assign('exam_name',$_type['exam_name']);
            //设置科目集合
            $this->assign('subjects',$_model->loadSubjects($_type['exam_id']));
            //显示(选择科目)
            $this->display();
        }
    }

    /**
     * 选择机构班级到自定义套餐。
     * @return void
     */
    public function select_class(){
        if(APP_DEBUG) trace('调用select_class...');
        $_groupId = I('groupId','');
        if(IS_POST){//添加班级
            $_classIds = array_column(I('classIds',array(array())),0);
            //反馈
            $_result = array();
            if(isset($_groupId) && !empty($_groupId)){
                //清空Session
                session($this->session_class_id,null);
                //
                if(!empty($_classIds)){
                    //初始化模型
                    $_model = D('JigouTaocan');
                    //插入数据
                    $_model->addTaocanAgencyClass($_groupId,$_classIds);
                }
                $_result['url'] = U("JigouTaocan/edit",array('id' => $_groupId));
            }else{//保存到Session中
                if(!empty($_classIds)){
                    $_classes = session($this->session_class_id);
                    if(!$_classes){
                        $_classes = [];
                    }
                    //合并数组
                    $_classes = array_merge($_classes,$_classIds);
                    //数组去重复
                    $_classes = array_unique($_classes,SORT_NUMERIC);
                    //添加到Session
                    session($this->session_class_id,$_classes);
                }
                $_result['url'] = U("JigouTaocan/add",array('typeId' => I('typeId','')));
            }
            $this->ajaxReturn($_result);
        }else{
            //初始化模型
            $_model = D('JigouTaocan');
            //设置套餐ID
            $this->assign('group_id', $_groupId);
            //设置套餐分类ID
            $this->assign('type_id',I('get.typeId',''));
            //获取科目subjectId
            $this->assign('data_list',$_model->loadAgencyClasses(I('session.JGID',''),I('get.subjectId'),$_groupId));
            //显示(选择机构班级)
            $this->display();
        }
    }

    /**
     * 添加系统套餐到自定义套餐。
     * @return void
     */
    public function select_taocan(){
        if(APP_DEBUG) trace('调用select_taocan...');
        //加载当前机构ID
        $_agencyId = I('session.JGID','');
        //获取套餐ID
        $_groupId = I('get.groupId','');
        //获取套餐分类ID
        $_typeId = I('get.typeId','');
        //初始化模型
        $_model = D('JigouTaocan');
        $_type = $_model->loadTaocanClass($_typeId);
        if(!$_type){
            $this->error("加载套餐分类[$_typeId]失败,请联系技术人员!");
        }else{
            //设置套餐ID
            $this->assign('group_id',$_groupId);
            //设置套餐分类ID
            $this->assign('type_id',$_typeId);
            //设置考试名称
            $this->assign('exam_name',$_type['exam_name']);
            //设置科目集合
            $this->assign('sysTaocans',$_model->loadSysTaocans($_agencyId,$_type['exam_id']));
            //显示(选择科目)
            $this->display();
        }
    }

    /**
     * 编辑机构套餐。
     * @return void
     */
    public function edit(){
        if(APP_DEBUG) trace('调用add...');
        //加载当前机构ID
        $_agencyId = I('session.JGID','');
        //初始化模型
        $_model = D('JigouTaocan');
        //是否提交数据
        if(IS_POST){
            if(APP_DEBUG) trace('提交更新数据...'.serialize(I()));
            //验证数据
            if(!$result = $_model->create()){
                if(APP_DEBUG) trace('验证数据错误!');
                $this->error($_model->getError());
            }else{
                $result['JGID'] = $_agencyId;
                $result['last_time'] = date('Y-m-d H:i:s',time());
                //
                if($_model->updateTaocan($result)){
                    $this->success('更新套餐分类成功!',U('JigouTaocan/listAction'));
                }else{
                    $this->error('更新套餐分类失败,请联系技术人员');
                }
            }
        }else{
            if(APP_DEBUG)trace('加载修改页面...');
            //获取套餐ID
            $_id = I('get.id','');
            //设置机构套餐分类集合
            $this->assign('taocanClasses',$_model->loadTaocanClasses($_agencyId));
            //设置数据
            $this->assign('data', $_model->loadTaocan($_id));
            //设置数据集合
            $this->assign('data_list', $_model->loadTaocanAgencyClasses($_id));
            //显示模版
            $this->display('edit');
        }
    }

    /**
     * 删除机构套餐班级。
     * @return void
     */
    public function del_edit(){
        if(APP_DEBUG) trace('调用del_edit...');
        //初始化模型
        $_model = D('JigouTaocan');
        //删除套餐班级
        $_model->delTaocanAgencyClass(I('get.id',''),I('get.classId',''));
        //显示
        $this->edit();
    }

    /**
     * 删除机构套餐。
     * @return void
     */
    public function del(){
        if(APP_DEBUG) trace('调用del...');
        $_id = I('id');
        //判断删除主键ID是否存在
        if(isset($_id) && !empty($_id)){
            //初始化模型
            $_model = D('JigouTaocan');
            if($_model->deleteTaocan($_id)){
                if(APP_DEBUG) trace("删除数据[$_id]成功!");
                $this->success('删除成功',U('JigouTaocan/listAction'));
            }else{
                if(APP_DEBUG) trace("删除数据[$_id]失败!");
                $this->error('删除失败,请联系技术人员');
            }
        }else{
            $this->index();
        }
    }
}