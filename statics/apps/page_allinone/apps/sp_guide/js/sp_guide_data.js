/**
 * Created by 曲征宇 on 2018/5/9.
 */
$(".content_left_item").click(function () {
    $(".content_left_item").removeClass("content_left_item_active");
    $(this).addClass("content_left_item_active")
});

var integral = echarts.init(document.getElementById("integral"));

integral.setOption({
    title: {},
    tooltip: {},
    grid: {
        left: '6%'
    },
    barMaxWidth: "30",
    xAxis: {
        data: ["张思德","张而非","王熙凤","王守仁","李世伟","李明科", "谢飞", "沃尔夫", "马克瑞", "程建设", "马库斯"],
        name: "地区",
        nameTextStyle : {
            color: "#f60",
            fontSize: 28
        }
    },
    yAxis: {
        splitLine:{
            show: false
        },
        name: "积分",
        nameTextStyle : {
            color: "#f60",
            fontSize: 28
        }
    },
    series: [{
        name: '积分',
        type: 'bar',
        itemStyle: {
            color: "#f60"
        },
        data: [56, 30, 36, 50, 30, 20, 56, 30, 36, 50, 30]
    }]
});

$(".detail_btn").click(function() {
    window.open("./sp_detail.html", "_self", "", true);
});

//列表数据
var data = [
    {
        title: "一级",
        list: [
            {
                title: "二级",
                list: [
                    {
                        title: "三级",
                        list: [
                            {
                                title: "四级",
                                list: [
                                    {
                                        title: "五级",
                                        list: []
                                    }]
                            }
                        ]
                    },
                    {
                        title: "三级",
                        list: []
                    }
                ]
            },
            {"title": "二级", list: []}
        ]
    },
    {
        title: "一级",
        list: []
    }
];

var old;

//循环数据，创建列表
for ( var d = 0; d < data.length; d ++ ){
    var hasChildFir ;
    var hasChildSec ;
    var hasChildTrd ;
    var hasChildFour ;
    var hasChildFive ;
    //二级列表数据
    var secList = data[d].list;

    secList.length === 0 ? hasChildFir = false : hasChildFir = true;
    //创建一级列表
    createLi(".list_box", "listbox_title-fir", data[d]["title"], hasChildFir);

    if ( secList.length >= 0 ) {
        for ( var s = 0; s < secList.length; s ++){

            //判断二级是否有子项
            secList[s]["list"].length === 0 ? hasChildSec = false : hasChildSec = true;

            //创建二级列表
            createLi(".listbox_title-fir + ul", "listbox_title-sec", secList[s]["title"], hasChildSec);

            var trdList = secList[s].list;          //二级子菜单列表

            if ( trdList.length >= 0 ) {

                for ( var t = 0; t < trdList.length; t ++){

                    //判断三级是否有子项
                    trdList[t]["list"].length === 0 ? hasChildTrd = false : hasChildTrd = true;

                    //创建三级列表
                    createLi(".listbox_title-sec + ul", "listbox_title-trd", trdList[t]["title"], hasChildTrd);

                    var fourList = trdList[t].list;

                    if ( fourList.length >= 0 ){

                        for ( var f = 0; f < fourList.length; f ++ ){

                            //判断四级是否有子项
                            fourList[f]["list"].length === 0 ? hasChildFour = false : hasChildFour = true;

                            //创建四级列表
                            createLi(".listbox_title-trd + ul", "listbox_title-four", fourList[f]["title"], hasChildFour);

                            var fiveList = fourList[f].list;

                            if ( fiveList.length >= 0 ){

                                for ( var fi = 0; fi < fiveList.length; fi ++ ){

                                    //判断五级是否有子项
                                    fiveList[fi]["list"].length === 0 ? hasChildFive = false : hasChildFive = true;

                                    //创建五级列表
                                    createLi(".listbox_title-four + ul", "listbox_title-five", fiveList[fi]["title"], hasChildFive);

                                }

                            }

                        }

                    }
                }
            }
        }
    }
}

//创建列表项
function createLi($el, $class, $title, hasChild) {       //类名、等级、数据、是否有子集
    var listWrap = hasChild ? "<ul class='listbox_wrap'></ul>" : "" ;
    var ifUnique = hasChild ? "" : " unique" ;
    var item = "<li><div class='"+ $class + ifUnique +"' onclick='clickItem(this)'>"+$title+"</div>"+ listWrap +"</li>";
    $($el).append(item);

}

//列表项的点击事件
function clickItem ( _this ) {
    var thisClass = $(_this).prop("className").split(" ");

    if (old) {
        var oldClass = $(old).prop("className").split(" ");

        //如果同级就移除同级选中样式
        if ( thisClass[0] === oldClass[0] ){

            //如果两次点击同一项，展开或收起子列表
            if ( old === _this ){

                if ( oldClass.indexOf("is-active") < 0 ){
                    ifActive(old, true);
                }else {
                    ifActive(old, false);
                }
                return;
            }

            //如果不是同一项，移除旧项的选种样式
            ifActive(old, false);
        }
        else {
            //如果不同级就点击等级其他节点选中样式
            if ( thisClass.indexOf("is-active") > 0 ){
                //如果当前项被选中就关闭
                ifActive(_this, false);
                old = _this;
                return;

            }else {
                //若未被选中，就关闭统计项，选中当前项
                $(_this).parent().siblings().find("*").removeClass("is-active");
                $(_this).parent().siblings().find(".listbox_wrap").hide();
            }
        }
    }
    ifActive(_this, true);
    old = _this;
}

//当前项是否是选中状态
function ifActive ( item, active ) {            //点击的项， true: 选中, false: 移除选中
    if ( active === true ){
        $( item ).addClass("is-active");
        if ($( item ).next()) $( item ).next().show();
    }else{
        $( item ).removeClass("is-active");
        if ($( item ).next()){
            $( item ).next().hide();
            $( item ).next().find("*").removeClass("is-active");
            $( item ).next().find(".listbox_wrap").hide();
        }
    }
}
