<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/10/13
 * Time: 下午2:29
 */
namespace Home\Model;
use Think\Model;

class IndexModel{
    public function responseNews($postObj ,$arr){
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <ArticleCount>".count($arr)."</ArticleCount>
                    <Articles>";
        foreach($arr as $k=>$v){
            $template .="<item>
                        <Title><![CDATA[".$v['title']."]]></Title> 
                        <Description><![CDATA[".$v['description']."]]></Description>
                        <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
                        <Url><![CDATA[".$v['url']."]]></Url>
                        </item>";
        }

        $template .="</Articles>
                    </xml> ";
        echo sprintf($template, $toUser, $fromUser, time(), 'news');
    }
    public function responseText($postObj,$content){
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
        $fromUser = $postObj->ToUserName;
        $toUser   = $postObj->FromUserName;
        $time     = time();
        $msgType  = 'text';
        echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
    }
    public function responseSubscribe(){

    }

}