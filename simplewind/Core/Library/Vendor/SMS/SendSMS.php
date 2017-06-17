<?php
include_once 'CCPRestSmsSDK.php';

class SendSMS
{
    //主帐号
    private $accountSid='8aaf07085c62aa66015c81c8e9670bf5';

    //主帐号Token
    private $accountToken='dc462e8402354073bd51f4b4afdd0e5d';

    //应用Id
    private $appId='8aaf07085c62aa66015c81c8e9b90bfa';

    //请求地址，格式如下，不需要写https://
    private $serverIP='sandboxapp.cloopen.com';

    //请求端口
    private $serverPort='8883';

    //REST版本号
    private $softVersion='2013-12-26';

    /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id
    */
    public function sendTemplateSMS($to,$datas,$tempId)
    {

        // 初始化REST SDK
        $rest = new CCPRestSmsSDK($this->serverIP,$this->serverPort,$this->softVersion);

        $rest->setAccount($this->accountSid,$this->accountToken);
        $rest->setAppId($this->appId);

        // 发送模板短信
//          echo "Sending TemplateSMS to $to <br/>";

        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            m3_result(3,'result error!');
        }
        if($result->statusCode != 0) {
            m3_result($result->statusCode,$result->statusMsg);

        }else{
            $data['status'] = 0;
            $data['message'] = '发送成功';
            return $data;
        }
    }
}

//sendTemplateSMS("18576437523", array(1234, 5), 1);