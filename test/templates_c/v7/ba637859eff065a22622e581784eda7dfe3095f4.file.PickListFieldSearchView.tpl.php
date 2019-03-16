<?php /* Smarty version Smarty-3.1.7, created on 2019-02-11 08:32:02
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Vtiger/uitypes/PickListFieldSearchView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12136388715c6108d2311875-71926964%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ba637859eff065a22622e581784eda7dfe3095f4' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Vtiger/uitypes/PickListFieldSearchView.tpl',
      1 => 1508495595,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12136388715c6108d2311875-71926964',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'FIELD_MODEL' => 0,
    'FIELD_INFO' => 0,
    'SEARCH_INFO' => 0,
    'PICKLIST_VALUES' => 0,
    'PICKLIST_KEY' => 0,
    'SEARCH_VALUES' => 0,
    'PICKLIST_LABEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c6108d234688',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c6108d234688')) {function content_5c6108d234688($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars['FIELD_INFO'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldInfo(), null, 0);?><?php $_smarty_tpl->tpl_vars['PICKLIST_VALUES'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_INFO']->value['picklistvalues'], null, 0);?><?php $_smarty_tpl->tpl_vars['FIELD_INFO'] = new Smarty_variable(Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($_smarty_tpl->tpl_vars['FIELD_INFO']->value)), null, 0);?><?php $_smarty_tpl->tpl_vars['SEARCH_VALUES'] = new Smarty_variable(explode(',',$_smarty_tpl->tpl_vars['SEARCH_INFO']->value['searchValue']), null, 0);?><div class="select2_search_div"><input type="text" class="listSearchContributor inputElement select2_input_element"/><select class="select2 listSearchContributor" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name');?>
" multiple data-fieldinfo='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_INFO']->value, ENT_QUOTES, 'UTF-8', true);?>
' style="display:none"><?php  $_smarty_tpl->tpl_vars['PICKLIST_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_LABEL']->key => $_smarty_tpl->tpl_vars['PICKLIST_LABEL']->value){
$_smarty_tpl->tpl_vars['PICKLIST_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value = $_smarty_tpl->tpl_vars['PICKLIST_LABEL']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['PICKLIST_KEY']->value,$_smarty_tpl->tpl_vars['SEARCH_VALUES']->value)&&($_smarty_tpl->tpl_vars['PICKLIST_KEY']->value!='')){?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['PICKLIST_LABEL']->value;?>
</option><?php } ?></select></div><?php }} ?>