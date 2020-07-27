/**
 * Created by 邵月 on 2018/6/2.
 */
//注册主题
//(function resTheme( theme ){
//    $.get('../../statics/apps/sp_data/theme/sp_theme.json', function (themeJSON) {
//        for( var item in themeJSON ){
//            $.get(themeJSON[item], function (theme) {
//                echarts.registerTheme(item, theme);
//            });
//        }
//    });
//})();

//创建导航栏
function createNav() {}
var i = 1;
var showLength = Math.floor ($(".data-list_wrap").height() / $(".o-list_item").height());
var itemLength = $(".o-list_item").length;

function scrollTop() {
    $(".o-list_wrap").css("transform", "translateY(" + (- ($(".o-list_item").height() + 1) * i) + "px)");
    i < itemLength - showLength ? i ++ : i = 0 ;
}

//右上列表滚动
setInterval(function(){
    scrollTop();
}, 2000);

//绘制echarts
var themePro = "shine";
function drawCharts(el, data){
    var chartData = [];
    var chartStyle = {};
    $.getJSON(data, function (dataJson) {
        console.log(dataJson);
        $.getJSON('../../uploads/sp_bigdata/theme/sp_theme.json', function (themes) {
            $.getJSON(themes[themePro], function (theme) {
                echarts.registerTheme(themePro, theme);
                var myChart = echarts.init(el, 'light');

                //设置各类型图的样式及数据
                for (var i = 0; i < dataJson['series'].length; i ++){
                    switch (dataJson["type"][i]){
                        case "bar":
                            chartStyle = {
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
                            };
                            break;
                        case "line":
                            chartStyle = {
                                "lineStyle": {
                                    //"color": "#ff2e12"
                                },
                                "smooth": true,
                                "animationDelay":  function (idx) {
                                    return idx * 10 + 100;
                                }
                            };
                            break;
                        case "pie":
                            chartStyle = {
                                "center": [
                                    "42%",
                                    "60%"
                                ],
                                radius: [0, '55%']
                            };
                            break;
                        case "radar":
                            chartStyle = {
                                itemStyle: {
                                    color: "#fd0"
                                },
                                areaStyle: {
                                    color: "#fcc000"
                                }
                            };
                            break;
                    }
                    var item = {
                        name: dataJson["series"][i]["name"],
                        type: dataJson["type"][i],
                        data: dataJson["series"][i]["data"]
                    };
                    for (var style in chartStyle) {
                        item[style] = chartStyle[style];
                    }
                    chartData.push(item)
                }
                myChart.setOption({
                    title: {
                        text: dataJson.hasOwnProperty('title') ? dataJson['title'] : "",
                        textStyle: {
                            color: "#fff",
                            fontWeight: '400',
                            fontSize: 16
                        },
                        left: '6%',
                        top: '6%'
                    },
                    grid: {
                        bottom: '20%',
                        left: '13%',
                        width: '68%',
                        height: '42%'
                    },
                    radar: {
                        name: {
                            textStyle: {
                                color: '#fff',
                                backgroundColor: '#999',
                                borderRadius: 3,
                                padding: [3, 5]
                            }
                        },
                        center: ['50%', '60%'],
                        radius: '60%',
                        indicator: dataJson.hasOwnProperty("radar") ? dataJson["radar"]["indicator"] : ""
                    },
                    tooltip: {
                        show: true,             //提示框组件是否显示
                        formatter: function (dataJson,ticket,callback) {
                            if(dataJson["name"] == undefined){
                                var res = dataJson[0]["name"]+   '<br/>'+ dataJson[0]["seriesName"] +' : ' + dataJson[0]["data"];
                            } else {
                                var res = dataJson["name"]+   '<br/>'+ dataJson["seriesName"] +' : ' + dataJson["data"];
                            }
                            return res;
                        }
                    },
                    xAxis: dataJson.hasOwnProperty('axis') && dataJson['axis'].hasOwnProperty('xAxis') ?
                    {
                        data : dataJson['axis']['xAxis'].hasOwnProperty('data') ? dataJson['axis']['xAxis']['data']: "",
                        name: dataJson['axis']['xAxis'].hasOwnProperty('name') ? "(" + dataJson['axis']['xAxis']['name'] + ")" : "",
                        axisLabel: {
                            color: "#fff",           //坐标轴标识字体颜色
                            interval:0,
                            rotate:20
                            // formatter : function(params){
                            //     var newParamsName = "";// 最终拼接成的字符串
                            //     var paramsNameNumber = params.length;// 实际标签的个数
                            //     var provideNumber = 2;// 每行能显示的字的个数
                            //     var rowNumber = Math.ceil(paramsNameNumber / provideNumber);// 换行的话，需要显示几行，向上取整
                            //     /**
                            //      * 判断标签的个数是否大于规定的个数， 如果大于，则进行换行处理 如果不大于，即等于或小于，就返回原标签
                            //      */
                            //     // 条件等同于rowNumber>16
                            //     if (paramsNameNumber > provideNumber) {
                            //         /** 循环每一行,p表示行 */
                            //         for (var p = 0; p < rowNumber; p++) {
                            //             var tempStr = "";// 表示每一次截取的字符串
                            //             var start = p * provideNumber;// 开始截取的位置
                            //             var end = start + provideNumber;// 结束截取的位置
                            //             // 此处特殊处理最后一行的索引值
                            //             if (p == rowNumber - 1) {
                            //                 // 最后一次不换行
                            //                 tempStr = params.substring(start, paramsNameNumber);
                            //             } else {
                            //                 // 每一次拼接字符串并换行
                            //                 tempStr = params.substring(start, end) + "\n";
                            //             }
                            //             newParamsName += tempStr;// 最终拼成的字符串
                            //         }

                            //     } else {
                            //         // 将旧标签的值赋给新标签
                            //         newParamsName = params;
                            //     }
                            //     //将最终的字符串返回
                            //     return newParamsName
                            // }
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
                        nameTextStyle: {
                            color: '#ffd400'
                        },
                        nameLocation: 'end',
                        position: "bottom"
                    } : {show: false},
                    yAxis: dataJson.hasOwnProperty('axis') && dataJson['axis'].hasOwnProperty('yAxis') ?
                    {
                        name: dataJson['axis']['yAxis'].hasOwnProperty('name') ? '(' + dataJson['axis']['yAxis']['name'] + ')' : "",
                        data : dataJson['axis']['yAxis'].hasOwnProperty('data') ? dataJson['axis']['yAxis']['data']: "",
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
                        nameLocation: 'end',
                        nameTextStyle: {
                            color: '#ffd400'
                        },
                        splitLine: {
                            show: false                 //纵轴的分割线是否展示
                        }
                    } : {show: false},
                    legend: {
                        type: 'scroll',
                        top: 10,
                        right: '8%',
                        padding: 8,
                        itemHeight: 12,
                        itemWidth: 20,
                        itemGap: 8,
                        orient: 'vertical',
                        textStyle: {
                            color: "#fff"
                        }
                    },
                    series: chartData
                });
            });
        });
    });
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

