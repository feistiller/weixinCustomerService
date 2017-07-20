<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class IndexController extends BaseController
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
                if ($temp_data) {
                    $temp_table->where('id', $temp_data[0]->id)->update(['finalchattime' => time(), 'finalchatnum' => 0]);
                } else {
//                    介入机器人0
                    $temp_table->insert(['useropenid' => $temp_array->FromUserName, 'createtime' => time(), 'finalchattime' => time(), 'finalchatnum' => 0, 'robot' => 0]);
                }
//                事件无msgid
                $id = DB::table('wx_temp_save_chat')->insertGetId(['time' => time(), 'tousername' => $temp_array->ToUserName, 'fromusername' => $temp_array->FromUserName,
                    'createtime' => $temp_array->CreateTime, 'msgtype' => $temp_array->MsgType]);
                DB::table('wx_temp_save_event')->insert(['sessionfrom' => $temp_array->SessionFrom, 'event' => $temp_array->Event, 'chat_id' => $id]);
                $this->sendWelcome($temp_array->FromUserName);
                $this->chatLog($temp_array->FromUserName, '进入聊天', '用户:');
            }
            if ($temp_array->MsgType == 'text') {
                if ($temp_array->Content == "人工客服") {
                    DB::table('wx_chat_user')->where('useropenid', $temp_array->FromUserName)->update(['robot' => 1]);
                }
                $id = DB::table('wx_temp_save_chat')->insertGetId(['time' => time(), 'tousername' => $temp_array->ToUserName, 'fromusername' => $temp_array->FromUserName,
                    'createtime' => $temp_array->CreateTime, 'msgtype' => $temp_array->MsgType, 'msgid' => $temp_array->MsgId]);
                DB::table('wx_temp_save_text')->insert(['content' => $temp_array->Content, 'chat_id' => $id]);
                $temp_data = DB::table('wx_chat_user')->where('useropenid', $temp_array->FromUserName)->get()->toArray();
                if ($temp_data[0]->finalchatnum == 0) {
//                    第一次说话
                    $this->sendMessage($temp_array->FromUserName, 'text', '收到您的消息，请输入您的问题');
                } else if ($temp_data[0]->robot == 0) {
//                    机器人回复
                    $this->talk2Robot($temp_array->FromUserName,$temp_array->Content);
                }
                DB::table('wx_chat_user')->where('id', $temp_data[0]->id)->update(['finalchatnum' => 1]);
                $this->chatLog($temp_array->FromUserName, $temp_array->Content, '用户:');
            }
            if ($temp_array->MsgType == 'img') {
                $id = DB::table('wx_temp_save_chat')->insertGetId(['time' => time(), 'tousername' => $temp_array->ToUserName, 'fromusername' => $temp_array->FromUserName,
                    'createtime' => $temp_array->CreateTime, 'msgtype' => $temp_array->MsgType, 'msgid' => $temp_array->MsgId]);
                DB::table('wx_temp_save_img')->insert(['picurl' => $temp_array->PicUrl, 'mediaid' => $temp_array->MediaId, 'chat_id' => $id]);
                $temp_data = DB::table('wx_chat_user')->where('useropenid', $temp_array->FromUserName)->get()->toArray();
                if ($temp_data[0]->finalchatnum == 0) {
//                    第一次说话
                    $this->sendMessage($temp_array->FromUserName, 'text', '收到您的消息，准备为您分配客服，输入查询回复当前排队状况');
                }
                DB::table('wx_chat_user')->where('id', $temp_data[0]->id)->update(['finalchatnum' => 1]);
            }
        } else {
//            没有信息返回，等待下次发送
            return;
        }
//        file_put_contents("temp_chat.log", "This is all.".json_encode($req).PHP_EOL, FILE_APPEND);
        return 'success';
    }




    private function sendWelcome($touser)
    {
//        调用此欢迎
        $this->sendMessage($touser, 'text', '欢迎联系，输入"人工客服"进入非机器人模式');
    }


}
