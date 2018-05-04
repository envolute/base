/*
Plugin Name: base-notify => iao alert
		Key: iao-alert
	Version: 1.0.5
	 Author: Prashant Kapoor
	Website: http://www.itsallonly.com
	   Repo: https://github.com/Itsallonly/iao-alert
	 Issues: https://github.com/Itsallonly/iao-alert/issues
	  Files: iao-alert.jquery.js, iao-alert.css
 Dependency: Jquery
*/
(function( $ ) {
    $.fn.baseNotify = $.baseNotify = function(arr) {
        var opt = $.extend( {
            msg: "",
            type: "success",
            autoHide: true,
            alertTime: "15000",
            fadeTime: "500",
            closeButton: true,
            closeOnClick: false,
            fadeOnHover: true,
            position: 'top-right'
        }, arr );
        var timeStamp = $.now();
        var ext = {
            chkPosition : (opt.position == 'bottom-right')?'bottom-right':((opt.position == 'bottom-left')?'bottom-left':(opt.position == 'top-left')?'top-left':'top-right'),
            closeOption : (opt.closeButton)?'<base-notify-close></base-notify-close>':'<style>#notify'+timeStamp+':before,#notify'+timeStamp+':after{display:none}</style>',
            chkMsg : (opt.msg.toString().indexOf(" "))?'white-space:pre-wrap;word-wrap:break-word;':''
        };
        if($('base-notify-box').length==0)
        $('body').append('<base-notify-box position="top-left"><base-notify-start></base-notify-start></base-notify-box><base-notify-box position="top-right"><base-notify-start></base-notify-start></base-notify-box><base-notify-box position="bottom-right"><base-notify-start></base-notify-start></base-notify-box><base-notify-box position="bottom-left"><base-notify-start></base-notify-start></base-notify-box>');
        var baseNotify = $('<base-notify id="notify'+timeStamp+'" close-on-click='+opt.closeOnClick+' fade-on-hover='+opt.fadeOnHover+' class="alert alert-'+opt.type+'" style="'+ext.chkMsg+'">'+opt.msg+ext.closeOption+'</base-notify>')
        .insertAfter('base-notify-box[position="'+ext.chkPosition+'"] > base-notify-start');
        if(opt.autoHide)
        setTimeout(function(){
            baseNotify.fadeOut(opt.fadeTime, function() {
                $(this).remove();
            });
        }, opt.alertTime);
        $('base-notify[close-on-click="true"]').click(function() {
            $(this).fadeOut(opt.fadeTime, function() {
                $(this).remove();
            });
        });
        $('base-notify > base-notify-close').click(function() {
            $(this).parent()
            .fadeOut(opt.fadeTime, function() {
                $(this).remove();
            });
        });
        return this;
    };
}( jQuery ));
