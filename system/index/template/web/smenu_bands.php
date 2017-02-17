<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2017/2/17
 * Time: 9:20
 */
?>
<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php if(checkrule('shop','bands')){ ?>
    <li class="open active">
        <!-- 导航第一级 -->
        <a href="#" class="dropdown-toggle">
            <i class="icon-group"></i>
            <span class="menu-text"> 商家管理</span>

            <b class="arrow icon-angle-down"></b>
        </a>
        <ul class="submenu">
            <?php if(checkrule('shop','bands')){ if(!empty($bands)){ foreach ($bands as $band) {?>
                <li> <a  onclick="navtoggle('商家管理 - > <?php echo $band['band']?>')"  href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -99, 'bandmanage' => $band['band'])) ?>" target="main">
                        <i class="icon-double-angle-right"></i>
                        <?php echo $band['band']?>
                    </a>
                </li>
            <?php } } } ?>
        </ul>
    </li>
<?php }?>


