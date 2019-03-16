<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 10:58:57
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/Vtiger/CompanyDetails.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12151898875c5d36c1d22de9-68538097%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e9855186fd0d6d6734bee3a5ccf8c2dc1d68f9f6' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/Vtiger/CompanyDetails.tpl',
      1 => 1523977545,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12151898875c5d36c1d22de9-68538097',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SELECTED_COMPANY' => 0,
    'QUALIFIED_MODULE' => 0,
    'MODULE_MODEL' => 0,
    'COMPANY' => 0,
    'CURRENT_USER_MODEL' => 0,
    'ERROR_MESSAGE' => 0,
    'FIELD' => 0,
    'WIDTHTYPE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d36c1d749d',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d36c1d749d')) {function content_5c5d36c1d749d($_smarty_tpl) {?>




<div class=" col-lg-12 col-md-12 col-sm-12"><input type="hidden" id="supportedImageFormats" value='<?php echo ZEND_JSON::encode(Settings_Vtiger_CompanyDetails_Model::$logoSupportedFormats);?>
' /><div class="clearfix"><div class="btn-group pull-left editbutton-container"><button id="deleteCompany" class="btn btn-danger <?php if ($_smarty_tpl->tpl_vars['SELECTED_COMPANY']->value==Settings_Vtiger_CompanyDetails_Model::getDefaultCompanyType()){?>hide<?php }?>"><?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button></div><div class="btn-group pull-right editbutton-container"><button id="createCompany" class="btn btn-success marginRight10px"><?php echo vtranslate('LBL_CREATE_NEW_COMPANY',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button><button id="updateCompanyDetails" class="btn btn-default "><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button></div></div><div class="clearfix marginTop10px marginBottom10px" id="selectedCompanyContainer"><div class="pull-left"><span class="marginRight10px"><strong><?php echo vtranslate('LBL_COMPANY',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></span><select id="currentCompany" name="currentCompany" class="select2 col-lg-12 col-md-12 col-sm-12" style="min-width: 300px;"><?php  $_smarty_tpl->tpl_vars['COMPANY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['COMPANY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getCompanies(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['COMPANY']->key => $_smarty_tpl->tpl_vars['COMPANY']->value){
$_smarty_tpl->tpl_vars['COMPANY']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['COMPANY']->value;?>
" <?php if (decode_html($_smarty_tpl->tpl_vars['COMPANY']->value)==decode_html($_smarty_tpl->tpl_vars['SELECTED_COMPANY']->value)){?>selected<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['COMPANY']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</option><?php } ?></select></div></div><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><div id="CompanyDetailsContainer" class=" detailViewContainer <?php if (!empty($_smarty_tpl->tpl_vars['ERROR_MESSAGE']->value)){?>hide<?php }?>" ><div class="block"><div><h4><?php echo vtranslate('LBL_COMPANY_LOGO',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><hr><div class="blockData"><table class="table detailview-table no-border"><tbody><tr><td class="fieldLabel"><div class="companyLogo"><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getLogoPath()){?><img src="<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getLogoPath();?>
" class="alignMiddle" style="max-width:700px;"/><?php }else{ ?><?php echo vtranslate('LBL_NO_LOGO_EDIT_AND_UPLOAD',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?></div></td></tr></tbody></table></div></div><br><div class="block"><div><h4><?php echo vtranslate('LBL_COMPANY_INFORMATION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><hr><div class="blockData"><table class="table detailview-table no-border"><tbody><?php  $_smarty_tpl->tpl_vars['FIELD_TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_TYPE']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getFields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_TYPE']->key => $_smarty_tpl->tpl_vars['FIELD_TYPE']->value){
$_smarty_tpl->tpl_vars['FIELD_TYPE']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD']->value = $_smarty_tpl->tpl_vars['FIELD_TYPE']->key;
?><?php if ($_smarty_tpl->tpl_vars['FIELD']->value!='logoname'&&$_smarty_tpl->tpl_vars['FIELD']->value!='logo'&&$_smarty_tpl->tpl_vars['FIELD']->value!='company'){?><tr><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldLabel" style="width:25%"><label ><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
" style="word-wrap:break-word;"><?php if ($_smarty_tpl->tpl_vars['FIELD']->value=='address'){?> <?php echo nl2br(decode_html($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value)));?>
 <?php }else{ ?> <?php echo decode_html($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value));?>
 <?php }?></td></tr><?php }?><?php } ?></tbody></table></div></div></div></div></div>
<?php }} ?>