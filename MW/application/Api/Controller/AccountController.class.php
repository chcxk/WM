<?php
namespace APi\Controller;
use Think\Controller;

class AccountController extends Controller
{
    protected $users_model;
    protected $smscode_model;
    public function _initialize()
    {
        $this->users_model = D("Common/Users");
        $this->smscode_model = D("Common/Smscode");
    }

    public function register($phone,$code,$pwd)
    {
        if ($phone == null || $code == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            if (count($this->users_model->where("mobile=$phone")->find()) == 0) {
                $smscode = $this->smscode_model->where("mobile=$phone and smscode=$code")->find();
                $date = $smscode["senddate"];
                $minute = floor((strtotime(date("y-m-d h:i:s")) - strtotime($date)) % 86400 / 60);
                if ($minute > 5) {
                    $data['status'] = "0";
                    $data['data'] = "验证码超时";
                    $this->ajaxReturn($data, 'JSON');
                } else {
                    $this->users_model->mobile = $phone;
                    $this->users_model->user_login = $phone;
                    $this->users_model->user_pass = sp_password($pwd);
                    $this->users_model->user_type = 2;
                    $this->users_model->type = 0;
                    $this->users_model->create_time = date("Y-m-d H:i:s");
                    $this->users_model->invitecode = uniqid();
                    if ($this->users_model->add() != false) {
                        $data['status'] = "1";
                        $data['data'] = "注册成功";
                        $this->ajaxReturn($data, 'JSON');
                    } else {
                        $data['status'] = "0";
                        $data['data'] = "注册失败";
                        $this->ajaxReturn($data, 'JSON');
                    }

                }
            }
            else
            {
                $data['status'] = "0";
                $data['data'] = "该手机号已经注册";
                $this->ajaxReturn($data, 'JSON');
            }
        }
    }
    public function login($phone, $pwd)
    {
        if ($phone == null || $pwd == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $count = $this->users_model->where(array('mobile' => $phone, 'user_pass' => sp_password($pwd)))->count();
            if ($count == 0) {
                $data['status'] = "0";
                $data['data'] = "账号或密码错误";
                $this->ajaxReturn($data, 'JSON');
            } else {
                $user = $this->users_model->field("id,user_login,avatar,mobile")->where(array('mobile' => $phone, 'user_pass' => sp_password($pwd)))->find();
                $data['status'] = "1";
                $data['data'] = $user;
                $this->ajaxReturn($data, 'JSON');
            }
        }
    }

    public function forgetPwd($phone,$code,$pwd)
    {
        if ($phone == null||$code==null||$pwd==null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $count = $this->users_model->where("mobile=$phone")->count();
            if ($count == 0) {
                $data['status'] = "0";
                $data['data'] = "该用户不存在";
                $this->ajaxReturn($data, 'JSON');
            } else {
                $smscode = $this->smscode_model->where("mobile=$phone and smscode=$code")->find();
                $date=$smscode["senddate"];
                $zero1=strtotime (date("y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时
                $zero2=strtotime ("$date");  //不能写2014-1-21 24:00:00  这样不对
                $x=ceil(($zero2-$zero1)/60);
                if ($x > 5) {
                    $data['status'] = "0";
                    $data['data'] = "验证码失效";
                    $this->ajaxReturn($data, 'JSON');
                } else {
                    $user=$this->users_model->where("mobile=$phone")->find();
                    $this->users_model->id=$user["id"];
                    $this->users_model->user_pass=sp_password($pwd);
                    $this->users_model->save();
                    $data['status'] = "1";
                    $data['data'] = $pwd;
                    $this->ajaxReturn($data, 'JSON');
                }
            }
        }
    }
}