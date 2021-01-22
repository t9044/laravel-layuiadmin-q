@section("title", "导航菜单")

@extends("admin::layouts.admin")

@section("breadcrumb")
    <div class="admin-breadcrumb">
         <span class="layui-breadcrumb">
            <a href="{{ route("permission-group.index") }}">导航菜单</a>
        </span>
    </div>
@endsection
@section("content")
    <div class="layui-card-body ">
        <form class="layui-form layui-col-space5" id="search-form">
            <div class="layui-inline layui-show-xs-block">
                <select name="type">
                    <option value="">请选择导航类型</option>
                    {!! admin_enum_option_string("navigation_type", request("type")) !!}
                </select>
            </div>
            <div class="layui-inline layui-show-xs-block">
              <select name="guard_name">
                <option value="">请选择Guard</option>
                {!! admin_enum_option_string("guard_names", request("guard_name")) !!}
              </select>
            </div>
            <div class="layui-inline layui-show-xs-block">
              <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
            </div>
            <div class="layui-inline layui-show-xs-block">
                @if(admin_user_can("gm.nav"))
                    <a class="layui-btn newNav" onclick=""><i class="layui-icon"></i>添加</a>
                @endif
            </div>
        </form>
    </div>
    <div class="layui-card-body ">
        <table class="layui-table layui-form"  id="tree-table" lay-size="sm"></table>
    </div>
@endsection

@section("script")
    <script>
      layui.use(['form', 'table', 'treeTable', 'treeSelect'], function(){
        var table = layui.table;
        var form = layui.form;
        table.init("table-hide");

        var treeTable = layui.treeTable;
        var treeSelect = layui.treeSelect;
        treeTable.render({
          elem: '#tree-table',
          data: {!! $navigation !!},
          icon_key: 'name',
          parent_key: "parent_id",
          end: function(e){
            form.render();
          },
          cols: [
            {
              key: 'id',
              title: 'ID',
            },
            {
              key: 'name',
              title: '名称',
              template: function(item){
                if(item.level == 0){
                  return '<span style="color:red;">'+item.name+'</span>';
                }else if(item.level == 1){
                  return '<span style="color:green;">'+item.name+'</span>';
                }else if(item.level == 2){
                  return '<span style="color:#aaa;">'+item.name+'</span>';
                }
              }
            },
            {
              key: 'parent_id',
              title: '父级ID',
            },
            {
              key: 'uri',
              title: 'URI',
            },
            {
              key: 'permission_name',
              title: '关联权限',
              template: function (item) {
                return item.permission_name ? item.permission_name : '';
              }
            },
            {
              key: 'type',
              title: '菜单类型代码',
              align: 'center',
            },
            {
              key: 'guard_name',
              title: '权限守卫',
              align: 'center',
            },
            {
              key: 'sequence',
              title: '排序',
              align: 'center',
            },
            {
              title: '操作',
              align: 'center',
              template: function(item){
                return '@if(admin_user_can("gm.nav"))<a class="layui-btn layui-btn-xs" lay-filter="edit">编辑</a>@endif' +
                    '@if(admin_user_can("gm.nav"))<a class="layui-btn layui-btn-xs layui-btn-danger" lay-filter="delete">删除</a>   @endif ';
              }
            }
          ]
        });

          openSuccess = () =>{
              treeSelect.render({
                  // 选择器
                  elem: '#parent_id',
                  // 数据
                  {{--data: {!! $navigationTree !!},--}}
                  data: {!! $navigationTree !!},
                  // 异步加载方式：get/post，默认get
                  // 占位符
                  placeholder: '请选择上级菜单',
                  // 是否开启搜索功能：true/false，默认false
                  search: false,
                  // 一些可定制的样式
                  style: {
                      folder: {
                          // enable: true
                      },
                      line: {
                          enable: true
                      }
                  },
                  // 点击回调
                  click: function(d){
                      // console.log(d);
                  },
                  // 加载完成后的回调函数
                  success: function (d) {
                      // console.log(d);
//                选中节点，根据id筛选
                      var val = $('#parent_id').val();
                      if(val !== '' && typeof a !== 'undefined') {
                          treeSelect.checkNode('parent_id', val);
                          treeSelect.refresh('parent_id');
                      }
                      // console.log($('#parent_id').val());
//                获取zTree对象，可以调用zTree方法
//                       var treeObj = treeSelect.zTree('parent_id');
//                       console.log(treeObj);
//                刷新树结构
//                       treeSelect.refresh('parent_id');
                  }
              });
          };

        $('.newNav').click(function () {
            admin.openLayerForm('{{ route("navigation.create") }}', '添加', 'POST', '700px', '500px', false, false, openSuccess);
        });

        treeTable.on('tree(delete)', function (data) {
          admin.tableDataDelete("/admin/navigation/" + data.item.id, data, true);
        });

        treeTable.on('tree(edit)', function (data) {
          admin.openLayerForm("/admin/navigation/" + data.item.id + "/edit", "编辑", 'PATCH', '750px', '600px', false, false, openSuccess);
        });
      });
    </script>
@endsection