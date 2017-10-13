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

                //临时二维码
                if ($postObj->EventKey == 'qrscene_Temp'){
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
                        array(
                            'title' => '临时二维码',
                            'description' => '技巧',
                            'picUrl' => 'https://semantic-ui.com/examples/assets/images/logo.png',
                            'url' => 'https://icharle.com/uooconline.html',
                        ),
                    );
                    $IndexModel = D('index');
                    $IndexModel -> responseNews($postObj ,$arr);
                 //永久二维码
                }else if ($postObj->EventKey == 'qrscene_Last'){
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
                        array(
                            'title' => '永久二维码',
                            'description' => '技巧',
                            'picUrl' => 'https://semantic-ui.com/examples/assets/images/logo.png',
                            'url' => 'https://icharle.com/uooconline.html',
                        ),
                    );
                    $IndexModel = D('index');
                    $IndexModel -> responseNews($postObj ,$arr);
                 //推送
                }else{
                    $this->duotu($postObj);
                }

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

            /**
             * 扫描二维码推送事件
             */

            //关注后扫描二维码
            if ( strtolower($postObj->Event == 'SCAN') && ($postObj->EventKey == 'Temp') ){
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
                    array(
                        'title' => '临时二维码--关注',
                        'description' => '技巧',
                        'picUrl' => 'https://semantic-ui.com/examples/assets/images/logo.png',
                        'url' => 'https://icharle.com/uooconline.html',
                    ),
                );
                $IndexModel = D('index');
                $IndexModel -> responseNews($postObj ,$arr);
            }elseif (strtolower($postObj->Event == 'SCAN') && ($postObj->EventKey == 'Last') ){
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
                    array(
                        'title' => '永久二维码--关注',
                        'description' => '技巧',
                        'picUrl' => 'https://semantic-ui.com/examples/assets/images/logo.png',
                        'url' => 'https://icharle.com/uooconline.html',
                    ),
                );
                $IndexModel = D('index');
                $IndexModel -> responseNews($postObj ,$arr);
            }

            //菜单栏按钮
            if( strtolower($postObj->Event == 'CLICK') ){
                if ( strtolower($postObj->EventKey == 'VIP') ){
                    $arr = array(
                        array(
                            'title' => 'VIP视频解析站',
                            'description'=>'视频解析',
                            'picUrl'=>'http://soarteam.cn/vip/style.png',
                            'url'=>'http://soarteam.cn/vip/',
                        )
                    );
                    $IndexModel = D('index');
                    $IndexModel -> responseNews($postObj ,$arr);
                }elseif ( strtolower($postObj->EventKey == 'tianqi') ){
                    $arr = array(
                        array(
                            'title' => '广州天气',
                            'description'=>'广州天气',
                            'picUrl'=>'http://worldweather.wmo.int/images/22a.png',
                            'url'=>'http://soarteam.cn/wechat/index.php/Home/Index/tianqi',
                        )
                    );
                    $IndexModel = D('index');
                    $IndexModel -> responseNews($postObj ,$arr);
                }

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
        $appid = 'wxa87cc760b17dd72c';
        $secret = 'ba6fbf30ff7ff16f801521726339d9d7';

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



    /**
     * 获取临时二维码
     */
    public function TempQRcode()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$Access_Token;
        $arr = array(
            "expire_seconds" => 604800,
            "action_name" => "QR_STR_SCENE",
            "action_info" => array(
                "scene" => array(
                    "scene_str" => "Temp"
                )
            ),
        );
        $postArr = json_encode($arr);

        //创建二维码ticket
        $res = $this->http_curl($url,'post','json', $postArr);

        //通过ticket换取二维码
        $TICKET = $res['ticket'];
        $url1 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$TICKET;
        echo "<img src='". $url1 ."'/>";
        echo "<h1>临时二维码</h1>";
    }



    /**
     * 获取永久二维码
     */
    public function LastQRcode()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$Access_Token;
        $arr = array(
            "action_name" => "QR_LIMIT_STR_SCENE",
            "action_info" => array(
                "scene" => array(
                    "scene_str" => "Last"
                )
            ),
        );
        $postArr = json_encode($arr);

        //创建二维码ticket
        $res = $this->http_curl($url,'post','json', $postArr);

        //通过ticket换取二维码
        $TICKET = $res['ticket'];
        $url1 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$TICKET;
        echo "<img src='". $url1 ."'/>";
        echo "<h1>永久二维码</h1>";
    }



    /**
     * 长链接转短链接接口
     */
    public function ShortUrl()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token='.$Access_Token;
        $longurl = 'https://icharle.com';
        $arr = array(
            "action" => "long2short",
            "long_url" => $longurl,
        );
        $postarr = json_encode($arr);
        $res = $this->http_curl($url,'post','json',$postarr);
        echo $res['short_url'];
    }
    
    
    /**
     * 自定义菜单栏
     */
    public function MenuBar()
    {
        $Access_Token = $this->getAccessToken();
        header('content-type:text/html;charset=utf-8');
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$Access_Token;
        $arr = array(
            "button" =>
                array(
                    array(
                        "name" => urlencode("功能"),
                        "sub_button" => array(
                            array(
                                "type" => "click",
                                "name" => urlencode("VIP视频解析"),
                                "key"  => "VIP"
                            ),
                            array(
                                "type" => "click",
                                "name" => urlencode("广州天气"),
                                "key"  => "tianqi"
                            ),
                        ),
                    ),
                    array(
                        "name" => urlencode("博客"),
                        "sub_button" => array(
                            array(
                                "type" => "view",
                                "name" => urlencode("我的博客"),
                                "url" => "https://icharle.com"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("关于我"),
                                "url"  => "https://icharle.com/about.html"
                            ),
                        ),
                    ),
                    array(
                        "name" => urlencode("Github"),
                        "sub_button" => array(
                            array(
                                "type" => "view",
                                "name" => urlencode("我的项目"),
                                "url"  => "https://github.com/icharle"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("超级课程表"),
                                "url"  => "https://github.com/icharle/jwxt"
                            ),
                        ),
                    ),
                ),
        );
        $postarr = urldecode(json_encode($arr));
        $res = $this->http_curl($url,'post','json',$postarr);
        dump($res);
    }


    /**
     * 菜单栏天气接口
     */
    public function tianqi()
    {
        $url = 'http://www.tuling123.com/openapi/api?key=b3e974784faa41a68f6b73d92ddae8db&info=%E5%B9%BF%E5%B7%9E%E5%A4%A9%E6%B0%94';
        $res = $this->http_curl($url);
        echo $res['text'];
    }





    
}