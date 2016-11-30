<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php
/**
* Created by PhpStorm.
* User: yanru02
* Date: 2016/11/29
* Time: 20:16
*/
?>
<div class="bonus_item" id='bonus_<?php echo $bonus_selected;?>' >
    <table  class="table">
        <tr>
            <td style='width:80px;'>选择优惠券:</td>
            <td>
                <select  id="select_bonus_<?php echo $bonus_selected;?>" name="select_bonus_<?php echo $bonus_selected;?>" autocomplete="off" class="span3  spec_title">
                    <option value="0">请选择优惠券</option>
                    <?php  if(is_array($bonuslist)) { foreach($bonuslist as $bonus) { ?>
                        <option value="<?php  echo $bonus['type_id'];?>" <?php  if($bonus['type_id'] == $bonus_selected) { ?> selected="selected"<?php  } ?>><?php  echo $bonus['type_name'];?></option>
                    <?php  } } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <a href="javascript:void(0);" class='btn btn-danger' onclick="removeBonus('<?php  echo $bonus_selected;?>')"> 移除优惠券</a>
            </td>
        </tr>
    </table>
</div>