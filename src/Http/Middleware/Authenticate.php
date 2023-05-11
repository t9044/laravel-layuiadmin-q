<?php

namespace Moell\LayuiAdmin\Http\Middleware;


use Hsk9044\LwhyCasClient\Contracts\CasFactor;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class Authenticate extends Middleware
{
    protected $guards;

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (!$this->guards) {
            return route('login');
        }

        if (in_array('admin', $this->guards)) {
            //$url = CasFactor::make()->getLoginUrl("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/admin/auth");

            //header("Location: {$url}");
           // exit();
            return route("admin.login-show-form");
        }
    }

    public function authenticate($request, array $guards)
    {
        $this->guards = $guards;

        try{
            parent::authenticate($request, $guards);
        }catch (CasKeyInvalidException $e) {
            Auth::guard('admin')->logout();
        }
    }
}
