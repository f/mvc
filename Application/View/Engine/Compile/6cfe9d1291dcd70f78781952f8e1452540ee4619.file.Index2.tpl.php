<?php /* Smarty version Smarty-3.0.6, created on 2011-01-18 22:04:33
         compiled from "/data/projects/personal/mvc/Application/View/Index/Index2.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11626459804d35f251ac3503-77784711%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6cfe9d1291dcd70f78781952f8e1452540ee4619' => 
    array (
      0 => '/data/projects/personal/mvc/Application/View/Index/Index2.tpl',
      1 => 1295380953,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11626459804d35f251ac3503-77784711',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<page>
	<body>
		<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('adi')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
?>
		<block>
			<?php echo $_smarty_tpl->tpl_vars['value']->value;?>

		</block>
		<?php }} ?>
		<img src="/mvc/public/firat.png" resize="true" width="100" />
	</body>
</page>