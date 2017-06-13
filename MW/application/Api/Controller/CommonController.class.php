<?php
namespace APi\Controller;
use Think\Controller;

class CommonController extends Controller
{
    protected $user_model;

    public function _initialize()
    {
        $this->user_model = D("Common/Users");

    }

    public function uploadFile()
    {
        if (IS_POST) {
            $name = uniqid();
            $config = array(
                'rootPath' => './' . C("UPLOADPATH"),
                'savePath' => './common/',
                // 'maxSize' => 512000,//500K
                'saveName' => $name,
                'exts' => 'jpg',
                'autoSub' => true,
                'replace' => true
            );
            $upload = new \Think\Upload($config);
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $a = $upload->getError();

                $date['status'] = "0";
                $date['data'] = $a;
                $this->ajaxReturn($date, 'JSON');
            } else {
                $img="";
                foreach($info as $file){
                    echo $file['savepath'].$file['savename'];
                   $img.=  $file['savepath'] . date("Y-m-d", time()) . "/" .$file['savename'].",";
                }
                $date['status'] = "1";
                $date['data'] = $img;
                $this->ajaxReturn($date, 'JSON');
            }
        } else {
            $date['status'] = "0";
            $date['data'] = "不是post数据";
            $this->ajaxReturn($date, 'JSON');
        }
    }
}