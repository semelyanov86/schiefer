<?php /* Smarty version Smarty-3.1.7, created on 2019-03-07 12:27:05
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPTips/RulesTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:463475915c810009eb1058-04029840%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bd0c0cd1a1abac587026fe7d9bf59b2cf3d5ecef' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPTips/RulesTable.tpl',
      1 => 1549640771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '463475915c810009eb1058-04029840',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'EXISTING_RULES' => 0,
    'ITEM' => 0,
    'TIP_FIELD' => 0,
    'DEPENDENT_FIELD_MODEL' => 0,
    'VTIGER_FIELD' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c810009ede01',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c810009ede01')) {function content_5c810009ede01($_smarty_tpl) {?>
<table class="table table-bordered rulesTable"><thead><tr class="listViewContentHeader"><th class="listViewEntryValue"></th><th class="listViewEntryValue"><?php echo vtranslate('LBL_MODULE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th><th class="listViewEntryValue"><?php echo vtranslate('LBL_TIP_TYPE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th><th class="listViewEntryValue"><?php echo vtranslate('LBL_SELECTED_AUTOCFOMPLETE_FIELD',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th><th class="listViewEntryValue"><?php echo vtranslate('LBL_FILL_IN_FIELDS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['ITEM'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ITEM']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['EXISTING_RULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ITEM']->key => $_smarty_tpl->tpl_vars['ITEM']->value){
$_smarty_tpl->tpl_vars['ITEM']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['ITEM']->key;
?><?php $_smarty_tpl->tpl_vars['TIP_FIELD'] = new Smarty_variable($_smarty_tpl->tpl_vars['ITEM']->value->getTipFieldModel(), null, 0);?><tr class="listViewEntries"><td width="5%"><div class="table-actions text-center"><a href="index.php?module=SPTips&view=EditRules&parent=Settings&record=<?php echo $_smarty_tpl->tpl_vars['ITEM']->value->getId();?>
&providerId=<?php echo $_smarty_tpl->tpl_vars['ITEM']->value->get('provider_id');?>
"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="#" class="deleteRule" data-rule-id="<?php echo $_smarty_tpl->tpl_vars['ITEM']->value->getId();?>
"><i class="fa fa-trash"></i></a></div></td><td class="listViewEntryValue"><?php echo vtranslate($_smarty_tpl->tpl_vars['ITEM']->value->getModuleName(),$_smarty_tpl->tpl_vars['ITEM']->value->getModuleName());?>
</td><td class="listViewEntryValue"><?php echo vtranslate($_smarty_tpl->tpl_vars['ITEM']->value->getType(),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</td><td class="listViewEntryValue"><?php if ($_smarty_tpl->tpl_vars['TIP_FIELD']->value){?><?php echo vtranslate($_smarty_tpl->tpl_vars['TIP_FIELD']->value->get('label'),$_smarty_tpl->tpl_vars['ITEM']->value->getModuleName());?>
<?php }else{ ?><?php echo vtranslate($_smarty_tpl->tpl_vars['ITEM']->value->getTipFieldName(),$_smarty_tpl->tpl_vars['ITEM']->value->getModuleName());?>
<?php }?></td><td class="listViewEntryValue"><ul class="lists-menu"><?php  $_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ITEM']->value->getDependentFields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['VTIGER_FIELD'] = new Smarty_variable($_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->value->getVtigerField(), null, 0);?><li style="font-size:12px;" class="listViewFilter" ><?php if ($_smarty_tpl->tpl_vars['VTIGER_FIELD']->value){?><?php echo vtranslate($_smarty_tpl->tpl_vars['VTIGER_FIELD']->value->get('label'),$_smarty_tpl->tpl_vars['ITEM']->value->getModuleName());?>
 &nbsp;&nbsp; <i class="fa fa-arrow-left"> &nbsp;&nbsp; </i> <?php echo vtranslate($_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->value->getProviderFieldName(),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }else{ ?><?php echo vtranslate($_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->value->getVtigerFieldName(),$_smarty_tpl->tpl_vars['ITEM']->value->getModuleName());?>
&nbsp;&nbsp;<i class="fa fa-arrow-left">&nbsp;&nbsp;</i> <?php echo vtranslate($_smarty_tpl->tpl_vars['DEPENDENT_FIELD_MODEL']->value->getProviderFieldName(),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?></li><?php } ?></ul></td></tr><?php } ?></tbody></table><?php }} ?>