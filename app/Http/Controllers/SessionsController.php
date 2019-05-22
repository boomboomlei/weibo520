<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{

	public function _construct(){
		$this->middleware('guest',[
			'only'=>['create']
		]);
	}
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
	   		if(Auth::user()->activated){
	   			session()->flash('success','欢迎回来');

		   		$fallback=route('users.show',Auth::user());
		   		return redirect()->intended($fallback);
	   		}else{
	   			Auth::logout();
	   			session()->flash('warning','您的账号未激活，请检查邮箱中的注册邮件进行激活。');
	   			return redirect('/');

	   		}

	   		// return redirect()->route('users.show',[Auth::user()]);
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
