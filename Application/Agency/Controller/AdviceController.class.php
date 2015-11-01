<?php
namespace Agency\Controller;
use Think\Controller;

class AdviceController extends Controller{
   
        /*留言列表*/
    public function select_advice(){
        $model = D('Agency/LearnAdvice');
        $data = $model->query_advice();
        $this->assign('advicelist',$data);
        $this->display();
    }
    
     /**
     * 删除留言
     * @param int $advid 留言ID
     * @return int 影响行数
     */
    public function del_advice($advid){
        $model = D('Agency/JigouClass');
        if($model->delete_class($advid)){
            $this->success('删除成功',U('Advice/select_advice'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
}