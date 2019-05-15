<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request){
        $str=file_get_contents('php://input');
        $data=json_decode($str,true);
        $userInfo=DB::table('users')->where(['email'=>$data['email']])->first();
//        dd($userInfo);
        if ($userInfo){
            if (password_verify($data['pass'],$userInfo->pass)){
                //生成token
                $token=$this->loginToken($userInfo->id);
//                dd($token);
                $redis_token_key='login_token:id:'.$userInfo->id;
                Redis::set($redis_token_key,$token);
                Redis::expire($redis_token_key,604800);
                $response=[
                    'errno'=>0,
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token
                    ]
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response=[
                    'errno'=>50010,
                    'msg'=>'账号或密码不正确',
                ];
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }
        }else{
            //用户不存在
            $response=[
                'errno'=>50010,
                'msg'=>'用户不存在',
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }
    }

    //登陆token
    public function loginToken($id){
        $token=substr(sha1($id).time().Str::random(16),5,15);
        return $token;
    }
}
