# 微信公众号开发篇
## 前言
最近在做一个投票项目涉及到微信公众号获取用户的openID，但是咧，之前做的微信公众号开发被我重装服务器系统的时候删除了，本地没有保存GG！！因此，发费了一天半的时间重新写了一遍。
## 功能
当然，本次微信公众号开发基于thinkPHP3.2.3开发。

* **订阅消息的回复以及关键字图文消息回复**
    ![32-1](https://icharle-1251944239.cosgz.myqcloud.com/%E5%8D%9A%E5%AE%A2/%E5%BE%AE%E4%BF%A1%E5%85%AC%E4%BC%97%E5%8F%B7%E5%BC%80%E5%8F%91%E7%AF%87/32-1.png)
    ![32-2](https://icharle-1251944239.cosgz.myqcloud.com/%E5%8D%9A%E5%AE%A2/%E5%BE%AE%E4%BF%A1%E5%85%AC%E4%BC%97%E5%8F%B7%E5%BC%80%E5%8F%91%E7%AF%87/32-2.png)
    
* **Access_Token、CURL、微信服务器IP**
    Access_Token值是一个凭证，对于接下来的其它方面的功能的实现起到至关重要的作用。但是Access_Token每天只能有2000次的获取次数，每次有两个小时的有效期，因此我需要对其进行存储。存储有很多种方式：session、插入到数据库中等等。这里使用的是session方法。
    
    ```
    /**
     * @return mixed
     * 获取access_token
     */
    public function getAccessToken()
    {
        $appid = '';
        $secret = '';

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
    ```
    
    CURL这个很重要，每一次请求都需要执行一次它，因此，我们可以将它写成一个模板
    
    
    ```
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
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
    ```
    
    微信服务器IP地址这个比较简单，看开发者文档可以解决，这里就不做详细说明。

* **长链接转短链接接口**
    [Demo](http://soarteam.cn/wechat/index.php/Home/Index/ShortUrl)
    测试案列中将`https://icharle.com`转成[短的链接](https://w.url.cn/s/AGa409j)。
    
* **自定义菜单栏**
    
    ```
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
                            array(
                                "type" => "view",
                                "name" => urlencode("模板消息"),
                                "url"  => "http://soarteam.cn/wechat/index.php/Home/Index/Model"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("静默授权"),
                                "url"  => "http://soarteam.cn/wechat/index.php/Home/Index/ScopeBase"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("详细授权"),
                                "url"  => "http://soarteam.cn/wechat/index.php/Home/Index/ScopeUserinfo"
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
                            array(
                                "type" => "view",
                                "name" => urlencode("临时二维码"),
                                "url"  => "http://soarteam.cn/wechat/index.php/Home/Index/TempQRcode"
                            ),
                            array(
                                "type" => "view",
                                "name" => urlencode("永久二维码"),
                                "url"  => "http://soarteam.cn/wechat/index.php/Home/Index/LastQRcode"
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
    ```
    
* **获取二维码**
    
    永久二维码：
    ![32-3](https://icharle-1251944239.cosgz.myqcloud.com/%E5%8D%9A%E5%AE%A2/%E5%BE%AE%E4%BF%A1%E5%85%AC%E4%BC%97%E5%8F%B7%E5%BC%80%E5%8F%91%E7%AF%87/32-3.png)
    临时二维码：
    ![32-4](https://icharle-1251944239.cosgz.myqcloud.com/%E5%8D%9A%E5%AE%A2/%E5%BE%AE%E4%BF%A1%E5%85%AC%E4%BC%97%E5%8F%B7%E5%BC%80%E5%8F%91%E7%AF%87/32-4.png)
    
* **网页授权**
    静默授权方式[静默授权](http://soarteam.cn/wechat/index.php/Home/Index/ScopeBase)
    详细授权方式[详细授权](http://soarteam.cn/wechat/index.php/Home/Index/ScopeUserinfo)
    
* **素材获取**

    新增临时素材：将文件中的`upload`方法修改成如下，之后访问[文件上传](http://soarteam.cn/wechat/index.php/Home/Index/upload)
        
    ```
    /**
     * 素材文件上传功能
     */
    public function upload()
    {

        if ($_FILES['file']!=""){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'mp3');// 设置附件上传类型
            $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录
            $upload->saveName = '';       //保持原来的文件名
            $upload->autoSub = false;      //关闭子目录保存
            // 上传文件
            $info   =   $upload->upload();
            $this->TempFile($info);       //新增临时素材
        }else{
            $this->display();
        }

    }
    ```
    
    新增永久素材：将文件中的`upload`方法修改成如下，之后访问[文件上传](http://soarteam.cn/wechat/index.php/Home/Index/upload)
    
    
    ```
    /**
     * 素材文件上传功能
     */
    public function upload()
    {

        if ($_FILES['file']!=""){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'mp3');// 设置附件上传类型
            $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录
            $upload->saveName = '';       //保持原来的文件名
            $upload->autoSub = false;      //关闭子目录保存
            // 上传文件
            $info   =   $upload->upload();
            $this->LastFile($info);       //新增永久素材
        }else{
            $this->display();
        }

    }
    ```
    
## 项目地址
[微信公众号开发](https://github.com/icharle/WeChat) 欢迎star吧！


