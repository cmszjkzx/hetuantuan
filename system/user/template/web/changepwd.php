<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
 <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" onSubmit="return checkpassword();">
        <input type="hidden" name="id" value="<?php  echo $account['id'];?>" />
					<h3 class="header smaller lighter blue">修改密码</h3>		  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 新密码：</label>

										<div class="col-sm-9">
											   <input type="password" id="newpassword"  name="newpassword"  autocomplete="off" class="col-xs-10 col-sm-2" />
										</div>
									</div>
									
									  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 确认密码：</label>

										<div class="col-sm-9">
											<input type="password" id="confirmpassword"  name="confirmpassword" class="col-xs-10 col-sm-2"  />
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