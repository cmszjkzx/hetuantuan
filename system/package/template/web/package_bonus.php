<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<div class="spec_item" id='bonus_<?php  echo $add_bonus_number;?>' >
    <input type="hidden" name="add_bonus_number" value="<?php  echo $add_bonus_number;?>" />
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 类型名称</label>
        <div class="col-sm-9">
            <input type="text" name='bonus_name_<?php  echo $add_bonus_number;?>' class="col-xs-10 col-sm-2" value="<?php echo $bonus['bonus_name'];?>" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 优惠券金额</label>
        <div class="col-sm-9">
            <input type="text" name='bonus_money_<?php  echo $add_bonus_number;?>' class="col-xs-10 col-sm-2" value="<?php echo $bonus['bonus_money'];?>" />
            <div class="help-block">&nbsp;&nbsp;此类型的优惠券可以抵销的金额</div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 最小订单金额</label>
        <div class="col-sm-9">
            <input type="text" name='min_goods_amount_<?php  echo $add_bonus_number;?>' class="col-xs-10 col-sm-2" value="<?php echo $bonus['min_goods_amount'];?>" />
            <p class="help-block">&nbsp;&nbsp;只有商品总金额达到这个数的订单才能使用这种优惠券</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 优惠券可用天数</label>
        <div class="col-sm-9">
            <input type="text" name='eable_days_<?php  echo $add_bonus_number;?>' id="eable_days" class="col-xs-10 col-sm-2" value="<?php echo $bonus['eable_days'];?>" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 优惠券发放类型</label>
        <div class="col-sm-9">
            <input type="radio" name='bonus_send_type_<?php  echo $add_bonus_number;?>' value="0"  <?php echo empty($bonus['bonus_send_type'])?"checked=\"true\"":"";?> onclick="showunit($(this))">按用户发放
            <!--<input type="radio" name='bonus_send_type_<?php  echo $add_bonus_number;?>' value="1" onclick="showunit($(this))" <?php echo $bonus['bonus_send_type']==1?"checked=\"true\"":"";?>>按商品发放
            <input type="radio" name='bonus_send_type_<?php  echo $add_bonus_number;?>' value="2" onclick="showunit($(this))" <?php echo $bonus['bonus_send_type']==2?"checked=\"true\"":"";?>>按订单金额发放
            <input type="radio" name='bonus_send_type_<?php  echo $add_bonus_number;?>' value="3" onclick="showunit($(this))" <?php echo $bonus['bonus_send_type']==3?"checked=\"true\"":"";?>>线下发放的优惠券-->
        </div>
    </div>
    <div class="form-group" id='1_<?php  echo $add_bonus_number;?>' style="display:none">
        <label class="col-sm-2 control-label no-padding-left" > 订单下限</label>
        <div class="col-sm-9">
            <input type="text" name='min_send_amount_<?php  echo $add_bonus_number;?>' class="col-xs-10 col-sm-2" value="<?php echo $bonus['min_send_amount'];?>" />
            <p class="help-block">&nbsp;&nbsp;只要订单金额达到该数值，就会发放优惠券给用户</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > </label>
        <div class="col-sm-9">
            <a href="javascript:void(0);" class='btn btn-danger' onclick="removebonus('<?php  echo $add_bonus_number;?>')"> 删除规格</a>
        </div>
    </div>
</div>
