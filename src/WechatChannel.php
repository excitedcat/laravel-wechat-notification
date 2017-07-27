<?php

namespace ExcitedCat\WechatNotification;

use EasyWeChat\Core\Exceptions\HttpException;
use Illuminate\Notifications\Notification;
use Log;

class WechatChannel
{
    public function __construct()
    {
        $this->app = app('wechat');
    }

    /**
     * Send the given notification.
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $openid = $notifiable->routeNotificationFor('Wechat');

        if (!$openid || strlen($openid) < 20) {
            return;
        }

        $message = $notification->toWechat($notifiable);
        $message['openid'] = $openid;

        $data = $message['data'];
        $templateId = $message['templateId'];
        $url = $message['url'];

        try {
            $this->app->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openid)->send();
        } catch (HttpException $e) {
            Log::error('消息发送失败！', $message);
        }
    }
}