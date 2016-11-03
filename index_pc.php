<?php
if((empty($_REQUEST['name'])||!empty($_REQUEST['name'])&&$_REQUEST['name']!='modules')&&!file_exists(str_replace("\\",'/', dirname(__FILE__)).'/config/install.link'))
{
	header("location:install.php");
	exit;
}
/*yanru 2016-10-18 begin*/
$mod='mobile';
$mname='shopwap';
defined('SYSTEM_ACT') or define('SYSTEM_ACT', 'mobile');
$do='index_pc';
/*end*/
if(!empty($do))
{
	ob_start();
	$CLASS_LOADER="driver";
	require 'includes/init.php';
	ob_end_flush();
	exit;
}

