<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminController extends BaseController
{
    public function showChats()
    {
//        显示所有的对话列表
        $data = DB::table('wx_chat_user')->get();
        foreach ($data as $k => $v) {
//            limit by 48 hours
            if ((int)$v->finalchattime + 172800 < time()) {
                if ((int)$v->finalchatnum < 5) {
                    $data[$k]->status = "可对话";
                } else {
                    $data[$k]->status = "达到系统最大等待玩家回复";
                }
            } else {
                $data[$k]->status = "对话已到期";
            }
            $data[$k]->finalchattime = date("Y-m-d h:i:sa", $v->finalchattime);
            $data[$k]->createtime = date("Y-m-d h:i:sa", $v->createtime);
        }
        return view('wx_test.list', ['data' => $data]);
    }

    public function talk(Request $request)
    {
        return view('wx_test.talk');
    }

    public function showTalk(Request $request)
    {
        $userOpenId = $request->input('useropenid');
        $file = fopen('log/UserChat/' . $userOpenId . '.log', 'r');
        $data = fread($file, filesize('log/UserChat/' . $userOpenId . '.log'));
        fclose($file);
        return $data;
    }

    public function sendTalk(Request $request)
    {
        $userOpenId = $request->input('useropenid');
        $content = $request->input('content');
        $this->sendMessage($userOpenId, 'text', $content);
        return "success";
    }
}