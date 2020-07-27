(function($) {
    var Ep = function() {};
    
    //修改Bootstrap下拉菜单，让下拉菜单与trigger同宽
    Ep.prototype.initDropDown = function() {
        // adjust width of each dropdown to match content width
        $('.dropdown-default').each(function() {
            var btn = $(this).find('.dropdown-menu').siblings('.dropdown-toggle');
            var offset = 0;

            var padding = btn.innerWidth() - btn.width();
            var menuWidth = $(this).find('.dropdown-menu').outerWidth();
            if (btn.outerWidth() < menuWidth) {
                btn.width(menuWidth - offset);
                $(this).find('.dropdown-menu').width(btn.outerWidth());
            } else {
                $(this).find('.dropdown-menu').width(btn.outerWidth());
            }
            console.log(btn.outerWidth());
        });
    }

    //初始化side样式
    Ep.prototype.initSide = function() {
        //css加边框
        $(".side li.hover").each(function() {
            var oThis = $(this),
                oPrev = oThis.prev(),
                oNext = oThis.next();

            // first
            if (oPrev.length == 0) {
                oThis.find('h3').addClass("first-border-top");
            }
            //last
            if (oNext.length != 0) {
                oNext.find('h3').css("border-top", "none");
                oNext.css("border-top", "none");
            } else {
                $(".side-inner > ul").css("border-bottom", "none");
            }
        });

        $(".side li h3").each(function() {
            var oThis = $(this),
                oPartent = oThis.parent("li"),
                listView = oPartent.find('.listview');

            $(this).click(function(e) {
                if (listView.is(":hidden")) {
                    oThis.find('.ep').removeClass('ep-side-hide').addClass('ep-side-show');
                    listView.show();
                } else {
                    oThis.find('.ep').removeClass('ep-side-show').addClass('ep-side-hide');
                    listView.hide();
                }
            });
        });

        // $(".side .listview li").each(function() {
        //     $(this).click(function() {
        //         $(".side .listview li").removeClass('hover');
        //         $(this).addClass('hover');
        //     });
        // });
        

        function listview(){
            $(".side .listview li").each(function() {
                $(this).click(function() {
                    $(".side .listview li").removeClass('hover');
                    $(this).addClass('hover');
                });
            });
        }

        $(".side  li").each(function() {
           
            $(this).click(function() {
                var oThis = $(this),
                oPartent = oThis.parent("li"),
                listView = oPartent.find('.listview');

                if($(this).find('.listview').length == 0){//没有子元素
                     $(".side  li").removeClass('hover');
                     $(this).addClass('hover');
                 }else{//有子元素 
                    listview();
                 }

                 if($(this).prev("li").length == 0) {
                        oThis.find('h3').addClass("first-border-top");
                } 

                this.sideMainHeight();         
            });
        });
    }
    
    //form表单样式
    Ep.prototype.initFormGroupDefault = function() {
        $('.form-group.form-group-default').click(function() {
            $(this).find('input').focus();
        });
        $('body').on('focus', '.form-group.form-group-default :input', function() {
            $('.form-group.form-group-default').removeClass('focused');
            $(this).parents('.form-group').addClass('focused');
        });

        $('body').on('blur', '.form-group.form-group-default :input', function() {
            $(this).parents('.form-group').removeClass('focused');
            if ($(this).val()) {
                $(this).closest('.form-group').find('label').addClass('fade');
            } else {
                $(this).closest('.form-group').find('label').removeClass('fade');
            }
        });

        $('.form-group.form-group-default .checkbox, .form-group.form-group-default .radio').hover(function() {
            $(this).parents('.form-group').addClass('focused');
        }, function() {
            $(this).parents('.form-group').removeClass('focused');
        });
    }


   //Bootstrap的popover控件初始化
    Ep.prototype.initpopover = function() {
        $('[data-toggle="popover"]').popover();
    }

    //Bootstrap的tooltip控件初始化
    Ep.prototype.inittooltip = function() {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    //判断ie浏览器版本小于10，loading效果用gif图片
    Ep.prototype.progress = function() {
        var defaultVersion = 10;//ie9，loading不能转动。
        var ua = navigator.userAgent.toLowerCase();
        var isIE = ua.indexOf("msie") > -1;
        var borwserVersion;
        if (isIE) {
            borwserVersion = parseInt(ua.match(/msie ([\d.]+)/)[1]);
            if (borwserVersion < defaultVersion) {
                 $(".progress").each(function(){
                     if(!$(this).hasClass('progress-line')){
                        $(this).addClass("iefix");
                     }
                 });
               // $(".progress").addClass("iefix");
            } else { 
            }
        } else {
            //不是ie
        }
    }
    //滚动条
    Ep.prototype.scrollbar = function(){
        if($.fn.scrollbar != null){
             $('.scrollbar-inner').scrollbar();
         }
    };

    //日期控件
     
    Ep.prototype.datepicker = function(){
         if($.fn.datepicker != null){
             $('.date-picker').datepicker({
                    format: "yyyy-mm-dd",
                    language: "zh-CN",
                    autoclose: true
            });
         } 
         //http://www.bootcss.com/p/bootstrap-datetimepicker/index.htm
         if($.fn.datetimepicker != null){
             $(".date-time-picker").datetimepicker({
                format: "yyyy-mm-dd",
                autoclose: 1,
                language: "zh-CN",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0    
            });
             $(".date-time-picker-hour-min").datetimepicker({
                format: "yyyy-mm-dd hh:ii",
                autoclose: 1,
                minuteStep:1,
                language: "zh-CN" ,
                todayBtn:  1  
            });
         }
    };

    // 上传文件
     Ep.prototype.uploadfile = function(){
        $(".uploadfile").each(function(index, el) {
              var upload_input = $(this).find(".upload-input"),
                  upload_text = $(this).find(".upload-text");
              upload_input.change( function() {
                  upload_text.val(upload_input.val());
              });
          }); 
    };
    // ie fixed
    Ep.prototype.iefixed = function(){
        if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE8.0") 
        {  //还原radio跟checkbox
           $(".radio,.checkbox").addClass('iefix');
           //table隔行变色
           $(".table.table-striped tbody tr:nth-child(even)").addClass("table-gap-back");
           //select
           $("select").addClass('iefix');   
        }    
    }; 

    // 菜单跟内容等高
    Ep.prototype.sideMainHeight = function(){
         var sideDiv = $(".side"),
             mainDiv = $(".main"), 
             sideH = parseInt(sideDiv.outerHeight());
             mainH = parseInt(mainDiv.outerHeight());

         if(sideDiv.length>0 && mainDiv.length>0){
             if(sideH >= mainH) {
                mainDiv.css("min-height",sideH);
             }else if(sideH < mainH){
                sideDiv.css("min-height",mainH);
             }
         }
    }

    //初始化
    Ep.prototype.init = function() {
        // init layout
        this.initDropDown();
        this.initSide();
        this.initFormGroupDefault();
        this.initpopover();
        this.inittooltip();
        this.progress();
        this.scrollbar();
        this.datepicker(); 
        this.uploadfile();
        this.iefixed();
        this.sideMainHeight();
        // this.initSelect2Plugin();
        // this.initSelectFxPlugin();
    }

    $.Ep = new Ep();
    // $.Ep.Constructor = Ep;

})(window.jQuery);
(function($) {
    // Initialize layouts and plugins
    $.Ep.init();
})(window.jQuery);

