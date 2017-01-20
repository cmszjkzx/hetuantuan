<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/10/26
 * Time: 14:55
 */
defined('SYSTEM_IN') or exit('Access Denied');?>

<?php  include page('header');?>
    <h3 class="header smaller lighter blue">商品类别管理 &nbsp &nbsp&nbsp;<a href="<?php  echo web_url('kinds',array('op'=>'detail', 'st'=>'newkind'));?>" class="btn btn-primary">新建类别</a></h3>

    <table class="table table-striped table-bordered table-hover">
        <thead >
        <tr>
            <th style="text-align:center;">商品类别编号</th>
            <th style="text-align:center;">商品类别名称</th>
            <th style="text-align:center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($kindslist)) { foreach($kindslist as $v) { ?>
            <tr>
                <td class="text-center">
                    <?php  echo $v['kinds_level'];?>
                </td>
                <td class="text-center">
                    <?php  echo $v['kinds_name'];?>
                </td>
                <td class="text-center">
                    &nbsp;&nbsp;
                    <a  class="btn btn-xs btn-info" href="<?php  echo web_url('kinds',array('op'=>'detail','st'=>'changekind','kinds_level' => $v['kinds_level']));?>">
                        <i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;
                    </a>&nbsp;&nbsp;
                    &nbsp;&nbsp;
                    <a  class="btn btn-xs btn-info" href="<?php  echo web_url('kinds',array('op'=>'del','kinds_level' => $v['kinds_level']));?>">
                        <i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;
                    </a>&nbsp;&nbsp;
                </td>
            </tr>
        <?php  } } ?>
        </tbody>
    </table>
<?php  echo $pager;?>

<?php  include page('footer');?>