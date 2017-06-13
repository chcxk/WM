<?php
namespace APi\Controller;
use Think\Controller;

class ProductController extends Controller
{
    protected $users_model;
    protected $product_model;
    protected $product_type_model;
    public function _initialize()
    {
        $this->users_model = D("Common/Users");
        $this->product_model = D("Common/Product");
        $this->product_type_model = D("Common/ProductType");
    }
    public function getType()
    {
        $result = $this->product_type_model->where("type_id=0")->select();
        if(count($result)==0)
        {
            $data['status'] = "0";
            $data['data'] = "没有数据";
        }
       else
       {
           $data['status'] = "1";
           $data['data'] = $result;
       }
        $this->ajaxReturn($data, 'JSON');
    }
public function getSubclass($id)
{
    $result = $this->product_type_model->where("type_id=$id")->select();
    if (count($result) == 0) {
        $data['status'] = "0";
        $data['data'] = "没有数据";
    } else {
        $data['status'] = "1";
        $data['data'] = $result;
    }
    $this->ajaxReturn($data, 'JSON');
}




    public function productList($type, $ptType)
    {
        $str = "";
        if ($type != null) {
            $str .= "where type=$type ";
        }
        if ($ptType != null) {
            if ($str == "") {
                $str .= " where ptType='$ptType'' ";
            } else {
                $str .= " and ptType='$ptType'' ";
            }
        }
        $result = $this->product_model->query(" select  a.avatar,a.vip,a.company,a.city ,b.title,b.hitnum,b.ptType,c.type_name
 from mw_users  a
   join mw_product  b
     on a.id=b.userid
   join mw_product_type  c
     on b.type=c.id" . $str);

        if ($result != null) {
            $data['status'] = "1";
            $data['data'] = $result;

        } else {
            $data['status'] = "0";
            $data['data'] = "没有数据";
        }


        $this->ajaxReturn($data, 'JSON');
    }
    public function productRelease($userid, $type, $title, $info, $voice, $video, $image)
    {
        if ($type == null || $title == null || $userid == null || $info == null ) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $this->product_model->type = $type;
            $this->product_model->title = $title;
            $this->product_model->info = $info;
            $this->product_model->voice = $voice;
            $this->product_model->video = $video;
            $this->product_model->image = $image;
            $this->product_model->create_date = date("y-m-d h:i:s",time());
            $this->product_model->userid = $userid;

            if ($this->product_model->add() == false) {
                $data['status'] = "0";
                $data['data'] = "发布失败";
                $this->ajaxReturn($data, 'JSON');
            } else {
                $data['status'] = "1";
                $data['data'] = "ok";
                $this->ajaxReturn($data, 'JSON');
            }
        }
    }

    public function shareInfo($id)
    {
        if ($id == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";

        } else {
            $result = $this->share_model->query("select  mw_users.avatar,mw_users.vip,mw_users.credit,mw_share.* from mw_users   join mw_share   on mw_users.id=mw_share.userid WHERE mw_share.state=1");
            $data["data"] = $result;
            $data['status'] = "1";
        }
        $this->ajaxReturn($data, 'JSON');
    }
}