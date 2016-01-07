<?php
/**
 *  网站调用数据接口
 */
 namespace Api\Controller;
 class SiteController extends AuthController{
	 public function index(){
		 $this->send_callback_success(array('ok' => '接口测试!'));
	 }
	 
	 /**
	  *  获取班级相关信息
	  *  @param $token 令牌(一般为机构英语名称abbr_en)
	  *	 @param $classid 班级id
	  *  @param $sign 签名
	  *  @return json
	  */
	 public function single_class_info($token=null,$classid=null,$sign=null){
		 $agency_id = $this->verificationSignature(array('token' => $token,'classid'=>$classid,'sign'=>$sign));
		 if($agency_id){
			$db_jigou = M();
			//缓存接口班级信息
			$_cache_class = S('api_class_info_'.$classid);
			if(!$_cache_class){
				$where = array('JGID' => $agency_id,'classid' => $classid,'StateTF' => 1);
				$_cache_class = $db_jigou->table('view_site_class')->where($where)->find();
				$_cache_class['catalog'] = $this->class_list($_cache_class['SCID']);
			}
			$this->response($_cache_class);
		 }else{
			exit('error');
		 }
	 }
	 
	 /**
	  * 获取相关班级
	  * @param $token 令牌(一般为机构英语名称abbr_en)
	  * @param $classid 当前班级id
	  * @param $limit 取记录数
	  * @param $sign 签名
	  */
	 public function relate_class($token=null,$classid=null,$limit=null,$sign=null){
		 $agency_id = $this->verificationSignature(array('token' => $token,'classid'=>$classid,'limit'=>$limit,'sign'=>$sign));
		 if($agency_id){
			 $db = M('jigou_class');
			 $data = $db->where(array('JGCID'=>array('neq',$classid),'JGID'=>$agency_id))->field(array('JGCID','CnName','PicPath'))->limit($limit)->select();
			 $this->response($data);
		 }
	 }
	 
	 /**
	  * 获取套餐相关信息 
	  * @param $token 令牌(一般为机构英语名称abbr_en)
	  * @param $tcid 套餐id
	  * @param $sign 签名
      * @return json	  
	  */
	 public function taocan_info($token=null,$tcid=null,$sign=null){
		 $agency_id = $this->verificationSignature(array('token' => $token,'tcid'=>$tcid,'sign'=>$sign));
		 if($agency_id){
			 $db = M();
			 $where = array('JGID'=>$agency_id,'TaoCanID'=>$tcid);
			 $info = $db->table('view_site_taocan')->where($where)->find();
			 $this->response($info);
		 }
	 }
	 
	 /**
	  * 获取班级章节列表
	  *	@param $classid 单科id
	  */
	 private function class_list($SCID){
		 $db = M('class_resources');
		 $data = $db->where(array('SCID'=>$SCID))->field(array('CnName','TimeLen','FreeTF','LessonID','SortID'))->order('SortID asc')->select();
		 return $data;
	 }
 }
 