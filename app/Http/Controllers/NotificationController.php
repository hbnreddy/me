<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotificationManager;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(NotificationManager::getNotifications());
    }
}
