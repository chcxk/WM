<?php
namespace APi\Controller;
use Think\Controller;

class IndexController extends Controller
{
    protected $banner_model;
    protected $users_model;
    protected $brand_model;
    protected $ad_model;
    protected $product_model;
    protected $search_model;
    protected $posts_model;

    public function _initialize()
    {
        $this->users_model = D("Common/Users");
        $this->banner_model = D("Common/Banner");
        $this->brand_model = D("Common/brand");
        $this->ad_model = D("Common/Ad");
        $this->product_model = D("Common/Product");
        $this->search_model = D("Common/Search");
        $this->posts_model = D("PortalCommon/Posts");
    }

    public function Index()
    {
        //banner
        $data["banner"] = $this->banner_model->order("id desc")->limit(5)->select();
        //今日共享
        $sql = "select  mw_product.id,mw_product.title,mw_product.type,mw_product.hitnum,mw_users.avatar,
                   mw_product_type.type_name from mw_product   join mw_users   on mw_product.userid=mw_users.id
   join mw_product_type  on mw_product.type=mw_product_type.id  where mw_product.state=1 limit 15";
        $data["product"] = $this->product_model->query($sql);
        //广告
        $data["ad"] = $this->ad_model->field("ad_id,imgurl")->where("istop=1")->order("ad_id desc")->limit(7)->select();
        //品牌展示
        $data["brand"] = $this->brand_model->field("id,imgurl")->order("id desc")->limit(6)->select();
        //猜你喜欢
        //todo 根据用户搜索习惯
        $sql1 = "select  a.id,a.title,a.type,a.hitnum,b.avatar,
   c.type_name from mw_product  a join mw_users  b on a.userid=b.id
   join mw_product_type  c on a.type=c.id  where a.state=1 limit 10";
        $data["like"] = $this->product_model->query($sql1);
        $data['status'] = "1";
        $this->ajaxReturn($data, 'JSON');
    }

    public function search($userid)
    {
        //历史搜索
        if ($userid != null) {
            $data["history"] = $this->search_model->where("user_id=$userid")->order("id desc")->limit(8)->select();
        } else
            $data["history"] = $this->search_model->where("id=0")->select();
        //热门搜索
        $sql_hot = " SELECT  * from  ( select count(*) num,max(keyword) keyword
   from mw_search hot
   group by hot.keyword ORDER BY num desc) t limit 8";
        $data["hot"] = $this->search_model->query($sql_hot);
        //猜你喜欢
        $sql = "select  mw_product.id,mw_product.title,mw_product.createdate, mw_product_type.type_name from mw_product,mw_product_type where mw_product.type=mw_product_type.id and mw_product.state=1 order by id desc";
        $data["product"] = $this->product_model->query($sql);
        $data["status"] = 1;
        $this->ajaxReturn($data, 'JSON');
    }

    public function searchFunction($userid, $keyword, $type)
    {
        if ($keyword == null || $type == null) {
            $data['status'] = "0";
            $data['data'] = "搜索关键字不能为空";
            $this->ajaxReturn($data, 'JSON');
        } else {
            $this->search_model->user_id = $userid;
            $this->search_model->keyword = $keyword;
            $this->search_model->type = $type;
            $this->search_model->add();
            $data["data"] = "";
            if ($type == 0) {
                $sql = " select  a.id,a.title,a.type,a.hitnum,b.avatar,
   c.type_name from mw_product  a join mw_users  b on a.userid=b.id
   join mw_product_type  c on a.type=c.id  where a.state=1 and a.title like '%$keyword%' limit 10";
                $data["data"] = $this->product_model->query($sql);
            }
            if ($type == 1) {
                $data["data"] = $this->users_model->field("id,avatar,user_login,title,dis,type")->where("user_login like '%$keyword%'")->select();
            }
            if ($type == 2) {
                $data["data"] = $this->posts_model->field("id,cover,post_title,post_date,post_hits")->where("post_title like '%$keyword%'")->select();
            }
            if ($data["data"] === "") {
                $data['status'] = "0";
                $data['data'] = "无搜索结果";
                $this->ajaxReturn($data, 'JSON');
            } else {
                $data['status'] = "1";
                $this->ajaxReturn($data, 'JSON');
            }
        }
    }
}