<?php

namespace Moell\LayuiAdmin\Models;


use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $guarded = ['id'];

    public function permission()
    {
        return $this->hasMany('Moell\LayuiAdmin\Models\Permission', 'pg_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('admin.model_permission_groups'));
    }
}