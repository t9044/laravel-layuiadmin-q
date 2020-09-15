@section("title", "日志列表")

@extends("admin::layouts.admin")

@section("breadcrumb")
    <div class="admin-breadcrumb">
         <span class="layui-breadcrumb">
            <a href="{{ url("admin/log-list") }}">日志列表</a>
        </span>
    </div>
@endsection
@section("content")
    <div class="layui-card-body ">
        <form class="layui-form layui-col-space5" id="search-form">
            <div class="layui-inline layui-show-xs-block">
                <input type="text" name="file_name"  placeholder="请输日志名称" value="{{ request("file_name") }}" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-inline layui-show-xs-block">
                <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
            </div>
        </form>
    </div>
    <div class="layui-card-body ">

        <script type="text/html" id="table-action">
            <div class="layui-btn-container">
                <a class="layui-btn layui-btn-xs" data-href="{{ route("admin-user.create") }}" lay-event="edit">编辑</a>
                <a class="layui-btn layui-btn-xs layui-btn-danger" data-href="{{ route("admin-user.create") }}" lay-event="delete">删除</a>
            </div>
        </script>
        <table  lay-filter="table-hide" style="display: none" lay-data="{height:'full-310', cellMinWidth: 80,toolbar: '#toolbar' , limit: 10}}">
            <thead>
            <tr>
                <th lay-data="{field:'file_name'}">文件名称</th>
                <th lay-data="{field:'path_name'}">路径</th>
                <th lay-data="{field:'id', fixed: 'right', width:200, align:'center'}">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result['list'] as $loglist)
                <tr>
                    <td>{{ $loglist['file_name'] }}</td>
                    <td>{{ $loglist['path_name'] }}</td>
                    <td>
                        @if(admin_user_can("admin-user.edit"))
                            <a class="layui-btn layui-btn-xs" onclick="openIframe2('{{ url("admin/log-open?url=".$loglist['path_name'])}}','日志查看', '893px', '600px')">查看</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div id="page"></div>
    </div>
@endsection

@section("script")
    <script>
        function exportTpl() {
            window.open('{{url('admin/admin-user-tpl')}}');
        }
        function openIframe2(url, title, width, height) {
            $.get(url, function(view) {
                layer.open({
                    type: 1,
                    title: title,
                    shadeClose: true,
                    shade: false,
                    maxmin: true, //开启最大化最小化按钮
                    skin: 'layui-layer-rim', //加上边框
                    area:[
                        width ? width : '893px',
                        height ? height : '600px'
                    ],
                    content: view.big().replace(/\n/g,"<br/><br/>")
                });
            });
        };
        layui.use(['form', 'table', 'upload'], function(){

        var table = layui.table;
        var upload = layui.upload;
        table.init("table-hide");


        table.on("tool(table-hide)", function(obj) {
          console.log(obj);
            switch (obj.event) {
              case 'edit':
                console.log(obj.data);
                break;
              case 'delete':
                console.log(obj.data);
                break;
            }
        });

        console.log($('#chooseTemplate'));

        admin.paginate("{{ $result['list_count'] }}", "{{$result['page_curent'] }}","10",[10,20,30]);

      });
    </script>
@endsection