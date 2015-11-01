<?php
/**
 * 机构套餐分类控制器。
 *
 * @author yangyong <jeason1914@qq.com>
 */
namespace Agency\Controller;
use Agency\Controller\AdminController;

class JigouTaocanClassController extends AdminController{
    /**
     * 默认入口函数。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('调用index...');
        $this->listAction();
    }

    /**
     * 机构套餐分类列表。
     * @return void
     */
    public function listAction(){
        if(APP_DEBUG) trace('调用listAction...');
        //初始化模型
        $model = D('JigouTaocanClass');
        //设置查询数据
        $this->assign('data_list',$model->loadTaocanClasses(I('session.JGID','')));
        //显示列表
        $this->display('list');
    }

    /**
     * 添加选择向导。
     * @return void
     */
    public function select(){
        if(APP_DEBUG) trace('调用select...');
        //初始化模型
        $model = D('JigouTaocanClass');
        //设置机构考试集合
        $this->assign('exams',$model->loadExams(I('session.JGID','')));
        //显示选择向导
        $this->display();
    }

    /**
     * 新增套餐分类。
     * @return void
     */
    public function add(){
        if(APP_DEBUG) trace('调用add...');
        //加载当前机构ID
        $_agencyId = I('session.JGID','');
        //初始化模型
        $model = D('JigouTaocanClass');
        //是否提交数据
        if(IS_POST){
            if(APP_DEBUG) trace('提交新增数据...'.serialize(I()));
            //验证数据
            if(!$result = $model->create()){
                if(APP_DEBUG) trace('验证数据错误!');
                $this->error($model->getError());
            }else{
                $result['JGID'] = $_agencyId;
                $result['create_time'] = $result['last_time'] = date('Y-m-d H:i:s',time());
                if($model->addTaocanClass($result)){
                    $this->success('新增套餐分类成功!',U('JigouTaocanClass/listAction'));
                }else{
                    $this->error('新增套餐分类失败!');
                }
            }
        }else{
            if(APP_DEBUG)trace('加载添加页面...');
            //设置系统套餐类型集合
            $this->assign('sysTaocanTypes',$model->loadSysTaocanClasses());
            //设置机构考试集合
            $this->assign('exams',$model->loadExams($_agencyId));
            //设置选中的考试值
            $this->assign('exam_id',I('get.examId',''));
            //设置默认使用年
            $this->assign('use_year',date('Y'));
            //设置排序号
            $this->assign('order',$model->loadMaxSort($_agencyId));
            //显示模版
            $this->display('add');
        }
    }

    /**
     * 编辑套餐分类。
     * @return void
     */
    public function edit(){
        if(APP_DEBUG) trace('调用add...');
        //加载当前机构ID
        $_agencyId = I('session.JGID','');
        //初始化模型
        $model = D('JigouTaocanClass');
        //是否提交数据
        if(IS_POST){
            if(APP_DEBUG) trace('提交更新数据...'.serialize(I()));
            //验证数据
            if(!$result = $model->create()){
                if(APP_DEBUG) trace('验证数据错误!');
                $this->error($model->getError());
            }else{
                $result['JGID'] = $_agencyId;
                $result['last_time'] = date('Y-m-d H:i:s',time());
                if($model->updateTaocanClass($result)){
                    $this->success('更新套餐分类成功!',U('JigouTaocanClass/listAction'));
                }else{
                    $this->error('更新套餐分类失败!');
                }
            }
        }else{
            if(APP_DEBUG)trace('加载修改页面...');
            //设置系统套餐类型集合
            $this->assign('sysTaocanTypes',$model->loadSysTaocanClasses());
            //设置机构考试集合
            $this->assign('exams',$model->loadExams($_agencyId));
            //加载数据
            $this->assign('data',$model->loadTaocanClass(I('get.id')));
            //显示模版
            $this->display();
        }
    }

    /**
     * 删除套餐分类
     * @return void
     */
    public function del(){
        if(APP_DEBUG) trace('调用del...');
        $_id = I('id');
        //判断删除主键ID是否存在
        if(isset($_id) && !empty($_id)){
            //初始化模型
            $model = D('JigouTaocanClass');
            if($model->deleteTaocanClass($_id)){
                if(APP_DEBUG) trace("删除数据[$_id]成功!");
                $this->success('删除成功',U('JigouTaocanClass/listAction'));
            }else{
                if(APP_DEBUG) trace("删除数据[$_id]失败!");
                $this->error('删除失败,请联系技术人员');
            }
        }else{
            $this->index();
        }
    }
}