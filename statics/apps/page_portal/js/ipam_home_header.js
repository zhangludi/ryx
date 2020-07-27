/**
 * Created by 邵月 on 2018/3/20.
 */
/* 登录点击事件开始 */
$(".public_header li:nth-last-of-type(1)").click(function (e) {
    layer.open({
        type: 2 //此处以iframe举例
        , title: false //不显示标题栏
        , area: ["560px", "380px"]
        , skin : 'login'
        , shade: 0.8
        , id: 'LAY_layuipro1' //设定一个id，防止重复弹出
        , btnAlign: 'c'
        , moveType: 1 //拖拽模式，0或者1
        , content: './login.html'
        ,btn : ['点击登录']
        ,yes : function(){
            window.open("./ipam_home_userinfo.html", "_parent");
        }
    });
});
layui.use('layer', function() {});
/* 登录点击事件开始 */
/* header 点击事件开始 */
$(".ipam_home_public_header_nav_horizontal li").click(function () {
  $(this).css("background", "#fafafa");
  $(this).children("a").css("color", "#d10000");
});