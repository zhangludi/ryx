var AppInbox = function () {

    var content = $('.inbox-content');
    var listListing = '';

    var loadInbox = function (el, name) {
         var title = el.attr('data-title');
        listListing = name;
        $('.inbox-nav > li.active').removeClass('active');
        el.closest('li').addClass('active');
        $('.inbox-header > h1').text(title);
     }
// // 定义邮件列表的页面
//     var loadInbox = function (el, name) {
//         var url = 'p5_inbox_list.html';
//         var title = el.attr('data-title');
//         listListing = name;

//         App.blockUI({
//             target: content,
//             overlayColor: 'none',
//             animate: true
//         });

//         toggleButton(el);

//         $.ajax({
//             type: "GET",
//             cache: false,
//             url: url,
//             dataType: "html",
//             success: function(res) 
//             {
//                 toggleButton(el);

//                 App.unblockUI('.inbox-content');

//                 $('.inbox-nav > li.active').removeClass('active');
//                 el.closest('li').addClass('active');
//                 $('.inbox-header > h1').text(title);

//                 content.html(res);

//                 if (Layout.fixContentHeight) {
//                     Layout.fixContentHeight();
//                 }

//                 App.initUniform();
//             },
//             error: function(xhr, ajaxOptions, thrownError)
//             {
//                 toggleButton(el);
//             },
//             async: false
//         });

//         // handle group checkbox:
//         jQuery('body').on('change', '.mail-group-checkbox', function () {
//             var set = jQuery('.mail-checkbox');
//             var checked = jQuery(this).is(":checked");
//             jQuery(set).each(function () {
//                 $(this).attr("checked", checked);
//             });
//             jQuery.uniform.update(set);
//         });
//     }
// 定义邮件列表的页面结束
// 定义邮件详情页面
//     var loadMessage = function (el, name, resetMenu) {
//         var url = 'p5_inbox_view.html';

//         App.blockUI({
//             target: content,
//             overlayColor: 'none',
//             animate: true
//         });

//         toggleButton(el);

//         var message_id = el.parent('tr').attr("data-messageid");  
        
//         $.ajax({
//             type: "GET",
//             cache: false,
//             url: url,
//             dataType: "html",
//             data: {'message_id': message_id},
//             success: function(res) 
//             {
//                 App.unblockUI(content);

//                 toggleButton(el);

//                 if (resetMenu) {
//                     $('.inbox-nav > li.active').removeClass('active');
//                 }
//                 // $('.inbox-header > h1').text('');
//                  $('.inbox-header > h1').text('');

//                 // $('.inbox-header ').hide();
//                 content.html(res);
//                 Layout.fixContentHeight();
//                 App.initUniform();
//             },
//             error: function(xhr, ajaxOptions, thrownError)
//             {
//                 toggleButton(el);
//             },
//             async: false
//         });
//     }
// // 定义邮件详情页面js
// // 定义写邮件页面
//     var loadCompose = function (el) {
//         var url = 'p5_inbox_compose.html';

//         App.blockUI({
//             target: content,
//             overlayColor: 'none',
//             animate: true
//         });

//         toggleButton(el);

//     //     // load the form via ajax
//         $.ajax({
//             type: "GET",
//             cache: false,
//             url: url,
//             dataType: "html",
//             success: function(res) 
//             {
//                 App.unblockUI(content);
//                 toggleButton(el);

//                 $('.inbox-nav > li.active').removeClass('active');
//                 $('.inbox-header > h1').text('写邮件');

//                 content.html(res);

//                 initFileupload();
//                 initWysihtml5();

//                 $('.inbox-wysihtml5').focus();
//                 Layout.fixContentHeight();
//                 App.initUniform();
//             },
//             error: function(xhr, ajaxOptions, thrownError)
//             {
//                 toggleButton(el);
//             },
//             async: false
//         });
//     }
// 定义写邮件页面结束
// 定义回复邮件页面
    // var loadReply = function (el) {
    //     var messageid = $(el).attr("data-messageid");
    //     var url = 'p5_inbox_reply.html';
        
    //     App.blockUI({
    //         target: content,
    //         overlayColor: 'none',
    //         animate: true
    //     });

    //     toggleButton(el);

    // //     // load the form via ajax
    //     $.ajax({
    //         type: "GET",
    //         cache: false,
    //         url: url,
    //         dataType: "html",
    //         success: function(res) 
    //         {
    //             App.unblockUI(content);
    //             toggleButton(el);

    //             $('.inbox-nav > li.active').removeClass('active');
    //             $('.inbox-header > h1').text('回复邮件');

    //             content.html(res);
    //             $('[name="message"]').val($('#reply_email_content_body').html());

    //             handleCCInput(); // init "CC" input field

    //             initFileupload();
    //             initWysihtml5();
    //             Layout.fixContentHeight();
    //             App.initUniform();
    //         },
    //         error: function(xhr, ajaxOptions, thrownError)
    //         {
    //             toggleButton(el);
    //         },
    //         async: false
    //     });
    // }
// 定义回复邮件页面结束
// 定义写邮件的输入表单，抄送
    var handleCCInput = function () {
        var the = $('.inbox-compose .mail-to .inbox-cc');
        var input = $('.inbox-compose .input-cc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
        });
    }
// 定义写邮件的输入表单，抄送结束
// 定义写邮件的表单，暗抄送
    var handleBCCInput = function () {

        var the = $('.inbox-compose .mail-to .inbox-bcc');
        var input = $('.inbox-compose .input-bcc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
        });
    }
// 定义写邮件的表单，暗抄送 结束  

     var toggleButton = function(el) {
         if (typeof el == 'undefined') {
             return;
         }
         if (el.attr("disabled")) {
             el.attr("disabled", false);
         } else {
             el.attr("disabled", true);
         }
     }

    return {
        //main function to initiate the module
        init: function () {

            // handle compose btn click
            $('.inbox').on('click', '.compose-btn', function () {
                loadCompose($(this));
            });

            // handle discard btn
            $('.inbox').on('click', '.inbox-discard-btn', function(e) {
                e.preventDefault();
                loadInbox($(this), listListing);
            });

            // handle reply and forward button click
            $('.inbox').on('click', '.reply-btn', function () {
                loadReply($(this));
            });

            // handle view message
            $('.inbox').on('click', '.view-message', function () {
                loadMessage($(this));
            });

            // handle inbox listing
            $('.inbox-nav > li > a').click(function () {
                loadInbox($(this), 'inbox');
            });

            //handle compose/reply cc input toggle
            $('.inbox-content').on('click', '.mail-to .inbox-cc', function () {
                handleCCInput();
            });

            //handle compose/reply bcc input toggle
            $('.inbox-content').on('click', '.mail-to .inbox-bcc', function () {
                handleBCCInput();
            });

            //handle loading content based on URL parameter
            if (App.getURLParameter("a") === "view") {
                loadMessage();
            } else if (App.getURLParameter("a") === "compose") {
                loadCompose();
            } else {
               $('.inbox-nav > li:first > a').click();
            }

        }

    };

}();

jQuery(document).ready(function() {
    AppInbox.init();
});
