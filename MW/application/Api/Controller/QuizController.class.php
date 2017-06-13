<?php
namespace APi\Controller;
use Think\Controller;

class QuizController extends Controller
{
    protected $users_model;
    protected $quiz_model;
    protected $quiz_answer_model;

    public function _initialize()
    {
        $this->users_model = D("Common/Users");
        $this->quiz_model = D("Common/Quiz");
        $this->quiz_answer_model = D("Common/QuizAnswer");
    }

    public function quizList($keyword)
    {
        if ($keyword != null) {
            $result = $this->quiz_model->query("select  mw_users.avatar,mw_users.vip,mw_quiz.* from mw_users  
                   join mw_quiz on mw_users.id=mw_quiz.userid WHERE mw_quiz.title like '%$keyword%' order by id desc limit 100 ");
        } else {
            $result = $this->quiz_model->query("select  mw_users.avatar,mw_users.vip,mw_share.company,mw_quiz.* from mw_users  
                   join mw_quiz on mw_users.id=mw_quiz.userid order by id desc limit 100 ");
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


    public function quizRelease($userid, $title, $content, $image)
    {
        if ($userid == null || $title == null || $content == null || $image == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $this->quiz_model->userid = $userid;
            $this->quiz_model->create_date = date("y-m-d h:s:i", time());
            $this->quiz_model->post_content = str_replace('\n', "", "$content");
            $this->quiz_model->title = $title;
            $this->quiz_model->image = $image;
            if ($this->quiz_model->add() == false) {
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

    public function quizInfo($id)
    {
        if ($id == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
        } else {
            //todo 相关推荐
            $result = $this->quiz_model->query("select  mw_users.avatar,mw_users.vip,mw_quiz.* from mw_users  
                   join mw_quiz on mw_users.id=mw_quiz.userid WHERE mw_users.id=$id");
            $data["data"] = $result;
            $data['status'] = "1";
        }
        $this->ajaxReturn($data, 'JSON');
    }
    //回答
    public function answerSubmit($quiz_id,$content,$userid )
    {
        if ($quiz_id == null || $content == null || $userid == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";
        } else {
            $this->quiz_answer_model->quiz_id = $quiz_id;
            $this->quiz_answer_model->content = $content;
            $this->quiz_answer_model->userid = $userid;
            $this->quiz_answer_model->answer_date = date("y-m-d h:i:s", time());
            if ($this->quiz_answer_model->add() != null) {
                $result = $this->quiz_model->where("id=$quiz_id")->find();
                $this->quiz_model->id = $result["id"];
                $this->quiz_model->answer_num += 1;
                $this->quiz_model->save();
                $data['status'] = "1";
                $data['data'] = "ok";

            } else {

                $data['status'] = "0";
                $data['data'] = "回答失败";
            }
        }
        $this->ajaxReturn($data, 'JSON');
    }
    //采纳
    public function quizChoose($quiz_id, $answer_id)
    {
        if ($quiz_id == null || $answer_id == null) {
            $data['status'] = "0";
            $data['data'] = "参数不能为空";

        } else {
            $result = $this->quiz_model->where("id=$quiz_id")->find();
            if ($result["best_id"] != null) {
                $data['status'] = "0";
                $data['data'] = "该问题已经有最佳回答";

            } else {
                $this->quiz_model->best_id = $answer_id;
                $this->quiz_model->id = $quiz_id;
                if ($this->quiz_model->save() != null) {
                    $data['status'] = "1";
                    $data['data'] = "ok";
                } else {
                    $data['status'] = "0";
                    $data['data'] = "采纳失败";
                }
            }
            $this->ajaxReturn($data, 'JSON');
        }
    }
//todo 提问打赏
}