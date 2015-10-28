<?php
namespace Home\Controller;
use Think\Controller;

class UpimgController extends Controller{
    private $uid;
    public function uploder(){
        $up=new \Org\Util\UserUpload;
        $user_id = $_POST['authId'];
        if(!isset($user_id)||$user_id!==$this->uid){
            $this->ajaxReturn('','系统错误，请联系管理员',0,'json');
        }
        if (!empty($_FILES)) {
            $ext = pathinfo($_FILES['Filedata']['name']);
            $ext = strtolower($ext['extension']);
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath   = APP_PATH . 'uploads/';
            if(!is_dir($targetPath)){
                mkdir($targetPath,0777,true);
            }
            $new_file_name = $user_id.'_ori.'.$ext;
            $targetFile = $targetPath . $new_file_name;
            move_uploaded_file($tempFile,$targetFile);
            if( !file_exists( $targetFile ) ){
                $ret['result_code'] = 0;
                $ret['result_des'] = 'upload failure';
            } elseif( !$imginfo=$up->getImageInfo($targetFile) ) {
                $ret['result_code'] = 101;
                $ret['result_des'] = 'File is not exist';
            } else {
                $img = '/Application/uploads/'.$new_file_name;
                $up->resize($img);
                $this->updateuser($user_id, $img);
                $ret['result_code'] = 1;
                $ret['result_des'] = $img;
            }
        } else {
            $ret['result_code'] = 100;
            $ret['result_des'] = 'No File Given';
        }
        exit(json_encode($ret));
    }
    
    private function updateuser($uid,$imgurl){
        $db=M(security_users);
        $db->where("id='$uid'")->setField('imgUrl', $imgurl);
    }


    private function resize(){
        $up=new \Org\Util\UserUpload;
        if( !$image = I('img') ){
        $ret['result_code'] = 101;
        $ret['result_des'] = "图片不存在";
        } else {
            $image = C('APP_BIND_DOMAIN') . $image;
            $info = $up->getImageInfo($image);

            if( !$info ){
                $ret['result_code'] = 102;
                $ret['result_des'] = "图片不存在";
                $ret['result_img'] = $image;
            } else {
                $x = I('post.x');
                $y = I('post.y');
                $w = I('post.w');
                $h = I('post.h');
                $width = $srcWidth = $info['width'];
                $height = $srcHeight = $info['height'];
                $type = empty($type)?$info['type']:$type;
                $type = strtolower($type);
                unset($info);
                // 载入原图
                $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
                $srcImg = $createFun($image);
                //创建缩略图
                if($type!='gif' && function_exists('imagecreatetruecolor')){
                    $thumbImg = imagecreatetruecolor($width, $height);
                }   
                else{
                   $thumbImg = imagecreate($width, $height); 
                }
                
                // 复制图片
                if(function_exists("imagecopyresampled")){
                    imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight);
                }
                else{
                    imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight);
                }
                
                if('gif'==$type || 'png'==$type) {
                    $background_color  =  imagecolorallocate($thumbImg,  0,255,0);
                    imagecolortransparent($thumbImg,$background_color);
                }
                
        // 对jpeg图形设置隔行扫描
                
        if('jpg'==$type || 'jpeg'==$type) imageinterlace($thumbImg,1);
            // 生成图片
            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            $thumbname01 = str_replace("ori", "200", $image);
            $imageFun($thumbImg,$thumbname01);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            $ret['result_code'] = 1;
            $ret['result_des'] = array(
                "pic"   => str_replace(C('APP_BIND_DOMAIN'), "", $thumbname01)
                );
            }
        }
        echo json_encode($ret);
        exit();
    }
    
    public function _initialize(){
        $this->uid=session('userid');
    }
    
    public function _empty(){
        echo '您访问的页面不存在';
    }
}
