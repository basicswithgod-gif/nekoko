(function($){
    "use strict";
    // Smooth scroll
    $(document).on("click","a[href^=\"#\"]",function(e){
        var target=$(this.getAttribute("href"));
        if(target.length){e.preventDefault();$("html,body").animate({scrollTop:target.offset().top-80},400);}
    });
    // Active nav link
    var path=window.location.pathname;
    $(".ast-primary-menu a, .dashboard-sidebar a").each(function(){
        if($(this).attr("href")===path||(path.indexOf($(this).attr("href"))>-1&&$(this).attr("href")!=="/")){
            $(this).addClass("active");
        }
    });
})(jQuery);
