<?php

namespace App\Http\Controllers\Api;

use App\Model\ApiUserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
class ApiController extends Controller
{

   public function userlogin(){
       $url=$_GET['rediret'] ?? env('SHOP_URL');
       $data=[
         'rediret'=>$url,
       ];
       return view('api.login',$data);
   }
    public function loginall(Request $request){
        //print_r($_POST);
        $email = $request->input('email');
        $password = $request->input('password');
        $userInfo=ApiUserModel::where(['email'=>$email])->first();
        $url=env('SHOP_URL');
        if($userInfo){
            if(password_verify($password,$userInfo->password)){
                $token = substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$userInfo->id,time()+86400,'/','shop.com',false,true);
                setcookie('token',$token,time()+86400,'/','shop.com',false,true);

                $redis_k_web_token='str:u:key'.$userInfo->id;
                Redis::set($redis_k_web_token,$token);
                Redis::expire($redis_k_web_token,time()+86400);

                echo 'success';
                header("refresh:1;url=".$url);

            }else{
                echo "账号或密码错误";
            }
        }else{
            echo 222;
        }
    }
    public function apilogin(Request $request){
       //echo '<pre>';print_r($_POST);echo '</pre>';die;
        $email = $request->input('email');
        $password = $request->input('password');
        $userInfo=ApiUserModel::where(['email'=>$email])->first();
        if(empty($userInfo)){
            $response=[
                'errno'=>40001,
                'msg'=>'账号不存在',
            ];
            return $response;
        }
        if(password_verify($password,$userInfo->password)){
            $token = substr(md5(time() . mt_rand(1,99999)),10,10);


            $redis_token_api_login='api:login:token'.$userInfo->id;
            Redis::set($redis_token_api_login,$token);
            Redis::expire($redis_token_api_login,time()+86400);

            $response=[
                'erron'=>0,
                'msg'=>'登录成功'
            ];

        }else{
            $response=[
                'erron'=>40000,
                'msg'=>'账号或密码错误',
            ];
        }
        return $response;
    }
}
