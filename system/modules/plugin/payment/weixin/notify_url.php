<?php
/**
 * 通用通知接口demo
 * ====================================================
 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
 * 商户接收回调信息后，根据需要设定相应的处理流程。
 * 
 * 这里举例使用log文件形式记录回调信息。
*/
	$payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
   $configs=unserialize($payment['configs']);
          
	$settings=globaSetting(array("weixin_appId","weixin_appSecret"));
          
  $_CMS['weixin_pay_appid'] = $settings['weixin_appId'];
	//受理商ID，身份标识
	$_CMS['weixin_pay_mchId']  = $configs['weixin_pay_mchId'];
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	$_CMS['weixin_pay_paySignKey'] = $configs['weixin_pay_paySignKey'];
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
		$_CMS['weixin_pay_appSecret']= $settings['weixin_appSecret'];
             


	////存储微信的回调
	 $xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
	 mysqld_insert('paylog', array('typename'=>'微信支付记录','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin'));

	 $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
	if (empty($array_data)) {
		exit('fail');
	}
	
	ksort($array_data, SORT_STRING);
		$string1 = '';
		foreach($array_data as $k => $v) {
			if($v != '' && $k != 'sign') {
				$string1 .= "{$k}={$v}&";
			}
		}
		$signkey = $_CMS['weixin_pay_paySignKey'];
		$sign = strtoupper(md5($string1 . "key={$signkey}"));
		if($sign == $array_data['sign']) {
			
			if ($array_data["return_code"] == "FAIL") {
			//此处应该更新一下订单状态，商户自行增删操作
			  	mysqld_insert('paylog', array('typename'=>'通信出错','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
      exit;
		}
		elseif($array_data["result_code"] == "FAIL"){
			//此处应该更新一下订单状态，商户自行增删操作
			  	mysqld_insert('paylog', array('typename'=>'业务出错','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
       exit;
		}
		elseif($array_data["result_code"] == "SUCCESS"){
				mysqld_insert('paylog', array('typename'=>'微支付成功返回','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin'));
      $time_end=$array_data['time_end'];
				$total_fee=$array_data['total_fee'];
				$openid=$array_data['openid'];
				$transaction_id=$array_data['transaction_id'];
				$out_trade_no_str=$array_data['out_trade_no'];
				$out_trade_no=explode('-',$array_data['out_trade_no']);
				$ordersn = $out_trade_no[0];
					$orderid = $out_trade_no[1];
				$index=strpos($ordersn,"g");
				
				$paylog_weixin=array('createtime'=>time(),'mchId'=>$_CMS['weixin_pay_mchId'],'timeend'=>$time_end,'total_fee'=>$total_fee,'openid'=>$openid,'transaction_id'=>$transaction_id,'out_trade_no'=>$out_trade_no_str,'orderid'=>$orderid ,'ordersn'=>$ordersn,'presult'=>'error','order_table'=>'','reason'=>'');
				if(empty($index))
				{
					$paylog_weixin['order_table']='shop_order';
					 $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id and ordersn=:ordersn", array(':id' => $orderid,':ordersn'=>$ordersn));
					if(!empty($order['id']))
					{
						if($order['status']==0)
						{
							/*微信支付完成回调接口
							  支付成功状态由1改为2
							  20161206  by 杨东
							*/
						mysqld_update('shop_order', array('status'=>2,'paytime'=>time(),'transid'=>$transaction_id), array('id' =>  $order['id']));
	      		updateOrderStock($order['id']);
	      		
								$paylog_weixin['presult']='success';
						$paylog_weixin['reason']='支付成功';
	      	mysqld_insert('paylog_weixin', $paylog_weixin);
							
						mysqld_insert('paylog', array('typename'=>'支付成功','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin'));
	        require_once WEB_ROOT.'/system/shopwap/class/mobile/order_notice_mail.php';  
	             mailnotice($orderid);
						message('支付成功！',WEBSITE_ROOT.mobile_url('myorder',array('status'=>1)),'success');
						}else
						{
										
									message('该订单不是支付状态无法支付',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=myorder','error');
		
						}
					}else
					{
						mysqld_insert('paylog', array('typename'=>'未找到相关订单','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
						$paylog_weixin['presult']='error';
						$paylog_weixin['reason']='未找到相关订单';
	      	mysqld_insert('paylog_weixin', $paylog_weixin);
						message('未找到相关订单',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=myorder','error');
					}
					exit;
				}else
				{//余额充值
						$paylog_weixin['order_table']='gold_order';
						$order = mysqld_select("SELECT * FROM " . table('gold_order') . " WHERE id = :id and ordersn=:ordersn", array(':id' => $orderid,':ordersn'=>$ordersn));
						if(!empty($order['id']))
						{
							if($order['status']==0)
							{
								
							mysqld_update('gold_order', array('status'=>1,'paytime'=>time()), array('id' =>  $order['id']));
     				member_gold($order['openid'],$order['price'],'addgold','余额在线充值-微支付');
     				
     				
						$paylog_weixin['presult']='success';
						$paylog_weixin['reason']='支付成功';
	      	mysqld_insert('paylog_weixin', $paylog_weixin);
								
		      
							mysqld_insert('paylog', array('typename'=>'余额充值成功','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin'));
							
     					message('余额充值成功!',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=fansindex','success');
     					
     						 if($_CMS['addons_bj_message']) {
              bj_message_sendyeczcg( $order['price'],$order['openid']);
  }
     					
							}else
							{
							
								}
							exit;
						}else
						{
							mysqld_insert('paylog', array('typename'=>'余额充值未找到订单','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
												$paylog_weixin['presult']='error';
						$paylog_weixin['reason']='未找到相关订单';
	      	mysqld_insert('paylog_weixin', $paylog_weixin);
     		message('未找余额充值订单',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=fansindex','error');
		      exit;
						}
					
				}
		}else{}
		
	mysqld_insert('paylog', array('typename'=>'微支付出现错误','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
		}else
		{
			
	mysqld_insert('paylog', array('typename'=>'签名验证失败','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
		}
	

      
?>