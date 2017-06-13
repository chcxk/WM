<?php
namespace APi\Controller;
use Think\Controller;

class PostController extends Controller
{
    protected $users_model;
    protected $post_model;

    public function _initialize()
    {
        $this->users_model = D("Common/Users");
        $this->post_model = D("Portal/Common/Posts");
    }

    public function posts($keyword)
    {
        if ($keyword != null) {
            $result = $this->post_model->field("id,post_hits,post_date,post_title,cover")->where("post_status=1 and title like'%$keyword%'")->order("id desc")->limit(100)->select();
        } else {
            $result = $this->post_model->field("id,post_hits,post_date,post_title,cover")->where("post_status=1")->order("id desc")->limit(100)->select();
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

    public function postRelease($userid, $title, $content, $image)
    {
        if ($userid == null || $title == null || $content == null || $image == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $this->post_model->post_author = $userid;
            $this->post_model->post_date = date("y-m-d h:s:i", time());
            $this->post_model->post_content = str_replace('\n', "", "$content");
            $this->post_model->title = $title;
            $this->post_model->post_status = 0;
            $this->post_model->comment_count = 0;
            $this->post_model->post_hits = 0;
            $this->post_model->post_like = 0;
            $this->post_model->istop = 0;
            $this->post_model->cover = $image;
            $this->post_model->state = 0;
            if ($this->post_model->add() == false) {
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

    public function postInfo($id)
    {
        if ($id == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
        } else {
            //todo 评论 相关推荐
            $result = $this->post_model->field("post_author,post_date,post_title,post_content,post_hits,post_like")->where("id=$id")->select();
            $data["data"] = $result;
            $data['status'] = "1";
        }
        $this->ajaxReturn($data, 'JSON');
    }

    public function postLike($userid, $postid)
    {
        if ($userid == null || $postid == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            //todo 判断用户是否存在
            $result = $this->post_model->where("id=$postid")->find();
            $num = (int)($result['post_like']) + 1;
            $this->post_model->post_like = $num;
            $this->post_model->id = $result["id"];
            if ($result["like_user"] != null) {
                $this->post_model->like_user .= "," . $userid;
            } else
                $this->post_model->like_user .= $userid;
            $this->post_model->save();
            $data['status'] = "1";
            $data['data'] = "ok";
            $this->ajaxReturn($data, 'JSON');
        }
    }
}