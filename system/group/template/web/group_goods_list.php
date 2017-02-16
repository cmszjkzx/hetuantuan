<?php defined('SYSTEM_IN') or exit('Access Denied');?>

<?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">参团商品列表</h3>

<table class="table table-striped table-bordered table-hover">
    <tr >
        <th class="text-center" >团购ID</th>
        <th class="text-center" >首图</th>
        <th class="text-center">商品名称</th>
        <th class="text-center" >上架货号</th>
        <th class="text-center" >获赞数量</th>
        <th class="text-center" >状态</th>
        <th class="text-center" >操作</th>
    </tr>
	<?php if(is_array($list)) { foreach($list as $item) { ?>
		<tr>
			<td style="text-align:center;"><?php  echo $item['id'];?></td>
			<td>
				<p style="text-align:center">
					<img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $item['thumb'];?>" height="60" width="60">
				</p>
			</td>
			<td style="text-align:center;"><?php  echo $item['goodname'];?></td>
			<td style="text-align:center;"><?php  echo $item['goodsn'];?></td>
            <td style="text-align:center;"><?php  echo $item['praise'];?></td>
			<td style="text-align:center;">
				<?php  if($item['show']==1) { ?>  <label data='<?php  echo $item['show'];?>' class='label label-info' >已参团</label><?php  } ?>
				<?php  if($item['group']==1) { ?> <label data='<?php  echo $item['group'];?>' class='label label-info'>已上架</label><?php  } ?>
			</td>
			<td style="text-align:center;">
				<a  class="btn btn-xs btn-info" href="<?php  echo web_url('group', array('id' => $item['id'], 'op' => 'post'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
				<a  class="btn btn-xs btn-info" href="<?php  echo web_url('group', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></a>&nbsp;&nbsp;
        <?php if(0 == $item['isnotice']){ ?>
                <a  class="btn btn-xs btn-info" href="<?php  echo web_url('group', array('id' => $item['id'], 'goodname' => $item['goodname'], 'op' => 'notice'))?>" ><i class="icon-edit"></i>&nbsp;通&nbsp;知&nbsp;</a></a>
        <?php } else { ?>
                <a  class="btn btn-xs btn-info" ><i class="icon-edit"></i>&nbsp;已&nbsp;通&nbsp;知&nbsp;</a></a>
        <?php } ?>
            </td>
		</tr>
	<?php  } } ?>
</table>
<?php  echo $pager;?>

<?php  include page('footer');?>
