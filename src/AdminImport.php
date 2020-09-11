<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 16:16
 */

namespace Moell\LayuiAdmin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class AdminImport extends StringValueBinder implements ToArray, WithCustomValueBinder
{
    public function array(Array $collection)
    {
        //
    }
}