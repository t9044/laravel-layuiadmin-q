<?php

namespace Moell\LayuiAdmin\Http\Controllers;


use Illuminate\Http\Request;
use Moell\LayuiAdmin\AdminExport;
use Moell\LayuiAdmin\AdminImport;
use Moell\LayuiAdmin\Http\Requests\AdminUser\CreateOrUpdateRequest;
use Moell\LayuiAdmin\Models\AdminUser;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * @author moell<moel91@foxmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $adminUsers = AdminUser::query()->where(request_intersect(['name', 'email']))->paginate($request->get("limit"));

        return view("admin::admin_user.index", compact('adminUsers'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view("admin::admin_user.create");
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOrUpdateRequest $request)
    {
        $data = $request->only([
            'name', 'email', 'password', 'phone'
        ]);
        $data['password'] = bcrypt($data['password']);

        AdminUser::create($data);

        return $this->success();
    }

    /**
     * @param AdminUser $adminUser
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(AdminUser $adminUser)
    {
        return view("admin::admin_user.edit", compact('adminUser'));
    }

    /**
     * @author moell<moel91@foxmail.com>
     * @param CreateOrUpdateRequest $request
     * @param AdminUser $adminUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateOrUpdateRequest $request, AdminUser $adminUser)
    {
        $data = $request->only([
            'name', 'status', 'phone'
        ]);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);

        }

        $adminUser->fill($data);
        $adminUser->save();

        return $this->success();
    }

    /**
     * @author moell<moel91@foxmail.com>
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $adminUser = AdminUser::query()->findOrFail($id);

        $adminUser->delete();

        return $this->success();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function assignRolesForm($id)
    {
        $adminUser = AdminUser::query()->findOrFail($id);
        $roles = Role::query()->get();
        $userRoles = $adminUser->getRoleNames();

        return view("admin::admin_user.assign_role", compact("roles", "adminUser", "userRoles"));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRoles(Request $request, $id)
    {
        AdminUser::query()->findOrFail($id)->syncRoles($request->input('roles', []));

        return $this->success();
    }


    public function tpl()
    {
        return Excel::download(new AdminExport(), 'admin_user_template.xlsx');
    }


    public function importDataList()
    {
        $file = request()->file('file');
        $array = Excel::toArray(new AdminImport(), $file, null);

        $array = $array[0];
        if (count($array) < 2) return $this->fail('导入文件格式不正确或数据为空');

        array_shift($array);

        $result = [];
        DB::beginTransaction();
        try
        {
            foreach ($array as $index=>$item)
            {
                $data = [
                    'name'=>trim($item[0]),
                    'phone'=>trim($item[1]),
                    'user'=>trim($item[2]),
                    'pwd'=>trim($item[3]),
                    'role'=>trim($item[4]),
                ];

                $validator = Validator::make($data, [
                    'name' => 'required',
                    'phone' => 'required|size:11',
                    'user' => 'required|unique:admin_users,email',
                    'pwd' => 'required',
                    'role' => 'required',
                ]);

                if ($validator->fails())
                {
                    $s = $index + 2;
                    throw new \Exception("第{$s}行" . $validator->errors()->first());
                }

                $role = Role::where('id', $data['role'])->first();
                if(!$role) throw new \Exception("角色id不存在");

                $id = AdminUser::insertGetId([
                    'name' => $data['name'],
                    'email' => $data['user'],
                    'password' => bcrypt($data['pwd']),
                    'status' => '0',
                    'phone'=>$data['phone'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'remember_token' => ''
                ]);
                $adminUser = AdminUser::where('id', $id)->first();
                $adminUser->assignRole($role->name);

            }
            DB::commit();
            return $this->success();
        }catch (\Exception $e)
        {
            DB::rollBack();
            return $this->fail($e->getMessage());
        }

    }
}