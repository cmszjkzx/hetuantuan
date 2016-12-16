/**
 * Created by yanru02 on 2016/12/15.
 */

showComment = function(url){
    var html = "<div class=\"modal fade bs-evaluate-modal-sm\" id='myModal' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>";
        html += "<div class=\"modal-dialog modal-sm\" style=\"margin: 5rem 2rem\">";
        html += "<div class=\"modal-content\">";
        html += "<div class=\"modal-header\" style=\"border: none;padding: 0.75rem 0.925rem 0 0.925rem\">";
        html += "<span id='myModalLabel' class=\"font-28\">";
        html += "我要说:";
        html += "</span>";
        html += "</div>";
        html += "<div class=\"modal-body\" style=\"padding: 0 0.75rem\">";
        html += "<textarea type='text' id='comment' name='comment' style=\"background-color: #f6f6f6;border: none;border-radius: 0.5rem;height: 6.75rem;padding: 5px 10px;resize: none;width: 100%\">";
        html += "</textarea>";
        html += "</div>";
        html += "<div class=\"modal-footer\" style=\"text-align: center;border: none;padding: 0.375rem\">";
        html += "<button class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 5rem;padding: 0;background-color: #FFAE01;border: none\">提交</button>";
        html += "</div>";
        html += "</div>";
        html += "</div>";
        html += "</div>";

    $("body").append(html);

    $('button').on('click',function(){
        var order_comment = $('#comment').val();
        url = url+'&comment='+order_comment;
        $.getJSON(url,function(s){
            if(s.result==0){
                tip("评论失败!",true);
            }else{
                tip("评论成功, 正在跳转!",true);
                setTimeout(function(){
                    setTimeout(function(){
                        var rement_url = 'index.php?mod=mobile&op=&name='+s.name+'&status='+s.status+'&do='+s.do;
                        location.href = rement_url;
                    }, 2000);
                }, 2000);
            }
        });
    });
};

hideComment = function () {
    $("#myModal").remove();
};

showDelete = function(url, msg){
    //debugger;
    var html = "<div class=\"modal fade bs-evaluate-modal-sm\" id='myModal' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>";
    html += "<div class=\"modal-dialog modal-sm\" style=\"margin: 5rem 2rem\">";
    html += "<div class=\"modal-content\">";
    html += "<div class=\"modal-header\" style=\"border: none;padding: 0.75rem 0.925rem 0 0.925rem\">";
    html += "</div>";
    html += "<div class=\"modal-body\" style=\"padding: 0 0.75rem\">";
    html += "<textarea type='text' id='comment' name='comment' style=\"background-color: #f6f6f6;border: none;border-radius: 0.5rem;height: 1.50rem;padding: 5px 10px;resize: none;width: 100%\">";
    html += msg;
    html += "</textarea>";
    html += "</div>";
    html += "<div class=\"modal-footer\" style=\"text-align: center;border: none;padding: 0.375rem\">";
    html += "<button id='is_yes' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">确定</button>";
    html += "<button id='is_no' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">取消</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    $("body").append(html);

    $('#is_yes').on('click',function(){
        $.getJSON(url,function(s){
            //debugger;
            if(isNaN(s.result)){
                tip("您的订单已删除, 正在刷新页面!",true);
                setTimeout(function(){
                    setTimeout(function(){
                        var rement_url = 'index.php?mod=mobile&op=&name='+s.name+'&status='+s.status+'&do='+s.do;
                        location.href = rement_url;
                    }, 800);
                }, 800);
            }else{
                tip(s.result,true);
            }
        });
    });
    $('#is_no').on('click',function(){
        //debugger;
    });
};

hideDelete = function () {
    $("#myModal").remove();
};

showCanncel = function(url, msg){
    //debugger;
    var html = "<div class=\"modal fade bs-evaluate-modal-sm\" id='myModal' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>";
    html += "<div class=\"modal-dialog modal-sm\" style=\"margin: 5rem 2rem\">";
    html += "<div class=\"modal-content\">";
    html += "<div class=\"modal-header\" style=\"border: none;padding: 0.75rem 0.925rem 0 0.925rem\">";
    html += "</div>";
    html += "<div class=\"modal-body\" style=\"padding: 0 0.75rem\">";
    html += "<textarea type='text' id='comment' name='comment' style=\"background-color: #f6f6f6;border: none;border-radius: 0.5rem;height: 1.50rem;padding: 5px 10px;resize: none;width: 100%\">";
    html += msg;
    html += "</textarea>";
    html += "</div>";
    html += "<div class=\"modal-footer\" style=\"text-align: center;border: none;padding: 0.375rem\">";
    html += "<button id='is_yes' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">确定</button>";
    html += "<button id='is_no' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">取消</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    $("body").append(html);

    $('#is_yes').on('click',function(){
        $.getJSON(url,function(s){
            //debugger;
            if(isNaN(s.result)){
                tip("您的订单已取消，正在刷新页面!",true);
                setTimeout(function(){
                    setTimeout(function(){
                        var rement_url = 'index.php?mod=mobile&op=&name='+s.name+'&status='+s.status+'&do='+s.do;
                        location.href = rement_url;
                    }, 800);
                }, 800);
            }else{
                tip(s.result,true);
            }
        });
    });
    $('#is_no').on('click',function(){
        //debugger;
    });
};

hideCannel = function () {
    $("#myModal").remove();
};

showPay = function(url, msg){
    //debugger;
    var html = "<div class=\"modal fade bs-evaluate-modal-sm\" id='myModal' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>";
    html += "<div class=\"modal-dialog modal-sm\" style=\"margin: 5rem 2rem\">";
    html += "<div class=\"modal-content\">";
    html += "<div class=\"modal-header\" style=\"border: none;padding: 0.75rem 0.925rem 0 0.925rem\">";
    html += "</div>";
    html += "<div class=\"modal-body\" style=\"padding: 0 0.75rem\">";
    html += "<textarea type='text' id='comment' name='comment' style=\"background-color: #f6f6f6;border: none;border-radius: 0.5rem;height: 1.50rem;padding: 5px 10px;resize: none;width: 100%\">";
    html += msg;
    html += "</textarea>";
    html += "</div>";
    html += "<div class=\"modal-footer\" style=\"text-align: center;border: none;padding: 0.375rem\">";
    html += "<button id='is_yes' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">确定</button>";
    html += "<button id='is_no' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">取消</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    $("body").append(html);

    $('#is_yes').on('click',function(){
        //debugger;
        $.post(url);
    });
    $('#is_no').on('click',function(){
        //debugger;
    });
};

hidePay = function () {
    $("#myModal").remove();
};

showConfirm = function(url, msg){
    //debugger;
    var html = "<div class=\"modal fade bs-evaluate-modal-sm\" id='myModal' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>";
    html += "<div class=\"modal-dialog modal-sm\" style=\"margin: 5rem 2rem\">";
    html += "<div class=\"modal-content\">";
    html += "<div class=\"modal-header\" style=\"border: none;padding: 0.75rem 0.925rem 0 0.925rem\">";
    html += "</div>";
    html += "<div class=\"modal-body\" style=\"padding: 0 0.75rem\">";
    html += "<textarea type='text' id='comment' name='comment' style=\"background-color: #f6f6f6;border: none;border-radius: 0.5rem;height: 1.50rem;padding: 5px 10px;resize: none;width: 100%\">";
    html += msg;
    html += "</textarea>";
    html += "</div>";
    html += "<div class=\"modal-footer\" style=\"text-align: center;border: none;padding: 0.375rem\">";
    html += "<button id='is_yes' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">确定</button>";
    html += "<button id='is_no' class=\"btn btn-primary font-28\" data-dismiss='modal' aria-hidden='true' style=\"line-height: 2.15rem;width: 2.5rem;padding: 0;background-color: #FFAE01;border: none\">取消</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    $("body").append(html);

    $('#is_yes').on('click',function(){
        $.getJSON(url,function(s){
            //debugger;
            if(isNaN(s.result)){
                tip("操作已成功，正在刷新页面!",true);
                setTimeout(function(){
                    setTimeout(function(){
                        var rement_url = 'index.php?mod=mobile&op=&name='+s.name+'&status='+s.status+'&do='+s.do;
                        location.href = rement_url;
                    }, 800);
                }, 800);
            }else{
                tip(s.result,true);
            }
        });
    });
    $('#is_no').on('click',function(){
        //debugger;
    });
};

hideConfirm = function () {
    $("#myModal").remove();
};

function tip(msg, autoClose) {
    var div = $("#poptip");
    var content = $("#poptip_content");
    if (div.length <= 0) {
        div = $("<div id='poptip'></div>").appendTo(document.body);
        content = $("<div id='poptip_content' style='text-align:center'>" + msg + "</div>").appendTo(document.body);
    }
    else {
        content.html(msg);
        content.show();
        div.show();
    }
    if (autoClose) {
        setTimeout(function () {
            content.fadeOut(1000);
            div.fadeOut(1000);

        }, 1000);
    }
}
function tip_close() {
    $("#poptip").fadeOut(3000);
    $("#poptip_content").fadeOut(3000);
}