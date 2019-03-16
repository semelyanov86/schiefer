<?php /* Smarty version Smarty-3.1.7, created on 2019-03-07 12:27:05
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPTips/ListRules.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2168456645c810009ea45f7-91374569%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0b165851c29af184ffa615f5c14fe19fa1ea20d1' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPTips/ListRules.tpl',
      1 => 1549640771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2168456645c810009ea45f7-91374569',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c810009eae7b',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c810009eae7b')) {function content_5c810009eae7b($_smarty_tpl) {?>
<div class="col-sm-12 col-xs-12 "><div id="listview-actions" class="listview-actions-container marginTop10px"><div class="list-content row"><div class="col-sm-12 col-xs-12 "><h4 style="margin-top: 30px;"><?php echo vtranslate('LBL_EXISTING_RULES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4><div class="marginTop15px"><button id="addRule" class="btn btn-default pull-left marginBottom10px"><strong><?php echo vtranslate('LBL_CREATE_RULE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("RulesTable.tpl",$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div></div></div></div></div><?php }} ?>