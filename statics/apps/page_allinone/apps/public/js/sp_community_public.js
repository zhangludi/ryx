/**
 * Created by 邵月 on 2018/5/9.
 */
$(document).ready(function(){
    $(".page-title").load("../components/header.html");
});

$(".listbox_title").click(function () {
    $(this).parent(".listbox").hasClass("is-active")
            ? $(this).parent(".listbox").removeClass("is-active")
            : $(this).parent(".listbox").addClass("is-active");
});

var oldItem;
$(".listbox_item").click(function () {
    if(oldItem) $(oldItem).removeClass("is-active");
    $(this).addClass("is-active");
    oldItem = this;
});