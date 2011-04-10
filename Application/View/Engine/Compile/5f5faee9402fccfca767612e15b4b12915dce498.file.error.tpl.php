<?php /* Smarty version Smarty-3.0.6, created on 2011-01-26 13:14:05
         compiled from "/data/projects/personal/mvc/Application/View/Error/error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14418709854d4001fd1c6ba6-17212730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5f5faee9402fccfca767612e15b4b12915dce498' => 
    array (
      0 => '/data/projects/personal/mvc/Application/View/Error/error.tpl',
      1 => 1296040443,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14418709854d4001fd1c6ba6-17212730',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Error <?php echo $_smarty_tpl->getVariable('code')->value;?>
</title>
</head>
<body>
	<h1>Error <?php echo $_smarty_tpl->getVariable('code')->value;?>
 occured.</h1>
</body>
</html>