<?php
namespace Agency\Controller;
use Think\Controller;

class AskController extends Controller{
   
        /*提问列表*/
    public function select_ask(){
        $model = D('Agency/LearnAsk');
        $data = $model->query_ask();
        $this->assign('asklist',$data);
        $this->display();
    }
    /**
     * 查询提问答疑
     * @param $askid 提问id
     */
    public function select_ask_answer($askid){
        $model = D('Agency/LearnAsk');
        $data = $model->query_ask_answer($askid);
        $this->assign('info',$data);
        $this->display();
    }
     /**
     * 删除提问
     * @param int $askid 提问ID
     * @return int 影响行数
     */
    public function del_ask($askid){
        $model = D('Agency/LearnAsk');
        if($model->delete_ask($askid)){
            $this->success('删除成功',U('ask/select_ask'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    
    public function add_answer(){
        $model = D('Agency/LearnAnswer');
        $data = $model->create();
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['JGUID'] = session('JGUID');       
        if($model->insert_answer($data)){
            $this->success('回复成功',U('ask/select_ask_answer',array('askid' => $data['AskID'])));
        }else{
            $this->error('回复失败,请联系技术人员');
        }
    }
}