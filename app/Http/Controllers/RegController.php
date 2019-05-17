<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegController extends Controller
{
    public function reg(Request $request)
    {
        $str=file_get_contents('php://input');
        $data=json_decode($str,true);
//        dd($data);
        //验证邮箱是否唯一
        $emailonly = DB::table('users')->where(['email' => $data['email']])->first();
//        dd($emailonly);
        if ($emailonly) {
            $response = [
                'errno' => 50004,
                'msg' => '邮箱已存在'
            ];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        //密码加密
        $pass=password_hash($data['pass'],PASSWORD_BCRYPT);
        $data1=[
            'name'=>$data['name'],
            'email'=>$data['email'],
            'pass'=>$pass,
            'add_time'=>time()
        ];
        //加入数据库
        $res= DB::table('users')->insert($data1);
//        dd($id);
        if ($res) {
            //TODO 注册成功
            $response = [
                'errno' => 0,
                'msg' => 'ok'
            ];
        } else {
            //TODO 注册失败
            $response = [
                'errno' => 50004,
                'msg' => '注册失败'
            ];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);

    }
}
