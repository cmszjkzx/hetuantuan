<?php
		  $pindex = max(1, intval($_GP['page']));
      $psize = 30;
      $condition='';
      $conditiondata=array();
      if(!empty($_GP['realname']))
      {
      	
      	 $condition=$condition.' and realname like :realname';
      	 $conditiondata[':realname']='%'.$_GP['realname'].'%';
      }
         if(!empty($_GP['mobile']))
      {
      	
      	 $condition=$condition.' and mobile like :mobile';
      	 $conditiondata[':mobile']='%'.$_GP['mobile'].'%';
      }
          if(!empty($_GP['weixinname']))
      {
      	
      	 $condition=$condition.' and openid in (select wxfans.openid from ' . table('weixin_wxfans').' wxfans where wxfans.nickname like :weixinname)';
      	 $conditiondata[':weixinname']='%'.$_GP['weixinname'].'%';
      }
           if(!empty($_GP['alipayname']))
      {
      	
      	 $condition=$condition.' and openid in (select alifans.openid from ' . table('alipay_alifans').' alifans where alifans.nickname like :alipayname)';
      	 $conditiondata[':alipayname']='%'.$_GP['alipayname'].'%';
      }
      $status=1;
          if(empty($_GP['showstatus'])||$_GP['showstatus']==1)
      {
      	
      	 $status=1;
      }
     
         if($_GP['showstatus']==-1)
      {
      	
      	 $status=0;
      }
      if(!empty($_GP['rank_level']))
      {
      $rank_model = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level=".intval($_GP['rank_level']) );
      if(!empty($rank_model['rank_level']))
      {
      			$condition=$condition." and experience>=".$rank_model['experience'];
      	 		 	$rank_model2 = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level>".$rank_model['rank_level'].' order  by rank_level limit 1' );
  								if(!empty($rank_model2['rank_level']))
  								{
  									if(intval($rank_model['experience'])<intval($rank_model2['experience']))
  									{
  									$condition=$condition." and experience<".$rank_model2['experience'];
  									}
  								}
  							}
      }
      
      $rank_model_list = mysqld_selectall("SELECT * FROM " . table('rank_model')." order by rank_level" );

          if(!empty($condition)) {
              $list = mysqld_selectall('SELECT * FROM ' . table('member') . " where `istemplate`=0  and `status`=$status $condition " . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $conditiondata);
              $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member') . " where `istemplate`=0 and`status`=$status $condition ", $conditiondata);
          }else{
              $list = mysqld_selectall('SELECT * FROM ' . table('member') . " where `istemplate`=0  and `status`=$status " . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
              $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member') . " where `istemplate`=0 and`status`=$status ");
          }
      if(empty($total)){
          $total = 0;
      }
	 		$pager = pagination($total, $pindex, $psize);
      if(!empty($list)) {
          foreach ($list as $index => $item) {
              $list[$index]['weixin'] = mysqld_selectall("SELECT * FROM " . table('weixin_wxfans') . " WHERE openid = :openid", array(':openid' => $item['openid']));
              $list[$index]['alipay'] = mysqld_selectall("SELECT * FROM " . table('alipay_alifans') . " WHERE openid = :openid", array(':openid' => $item['openid']));
          }
      }
			include page('list');