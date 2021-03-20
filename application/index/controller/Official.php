<?php
/**
 *   User:   邦德
 *   Date:   2021/3/1 : 11:40
 */


namespace app\index\controller;


use app\common\controller\Api;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Image;
use think\Log;
use think\Request;

class Official extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function index()
    {

//        $this->success('ok', cache('FromUserName'));

        $app = Factory::officialAccount(config('official_account'));
        $accessToken = $app->access_token;
        $this->success('ok', $accessToken->getToken());

//        $message = new Text('Hello world!');
//
//        $result = $app->customer_service->message($message)->to('okNnu5wpAa1ILxYsXIG3a5zYN52A')->send();
//
//        $this->success('ok', $result);

        $app->server->push(function ($message) {

            switch ($message['MsgType']) {
                case 'event':
                    Log::init([
                        'apart_level' => ['Bourne'] // 把Bourne设置成独立日志
                    ]);
                    Log::write(json_encode($message), 'Bourne');
                    cache('FromUserName', $message['FromUserName'], 3600);
//                    return '收到事件消息';
                    return '你好,欢迎关注本公众号!!';
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

//            $a = new Text('您好, 欢迎你关注本公众号 !');
//            return $a;
//            $image = new Image('E4uxfs7yw1pZzlXplBZSiuhbdBRNwRFFOVg5-xIq38W51XCfCqWA2TorcNyyuIRb');
//            return $image;
        });

        $response = $app->server->serve();

        // 将响应输出
        $response->send();

        $message = new Text('Hello world!');
        $image = new Image('3ytPZXDzfhsG5EHaV2wakiolGoj6_RwkNlT01bIS2d8XjpcCKs8O255bdadDL9Af');
        $result = $app->customer_service->message($image)->to(cache('FromUserName'))->send();
    }

    public function upload()
    {
        $app = Factory::officialAccount(config('official_account'));
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'official' . DS . 'qrcode.png';
        $result =  $app->media->uploadImage($path);
        $this->success('ok', $result);
    }

}