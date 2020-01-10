<?php

namespace Moell\LayuiAdmin\Models;


use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $table = 'navigation';

    protected $fillable = ['parent_id', 'name', 'icon', 'uri', 'is_link', 'guard_name', 'type', 'permission_name', 'sequence'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('admin.model_navigation'));
    }

    public static function getTree()
    {
        $items = Navigation::query()
            ->orderBy('sequence', 'desc')
            ->get();

        return make_tree($items->toArray());
    }
}