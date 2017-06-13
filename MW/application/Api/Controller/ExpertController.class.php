<?php
namespace APi\Controller;
use Think\Controller;

class ExpertController extends Controller
{
    protected $expert_model;
    protected $expert_type_model;
    protected $user_model;

    public function _initialize()
    {
        $this->expert_model = D("Common/Expert");
        $this->user_model = D("Common/Users");
        $this->expert_type_model = D("Common/ExpertType");
    }


    public function expertType()
    {

           $result = $this->expert_type_model->where(array('type_id' => 0))->select();
           $data["status"]=1;
           $data["data"]=$result;
           $this->ajaxReturn($data, 'JSON');

    }
    public function expertSubclass($id)
    {

        $result = $this->expert_type_model->where(array('type_id' => $id))->select();
        if(count($result)!=0) {
            $data["status"] = 1;
            $data["data"] = $result;
            $this->ajaxReturn($data, 'JSON');
        }
        else
        {
            $data["status"] = 0;
            $data["data"] = "没有数据";
            $this->ajaxReturn($data, 'JSON');
        }
    }
    public function expertApply($id)
    {
        if ($id == null) {
            $data["status"] = 0;
            $data["data"] = "参数不能为空";

        } else {
            $user = $this->user_model->field("avatar,user_nicename,dis,mobile,age")->where("id=$id")->find();
            if ($user != null) {
                $data["status"] = 1;
                $data["data"] = $user;
            } else {
                $data["status"] = 0;
                $data["data"] = "该用户不存在";
            }
        }
        $this->ajaxReturn($data, 'JSON');
    }
    public function expertApply_submit($userid,$avatar,$type,$name,$age,$education,$city,$resume,$skill,$phone)
    {
        if($userid==null||$avatar==null||$type==null||$name==null||$age==null||$education==null||$city==null||$resume==null||$skill==null||$phone==null)
        //todo 预处理\
        $this->user_model->user_nicename=$name;
        $this->user_model->avatar =$avatar;
        $this->user_model->age=$age;
        $this->user_model->education=$education;
        $this->user_model->dis=$city;
        $this->user_model->resume=$resume;
        $this->user_model->skill=$skill;
        $this->user_model->expert_type=$type;
        $this->user_model->id=$userid;
        $this->user_model->save();

        $this->expert_model->user_id=$userid;
        $this->expert_model->user_name=$name;
        $this->expert_model->age=$age;
        $this->expert_model->education=$education;
        $this->expert_model->city=$city;
        $this->expert_model->mobile=$phone;
        $this->expert_model->user_resume=$resume;
        $this->expert_model->skill=$skill;
        $this->expert_model->create_date=date("y-m-d h:i:s",time());
        $this->expert_model->state=0;
        if($this->expert_model->add()!=false)
        {
            $data["status"] = 1;
            $data["data"] = "申请成功";
            $this->ajaxReturn($data, 'JSON');
        }
        else
        {
            $data["status"] = 1;
            $data["data"] = "申请失败";
            $this->ajaxReturn($data, 'JSON');
        }

    }
    public function  expertList($name)
    {
        if ($name != null) {

        }
    }
    public  function  expertInfo($id)
    {
        $expert=$this->expert_model->where("id=$id")->find();
    }
}