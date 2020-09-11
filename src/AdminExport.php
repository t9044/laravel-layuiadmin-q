<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 16:10
 */

namespace Moell\LayuiAdmin;

use App\Models\CommonConfig;
use App\Models\CommonConfigType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminExport extends StringValueBinder implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '姓名',
            '手机号',
            '账号',
            '密码(8位)',
            '角色id'
        ];
    }
}