<?php

namespace Moell\LayuiAdmin\Http\Controllers;


use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogListController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $size = $request->get('limit') ?? 10;
        $fileName = $request->get('file_name') ?? '';
        //验证路径是否合法
        $logPath = config('public.crontab_file_path');
        $isExist = File::exists($logPath);

        //路径下所有文件名称
        $logList = collect();
        if($isExist){
            $originList = File::allFiles($logPath);
            foreach ($originList as $value){
                $item['path'] = $value->getPath();
                $item['path_name'] = $value->getPathname();
                $item['file_name'] = $value->getFilename();
                $logList->push($item);
            }
        }
        $result = [];
        $result['page_curent'] = $page;
        $result['page_size'] = $size;
        //整合数据列表
        if(empty($fileName)){
            $result['list_count'] = $logList->count();
            $result['list'] = $logList->forPage($page,$size)->sortBy('file_name');
//            return $result;
        }
        else {
            $filtered = $logList->filter(function ($item, $key) use ($fileName) {
                if(stripos($item['file_name'],$fileName) !== false ){
                    return $item;
                }
            });
            $result['list_count'] = $filtered->count();
            $result['list'] = $filtered->forPage($page,$size)->sortBy('file_name');
//            return  $result;
        }
        return view("admin::log_list.index", compact('result'));
    }

    public function open(Request $request){
        $logPath = $request->get('url') ?? '';
        $isExist = File::exists($logPath);
        if($isExist){
            $originList = File::get($logPath);
            return $originList;
        }
        return false;
    }
}