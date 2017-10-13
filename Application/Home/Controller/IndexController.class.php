<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function __construct()
    {

    }

    //验证消息的确来自微信服务器
    public function index(){
        //将token、timestamp、nonce三个参数进行字典序排序
        $token = 'wechat';
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $signature = $_GET['signature'];
        $array = array($token, $timestamp, $nonce);
        sort($array);

        //将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpstr = implode('', $array);
        $tmpstr = sha1( $tmpstr);

        //开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        if ($tmpstr == $signature  && $_GET['echostr']){
            echo $_GET['echostr'];
            exit;
        }else{
            $this->reponseMsg();
        }
    }


    //接受事件推送并回复
    public function reponseMsg()
    {
        //获取到微信推送过来的post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];

        //处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string( $postArr );

        //接收事件推送类型
        if (strtolower( $postObj->MsgType) == 'event'){

            //关注公众号事件
            if( strtolower($postObj->Event == 'subscribe') ){

                $this->duotu($postObj);

//                回复用户消息(纯文本格式)
//                $toUser   = $postObj->FromUserName;
//                $fromUser = $postObj->ToUserName;
//                $time     = time();
//                $msgType  =  'text';
//                $content  = '欢迎关注艾超博客,hello';
//                $template = "<xml>
//							<ToUserName><![CDATA[%s]]></ToUserName>
//							<FromUserName><![CDATA[%s]]></FromUserName>
//							<CreateTime>%s</CreateTime>
//							<MsgType><![CDATA[%s]]></MsgType>
//							<Content><![CDATA[%s]]></Content>
//							</xml>";
//                $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
//                echo $info;
            }
        }

        //接受普通消息
        if(strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == 'tuwen'){

            $this->duotu($postObj);

        }else if (strtolower($postObj->MsgType) == 'text'){
            switch ( trim($postObj->Content)){
                case 1:
                    $content = '你输入的数字是1';
                    break;

                case 2:
                    $content = '你输入的数字是2';
                    break;

                case 3:
                    $content = '你输入的数字是3';
                    break;

                case 4:
                    $content = '你输入的数字是4';
                    break;

                case 'icharle':
                    $content = '<a href="https://icharle.com">艾超博客</a>';
                    break;
            }
            $IndexModel = D('index');
            $IndexModel -> responseText($postObj ,$content);

        }
    }

    /**
     * @param $postObj
     * 多图文
     */
    public function duotu($postObj)
    {
        $arr = array(
            array(
                'title' => '艾超博客',
                'description'=>'Icharle',
                'picUrl'=>'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                'url'=>'https://icharle.com',
            ),
            array(
                'title' => 'UoocOnline 优课在线刷课插件',
                'description' => '技巧',
                'picUrl' => 'https://semantic-ui.com/examples/assets/images/logo.png',
                'url' => 'https://icharle.com/uooconline.html',
            ),
        );
        $IndexModel = D('index');
        $IndexModel -> responseNews($postObj ,$arr);
    }


    /**
     * @param $url
     * @param string $type
     * @param string $res
     * @param string $arr
     * 万能请求
     */
    public function http_curl($url,$type='get',$res='json',$arr=''){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($type = 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        if ($res == 'json'){
            return json_decode($output,true);
        }
    }


    /**
     * @return mixed
     * 获取access_token
     */
    public function getAccessToken()
    {
        $appid = 'wx067f49099f34a834';
        $secret = 'c4080ff533cc1ad34e1744fba10f28ac';

        //如果access_token没有过期，直接return
        if ($_SESSION['access_token'] && $_SESSION['expire_time']>time() ){
            return $_SESSION['access_token'];
        }else{
            //重新获取access_token
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
            $res = $this->http_curl($url,'get','json');
            $_SESSION['access_token'] = $res['access_token'];
            $_SESSION['expire_time'] = time()+7200;
            return $res['access_token'];
        }
    }


    /**
     * @return mixed
     * 获取微信服务器IP地址
     */
    public function WxServerIp()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$Access_Token;
        $res = $this->http_curl($url,'get','json');
        dump($res);
    }
}