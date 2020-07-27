//轮播图效果
$(function(){
    //默认状态下左右滚动
    $("#s1").xslider({
        unitdisplayed:4,//可视的单位个数   必需项;
        movelength:1,//要移动的单位个数    必需项;
        unitlen:null,//移动的单位宽或高度     默认查找li的尺寸;
        autoscroll:null//自动移动间隔时间     默认null不自动移动;
    });
       
})