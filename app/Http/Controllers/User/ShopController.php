<?php


namespace App\Http\Controllers\User;

use App\Models\Member;
use App\Models\Address;
use App\Models\Cart;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use vendor\project\StatusTest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;


class ShopController extends Controller
{
    public function list()
    {
        $datas = [];
        $shops = Shop::all();
        foreach ($shops as $shop) {
            $data = [
                "id" => $shop->id,
                "shop_name" => $shop->shop_name,
                "shop_img" => $shop->shop_img,
                "shop_rating" => $shop->shop_rating,
                "brand" => $shop->brand,
                "on_time" => $shop->on_time,
                "fengniao" => $shop->fengniao,
                "bao" => $shop->bao,
                "piao" => $shop->piao,
                "zhun" => $shop->zhun,
                "start_send" => $shop->start_send,
                "send_cost" => $shop->send_cost,
                "distance" => $shop->distance,
                "estimate_time" => $shop->estimate_time,
                "notice" => $shop->notice,
                "discount" => $shop->discount,
                "status" => $shop->status
            ];
            $datas[] = $data;
        }
        return $datas;
    }
    //获取指定商家列表\
    public function shoplist(Request $request)
    {
        $id = $request->id;
        //dd($id);
        //商家店铺的信息
        $shops = DB::table('shops')->where('id', '=', $id)->first();
        //dd($shops);

        //查询商家菜品分类的数据
        $menucate = DB::table('menu_categories')->where('shop_id', '=', $id)->get();
        // dump($menucate);
        //商家菜单的信息
        $menus = DB::table('menus')->where('shop_id', '=', $id)->get();
        //dump($menus);

        //分类菜品所需的参数
        $cates = [];
        //分类下的详细菜品
        $goods_lists = [];
        foreach ($menucate as $menuc)
        {

            $goods_lists = [];
            foreach ($menus as $menu) {
                //dump($menu->category_id);
                if ($menuc->id == $menu->category_id) {
                    $goods_list = [
                        "goods_id" => $menu->id,
                        "goods_name" => $menu->goods_name,
                        "rating" => $menu->rating,
                        "goods_price" => $menu->goods_price,
                        "description" => $menu->description,
                        "month_sales" => $menu->month_sales,
                        "rating_count" => $menu->rating_count,
                        "tips" => $menu->tips,
                        "satisfy_count" => $menu->satisfy_count,
                        "satisfy_rate" => $menu->satisfy_rate,
                        "goods_img" => $menu->goods_img
                    ];
                    $goods_lists[] = $goods_list;
                }
            }
            // dd($goods_lists);
            $cate = [
                "description" => $menuc->description,
                "is_selected" => $menuc->is_selected,
                "name" => $menuc->name,
                "type_accumulation" => $menuc->type_accumulation,
                //菜品分类下面的菜品详细数据
                "goods_list" => $goods_lists
            ];

            $cates[] = $cate;

        }

//dd($cates);
        $data = [
            'id' => $shops->id,
            'shop_name' => $shops->shop_name,
            "shop_img" => $shops->shop_img,
            "shop_rating" => $shops->shop_rating,
            "brand" => $shops->brand,
            "on_time" => $shops->on_time,
            "fengniao" => $shops->fengniao,
            "bao" => $shops->bao,
            "piao" => $shops->piao,
            "zhun" => $shops->zhun,
            "start_send" => $shops->start_send,
            "send_cost" => $shops->send_cost,
            "distance" => $shops->discount,
            "estimate_time" => 30,
            "notice" => $shops->notice,
            "discount" => $shops->discount,
            //店铺评价的参数
            "evaluate" => [
                [
                    "user_id" => 12,
                    "username" => "w******k",
                    "user_img" => "/images/slider-pic4.jpeg",
                    "time" => "2017-2-22",
                    "evaluate_code" => 1,
                    "send_time" => 30,
                    "evaluate_details" => "还能凑合着吃"
                ]
            ],
            //菜品分类下面的菜品信息模块
            "commodity" => $cates
        ];
        return $data;
    }
    //地址列表
    public function addressList()
    {
        $id=Auth::user()->id;

        $addresses = Address::where('user_id','=',$id)->get();
//        dd($addresses);
      foreach ($addresses as &$v) {
            $v['area'] = $v['county'];
            $v['detail_address'] = $v['address'];
            $v['provence'] = $v['province'];
        }
        return $addresses;
    }
    //添加地址
    public function addAddress(Request $request)
    {
       //dd(1);
        $validator =Validator::make($request->all(), [
            'name' => 'required',
            'tel' => 'required',
            'provence' => 'required',
            'city' => 'required',
            'area' => 'required',
            'detail_address' => 'required',
        ], [
            'name.required' => '收货人姓名不能为空',
            'tel.required' => '收货人电话不能为空',
            'provence.required' => '省份不能为空',
            'city.required' => '城市不能为空',
            'area.required' => '区不能为空',
            'detail_address.required' => '详细地址不能为空',
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'false',
                'message' => $validator->errors(),
            ];
        }
        if (!preg_match('/^1[3456789]\d{9}$/', $request->tel)) {
            return [
                'status' => 'false',
                'message' => '电话不正确',
            ];
        }
        $user_id =Auth::user()->id;
        address::create([
            'user_id' => $user_id,
            'name' => $request->name,
            'tel' => $request->tel,
            'province' => $request->provence,
            'city' => $request->city,
            'county' => $request->area,
            'address' => $request->detail_address,
            'is_default' => 0,
        ]);
        return [
            'status' => 'true',
            'message' => '添加成功',
        ];
    }
    //指定地址接口
    public function address(Request $request)
    {
        $res = Address::where('id', '=', $request->id)->first();
        return [
            'id' => $res->id,
            'provence' => $res->province,
            'city' => $res->city,
            'area' => $res->county,
            'detail_address' => $res->address,
            'name' => $res->name,
            'tel' => $res->tel,
        ];
    }
    // 保存修改地址接口
    public function editAddress(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'tel' => 'required',
            'provence' => 'required',
            'city' => 'required',
            'area' => 'required',
            'detail_address' => 'required',
        ], [
            'name.required' => '收货人姓名不能为空',
            'tel.required' => '收货人电话不能为空',
            'provence.required' => '省份不能为空',
            'city.required' => '城市不能为空',
            'area.required' => '区不能为空',
            'detail_address.required' => '详细地址不能为空',
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'false',
                'message' => $validator->errors()->first(),
            ];
        }
        if (!preg_match('/^1[3456789]\d{9}$/', $request->tel)) {
            return [
                'status' => 'false',
                'message' => '电话不合法',
            ];
        }
        $address = Address::find($request->id);

        $address->update([
            'name' => $request->name,
            'tel' => $request->tel,
            'province' => $request->provence,
            'city' => $request->city,
            'county' => $request->area,
            'address' => $request->detail_address,
        ]);
        return [
            'status' => 'true',
            'message' => '修改成功',
        ];
        // dd(1);
    }
    //获取购物车数据接口
    public function cart()
    {
        $goods_list=[];
        $total=0;
        $id=Auth::user()->id;
        $goods=Cart::where('user_id',$id)->get();
        foreach($goods as $good){
            $goodsid=$good->goods_id;
//          dd($goodsid);
            $goodss=Menu::where('id',$goodsid)->get();
            $goodss=$goodss[0];
            $goods_list[]=[
                'goods_id'=>$goodss->id,
                'goods_name'=>$goodss->goods_name,
                'goods_img'=>$goodss->goods_img,
                'amount'=>$good->amount,
                'goods_price'=>$goodss->goods_price,
//                dd($goodss->goods_price),
            ];
            $total+=($good->amount)*$goodss->goods_price;
        }
        return[
          'goods_list'=> $goods_list,
          'totalCost'=>$total,
        ];

    }
    //保存购物车接口
    public function addCart(Request $request)
    {
        $user_id = Auth::user()->id;
        Cart::where('user_id', '=', $user_id)->delete();
        for ($i = 0; $i < count($request->goodsList); $i++) {
            Cart::create([
                'goods_id' => $request->goodsList[$i],
                'amount' => $request->goodsCount[$i],
                'user_id' => $user_id,
            ]);
        }
        return [
            'status' => 'true',
            'message' => '添加成功',
        ];
    }

    //添加订单接口
    public function addorder(Request $request)
    {
        $user_id=Auth::user()->id;
        //获取当前用户的购物车
        $goods_id=Cart::where('user_id',$user_id)->orderBy('created_at','desc')->first();
        //找到$shop_id
        $shop_id=Menu::where('id',$goods_id->goods_id)->first();
        //dd($shop_id->shop_id);
        $sn=date('Ymd',time()).uniqid();
        $address_id=$request->address_id;
        $address=Address::where('id',$address_id)->first();
        //dd($addre);
        $status=0;
        // $created_at=time();
        $out_trade_no=uniqid();
        $goods=Cart::where('user_id',$user_id)->get();
        //dd($goods);
        $total=0;
        $goods_ids=[];
        $amounts=[];
        foreach($goods as $v){
            $goods_id=$v->goods_id;
            $amount=$v->amount;
            $goods_price=Menu::where('id',$goods_id)->first()->goods_price;
            $total+=($amount)*($goods_price);
            $goods_ids[]=$goods_id;
            $amounts[]=$amount;
        }
        DB::beginTransaction();
        try{
            $order=Order::create([
                'user_id'=>$user_id,
                'shop_id'=>$shop_id->shop_id,
                'sn'=>$sn,
                'province'=>$address->province,
                'city'=>$address->city,
                'county'=>$address->county,
                'address'=>$address->address,
                'tel'=>$address->tel,
                'name'=>$address->name,
                'total'=>$total,
                'status'=>$status,
                //'create_at'=>$created_at,
                'out_trade_no'=>$out_trade_no,
            ]);
//            dd($goods_ids);
            $order_id=$order->id;
            foreach ($goods_ids as $k=>$goods_id){
                $goods=Menu::where('id',$goods_id)->first();
                $orderd=OrderDetail::create([
                    'order_id'=>$order_id,
                    'goods_id'=>$goods_id,
                    'goods_name'=>$goods->goods_name,
                    'goods_price'=>$goods->goods_price,
                    'goods_img'=>$goods->goods_img,
                    'amount'=>$amounts[$k],
                ]);
            }
            if ($order&& $orderd){
                DB::commit();
            }
        }catch (\Exception $e){
            DB::rollback();
        }
        return [
            "status"=> "true",
            "message"=> "添加成功",
            "order_id"=>"{$order_id}",
        ];

    }
    //获得指定订单接口
    public function order(Request $request)
    {
        $order_id=$request->id;
            //找到shop_id
        $shop_id=Order::where('id',$order_id)->first()->shop_id;
        //找到店铺的名字和图片
        $shops=Shop::where('id',$shop_id)->first();
        $shop_name=$shops->shop_name;
        $shop_img=$shops->shop_img;

        $orders=Order::where('id',$order_id)->first();
        $order_code=$orders->sn;
        $order_status=$orders->status;
        $order_price=$orders->total;
        $order_address=$orders->pronince.$orders->city.$orders->county.$orders->address;

        $goods=OrderDetail::where('order_id',$order_id)->get();
        $goods_list=[];
        foreach ($goods as $good) {

            $goods_list[]=[
                'goods_id'=>$good->goods_id,
                'goods_name'=>$good->goods_name,
                'goods_img'=>$good->goods_img,
                'goods_price'=>$good->goods_price,
                'amount'=>$good->amount,
            ];
        }
//        dd($goods_list);
        $data=[
            "id"=>$order_id,
            "order_code"=> $order_code,
            "order_birth_time"=>date("Y-m-d H:i:s",strtotime($orders->created_at)),
            "order_status"=> "代付款",
            "shop_id"=> $shop_id,
            "shop_name"=> $shop_name,
            "shop_img"=> $shop_img,
            "goods_list"=> $goods_list,
            "order_price"=> $order_price,
//            $goods_list,
            "order_address"=> $order_address
        ];
        return $data;
    }


    //获订单接口列表接口
    public function orderList(Request $request)
    {

    }
    //修改密码
    public function changePassword(Request $request)
    {
        $oldPassword=$request->oldPassword;
        $newPassword=bcrypt($request->newPassword);
        $user_id=Auth::user()->id;
        //取出数据库中的原密码
        $dbPassword=Member::where('id',$user_id)->first()->password;
//        dd( $dbPassword);
        if(!Hash::check($oldPassword,$dbPassword)){
            return json_encode([
                "status"=> "false",
                "message"=> "旧密码错误"
            ]);
        }
        DB::table('members')->where('id',$user_id)
            ->update(['password'=>$newPassword]);
        return [
            "status"=> "true",
            "message"=> "修改成功"
        ];
    }
    //忘记密码接口
    public function forgetPassword(Request $request)
    {
//        dd('fdv');
        $tel=$request->tel;
        $sms=$request->sms;
        $password=$request->password;
//        $oldsms  = Redis::get("tel");
        $oldsms=12;
        $vip=Member::where('tel',$tel)->first();


        $validator=Validator::make($request->all(),[
            'password'=>'required',
            'tel'=>'required',
            'sms'=>'required',

        ],[
            'password.required'=>'新密码不能为空',
            'tel.required'=>'电话不能为空',
            'sms.required'=>'验证码不能为空',
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'false',
                'errors' => $validator->errors()->first()
            ];
        }elseif($sms==$oldsms&&$tel==$vip->tel){
            $vip->update([
                'password'=>bcrypt($password),
            ]);
            return [
                "status"=> "true",
                "message"=> "修改成功"
            ];

        }else{
            return [
                "status"=> "false",
                "message"=> "修改失败"
            ];
        }
    }
}
