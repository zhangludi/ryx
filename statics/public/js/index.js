/**
 * Created by 曲征宇 on 2018/4/8.
 */

//默认第一个显示
$(".zero-level>li").eq(0).addClass("zero-bg-active");
//默认显示第二个
$(".zero-level>li").eq(0).find(".zero-level-item").addClass("zero-fcolor-active");
$(".zero-level>li").click(function() {
    $(this).siblings().removeClass("zero-bg-active");
    $(this).addClass("zero-bg-active");
});



//头部导航切换左侧菜单方法
var events = {
    menu_refresh: function(othis) { //刷新左侧菜单
        // var that = layui.view('TPL_layout');
        // that.container.siblings().remove();

        var domain = window.location.host; // 获取当前域名
        // 刷新菜单之前验证登陆信息是否存在  不存在跳转登陆页
        var check_url = 'http://'+domain+"/index.php/System/Public/get_session_info";
        $.ajax({
            url: check_url,
            type: "post",
            async:false,
            success: function(msg) {
                console.log(msg);
                if(msg == true){
                    console.log(msg);
                } else {
                    location.href=msg;
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
        var menu_li_length = $("#LAY-system-side-menu").find('li').length;
        if(menu_li_length > 0){
            var that = layui.view('TPL_layout');
            var url = othis.attr('my-url');
            that.container.attr('lay-url', url);
            that.container.siblings().remove();
            that.parse(that.container, 'refresh');
        } else {
            return true;
        }
    }
};
$('body').on('click', '*[my-event]', function() {
    var othis = $(this),
        attrEvent = othis.attr('my-event');
    events[attrEvent] && events[attrEvent].call(this, othis);
});

$('body').on('dblclick', '.layui-this',function() {
    var url = $(this).attr('lay-id');
    if (url == '' || url == null || url == 'undefined' || url == undefined) {
        url = $(this).find('a').attr('lay-href');
    }
    var target = $('.layadmin-iframe[src="' + url + '"]');
    var url = target.attr('src');
    //显示loading提示
    var loading = layer.load();
    target.attr('src', url).load(function() {
        //关闭loading提示
        layer.close(loading);
    });
});