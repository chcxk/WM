<?php
namespace APi\Controller;
use Think\Controller;

class ShareController extends Controller
{
    protected $users_model;
    protected $share_model;

    public function _initialize()
    {
        $this->users_model = D("Common/Users");
        $this->share_model = D("Common/Share");
    }

    public function shareList($type, $dis, $keyword)
    {
        $condition = "";
        if ($type != null) {
            $condition .= " type='$type' ";
        }
        if ($type != null) {
            if ($condition == "")
                $condition .= " city='$dis' ";
            else
                $condition .= " and city ='$dis' ";
        }
        if ($keyword != null) {
            if ($condition == "")
                $condition .= " title like'%$keyword%' ";
            else
                $condition .= " and title like'%$keyword%' ";
        }
        if ($type == null && $dis == null & $keyword == null) {
            $result = $this->share_model->query("select  mw_users.avatar,mw_users.vip,mw_share.company,mw_share.type,mw_share.city,
  mw_share.hit_num from mw_users   join mw_share   on mw_users.id=mw_share.userid WHERE mw_share.state=1");
        } else {
            $result = $this->share_model->query("select  mw_users.avatar,mw_users.vip,mw_share.company,mw_share.type,mw_share.city,
  mw_share.hit_num from mw_users   join mw_share   on mw_users.id=mw_share.userid WHERE mw_share.state=1 and " . $condition);
        }
        if ($result != null) {
            $data['status'] = "1";
            $data['data'] = $result;

        } else {
            $data['status'] = "0";
            $data['data'] = "没有数据";
        }
        $this->ajaxReturn($data, 'JSON');
    }

    public function shareRelease($userid, $type, $title, $company, $position, $city, $mobile, $info, $voice, $video, $image)
    {
        if ($type == null || $title == null || $company == null || $position == null || $city == null || $mobile == null
            || $info == null || $image == null || $userid == null
        ) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $this->share_model->type = $type;
            $this->share_model->title = $title;
            $this->share_model->company = $company;
            $this->share_model->position = $position;
            $this->share_model->city = $city;
            $this->share_model->mobile = $mobile;
            $this->share_model->info = $info;
            $this->share_model->voice = $voice;
            $this->share_model->video = $video;
            $this->share_model->image = $image;
            $this->share_model->create_date = date("y-m-d h:i:s",time());
            $this->share_model->state = 1;
            $this->share_model->hit_num = 0;
            $this->share_model->userid = $userid;

            if ($this->share_model->add() == false) {
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