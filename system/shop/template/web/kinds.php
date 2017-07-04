<?php
/**
 * Created by PhpStorm.
 * User: yanru
 * Date: 2016/10/26
 * Time: 14:46
 */
defined('SYSTEM_IN') or exit('Access Denied');?>

<?php  include page('header');?>

<h3 class="header smaller lighter blue">
    <?php  if('newkind' == $state) { ?>
        新增
    <?php  } else if('changekind' == $state){ ?>
        修改
    <?php  } ?>商品类别&nbsp;&nbsp;&nbsp;</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $kinds['kinds_level'];?>" />
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 商品类别编号</label>
        <div class="col-sm-9">
            <input type="text" name="kinds_level" class="col-xs-10 col-sm-3" value="<?php echo $kinds['kinds_level'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品类别名称</label>
        <div class="col-sm-9">
            <input type="text" name="kinds_name" class="col-xs-10 col-sm-3" value="<?php echo $kinds['kinds_name'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品类别图片(点击前)：</label>
        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <?php  if(!empty($kinds['kinds_thumb_before'])) { ?>
                        <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $kinds['kinds_thumb_before'];?>" alt="" onerror="$(this).remove();">
                    <?php  } ?>
                </div>
                <div>
                    <input name="kinds_thumb_before" id="kinds_thumb_before" type="file" />
                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品类别图片(点击后)：</label>
        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <?php  if(!empty($kinds['kinds_thumb_after'])) { ?>
                        <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $kinds['kinds_thumb_after'];?>" alt="" onerror="$(this).remove();">
                    <?php  } ?>
                </div>
                <div>
                    <input name="kinds_thumb_after" id="kinds_thumb_after" type="file" />
                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>
        <div class="col-sm-9">
            <input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
        </div>
    </div>
</form>

<?php  include page('footer');?>
<script>
    function deletepic(obj){
        if (confirm("确认要删除？")) {
            var $thisob=$(obj);
            var $liobj=$thisob.parent();
            var picurl=$liobj.children('input').val();
            $.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'picdelete'))?>',{ pic:picurl},function(m){
                if(m=='1') {
                    $liobj.remove();
                } else {
                    alert("删除失败");
                }
            },"html");
        }
    }
</script>