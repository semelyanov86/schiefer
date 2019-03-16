<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 19:00:07
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPSocialConnector/Index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16398476485c5da787850d23-26185940%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7095f1dae49ee878f4350ffeb63aeab418acdf86' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPSocialConnector/Index.tpl',
      1 => 1549611992,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16398476485c5da787850d23-26185940',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'MODULE_MODEL' => 0,
    'FIELDS' => 0,
    'FIELD_NAME' => 0,
    'RECORD_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5da78787151',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5da78787151')) {function content_5c5da78787151($_smarty_tpl) {?>
<div class="container-fluid"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="socialSettings"><div class="widget_header row-fluid clearfix"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><h3 style="margin-top: 0px;"><?php echo vtranslate('LBL_SPSOCIALCONNECTOR_SETTINGS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3></div><?php $_smarty_tpl->tpl_vars['MODULE_MODEL'] = new Smarty_variable(Settings_SPSocialConnector_Module_Model::getCleanInstance(), null, 0);?><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><div class="btn-group pull-right"><button class="btn btn-default editButton" data-url='<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getEditViewUrl();?>
&mode=showpopup' type="button" title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><strong><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button></div></div></div></div><br><div class="contents row-fluid"><table class="table table-bordered table-condensed themeTableColor"><thead><tr class="blockHeader"><th colspan="2" class="mediumWidthType"><span class="alignMiddle"><?php echo vtranslate('LBL_SPSOCIALCONNECTOR_CONFIG',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</span></th></tr></thead><tbody><?php $_smarty_tpl->tpl_vars['FIELDS'] = new Smarty_variable(Settings_SPSocialConnector_Module_Model::getSettingsParameters(), null, 0);?><?php  $_smarty_tpl->tpl_vars['FIELD_TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_TYPE']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_TYPE']->key => $_smarty_tpl->tpl_vars['FIELD_TYPE']->value){
$_smarty_tpl->tpl_vars['FIELD_TYPE']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_TYPE']->key;
?><tr><td width="25%"><label class="muted pull-right marginRight10px "><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td style="border-left: none;"><span><?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
</span></td></tr><?php } ?><input type="hidden" name="module" value="SPSocialConnector"/><input type="hidden" name="action" value="SaveAjax"/><input type="hidden" name="parent" value="Settings"/></tbody></table><br><div class="span4 alert alert-info container-fluid"><?php echo vtranslate('LBL_NOTE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
 <a target="blank" href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_640_%D0%98%D0%BD%D1%82%D0%B5%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F_%D1%81%D0%BE_%D1%81%D1%82%D0%BE%D1%80%D0%BE%D0%BD%D0%BD%D0%B8%D0%BC%D0%B8_%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0%D0%BC%D0%B8#.D0.98.D0.BD.D1.82.D0.B5.D0.B3.D1.80.D0.B0.D1.86.D0.B8.D1.8F_.D1.81_.D1.81.D0.BE.D1.86.D0.B8.D0.B0.D0.BB.D1.8C.D0.BD.D1.8B.D0.BC.D0.B8_.D1.81.D0.B5.D1.82.D1.8F.D0.BC.D0.B8"><?php echo vtranslate('LBL_DOCS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</a></div></div></div></div>
<?php }} ?>