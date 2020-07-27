var form, $;
layui.use(['form','layer'],function(){
    form = layui.form;
    var layer = parent.layer === undefined ? layui.layer : parent.layer;
    $ = layui.jquery;

    form.on("submit(changeUser)",function(data){
        var index = layer.msg('提交中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            layer.close(index);
            layer.msg("提交成功！");
        },2000);

    })

    $("select[name=aaa]").val(["4","5"]);
    form.render();
})