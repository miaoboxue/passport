<?php

namespace App\Http\Controllers\Curl;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurlController extends Controller
{
    //
    public function test(){
        print_r($_FILES);
        echo 111;
    }
    public function api(){
        //echo 111;
        $time=$_GET['t'];
        //echo $time;die;

        //echo 111;

        $key='pass';                                        //双方约定
        $slat='xxxxx';
        $method = 'AES-128-CBC';
        $iv = substr(md5($time . $slat),5,16);           //加密向量

        //接收加密数据
        $post_data=$_POST['data'];
        //print_r($post_data);die;
        $post_sign=$_POST['sign'];

        $pub_key=openssl_pkey_get_public(file_get_contents('./key/pub.key'));
        $sign_key=openssl_verify($post_data,base64_decode($post_sign),$pub_key,OPENSSL_ALGO_SHA256);
        var_dump($sign_key);die;
        //echo $pub_key;die;

        //解密加密数据
        $dec_str = openssl_decrypt($post_data,$method,$key,OPENSSL_RAW_DATA,$iv);

        if(1){       //解密成功 响应客户端
            $now = time();
            $response=[
                'errno'=> 0,
                'msg' => 'ok',
                'data'=> 'this is secret'
            ];
            $iv2 = substr(md5($now . $slat),5,16);
            //加密响应数据
            $enc_str = openssl_encrypt(json_encode($response),$method,$key,OPENSSL_RAW_DATA,$iv2);
            $arr =[
                't'=>$now,
                'data'=>base64_encode($enc_str),
            ];
            echo json_encode($arr);
        }
    }

    public function r(){
        $data=[
            'name'=>'zhangsan',
        ];
        echo json_encode($data);
    }

    public function ajaxmui(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data=[
            'ok'=>'登录成功',
        ];
        $error=[
            'on'=>0,
        ];

        if($username== 'zhangsan' && $password == 'admin123'){
            echo json_encode($data);
        }else{
            echo json_encode($error);

        }
    }



}
