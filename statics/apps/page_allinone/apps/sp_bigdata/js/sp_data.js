/**
 * Created by 邵月 on 2018/6/2.
 */

//(function resTheme( theme ){
//    $.get('../../statics/apps/sp_data/theme/sp_theme.json', function (themeJSON) {
//        for( var item in themeJSON ){
//            $.get(themeJSON[item], function (theme) {
//                echarts.registerTheme(item, theme);
//            });
//        }
//    });
//})();

//绘制echarts
function drawCharts(el, item){
    $.get(item).done(function(data){

        $.get('../../static/demo/theme/sp_theme.json', function (themeJSON) {

            $.get(themeJSON[data['theme']], function (theme) {
                var themeName = data['theme'];
                echarts.registerTheme(themeName, theme);

                var myChart = echarts.init(el, themeName);

                myChart.setOption({
                    title: {
                        text: data['title']['text'],
                        textStyle: {
                            color: "#fff",
                            fontWeight: '400',
                            fontSize: 16
                        },
                        left: '6%',
                        top: '6%'
                    },
                    tooltip: {
                        show: true              //提示框组件是否显示
                    },
                    xAxis: !data['xAxis'] ? {show: false} : {
                        axisLabel: {
                            color: "#fff"           //坐标轴标识字体颜色
                        },
                        axisPointer: {
                            //显示坐标信息
                            show: true,
                            type: 'line',
                            snap: true,
                            triggerTooltip: true,           //x轴是否触发显示提示框组件
                            label: {
                                show: false
                            }
                        },
                        axisTick: {show: false},
                        minInterval: 1,
                        boundaryGap: ['10%', '10%'],
                        data : data['xAxis']['data'],
                        name: data['xAxis']['name'] ? '(' + data['xAxis']['name'] + ')' : '',
                        nameTextStyle: {
                            color: '#ffd400'
                        },
                        nameLocation: 'end',
                        position: "bottom"
                    },
                    yAxis: !data['xAxis'] ? {show: false} : {
                        axisLabel: {
                            color: "#fff"           //坐标轴标识字体颜色
                        },
                        axisPointer: {
                            show: true,                 //展示轴的坐标
                            type: 'line',                //纵轴的标示线
                            triggerTooltip: false,      //纵轴的提示框组件不展示
                            label: {
                                show: false             //纵轴刻度的标识
                            },
                            lineStyle: {
                                type: 'dashed'          //纵轴指示器的直线类型
                            }
                        },
                        name: data['yAxis']['name'] ? '(' + data['yAxis']['name'] + ')' : '',
                        nameLocation: 'end',
                        nameTextStyle: {
                            color: '#ffd400'

                        },
                        splitLine: {
                            show: false                 //纵轴的分割线是否展示
                        }
                    },
                    legend: !data['legend'] ? {show:false} :{
                        type: 'scroll',
                        top: 10,
                        right: 10,
                        padding: 8,
                        itemHeight: 12,
                        itemWidth: 20,
                        itemGap: 8,
                        orient: 'vertical',
                        textStyle: {
                            color: "#ff2e12"
                        }
                    },
                    animationEasing: 'elasticOut',
                    animationDelayUpdate: function (idx) {
                        return idx * 5;
                    }
                });

                for (var i = 0; i < data['series'].length; i ++){
                    switch (data['series'][i]['type']){
                        case "bar":
                            myChart.setOption({
                                series : data['series']
                            });
                            myChart.setOption({
                                grid: {
                                    bottom: '20%',
                                    left: '13%',
                                    width: '71%',
                                    height: '40%'
                                },
                                series: {
                                    "itemStyle": {
                                        "normal": {
                                            "color": new echarts.graphic.LinearGradient(
                                                    0, 0, 0, 1,
                                                    [
                                                        {"offset": 0, "color": "#33eeff"},
                                                        {"offset": 1, "color": "transparent"}
                                                    ]
                                            )
                                        }
                                    }
                                }
                            });
                            break;
                        case "line":
                            myChart.setOption({
                                series : data['series']
                            });
                            myChart.setOption({
                                grid: {
                                    bottom: '20%',
                                    left: '13%',
                                    width: '80%',
                                    height: '42%'
                                },
                                series: {
                                    "lineStyle": {
                                        "color": "#ff2e12"
                                    },
                                    "smooth": true,
                                    "animationEasing": "bounceOut",
                                    "animationDelay":  function (idx) {
                                        return idx * 10 + 100;
                                    }
                                }
                            });
                            break;
                        case "pie":
                            myChart.setOption({
                                series : data['series']
                            });
                            myChart.setOption({
                                grid: {
                                    bottom: '20%',
                                    left: '13%',
                                    width: '71%',
                                    height: '38%'
                                },
                                tooltip: {
                                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                                },
                                series: {
                                    "center": [
                                        "42%",
                                        "60%"
                                    ],
                                    radius: data['series'][i]['radius'] === "hollow" ? ["35%", "50%"] : [0, '55%']
                                }
                            });
                            break;
                        case "radar":
                            myChart.setOption({
                                radar: {
                                    name: {
                                        textStyle: {
                                            color: '#fff',
                                            backgroundColor: '#999',
                                            borderRadius: 3,
                                            padding: [3, 5]
                                        }
                                    },
                                    indicator: data['radar']['indicator'],
                                    center: ['50%', '60%'],
                                    radius: '60%'
                                },
                                series : data['series']
                            });
                            myChart.setOption({
                                series: [{
                                    itemStyle: {
                                        color: "#fd0"
                                    },
                                    areaStyle: {
                                        color: "#fcc000"
                                    }}]
                            });
                            break;
                    }

                }

            });

        });

    });
}

function ajax(url, successFn, failFn){
    if (window.XMLHttpRequest) {
        var xhr = new XMLHttpRequest();//非IE6
    }else{
        var xhr = new ActiveXObject("Microsoft.XMLHTTP");//IE6
    }
    xhr.open('get', url, true);
    xhr.send();
    xhr.onreadystatechange = function (){
        if (xhr.readyState == 4){
            if((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304){
                successFn(xhr.responseText);
            }else {
                failFn(xhr.responseText)
            }
        }
    };
}

//窗口改变大小执行改变rem单位比例事件
$(window).resize(function (){// 绑定到窗口的这个事件中
    changeFs();
});

//窗口加载执行改变rem单位比例事件
$(window).load(function (){// 绑定到窗口的这个事件中
    changeFs();
});

//改变依据窗口大小改变html的fontsize
function changeFs(){
    var whdef = 100/1920;// 表示1920的设计图,使用100PX的默认值
    var wH = window.innerHeight;// 当前窗口的高度
    var wW = window.innerWidth;// 当前窗口的宽度
    var rem = wW * whdef;// 以默认比例值乘以当前窗口宽度,得到该宽度下的相应FONT-SIZE值
    $('html').css('font-size', rem + "px");
}

var i = 1;
var showLength = Math.floor ($(".data-list_wrap").height() / $(".o-list_item").height());
var itemLength = $(".o-list_item").length;

function scrollTop() {
    $(".o-list_wrap").css("transform", "translateY(" + - $(".o-list_item").height() * i + "px)");
    i < itemLength - showLength ? i ++ : i = 0 ;
}

//右上列表滚动
setInterval(function(){
    scrollTop();
}, 2000);
