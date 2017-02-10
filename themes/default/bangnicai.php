<!DOCTYPE html>
<html class="" lang="zh-CN" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="__RESOURCE__/css/base.css" type="text/css"/>
    <link rel="stylesheet" href="__RESOURCE__/css/iconfont.css"/>
    <link rel="stylesheet" href="__RESOURCE__/css/list.css" type="text/css"/>
    <link rel="stylesheet" href="__RESOURCE__/css/hetuantuanrest.css" type="text/css"/>
    <script type="text/javascript" src="__RESOURCE__/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="__RESOURCE__/script/remsetting.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="slider card card-nomb" style="visibility: visible;">
            <script type="text/javascript" src="__RESOURCE__/js/TouchSlide.1.1.js"></script>
            <div id="focus" class="focus">
                <div class="hd">
                    <ul>
                    </ul>
                </div>
                <div class="bd">
                    <ul>
                        <!--@php  if(is_array($advs)) { foreach($advs as $adv) { @-->
                        <li>
                            <!--@php if(empty($adv['link'])) {@-->
                            <a href="<!--@php echo mobile_url('getbonus', array('id' => $adv['id']))@-->">
                                <img style="height: 8.35rem" src="<!--@php echo WEBSITE_ROOT;@-->/attachment/<!--@php  echo $adv['thumb'];@-->"/>
                            </a>
                            <!--@php }@-->
                        </li>
                        <!--@php  } } @-->
                    </ul>
                </div>
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
    <div class="row">
        <div style="display: inline-block" class="col col-50">大家反馈</div>
        <div style="display: inline-block" class="col col-50">大家反馈</div>
    </div>
    <div class="row">
        <div style="display: inline-block" class="col-md-6">.col-md-4</div>
        <div style="display: inline-block" class="col-md-6">.col-md-4</div>
    </div>
    <div class="row">
        <div style="display: inline-block" class="col-md-6">.col-lg-12</div>
        <div style="display: inline-block" class="col-md-6">.col-lg-12</div>
    </div>
</div>
</body>
</html>