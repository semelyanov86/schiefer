<?php /* Smarty version Smarty-3.1.7, created on 2019-03-07 12:27:05
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPTips/Index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14050414395c810009e2c6f5-54602551%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0936cef538facfd7258918287b94b81c2e0b3cef' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPTips/Index.tpl',
      1 => 1549640771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14050414395c810009e2c6f5-54602551',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'EXISTING_PROVIDERS' => 0,
    'PROVIDER' => 0,
    'SELECTED_PROVIDER' => 0,
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c810009e9f10',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c810009e9f10')) {function content_5c810009e9f10($_smarty_tpl) {?><div class="container-fluid"><div class="tab-content layoutContent padding20 themeTableColor overflowVisible"><div class="tab-pane active" id="providersTab"><div id="pickListValuesTable"><div class=" vt-default-callout vt-info-callout"><h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;</span><?php echo vtranslate('LBL_INFORMATION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4><ul><li><?php echo vtranslate('LBL_DIFFERENT_RULES_FOR_PROVIDERS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</li><li><?php echo vtranslate('LBL_AUTOCOMPLETE_FIELDS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</li></ul></div><div class="controls fieldValue col-sm-6 marginTop10px"><select id="existingProviders" class="select2" name="modulesList" style="min-width: 250px;"><?php  $_smarty_tpl->tpl_vars['PROVIDER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PROVIDER']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['EXISTING_PROVIDERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PROVIDER']->key => $_smarty_tpl->tpl_vars['PROVIDER']->value){
$_smarty_tpl->tpl_vars['PROVIDER']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PROVIDER']->value->getId();?>
" <?php if ($_smarty_tpl->tpl_vars['PROVIDER']->value->getId()==$_smarty_tpl->tpl_vars['SELECTED_PROVIDER']->value->getId()){?> selected <?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['PROVIDER']->value->getName(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</option><?php } ?></select><button id="editProvider" type="button" class="btn btn-default marginLeft10px"><strong><?php echo vtranslate('LBL_EDIT_PROVIDER',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button></div></div></div><div id="rulesContainer"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ListRules.tpl",$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div></div></div>	
<?php }} ?>