<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: false,
        appId: "<?php echo $shopwap_weixin_share['appId'];?>",
        timestamp: <?php echo $shopwap_weixin_share['timestamp'];?>,
        nonceStr: "<?php echo $shopwap_weixin_share['nonceStr'];?>",
        signature: "<?php echo $shopwap_weixin_share['signature'];?>",
        jsApiList: [
            'checkJsApi',
        ]
    });
    wx.error(function(res){
        alert(res.errMsg);
//        if('<?php //echo $shopwap_weixin_share['signature']?>//'!='')
//        {
//            if(res.errMsg='config:invalid signature')
//            {
//                alert(res.errMsg);
//                alert("转发接口失效，请联系管理员");
//            }
//        }
    });

    wx.ready(function () {

    });
</script>

