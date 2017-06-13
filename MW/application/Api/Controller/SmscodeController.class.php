<?php
namespace APi\Controller;
use Think\Controller;

class SmscodeController extends Controller
{
    protected $smscode_model;

    public function _initialize()
    {
        $this->smscode_model = D("Common/Smscode");

    }


    /**
     * @param $phone 手机号，可以群发，以英文逗号隔开
     */
    public function SMSCode($phone)
    {
        $code = $this->GetRandStr(4);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sh2.ipyy.com/smsJson.aspx?action=send&userid=&account=jkwl128&password=mouldworld20150922&mobile=".$phone."&content=您的验证码：".$code."【模具世界】");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        $res = curl_exec($ch);
        curl_close($ch);
        $a = json_decode($res);
        if ($a->returnstatus == "Success") {
            $date['status'] = "1";
            $date['data'] = $code;
            $this->smscode_model->smscode=$code;
            $this->smscode_model->mobile=$phone;
            $this->smscode_model->senddate= date("Y-m-d H:i:s", time());
            $this->smscode_model->add();
            $this->ajaxReturn($date, 'JSON');

        } else {
            $date['status'] = "0";
            $date['data'] = "获取验证码失败";
            $this->ajaxReturn($date, 'JSON');
        }
    }

    /**
     * @param $len 字符长度
     * @return string
     */
    public function GetRandStr($len)
    {
        $chars = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = '';
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
}