<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class IndexController extends Controller
{
//    验证token
    public function checkToken(Request $request)
    {
        $signature = $request->input("signature");
        $timestamp = $request->input("timestamp");
        $nonce = $request->input("nonce");
        $echostr = $request->input("echostr");

        $token = getenv('WXCHECK_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return $echostr;
        } else {
            return 0;
        }
    }

//    保存对话
    public function saveChat(Request $request)
    {
//        取出post的数据json格式（未加密）
        $req = $request->getcontent();
        $temp_array = json_decode($req);
        if (isset($temp_array->MsgType)) {
            if ($temp_array->MsgType == 'event') {
                $temp_table = DB::table('wx_chat_user');
                $temp_data = $temp_table->where('useropenid', $temp_array->FromUserName)->get()->toArray();
                var_dump($temp_data);
                if ($temp_data) {
                    $temp_table->where('id', $temp_data[0]->id)->update(['finalchattime' => time(), 'finalchatnum' => 1]);
                } else {
                    $temp_table->insert(['useropenid' => $temp_array->FromUserName, 'createtime' => time(), 'finalchattime' => time(), 'finalchatnum' => 1]);
                }
//                事件无msgid
                $id = DB::table('wx_temp_save_chat')->insertGetId(['time' => time(), 'tousername' => $temp_array->ToUserName, 'fromusername' => $temp_array->FromUserName,
                    'createtime' => $temp_array->CreateTime, 'msgtype' => $temp_array->MsgType]);
                DB::table('wx_temp_save_event')->insert(['sessionfrom' => $temp_array->SessionFrom, 'event' => $temp_array->Event, 'chat_id' => $id]);
            }
            if ($temp_array->MsgType == 'text') {
                $id = DB::table('wx_temp_save_chat')->insertGetId(['time' => time(), 'tousername' => $temp_array->ToUserName, 'fromusername' => $temp_array->FromUserName,
                    'createtime' => $temp_array->CreateTime, 'msgtype' => $temp_array->MsgType, 'msgid' => $temp_array->MsgId]);
                DB::table('wx_temp_save_text')->insert(['content' => $temp_array->Content, 'chat_id' => $id]);
            }
            if ($temp_array->MsgType == 'text') {
                $id = DB::table('wx_temp_save_chat')->insertGetId(['time' => time(), 'tousername' => $temp_array->ToUserName, 'fromusername' => $temp_array->FromUserName,
                    'createtime' => $temp_array->CreateTime, 'msgtype' => $temp_array->MsgType, 'msgid' => $temp_array->MsgId]);
                DB::table('wx_temp_save_img')->insert(['picurl' => $temp_array->PicUrl, 'mediaid' => $temp_array->MediaId, 'chat_id' => $id]);
            }
        } else {
//            没有信息返回，等待下次发送
            return;
        }
//        file_put_contents("temp_chat.log", "This is all.".json_encode($req).PHP_EOL, FILE_APPEND);
        return 'success';
    }

    public function showChats()
    {
//        显示所有的对话列表
        $data = DB::table('wx_chat_user')->get();
        foreach ($data as $k => $v) {
//            limit by 48 hours
            if ((int)$v->finalchattime + 172800 < time()) {
                if ((int)$v->finalchatnum < 5) {
                    $data[$k]['status'] = "可对话";
                } else {
                    $data[$k]['status'] = "达到系统最大等待玩家回复";
                }
            } else {
                $data[$k]['status'] = "对话已到期";
            }
        }
        return view('wx_test.list', ['data' => $data]);
    }

//    发送信息（未加密）
    public function sendMessage($touser, $msgtype, $data)
    {
        if ($msgtype == 'text') {

        } else if ($msgtype == 'image') {

        } else if ($msgtype == 'link') {
            
        }
        $url = getenv('WX_KF_SEND_URL');

    }

    private function sendWelcome()
    {
//        调用此欢迎


    }
}
