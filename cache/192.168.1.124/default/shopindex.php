<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $cfg['shop_title'] ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/css/base.css" type="text/css"/>
    <link rel="stylesheet" href="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/css/iconfont.css"/>
    <link rel="stylesheet" href="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/css/list.css" type="text/css"/>
    <script type="text/javascript" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/js/jquery-1.11.3.min.js"></script>
</head>
<body>
<div class="container cboxshadow">
<!--<div class="indexsearch">-->
        <!--<form action="mobile.php" id="searchForm" name="searchForm" class="form-inline">-->
            <!--<input type="hidden" name="mod" value="mobile"/>-->
            <!--<input type="hidden" name="do" value="goodlist"/>-->
            <!--<input type="hidden" name="name" value="shopwap"/>-->
            <!--<input name="keyword" id="search_word" placeholder="请输入商品名进行搜索！" type="text" class="inputwidth">-->
            <!--<lable><a href="&lt;!&ndash;@php  echo mobile_url('listCategory')@&ndash;&gt;"><i id="fi" class="iconfont bjicon-listbullet"></i></a>-->
            <!--</lable>-->
        <!--</form>-->
<!--</div>-->
    <div class="row">
        <div class="slider card card-nomb" style="visibility: visible;">
            <script type="text/javascript" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/js/TouchSlide.1.1.js"></script>
            <div id="focus" class="focus">
                <div class="hd">
                    <ul>
                    </ul>
                </div>
                <div class="bd">
                    <ul>
                        <?php if(is_array($advs)) { foreach($advs as $adv) { ?>
                        <li>
                            <a href="<?php if(empty($adv['link'])) { ?><?php } else { ?><?php echo $adv['link'];?><?php } ?>">
                                <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php echo $adv['thumb'];?>"/></a>
                        </li>
                        <?php } } ?>
                    </ul>
                </div>
                <!--<div class="bd">
                    <ul>
                        &lt;!&ndash;@php foreach($category as $row){ @&ndash;&gt;

                        &lt;!&ndash;@php foreach($row['list'] as $item){ @&ndash;&gt;
                        &lt;!&ndash;@php if($item['isrecommand']==1&&$item['ishot']==1){ @&ndash;&gt;
                        <li>
                            <a href="&lt;!&ndash;@php echo mobile_url('detail', array('id' => $item['id']))@&ndash;&gt;">
                                <img src="&lt;!&ndash;@php echo WEBSITE_ROOT;@&ndash;&gt;/attachment/&lt;!&ndash;@php echo $item['thumb']@&ndash;&gt;"/></a>
                        </li>
                        &lt;!&ndash;@php } @&ndash;&gt;
                        &lt;!&ndash;@php } @&ndash;&gt;
                        &lt;!&ndash;@php } @&ndash;&gt;
                    </ul>
                </div>-->
            </div>
            <script type="text/javascript">
                TouchSlide({
                    slideCell: "#focus",
                    titCell: ".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                    mainCell: ".bd ul",
                    delayTime: 600,
                    interTime: 4000,
                    effect: "leftLoop",
                    autoPlay: true,//自动播放
                    autoPage: true, //自动分页
                    switchLoad: "_src" //切换加载，真实图片路径为"_src"
                });
            </script>
        </div>
    </div>
    <div class="paixu paixu_index">
        <div class="tab">
            <a <?php if($sort==1) { ?>  class="price on"<?php } else { ?>class="price"<?php } ?>
            onclick="location.href='<?php echo $sorturl;?>&sort=1&sortb1=<?php echo $sortb11;?>
            '">同事推荐</a>
            <a onclick="alert(1)">人气</a>
            <a <?php if($sort==3) { ?>  class="click on"<?php } else { ?>class="renqi"<?php } ?>
            onclick="location.href='<?php echo $sorturl;?>&sort=3&sortb3=<?php echo $sortb33;?>
            '">筛选</a>
            <a <?php if($sort==3) { ?>  class="click on"<?php } else { ?>class="click"<?php } ?>
            href="<?php echo mobile_url('listCategory');?>">搜索</a>
        </div>
    </div>
    <!--<div class="row ptb10">-->
        <!--<div class="col-xsl-2">-->
            <!--<a href="&lt;!&ndash;@php  echo mobile_url('goodlist')@&ndash;&gt;">-->
                <!--<img class="img-responsive" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/images/t1.jpg" style="display: inline;">-->
                <!--<span>全部商品</span>-->
            <!--</a>-->
        <!--</div>-->
    <!--<div class="col-xsl-2">-->
            <!--<a href="&lt;!&ndash;@php  echo mobile_url('myorder',array('status'=>99))@&ndash;&gt;">-->
                <!--<img class="img-responsive" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/images/t6.png" style="display: inline;">-->
                <!--<span>我的订单</span>-->
            <!--</a>-->
        <!--</div>-->

        <!--<div class="col-xsl-2">-->
            <!--<a href="&lt;!&ndash;@php  echo mobile_url('bonus')@&ndash;&gt;">-->
                <!--<img class="img-responsive" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/images/t5.png" style="display: inline;">-->
                <!--<span>优惠券</span>-->
            <!--</a>-->
        <!--</div>-->

        <!--<div class="col-xsl-2">-->
            <!--<a href="&lt;!&ndash;@php  echo mobile_url('help')@&ndash;&gt;">-->
                <!--<img class="img-responsive" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/images/t2.png" style="display: inline;">-->
                <!--<span>购物须知</span>-->
            <!--</a>-->
        <!--</div>-->

        <!--<div class="col-xsl-2">-->
            <!--<a href="&lt;!&ndash;@php  echo mobile_url('rechargegold')@&ndash;&gt;">-->
                <!--<img class="img-responsive" src="http://192.168.1.124/hetuantuan/themes/default/__RESOURCE__/images/t5.png" style="display: inline;">-->
                <!--<span>我的余额</span>-->
            <!--</a>-->
        <!--</div>-->
    <!--</div>-->
    <div class="row">
        <div id="home-page">
            <div role="main" class="ui-content ">
                <div class="home-categories">
                    <div class="category-container" style="background-color:#ffffff;">
                    <ul class="goods_list">
                        <?php foreach($category as $row){ ?>
                        <?php foreach($row['list'] as $item){ ?>

                        <li class="SeckillOne">
                            <a href="<?php echo mobile_url('detail', array('id' => $item['id']))?>" class="item">

                                <div class="img"><img
                                        src="<?php echo WEBSITE_ROOT;?>/attachment/<?php echo $item['thumb']?>"
                                        usesrc="1" alt=""></div>
                                <div class="txt">商品名称:<?php echo $item['title'];?></div>

                                <div class="buy">
                                    <span class="price">价格:<strong><em>￥
                                        <?php echo $item['marketprice'];?></em></strong>
                                        <!--<del>￥&lt;!&ndash;@php echo $item['productprice'];@&ndash;&gt;</del>-->
                                    </span>

                                </div>
                            </a>
                        </li>

                        <?php } ?>
                        <?php } ?>
                    </ul>

                    <!--&lt;!&ndash;@php foreach($category as $row){ @&ndash;&gt;-->
                    <!--<div class="category-container" style="background-color:#ffffff;">-->
                        <!--<div class="category-name">-->
                            <!--<a class="category-url"-->
                               <!--href="&lt;!&ndash;@php  echo mobile_url('goodlist', array('pcate' => $row['id']));@&ndash;&gt;">-->
                                <!--<div class="name-border"></div>-->
                                <!--<div class="name">&lt;!&ndash;@php echo $row['name'];@&ndash;&gt;</div>-->
                                <!--<div class="name-more">-->
                                    <!--<i class="icons-arrow-right2"></i>-->
                                <!--</div>-->
                                <!--<div class="text-more"><a-->
                                        <!--href="&lt;!&ndash;@php  echo mobile_url('goodlist', array('pcate' => $row['id']));@&ndash;&gt;"-->
                                        <!--style="color:#333;font-size: 12px">更多>></a></div>-->

                        <!--</div>-->

                        <!--</a>-->
                    <!--</div>-->


                    <!--<ul class="os_box_list">-->


                        <!--&lt;!&ndash;@php foreach($row['list'] as $item){ @&ndash;&gt;-->

                        <!--<li>-->
                            <!--<a href="&lt;!&ndash;@php echo mobile_url('detail', array('id' => $item['id']))@&ndash;&gt;" class="item">-->

                                <!--<div class="img"><img-->
                                        <!--src="&lt;!&ndash;@php echo WEBSITE_ROOT;@&ndash;&gt;/attachment/&lt;!&ndash;@php echo $item['thumb']@&ndash;&gt;"-->
                                        <!--usesrc="1" alt=""></div>-->
                                <!--<div class="txt">&lt;!&ndash;@php echo $item['title'];@&ndash;&gt;</div>-->

                                <!--<div class="buy">-->
                                    <!--<span class="price"><strong><em>￥-->
                                        <!--&lt;!&ndash;@php echo $item['marketprice'];@&ndash;&gt;</em></strong><del>￥-->
                                        <!--&lt;!&ndash;@php echo $item['productprice'];@&ndash;&gt;</del></span>-->

                                <!--</div>-->
                            <!--</a>-->
                        <!--</li>-->

                        <!--&lt;!&ndash;@php } @&ndash;&gt;-->


                    <!--</ul>-->
                    <!--&lt;!&ndash;@php } @&ndash;&gt;-->


                <!--</div>-->

            </div>
        </div>
    </div>

    <?php include page('footer');?>
</div>
<?php include themePage('footer');?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#submit").click(function () {
            if ($("#search_word").val()) {
                $("#searchForm").submit();
            } else {
                alert("请输入关键词！");
                return false;
            }
        });


    });
</script>
</body>
</html>