<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 10:48:19
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/ExtensionStore/Promotions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12716173495c5d344308d3b9-38962379%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e9e4915e96173705ad557fe47e3d25ff775e8cf' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/ExtensionStore/Promotions.tpl',
      1 => 1549612010,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12716173495c5d344308d3b9-38962379',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'HEADER_SCRIPTS' => 0,
    'SCRIPT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d344309d70',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d344309d70')) {function content_5c5d344309d70($_smarty_tpl) {?>

<?php  $_smarty_tpl->tpl_vars['SCRIPT'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SCRIPT']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['HEADER_SCRIPTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SCRIPT']->key => $_smarty_tpl->tpl_vars['SCRIPT']->value){
$_smarty_tpl->tpl_vars['SCRIPT']->_loop = true;
?><script type="<?php echo $_smarty_tpl->tpl_vars['SCRIPT']->value->getType();?>
" src="<?php echo $_smarty_tpl->tpl_vars['SCRIPT']->value->getSrc();?>
" /><?php } ?>
<?php }} ?>