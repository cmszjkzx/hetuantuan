<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/12/12
 * Time: 11:26
 */
?>
<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
    杨东专用
</h3>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<form action="" method="post" name="weixinuploadform" enctype="multipart/form-data" class="form-horizontal" role="form" onsubmit="">

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > access_token:</label>
        <div class="col-sm-9">
            <input type="text" name="access_token" id="access_token"  class="col-xs-10 col-sm-4" value="<?php echo $access_token; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >JSON内容：<br/>
        </label>
        <div class="col-sm-9">
            <textarea  id="data" name="data" style="width: 600px; height: 450px; text-align: left;">
            </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >上传地址：</label>
        <div class="col-sm-9">
            <input type="text" name="url" id="url" class="col-xs-10 col-sm-4"  value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >返回结果：<br/>
        </label>
        <div class="col-sm-9">
            <!--<input type="text" name="result" id="result" class="col-xs-10 col-sm-4"  value="" />-->
            <textarea  id="result" name="result" style="width: 600px; height: 450px; text-align: left;">
            </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" ></label>
        <div class="col-sm-9">
            <button type="button" class="btn btn-primary span2" name="submit" value="submit" onclick="weixin_upload();"><i class="icon-edit" ></i>上传素材</button>
        </div>
    </div>
</form>
<script>
    function weixin_upload(){
        var data = $('#data').val();
        var url = $('#url').val();
        var return_url = 'index.php?mod=site&name=weixin&do=yangdong&op=submit';
        $.post(return_url,{data:data, url:url},function(result){
            alert(result);
            $("#result").val("");
            $("#result").val(result);
        },"json");
    }
</script>

<?php  include page('footer');?>
