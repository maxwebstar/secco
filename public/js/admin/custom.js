
$(document).ready(function() {

    setTimeout(function() {
        $(".alert-auto-close").fadeOut('slow');
    }, 15000);

});

var jsAlertHtml = {

    type : "",
    title : "",
    message : "",
    hide : "",
    class_hide : {
        0 : '',
        1 : 'alert-auto-close',
    },
    class_icon : {
        danger : '<i class="icon fa fa-ban"></i>',
        info : '<i class="icon fa fa-info"></i>',
        warning : '<i class="icon fa fa-warning"></i>',
        success : '<i class="icon fa fa-check"></i>',
    },

    set : function (type, title, message, hide){

        this.type = type;
        this.title = title;
        this.message = message;
        this.hide = hide;
    },
    get : function (){

        var html = '<div class="alert alert-' + this.type + ' alert-dismissible ' + this.class_hide[this.hide] + ' ">' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
            '<h4>' + this.class_icon[this.type] + this.title + '</h4>' + this.message + '</div>';

        return html;

    }
};

var jsNotValidHtml = {

    type : "",
    message : "",
    hide : "",
    class_hide : {
        0 : '',
        1 : 'alert-auto-close',
    },

    set : function (type, message, hide){

        this.type = type;
        this.message = message;
        this.hide = hide;
    },
    get : function (){

        var html = '<div class="alert alert-' + this.type + ' alert-dismissible ' + this.class_hide[this.hide] + ' ">' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + this.message + '</div>';

        return html;

    }
};
