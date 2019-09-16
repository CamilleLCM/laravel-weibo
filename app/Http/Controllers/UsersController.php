<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Mail;

class UsersController extends Controller
{
    //中间件过滤
  public function __construct()
  {
      //除了create和store方法其余都要登录才能访问
      $this->middleware('auth',[
          'except'=>['create','store','index','confirmEmail']
      ]);
      //只有未登录的用户能访问create方法
      $this->middleware('guest',[
          'only'=>'create'
      ]);

  }
    //用户列表
    public function index(){
      $users = User::paginate(10);
      return view('users.index',compact('users'));

    }

    //注册表单
    public function create()
    {
        return view('users.create');
    }

    //显示用户个人信息
    public function show(User $user)
    {
        $statuses = $user->statuses()
                        ->orderBy('created_at','desc')
                        ->paginate(10);
        return view('users.show', compact('user','statuses'));
    }

    //用户注册逻辑
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    //用户编辑页面
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //用户编辑逻辑
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $data['name'] = $request->name;

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','个人资料更新成功');
        return redirect()->route('users.show', $user);
    }

    //删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','删除成功');
        return back();
    }

    //邮箱发送
    public function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = '111111111@qq.com';
        $name = 'camille';
        $to = $user->email;
        $subject = "欢迎注册Camille的微博，请确认您的邮箱";
        Mail::send($view,$data,function ($message)use($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });

    }

    //邮箱激活
    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated = true;
        $user->activation_token =null;
        $user->save();
        Auth::login($user);
        session()->flash('success','恭喜你，激活成功');
        return redirect()->route('users.show',[$user]);
    }

    //用户关注的人列表显示
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name."关注的人";
        return view('users.show_follow',compact('users','title'));
    }

    //用户粉丝列表显示
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name."的粉丝";
        return view('users.show_follow',compact('users','title'));
    }

}
