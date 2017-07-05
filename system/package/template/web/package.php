<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">优惠券礼包管理</h3>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script>
<form action="" method="post" class="form-horizontal" >
    <h4 class="header smaller lighter blue">礼包设置</h4>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 礼包编号</label>
        <div class="col-sm-9">
            <input type="text" name="package_id" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['package_id'];?>" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 礼包金额</label>
        <div class="col-sm-9">
            <input type="text" name="max_amount" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['max_amount'];?>" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 最大人数</label>
        <div class="col-sm-9">
            <input type="text" name="max_number" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['max_number'];?>" />
        </div>
    </div>
    <h4 class="header smaller lighter blue">优惠券添加</h4>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >&nbsp;</label>
        <div class="col-sm-9">
            <a href="javascript:;" class='btn btn-primary' id='add-bonus' onclick="addbonus()" style="margin-top:10px;margin-bottom:10px;"  title="添加优惠券"><i class="icon-plus"></i>添加优惠券</a>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >&nbsp;</label>
        <div class="col-sm-9">
            <div id='bonus'>
                <?php  //if(is_array($allbonus)) { foreach($allbonus as $bonus) { ?>
                    <?php  //include page('package_bonus')?>
                <?php  //} } ?>
                <?php  if(!empty($bonus)) { ?>
                    <?php  include page('package_bonus')?>
                <?php  } ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > </label>
        <div class="col-sm-9">
            <input name="submit" type="submit" value="提交" class="btn btn-primary span3">
        </div>
    </div>
</form>   
<script>
    var rsource_url="<?php echo RESOURCE_ROOT;?>";
    //2017-07-05-yanru
    <?php if (empty($bonus_id)){?>
    var count = 1;
    <?php }else{ ?>
    var count = 2;
    <?php }?>
    //var count = 1;
    //end
    //var package_bonus_url="<?php //echo create_url('sit',array('name'=>'package','do'=>'package','op'=>'addbonus')) ?>";

    function addbonus(){
        if(count <=1 ) {
            $("#add-bonus").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
            var package_bonus_url = "<?php echo create_url('sit', array('name' => 'package', 'do' => 'package', 'op' => 'addbonus', 'bonus_id' => $bonus_id)) ?>" + "&count=" + count;
            var t = package_bonus_url;
            $.ajax({
                url: t, success: function (t) {
                    $("#add-bonus").html('<i class="icon-plus"></i> 添加优惠券').removeAttr("disabled").toggleClass("btn-primary"), $("#bonus").append(t);
                    var e = $(".add-bonus").length - 1;
                    $(".add-bonus:eq(" + e + ")").focus(), window.optionchanged = !0
                }
            })
            count++;
        }
    }

    function removebonus(t){
        confirm("确认要删除此规格?")&&($("#bonus_"+t).remove(),window.optionchanged=!0,count--)
    }

    function gObj(obj) {
        if (document.getElementById) {
            if (typeof obj=="string") {
                return document.getElementById(obj);
            } else {
                return obj.style;
            }
        }
        return null;
    }

    function showunit(obj) {
        debugger;
        var get_value = obj.val();
        var get_id = obj.parent().parent().parent().find("input").first().val();
        gObj("1_"+get_id).style.display = (get_value == 2) ? "" : "none";
        return;
    }

</script>
<?php  include page('footer');?>
