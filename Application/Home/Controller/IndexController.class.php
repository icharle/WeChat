<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    //验证消息的确来自微信服务器
    public function index()
    {
        //将token、timestamp、nonce三个参数进行字典序排序
        $token = 'wechat';
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $signature = $_GET['signature'];
        $array = array($token, $timestamp, $nonce);
        sort($array);

        //将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpstr = implode('', $array);
        $tmpstr = sha1($tmpstr);

        //开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        if ($tmpstr == $signature && $_GET['echostr']) {
            echo $_GET['echostr'];
            exit;
        } else {
            $this->reponseMsg();
        }
    }


    //接受事件推送并回复
    public function reponseMsg()
    {
        //获取到微信推送过来的post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];

        //处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);

        //接收事件推送类型
        if (strtolower($postObj->MsgType) == 'event') {

            //关注公众号事件
            if (strtolower($postObj->Event == 'subscribe')) {

                //临时二维码
                if ($postObj->EventKey == 'qrscene_Temp') {
                    $arr = array(
                        array(
                            'title' => '艾超博客',
                            'description' => 'Icharle',
                            'picUrl' => 'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                            'url' => 'https://icharle.com',
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
                    $IndexModel->responseNews($postObj, $arr);
                    //永久二维码
                } else if ($postObj->EventKey == 'qrscene_Last') {
                    $arr = array(
                        array(
                            'title' => '艾超博客',
                            'description' => 'Icharle',
                            'picUrl' => 'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                            'url' => 'https://icharle.com',
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
                    $IndexModel->responseNews($postObj, $arr);
                    //推送
                } else {
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
            if (strtolower($postObj->Event == 'SCAN') && ($postObj->EventKey == 'Temp')) {
                $arr = array(
                    array(
                        'title' => '艾超博客',
                        'description' => 'Icharle',
                        'picUrl' => 'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                        'url' => 'https://icharle.com',
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
                $IndexModel->responseNews($postObj, $arr);
            } elseif (strtolower($postObj->Event == 'SCAN') && ($postObj->EventKey == 'Last')) {
                $arr = array(
                    array(
                        'title' => '艾超博客',
                        'description' => 'Icharle',
                        'picUrl' => 'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                        'url' => 'https://icharle.com',
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
                $IndexModel->responseNews($postObj, $arr);
            }

            //菜单栏按钮
            if (strtolower($postObj->Event == 'CLICK')) {
                if (strtolower($postObj->EventKey == 'VIP')) {
                    $arr = array(
                        array(
                            'title' => 'VIP视频解析站',
                            'description' => '视频解析',
                            'picUrl' => 'http://soarteam.cn/vip/style.png',
                            'url' => 'http://soarteam.cn/vip/',
                        )
                    );
                    $IndexModel = D('index');
                    $IndexModel->responseNews($postObj, $arr);
                } elseif (strtolower($postObj->EventKey == 'tianqi')) {
                    $arr = array(
                        array(
                            'title' => '广州天气',
                            'description' => '广州天气',
                            'picUrl' => 'http://worldweather.wmo.int/images/22a.png',
                            'url' => 'http://soarteam.cn/wechat/index.php/Home/Index/tianqi',
                        )
                    );
                    $IndexModel = D('index');
                    $IndexModel->responseNews($postObj, $arr);
                }

            }


        }

        //接受普通消息
        if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == 'tuwen') {

            //$this->duotu($postObj);
            $this->test($postObj);

        } else if (strtolower($postObj->MsgType) == 'text') {
            switch (trim($postObj->Content)) {
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
            $IndexModel->responseText($postObj, $content);

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
                'description' => 'Icharle',
                'picUrl' => 'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                'url' => 'https://icharle.com',
            ),
            array(
                'title' => 'UoocOnline 优课在线刷课插件',
                'description' => '技巧',
                'picUrl' => 'https://semantic-ui.com/examples/assets/images/logo.png',
                'url' => 'https://icharle.com/uooconline.html',
            ),
        );
        $IndexModel = D('index');
        $IndexModel->responseNews($postObj, $arr);
    }


    /**
     * @param $postObj
     * 接口测试
     */
    public function test($postObj)
    {
        $toUser = (string)$postObj->FromUserName;
        $encrypt = md5(sha1($toUser));
        $arr = array(
            array(
                'title' => '班级投票',
                'description' => '班级投票',
                'picUrl' => 'https://avatars3.githubusercontent.com/u/25547121?s=460&v=4',
                'url' => 'http://soarteam.cn/class/index.php/Home/Index/index?openid=' . $encrypt,
            ),
        );
        $url = 'http://soarteam.cn/class/index.php/Home/Index/test?openid=' . $encrypt;
        $this->http_curl($url);
        $IndexModel = D('index');
        $IndexModel->responseNews($postObj, $arr);
    }


    /**
     * @param $url
     * @param string $type
     * @param string $res
     * @param string $arr
     * 万能请求
     */
    public function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
        if ($type = 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        if ($res == 'json') {
            return json_decode($output, true);
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
        if ($_SESSION['access_token'] && $_SESSION['expire_time'] > time()) {
            return $_SESSION['access_token'];
        } else {
            //重新获取access_token
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            $res = $this->http_curl($url, 'get', 'json');
            $_SESSION['access_token'] = $res['access_token'];
            $_SESSION['expire_time'] = time() + 7200;
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
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $Access_Token;
        $res = $this->http_curl($url, 'get', 'json');
        dump($res);
    }


    /**
     * 获取临时二维码
     */
    public function TempQRcode()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $Access_Token;
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
        $res = $this->http_curl($url, 'post', 'json', $postArr);

        //通过ticket换取二维码
        $TICKET = $res['ticket'];
        $url1 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $TICKET;
        echo "<img src='" . $url1 . "'/>";
        echo "<h1>临时二维码</h1>";
    }


    /**
     * 获取永久二维码
     */
    public function LastQRcode()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $Access_Token;
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
        $res = $this->http_curl($url, 'post', 'json', $postArr);

        //通过ticket换取二维码
        $TICKET = $res['ticket'];
        $url1 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $TICKET;
        echo "<img src='" . $url1 . "'/>";
        echo "<h1>永久二维码</h1>";
    }


    /**
     * 长链接转短链接接口
     */
    public function ShortUrl()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $Access_Token;
        $longurl = 'https://icharle.com';
        $arr = array(
            "action" => "long2short",
            "long_url" => $longurl,
        );
        $postarr = json_encode($arr);
        $res = $this->http_curl($url, 'post', 'json', $postarr);
        echo $res['short_url'];
    }


    /**
     * 自定义菜单栏
     */
    public function MenuBar()
    {
        $Access_Token = $this->getAccessToken();
        header('content-type:text/html;charset=utf-8');
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $Access_Token;
        $arr = array(
            "button" =>
                array(
                    array(
                        "name" => urlencode("功能"),
                        "sub_button" => array(
                            array(
                                "type" => "click",
                                "name" => urlencode("VIP视频解析"),
                                "key" => "VIP"
                            ),
                            array(
                                "type" => "click",
                                "name" => urlencode("广州天气"),
                                "key" => "tianqi"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("模板消息"),
                                "url" => "http://soarteam.cn/wechat/index.php/Home/Index/Model"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("静默授权"),
                                "url" => "http://soarteam.cn/wechat/index.php/Home/Index/ScopeBase"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("详细授权"),
                                "url" => "http://soarteam.cn/wechat/index.php/Home/Index/ScopeUserinfo"
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
                                "url" => "https://icharle.com/about.html"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("临时二维码"),
                                "url" => "http://soarteam.cn/wechat/index.php/Home/Index/TempQRcode"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("永久二维码"),
                                "url" => "http://soarteam.cn/wechat/index.php/Home/Index/LastQRcode"
                            ),
                        ),
                    ),
                    array(
                        "name" => urlencode("Github"),
                        "sub_button" => array(
                            array(
                                "type" => "view",
                                "name" => urlencode("我的项目"),
                                "url" => "https://github.com/icharle"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("超级课程表"),
                                "url" => "https://github.com/icharle/jwxt"
                            ),
                        ),
                    ),
                ),
        );
        $postarr = urldecode(json_encode($arr));
        $res = $this->http_curl($url, 'post', 'json', $postarr);
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


    /**
     * 模板消息
     */
    public function Model()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $Access_Token;
        $arr = array(
            "touser" => "oM600xIZIPjcBkvJ3nDvZ4PvdqHA",
            "template_id" => "-zxx8XNP_xchzUAnf9_NkEW1SqjS-nFL32i-_7TFI8A",
            "url" => "https://icharle.com",
            "data" => array(
                "name" => array(
                    "value" => "艾超先生",
                    "color" => "#040100"
                ),
                "time" => array(
                    "value" => date('Y-m-d H:i:s'),
                    "color" => "#6BF28A"
                ),
                "money" => array(
                    "value" => "30.69元",
                    "color" => "#F23209"
                ),
                "smoney" => array(
                    "value" => "1045265元",
                    "color" => "#F00D33"
                )
            ),
        );
        $postarr = json_encode($arr);
        $res = $this->http_curl($url, 'post', 'json', $postarr);
        dump($res);
    }

    /**
     * 微信网页授权之静默授权
     */
    public function ScopeBase()
    {
        $appid = 'wxa87cc760b17dd72c';
        $redirect_uri = urlencode('http://soarteam.cn/wechat/index.php/Home/Index/ScopeBaseSecond');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
        header('location:' . $url);

    }

    public function ScopeBaseSecond()
    {
        $appid = 'wxa87cc760b17dd72c';
        $appsecret = 'ba6fbf30ff7ff16f801521726339d9d7';
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        $res = $this->http_curl($url);
        dump($res);
    }


    /**
     * 微信网页授权之详细授权
     */
    public function ScopeUserinfo()
    {
        $appid = 'wxa87cc760b17dd72c';
        $redirect_uri = urlencode('http://soarteam.cn/wechat/index.php/Home/Index/ScopeUserinfoSecond');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        header('location:' . $url);
    }

    public function ScopeUserinfoSecond()
    {
        $appid = 'wxa87cc760b17dd72c';
        $appsecret = 'ba6fbf30ff7ff16f801521726339d9d7';
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code ';
        $res = $this->http_curl($url);
        $access_token = $res['access_token'];
        $openid = $res['openid'];
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $res = $this->http_curl($url);
        dump($res);
    }


    /**
     * 素材文件上传功能
     */
    public function upload()
    {

        if ($_FILES['file'] != "") {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'mp3');// 设置附件上传类型
            $upload->rootPath = './Upload/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->saveName = '';       //保持原来的文件名
            $upload->autoSub = false;      //关闭子目录保存
            // 上传文件
            $info = $upload->upload();
            //$this->TempFile($info);       //新增临时素材
            $this->LastFile($info);       //新增永久素材
        } else {
            $this->display();
        }

    }


    /**
     * 新增临时素材
     */
    public function TempFile($res)
    {
        $type = $res['file']['type'];
        if (explode("image", $type)) {
            $type = "image";
        } elseif (explode("voice", $type)) {
            $type = "voice";
        } elseif (explode("video", $type)) {
            $type = "video";
        } elseif (explode("thumb", $type)) {
            $type = "thumb";
        }
        $filepath = dirname(dirname(dirname(dirname(__FILE__)))) . "\upload\\" . $res['file']['savename'];
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $Access_Token . '&type=' . $type;
        $data = array('media' => new \CURLFile($filepath));
        $res = $this->http_curl($url, 'post', 'json', $data);
        dump($res);
    }

    /**
     * 获取临时素材
     */
    public function GetTempFile()
    {
        $Access_Token = $this->getAccessToken();
        $media_id = 'iDNV3QlfYCKetTXcNCQ-kII9BDz2pqjl3bWfK5JTWltELRDPL36ikkIeXbpM0zQ5';
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $Access_Token . '&media_id=' . $media_id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);//只取body头
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//curl_exec执行成功后返回执行的结果；不设置的话，curl_exec执行成功则返回true
        $output = curl_exec($ch);
        curl_close($ch);
        dump($output);
    }


    /**
     * 新增永久素材
     */
    public function LastFile($res)
    {
        $type = $res['file']['type'];
        if (explode("image", $type)) {
            $type = "image";
        } elseif (explode("voice", $type)) {
            $type = "voice";
        } elseif (explode("video", $type)) {
            $type = "video";
        } elseif (explode("thumb", $type)) {
            $type = "thumb";
        }
        $filepath = dirname(dirname(dirname(dirname(__FILE__)))) . "\upload\\" . $res['file']['savename'];
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $Access_Token . '&type=' . $type;
        $data = array('media' => new \CURLFile($filepath));
        $res = $this->http_curl($url, 'post', 'json', $data);
        dump($res);
    }


    /**
     * 获取永久素材
     */
    public function GetLastFile()
    {
        $Access_Token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . $Access_Token;
        $arr = array("media_id" => 'XpOcXxU37p6LHTcLLimjH8BCIuJS5qUrco-8lVvnZK0');
        $postarr = json_encode($arr);
        $res = $this->http_curl($url, 'post', 'json', $postarr);
        dump($res);
    }

    /**
     * @return mixed
     * 获取JsTicket
     */
    public function GetJsTicket()
    {
        if (!$_SESSION['JsTicket'] && $_SESSION['JsExpire_time'] < time()) {
            $Access_Token = $this->getAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token==' . $Access_Token . '&type=jsapi';
            $res = $this->http_curl($url, 'get', 'json', '');
            $_SESSION['JsTicket'] = $res['ticket'];
            $_SESSION['JsExpire_time'] = time() + 7000;
            return $res['ticket'];
        } else {
            return $_SESSION['JsTicket'];
        }

    }

    /**
     * @param int $length
     * @return string
     * 获取16位随机数
     */
    public function GetNoncestr($length = 16)
    {
        $str = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        $res = "";
        for ($i = 0; $i < $length; $i++) {
            $res .= substr($str, mt_rand(0, strlen($str) - 1), 1);
        }
        return $res;
    }


    public function RspSignature($PostUrl = null)
    {
        if (!empty($PostUrl)) {
            $noncestr = $this->GetNoncestr();
            $jsapi_ticket = $this->GetJsTicket();
            $timestamp = time();
            $url = $PostUrl;
            $str = 'jsapi_ticket=' . $jsapi_ticket . '&noncestr=' . $noncestr . '×tamp=' . $timestamp . '&url=' . $url;
            $res = array(
                'status' => '200',
                'appId' => 'wxa87cc760b17dd72c',
                'timestamp' => $timestamp ,
                'nonceStr' =>  $noncestr,
                'signature' =>  sha1($str),
            );
            $res = json_encode($res);
        } else {
            $res = array(
                'status' => '403',
                'msg' => '请输入url链接'
            );
            $res = json_encode($res);
        }
        print $res;
    }


}