<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/12/11
 * Time: 10:50
 */
?>
<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
    微信上传素材
</h3>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<form action="" method="post" name="weixinuploadform" enctype="multipart/form-data" class="form-horizontal" role="form" onsubmit="">
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 是否为永久素材：</label>
        <div class="col-sm-9">
            <input type="radio" name="status" value="1" id="status_yese" /> 是  &nbsp;&nbsp;
            <input type="radio" name="status" value="0" id="status_no"/> 否
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 类别：</label>
        <div class="col-sm-9">
            <select  style="margin-right:15px;" id="type" name="type" onchange="selecttype(this.options[this.selectedIndex].value)"  autocomplete="off">
                <option value="0">请选择素材类型</option>
                <option value="image">image</option>
                <option value="voice">voice</option>
                <option value="video">video</option>
                <option value="thumb">thumb</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 标题：</label>
        <div class="col-sm-9">
            <input type="text" name="title" id="title" maxlength="100" class="span7"  value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 作者：</label>
        <div class="col-sm-9">
            <input type="text" name="author" id="author" maxlength="100" class="span7"  value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 素材ID:</label>
        <div class="col-sm-9">
            <input type="text" name="thumb_media_id" id="thumb_media_id" class="col-xs-10 col-sm-4" value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > access_token:</label>
        <div class="col-sm-9">
            <input type="text" name="access_token" id="access_token"  class="col-xs-10 col-sm-4" value="<?php echo $access_token; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 是否为封面：</label>
        <div class="col-sm-9">
            <input type="radio" name="show_cover_pic" value="1" id="show_cover_pic_yes" /> 是  &nbsp;&nbsp;
            <input type="radio" name="show_cover_pic" value="0" id="show_cover_pic_no"/> 否
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >素材上传：<br/>（建议640*640）</label>
        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <img src="" alt="" onerror="$(this).remove();">
                </div>
                <div>
                    <input name="material" id="material" type="file" />
                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >摘要：</label>
        <div class="col-sm-9">
            <input type="text" name="description" id="description" maxlength="100" class="col-xs-10 col-sm-4"  value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >图文信息具体内容：<br/>
            <span style="font-size:12px">(建议图片宽不超过640px)</span>
        </label>
        <div class="col-sm-9">
            <textarea  id="content" name="content" style="width: 400px; height: 300px; text-align: left;">
            </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >原文地址：<br/>（填写url）</label>
        <div class="col-sm-9">
            <input type="text" name="content_source_url" id="content_source_url" maxlength="100" class="col-xs-10 col-sm-4"  value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" ></label>
        <div class="col-sm-9">
            <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i>上传素材</button>
        </div>
    </div>
</form>

<?php  include page('footer');?>