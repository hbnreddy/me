<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class NotificationManager
{
    private static $notifSessionKey = 'NOTIFICATIONS';
    private static $notificationsKey = 'NOTIFICATIONS';

    public static function init()
    {
        $data = [
            'count' => 0,
        ];

        if (! Session::has(self::$notifSessionKey)) {
            Session::put(self::$notifSessionKey, $data);
        }

        return self::$notifSessionKey;
    }

    public static function getNotifications()
    {
        return Session::get(self::$notifSessionKey);
    }

    public static function setNotifications($value)
    {
        Session::put(self::$notifSessionKey, $value);
    }

    public static function increase()
    {
        $notifications = self::getNotifications();
        if (! isset($notifications['count'])) {
            $notifications['count'] = 0;
        }

        $notifications['count']++;

        self::setNotifications($notifications);

        return true;
    }

    public static function decrease()
    {
        $notifications = self::getNotifications();
        $notifications['count']--;

        if ($notifications['count'] < 0) {
            $notifications['count'] = 0;
        }

        self::setNotifications($notifications);

        return true;
    }

    public static function clear()
    {
        $notifications = self::getNotifications();
        $notifications['count'] = 0;

        self::setNotifications($notifications);
    }
}
