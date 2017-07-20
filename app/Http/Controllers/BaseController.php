<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BaseController extends Controller
{


//    发送信息（未加密）
    public function sendMessage($touser, $msgtype, $data)
    {
        if ($msgtype == 'text') {
            $data = array(
                'touser' => $touser,
                'msgtype' => 'text',
                "text" => array(
                    'content' => $data
                )
            );
        } else if ($msgtype == 'image') {
            $data = array(
                'touser' => $touser,
                'msgtype' => 'image',
                "image" => array(
                    'media_id' => $data
                )
            );
        } else if ($msgtype == 'link') {
            $data = array(
                'touser' => $touser,
                'msgtype' => 'link',
                "link" => array(
                    "title" => "Happy Day",
                    "description" => "Is Really A Happy Day",
                    "url" => "URL",
                    "thumb_url" => "THUMB_URL"
                )
            );
        }
        $url = getenv('WX_KF_SEND_URL');
        $token = $this->getAccess_token();
        $this->log('debug', $token, 'This is token:');
        $url=$url.$token;
        $this->curlPost($url, $data, 10);
    }

//    获得有期限的token并且记录一定的时间
    private function getAccess_token()
    {
//    暂存文件
        $url = getenv('GET_ACCESS_TOKEN_URL');
        $myfile = fopen("access_token.log", "a+");
        $content = json_decode(fgets($myfile));
        if ($content != '' && $content->time + 7100 > time()) {
            return $content->access_token;
        } else {
            $data = $this->curlGet($url);
            $data = json_decode($data);
            $data->time = time();
            fwrite($myfile, json_encode($data));
        }
        fclose($myfile);
        return $data->access_token;
    }

    //post请求
    public function curlPost($url, $request, $timeout = 5)
    {
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, json_encode($request, JSON_UNESCAPED_UNICODE));
        $this->log('curl', $url, 'This is postURL:');
        $this->log('curl', $request, 'This is post:');
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
        $output = curl_exec($con);
        $this->log('curl', $output, 'This is postReturn:');
        return json_decode($output, true);
    }

    //get请求
    public function curlGet($url, $data = null, $timeout = 5)
    {
        //初始化
        if ($data != null) {
            $url = $url . '?' . http_build_query($data);
        }
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);//这个是https。
        $this->log('curl', $url, 'This is get:');
        $output = curl_exec($con);
        $this->log('curl', $output, 'This is getReturn:');
        return $output;
    }

//    简单日志方法
    public function log($name, $data, $data_before = '')
    {
        $date = date("Y-m-d", time());
        $time = date("Y-m-d h:i:sa", time());
        file_put_contents('log/' . $date . $name . '.log', $time . $data_before . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }

}