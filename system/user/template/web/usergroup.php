<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
    <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php  echo $usergroup['id'];?>" />
        <h3 class="header smaller lighter blue">用户组</h3>
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-left" > 用户组名称(优先)：</label>
            <div class="col-sm-9">
                <input type="text" name="groupName"  class="col-xs-10 col-sm-2"  value="<?php  echo $usergroup['groupName'];?>"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-left" > 可选品牌名称：</label>
            <div class="col-sm-9">
                <select style="margin-right:15px;" id="band" name="band">
                    <option value="" <?php  echo empty($_GP['band'])?'selected':'';?>>--未选择--</option>
                    <?php  if(is_array($bands)) { foreach($bands as $item) { ?>
                        <option value="<?php  echo $item['groupName'];?>" <?php  echo $item['groupName']==$_GP['groupName']?'selected':'';?>><?php  echo $item['groupName']?></option>
                    <?php  } } ?>
                </select>
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