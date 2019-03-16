<?php /* Smarty version Smarty-3.1.7, created on 2019-02-28 09:18:27
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPVoipIntegration/Index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11039420615c777d33782d46-58219565%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '596b3408a6bcca2858facc1f7855e88b38036ba6' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/SPVoipIntegration/Index.tpl',
      1 => 1549640771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11039420615c777d33782d46-58219565',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'MODULE_MODEL' => 0,
    'PROVIDER_NAME' => 0,
    'FIELDS_INFO' => 0,
    'PROVIDER_FIELDS_INFO' => 0,
    'FIELD_INFO' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c777d3389024',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c777d3389024')) {function content_5c777d3389024($_smarty_tpl) {?><div class="container-fluid"><div class="widget_header row"><div class="col-sm-8"><h4><?php echo vtranslate('LBL_SP_VOIP_SETTINGS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><div class="col-sm-4"><div class="btn-group pull-right"><button class="btn btn-default editButton" data-url='<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getEditViewUrl();?>
&mode=showpopup' type="button" title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><strong><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button></div></div></div><hr><?php $_smarty_tpl->tpl_vars['PROVIDER_NAME'] = new Smarty_variable(Settings_SPVoipIntegration_Record_Model::getDefaultProvider(), null, 0);?><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 marginTop15px"><table class="table table-bordered table-condensed themeTableColor"><tbody><tr><td width="25%"><label class="muted pull-right marginRight10px text-right"><?php echo vtranslate('LBL_USE_CLICK_TO_CALL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td style="border-left: none;"><span><?php if (Settings_SPVoipIntegration_Record_Model::isClickToCallEnabled()){?><?php echo vtranslate('LBL_YES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }else{ ?><?php echo vtranslate('LBL_NO',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?></span></td></tr><tr><td width="25%"><label class="muted pull-right marginRight10px "><?php echo vtranslate('LBL_DEFAULT_PROVIDER',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td style="border-left: none;"><span><?php echo vtranslate($_smarty_tpl->tpl_vars['PROVIDER_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</span></td></tr></tbody></table></div><?php $_smarty_tpl->tpl_vars['PROVIDER_FIELDS_INFO'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELDS_INFO']->value[$_smarty_tpl->tpl_vars['PROVIDER_NAME']->value], null, 0);?><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="<?php echo $_smarty_tpl->tpl_vars['PROVIDER_NAME']->value;?>
settings"><div class="widget_header row-fluid clearfix"><div class="pull-left" style="width: 100%;"><h4><?php echo vtranslate('LBL_VOIP_PROVIDER_PARAMETERS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
 <?php echo ucfirst(vtranslate($_smarty_tpl->tpl_vars['PROVIDER_NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value));?>
</h4></div><table class="table table-bordered table-condensed themeTableColor"><tbody><?php  $_smarty_tpl->tpl_vars['FIELD_INFO'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_INFO']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PROVIDER_FIELDS_INFO']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_INFO']->key => $_smarty_tpl->tpl_vars['FIELD_INFO']->value){
$_smarty_tpl->tpl_vars['FIELD_INFO']->_loop = true;
?><tr><td width="25%"><label class="muted pull-right marginRight10px "><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_INFO']->value['field_label'],$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td style="border-left: none;"><span><?php echo $_smarty_tpl->tpl_vars['FIELD_INFO']->value['field_value'];?>
</span></td></tr><?php } ?></tbody></table></div><?php if ($_smarty_tpl->tpl_vars['PROVIDER_NAME']->value=='zebra'){?><button id='registerWebhooks' class="btn btn-default pull-right" type="button"><strong><?php echo vtranslate('LBL_REGISTER_WEBHOOKS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button><br><br><?php }?></div></div>	
<?php }} ?>