<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    //只让未登录用户访问登录界面
    public function __construct()
    {
        $this->middleware('guest', [
                'only' => 'create'
            ]
        );
    }

    public function create()
    {
        return view('sessions.create');
    }

    //用户登录
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            if (Auth::user()->activated) {
                // 登录成功后的相关操作
                session()->flash('success', '欢迎回来');
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
            } else {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
        } else {
            // 登录失败后的相关操作
            session()->flash('danger', '很抱歉您的邮箱和密码不匹配');
            return redirect()->back()->withInput();

        }
    }

    //用户登出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出');
        return redirect('login');
    }
}
