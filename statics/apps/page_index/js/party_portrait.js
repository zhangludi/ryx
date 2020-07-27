/**————————————————————————————————适配——————————————————————————————————————**/
(function (doc, win) {
  var docEl = doc.documentElement,
    // 手机旋转事件,大部分手机浏览器都支持 onorientationchange 如果不支持，可以使用原始的 resize
      resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
      recalc = function () {
        //clientWidth: 获取对象可见内容的宽度，不包括滚动条，不包括边框
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        docEl.style.fontSize = 100*(clientWidth / 1920) + 'px';
      };
 
  recalc();
  //判断是否支持监听事件 ，不支持则停止
  if (!doc.addEventListener) return;
  //注册翻转事件
  win.addEventListener(resizeEvt, recalc, false);
 
})(document, window);
/**————————————————————————————————党员画像 中间——————————————————————————————————————**/
var dom = document.getElementById("container_c");
var myChart = echarts.init(dom);
var app = {};
option = null;
option = {
    tooltip: {},
    animationDurationUpdate: function(idx) {
        // 越往后的数据延迟越大
        return idx * 100;
    },
    animationEasingUpdate: 'bounceIn',
    color: ['#fff', '#fff', '#fff'],
    series: [{
        type: 'graph',
        layout: 'force',
        force: {
            repulsion: 1000,
            edgeLength: 200
        },
        roam: false,
        label: {
                normal: {
                    show: true,
					formatter:  function( data ) {
						if(data.name=="优秀党员"){
							 return '{x3|' + data.name.substr(0,2) + '}{x3|\n' + data.name.substr(2) + '}';
						}else if(data.name=="年轻党员"){
							return '{x3|' + data.name.substr(0,2) + '}{x3|\n' + data.name.substr(2) + '}';
						}else if(data.name=="本科"){
							return '{x3|' + data.name + '}';
						}else{
							 return '{x1|' + data.name.substr(0,2) + '}{x2|\n' + data.name.substr(2) + '}';
						}
						
						 
					},
					rich: {
						x1: {
							fontSize: 20,
							color: '#fff',
							fontStyle: '600',
							align:'center',
							lineHeight: 26,
							
						},
						x2: {
							fontSize: 14,
							color: '#fff',
							fontStyle: '300',
							align:'center',
							 lineHeight: 20,
							
						},
						x3: {							
							fontSize: 16,
							color: '#fff',
							fontStyle: '600',
							align:'center',
							lineHeight: 22,
						},
					},
                    position: 'inside',
                    //formatter: '{b}',
                    fontSize: 14,
                   // fontStyle: '600',
                }
            }
            ,
        data: [{
            "name": "优秀党员",
            "value": 10000,
            x: 0,
            y: 0,
            "symbolSize": 80,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#ffaf48",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#ffaf48",
                    "color": "#ffaf48"
                }
            }
        }, {
            "name": "25元党费",
			"name1": "党费",
            "value": 6181,
            x: 0,
            y: 0,
            "symbolSize": 80,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#ff4b2c",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#ff4b2c",
                    "color": "#ff4b2c",
                }
            },
            
        }, {
            "name": "23积分",
            "value": 4386,
            x: 0,
            y: 0,
            "symbolSize": 60,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#37b1f3",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#37b1f3",
                    "color": "#37b1f3"
                }
            }
        }, {
            "name": "13篇笔记",
            "value": 4055,
            "symbolSize": 63,
            x: 0,
            y: 0,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#ffaf48",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#ffaf48",
                    "color": "#ffaf48"
                }
            }
        }, {
            "name": "年轻党员",
            "value": 2467,
            x: 0,
            y: 0,
            "symbolSize": 70,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#ffaf48",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#ffaf48",
                    "color": "#ffaf48"
                }
            }
        }, {
            "name": "16年党龄",
            "value": 2244,
            x: 0,
            y: 0,
            "symbolSize": 70,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#ff4b2c",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#ff4b2c",
                    "color": "#ff4b2c"
                }
            }
        },
        {
            "name": "本科",
            "value": 2244,
            x: 0,
            y: 0,
            "symbolSize": 53,
            "draggable": true,
            "itemStyle": {
                "normal": {
                    "borderColor": "#37b1f3",
                    "borderWidth": 0,
                    "shadowBlur": 5,
                    "shadowColor": "#37b1f3",
                    "color": "#37b1f3"
                }
            }
        },{
            "name": " ",
            "value": 2244,
			"symbol":'image://http://pysydj.sp11.cn/statics/public/images/center.png',
            x: 600,
            y: 50,
            "symbolSize": [150,160],//宽 高
            "draggable": true,
            "itemStyle": {
                "normal": {
                   // "borderColor": "#ff4b2c",
                   // "borderWidth": 0,
                   // "shadowBlur": 10,
                   // "shadowColor": "#ff4b2c",
                   // "color": "#ff4b2c"
                }
            }
        }],
        links:[{
            "source": "本科",
            "target": " "
        },
        {
            "source": "23积分",
            "target": " "
        },
        {
            "source": "16年党龄",
            "target": " "
        },
        {
            "source": "优秀党员",
            "target": " "
        },
        {
            "source": "年轻党员",
            "target": " "
        },{
            "source": "25元党费",
            "target": " "
        },{
            "source": "13篇笔记",
            "target": " "
        }]
    }]
}
if (option && typeof option === "object") {
    myChart.setOption(option, true);
     window.onresize = function () {
        myChart.resize();
    }
}
