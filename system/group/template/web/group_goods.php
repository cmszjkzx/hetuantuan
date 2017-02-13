<?php defined('SYSTEM_IN') or exit('Access Denied');?>

<?php  include page('header');?>
<h3 class="header smaller lighter blue">
    <?php  if(!empty($item['id'])) { ?>
        编辑
    <?php  } else{ ?>
        新增
    <?php  } ?>商品</h3>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script>
<form action="" method="post" name="theForm" enctype="multipart/form-data" class="form-horizontal" role="form" onsubmit="return fillform()">
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >广告图：</label>
        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <?php  if(!empty($head_thumb)) { ?>
                        <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $head_thumb['thumb'];?>" alt="" onerror="$(this).remove();">
                    <?php  } ?>
                </div>
                <div>
                    <input name="head_thumb" id="head_thumb" type="file" />
                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 商品名称：</label>
        <div class="col-sm-9">
            <input type="text" name="goodname" id="goodname" maxlength="100" class="span7"  value="<?php  echo $item['goodname'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 是否参团：</label>
        <div class="col-sm-9">
            <input type="radio" name="isshow" value="1" id="isshow1" <?php  if($item['show'] == 1) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
            <input type="radio" name="isshow" value="0" id="isshow2"  <?php  if($item['show'] == 0) { ?>checked="true"<?php  } ?> /> 否
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >参团截止时间：</label>
        <div class="col-sm-9">
            <input type="text" readonly="readonly" name="limittime" id="datepicker_limittime" value="<?php  echo date('Y-m-d H:i',empty($item['limittime'])?time():$item['limittime']);?>" /></div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 是否上架：</label>
        <div class="col-sm-9">
            <input type="radio" name="isgroup" value="1" id="isgroup1" <?php  if($item['group'] == 1) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
            <input type="radio" name="isgroup" value="0" id="isgroup2"  <?php  if($item['group'] == 0) { ?>checked="true"<?php  } ?> /> 否
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 上架货号：</label>
        <div class="col-sm-9">
            <input type="text" name="goodsn"  value="<?php  echo $item['goodsn'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 获赞数量：</label>
        <div class="col-sm-9">
            <input type="text" name="praise"  value="<?php  echo $item['praise'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品参团图：</label>
        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <?php  if(!empty($item['thumb'])) { ?>
                        <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $item['thumb'];?>" alt="" onerror="$(this).remove();">
                    <?php  } ?>
                </div>
                <div>
                    <input name="thumb" id="thumb" type="file" />
                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品上架图：</label>
        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <?php  if(!empty($item['sucessthumb'])) { ?>
                        <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $item['sucessthumb'];?>" alt="" onerror="$(this).remove();">
                    <?php  } ?>
                </div>
                <div>
                    <input name="sucessthumb" id="sucessthumb" type="file" />
                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品分享描述：</label>
        <div class="col-sm-9">
            <input type="text" name="description" id="description" maxlength="100" class="col-xs-10 col-sm-4"  value="<?php  echo $item['description'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >商品简介描述：</label>
        <div class="col-sm-9">
            <input type="text" name="express" id="express" maxlength="100" class="col-xs-10 col-sm-4"  value="<?php  echo $item['express'];?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" ></label>
        <div class="col-sm-9">
            <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i>保存商品信息</button>
        </div>
    </div>
</form>

<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/themes/default/default.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js?x=201508021"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js?x=141"></script>
<script type="text/javascript">var ue = UE.getEditor('container');</script>
<script language="javascript">
    $("#datepicker_limittime").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        minView: "0",
        //pickerPosition: "top-right",
        autoclose: true
    });
    $("#datepicker_timestart").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        minView: "0",
        //pickerPosition: "top-right",
        autoclose: true
    });
    $("#datepicker_timeend").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        minView: "0",
        autoclose: true
    });

    $(function(){
        var i = 0;
        $('#selectimage').click(function() {
            var editor = KindEditor.editor({
                allowFileManager : false,
                imageSizeLimit : '10MB',
                uploadJson : '<?php  echo mobile_url('upload')?>'
            });
            editor.loadPlugin('multiimage', function() {
                editor.plugin.multiImageDialog({
                    clickFn : function(list) {
                        if (list && list.length > 0) {
                            for (i in list) {
                                if (list[i]) {
                                    html =	'<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;  width: 125px;  height: 130px;">'+
                                        '<span class="item_box"> <img src="'+list[i]['url']+'" style="width:50px;height:50px"></span>'+
                                        '<a href="javascript:;" onclick="deletepic(this);" title="删除">删除</a>'+
                                        '<input type="hidden" name="attachment-new[]" value="'+list[i]['filename']+'" />'+
                                        '</li>';
                                    $('#fileList').append(html);
                                    i++;
                                }
                            }
                            editor.hideDialog();
                        } else {
                            alert('请先选择要上传的图片！');
                        }
                    }
                });
            });
        });
    });
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
    //ueditor
    function fillform()
    {
        if(ue.queryCommandState( 'source' )==1)
        {
            document.getElementById("container").value=ue.getContent();
        }else
        {
            document.getElementById("container").value=ue.body.innerHTML;
        }
        return true;
    }
</script>
<?php  include page('footer');?>
