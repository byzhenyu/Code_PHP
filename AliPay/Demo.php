<?php

/**
 * 支付宝插件Demo
 * @author Jack_YanTC <627495692@qq.com>
 */
class Demo {

    //config 示例
    public $config = array(

        /* 微信支付相关配置 */
        'WxPay' => array(
            #微信商户平台应用APPID
            'app_id' => 'wx278f0cfe8cf5be1c',
            #商户号
            'mch_id' => '1482937212',
            //api密钥
            'key' => 'yiwenjiaoyuyiwenjiaoyuyiwenjiaoy',
            #异步回调地址
            'notify_url' => 'http://yiwen.host3.liuniukeji.com/index.php/Home/Payment/wechatNotify',
            //公众帐号secert（仅JSAPI支付的时候需要配置)
            'appsecret'=>'e2970084a53543160d8e93a865d94621',
        ),

    );


    function __construct(){
        $config['appId']='135';
        $config['rsaPrivateKey']='135';
        $config['signType']='135';
        $config['alipayrsaPublicKey']='135';
        $this -> config = $config;
    }

    public function index() {
        $data['body']='六牛订单详情';
        $data['subject']='六牛订单';
        $data['out_trade_no']='201705201314';
        $data['total_amount']='0.01';
        require_once("./Plugins/AliPay/AliPay.php");

        //可传入$options自己的appid
        //$alipay =new \AliPay($this -> config);

        //配置到config的话，不许传递参数
        $alipay =new \AliPay();

        $result['app'] =$alipay->AliPayApp($data);
        $result['web'] =$alipay->AliPayWeb($data);
        $result['check']=$alipay->AliPayNotifyCheck();
        p($result['web']);
        //jsonReturn(0, 1, $result);
    }

    //app支付 示例
    public function appPay() {
        $data['body']='六牛订单详情';
        $data['subject']='六牛订单';
        $data['out_trade_no']='201705201314';
        $data['total_amount']='0.01';
        require_once("Plugins/AliPay/AliPay.php");
        $alipay =new \AliPay();
        $result =$alipay->AliPayApp($data);
        return $result;
    }

    //web支付 示例
    public function webPay() {
        $data['body']='六牛订单详情';
        $data['subject']='六牛订单';
        $data['out_trade_no']='201705201314';
        $data['total_amount']='0.01';
        require_once("Plugins/AliPay/AliPay.php");
        $alipay =new \AliPay();
        $result =$alipay->AliPayWeb($data);
        return $result;
    }

    //移动web支付 示例
    public function mobileWebPay() {
        $data['body']='六牛订单详情';
        $data['subject']='六牛订单';
        $data['out_trade_no']='201705201314';
        $data['total_amount']='0.01';
        require_once("Plugins/AliPay/AliPay.php");
        $alipay =new \AliPay();
        $result =$alipay->AliPayMobileWeb($data);
        return $result;
    }

    //支付回调地址 示例
    public function alipayNotify() {
        require_once("Plugins/AliPay/AliPay.php");
        $alipay =new \AliPay();
        //验证是否是支付宝发送
        $flag=$alipay->AliPayNotifyCheck();
        if ($flag) {
            if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
                $out_trade_no = trim($_POST['out_trade_no']); //商户订单号

                //成功后的业务逻辑处理
                $result = D('Common/Order')->notify($out_trade_no);

                if ($result) {
                    echo "success"; //告诉支付宝支付成功
                    die;
                }
            }
        }
        echo "fail"; //验证失败
        die;
    }

    //提现转账 示例
    public function withdraw() {
        $data['out_biz_no']='201705201314';//订单号
        $data['payee_account'] ='627495692@qq.com';//收款支付宝账号
        $data['payee_real_name'] ='颜廷超';//收款支付宝账号真实姓名
        $data['amount'] ='0.01';//金额
        $data['payer_show_name']='六牛科技转账';
        $data['remark'] = '备注';
        require_once("Plugins/AliPay/AliPay.php");
        $alipay =new \AliPay();
        $result =$alipay->AliPayWithdraw($data);
        return $result;
    }
}