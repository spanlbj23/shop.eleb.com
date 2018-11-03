<?php

namespace App\Http\Controllers\User;

use App\Models\Member;
use App\Models\SignatureHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Validator;

class UserController extends Controller
{
    //注册
    public function regist(Request $request)
    {

        //Redis::connection();
        $username = $request->username;
        $tel=$request->tel;
        $password=$request->password;
        //$sms= Redis::get("tel");
        $sms=12;

        if ($sms != $request->sms) {
            return [
                'status' => 'false',
                'message' => '注册失败'
            ];
        } else {
            Member::create([
                'username' => $username,
                'tel' => $tel,
                'password' => bcrypt($password),
            ]);
            return [
                'status' => 'true',
                'message' => '注册成功'
            ];
        }

    }
//登录
//    public function login(Request $request){
////        dd('fe');
//        //dd($request);
////        return "login";
//       //验证数据
//        $validator = Validator::make($request->all(), [
//            'username' => 'required',
//            'password' => 'required',
//        ],[
//            'username.required'=>'用户名不能为空',
//            'password.required'=>'密码不能为空',
//        ]);
//
//        if ($validator->fails()) {
//          return[
//              "status"=>"false",
//              "message"=>"登录失败1",
//              "user_id"=>$request->id,
//              "username"=>"$request->name",
//              //$validator_errors()获取字段验证信息
//          ];
//
//        }
//        //登录验证
//        if(Auth::attempt(['username'=>$request->name,'password'=>$request->password])){
//           return[
//               "status"=>"true",
//               "message"=>"登录成功2",
//               "user_id"=>$request->id,
//               "username"=>$request->username,
//           ];
//        }
//           return  [
//               "status"=>"false",
//               "message"=>"登录失败3",
//               "user_id"=>$request->id,
//               "username"=>$request->username,
//           ];
//
//        //返回结果
//    }



    public function login(Request $request)
    {
        if (Auth::attempt([
            'username' => $request->name,
            'password' => $request->password,
        ])
        ) {
            return [
                'status' => 'true',
                'message' => '登录成功',
                'id' => Auth::user()->id,
                'username' =>$request->name,
            ];
        } else {
            return [
                'status' => 'false',
                'message' => '登录失败',
            ];
        }
    }

    //短信验证
//    public function sendSms(Request $request)
//    {
//        $tel = $request->tel;
//        $params = [];
//        // *** 需用户填写部分 ***
//        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
//        $accessKeyId = "LTLTAIKUd5ogD8vGv4";
//        $accessKeySecret = "6vyJOAmp3aVcHwlYY5a1IAzOEBJZ6h";
//
//        // fixme 必填: 短信接收号码
//        $params["PhoneNumbers"] = $tel;
//
//        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
//        $params["SignName"] = "Sakura个人记录";
//
//        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
//        $params["TemplateCode"] = "SMS_149102596";
//
//        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
//        $params['TemplateParam'] = Array(
//            "code" => random_int(1000, 9999)
//        );
//
//        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";
//
//        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";
//        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
//        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
//            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
//        }
//
//        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
//        $helper = new SignatureHelper();
//
//        // 此处可能会抛出异常，注意catch
//        $content = $helper->request(
//            $accessKeyId,
//            $accessKeySecret,
//            "dysmsapi.aliyuncs.com",
//            array_merge($params, array(
//                "RegionId" => "cn-hangzhou",
//                "Action" => "SendSms",
//                "Version" => "2017-05-25",
//            ))
//        // fixme 选填: 启用https
//        // ,true
//        );
//
//        $redis = Redis::connection(); //创建一个redis连接  Redis 实例
//        $redis = Redis::set('tel', $redis);//将验证码保存到redis
//        Redis::expire('tel', 300);//设置过期时间300S
//        if (!empty($redis)) {
//            return json_encode([
//                "status" => "true",
//                "message" => "获取短信验证码成功"
//            ]);
//        } else {
//            return json_encode([
//                "status" => "false",
//                "message" => "获取短信验证码失败"
//            ]);
//        }
//    }
}
