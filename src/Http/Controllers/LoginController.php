<?php

namespace Moell\LayuiAdmin\Http\Controllers;


use Hsk9044\LwhyCasClient\Contracts\CasFactor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginShowForm()
    {
        return view("admin::login");
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 0;

        if (Auth::guard("admin")->attempt($credentials)) {
            return $this->success("登录成功");
        }

        return $this->fail('账号或密码有误');
    }

    public function logout()
    {
        Auth::guard("admin")->logout();

        return redirect()->route("admin.login-show-form");
    }


    public function auth(Request $request) {

        $ticket = $request->get('ticket');
        $id = $request->get('id');
        if(empty($ticket) || empty($id)) {
            return $this->fail('缺少参数');
        }

        $factor = CasFactor::make();
        $res = $factor->authCheck($ticket, $id);
        $casUser = $factor->_getCasUser($res['token']);

        if(!($casUser->hasRole('php.developer') || $casUser->hasRole('super')))
            return $this->fail("无PHP开发组权限");

        Auth::guard('admin')->login($casUser);

        return redirect()->route('admin.index');
    }
}