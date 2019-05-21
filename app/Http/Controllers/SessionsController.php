<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
	//展现登陆页面
   public function create(){
   		return view('sessions.create');
   }
   //登录
   public function store(Request $request){
	   	$credentials=$this->validate($request,[
	   		'email'=>'required|email|max:255',
	   		'password'=>'required'

	   	]);
	   	if(Auth::attempt($credentials,$request->has('remember'))){
	   		session()->flash('success','欢迎回来');
	   		return redirect()->route('users.show',[Auth::user()]);
	   	}else{
	   		session()->flash('danger','失败，邮箱密码不匹配');
	   		return redirect()->back()->withInput();
	   	}
   }

   //推出
   public  function destroy(){
   		Auth::logout();
   		session()->flash('success','您已经成功推出');
   		return redirect('login');
   }
}
