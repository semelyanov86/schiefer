<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 10:59:10
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/Vtiger/CompanyDetailsEdit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5358104955c5d36ceda3662-05141502%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4f5a41b507cbbe8f418ed344cff44cf81ba2624' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Settings/Vtiger/CompanyDetailsEdit.tpl',
      1 => 1508495595,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5358104955c5d36ceda3662-05141502',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'COMPANY_MODEL' => 0,
    'QUALIFIED_MODULE' => 0,
    'FIELD' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d36cedebc8',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d36cedebc8')) {function content_5c5d36cedebc8($_smarty_tpl) {?>



<div class="editViewContainer"><input type="hidden" id="existsCompanies" value='<?php echo htmlspecialchars(ZEND_JSON::encode(Settings_Vtiger_CompanyDetails_Model::getCompanies()), ENT_QUOTES, 'UTF-8', true);?>
'><input type="hidden" id="organizationId" value="<?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->getId();?>
"><form class="form-horizontal" id="updateCompanyDetailsForm" method="post" action="index.php" enctype="multipart/form-data"><input type="hidden" name="module" value="Vtiger" /><input type="hidden" name="parent" value="Settings" /><input type="hidden" name="action" value="CompanyDetailsSave" /><div class="form-group companydetailsedit"><label class="col-sm-2 fieldLabel control-label"> <?php echo vtranslate('LBL_COMPANY_LOGO',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label><div class="fieldValue col-sm-5" ><div class="company-logo-content"><img src="<?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->getLogoPath();?>
" class="alignMiddle" style="max-width:700px;"/><br><hr><input type="file" name="logo" id="logoFile" /></div><br><div class="alert alert-info" ><?php echo vtranslate('LBL_LOGO_RECOMMENDED_MESSAGE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</div></div></div><?php  $_smarty_tpl->tpl_vars['FIELD_TYPE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_TYPE']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->getFields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_TYPE']->key => $_smarty_tpl->tpl_vars['FIELD_TYPE']->value){
$_smarty_tpl->tpl_vars['FIELD_TYPE']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD']->value = $_smarty_tpl->tpl_vars['FIELD_TYPE']->key;
?><?php if ($_smarty_tpl->tpl_vars['FIELD']->value!='logoname'&&$_smarty_tpl->tpl_vars['FIELD']->value!='logo'){?><div class="form-group companydetailsedit"><label class="col-sm-2 fieldLabel control-label "><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php if ($_smarty_tpl->tpl_vars['FIELD']->value=='organizationname'||$_smarty_tpl->tpl_vars['FIELD']->value=='company'){?>&nbsp;<span class="redColor">*</span><?php }?></label><div class="fieldValue col-sm-5"><?php if ($_smarty_tpl->tpl_vars['FIELD']->value=='company'){?><?php if ($_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->getId()!=''){?><input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value);?>
"/><div class="marginTop5px"><?php echo vtranslate($_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value,$_smarty_tpl->tpl_vars['MODULE']->value));?>
</div><?php }else{ ?><input type="text" data-rule-required="true" class="inputElement" name="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value);?>
"/><?php }?><?php }elseif($_smarty_tpl->tpl_vars['FIELD']->value=='address'){?><textarea class="form-control col-sm-6 resize-vertical" rows="2" name="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value);?>
</textarea><?php }elseif($_smarty_tpl->tpl_vars['FIELD']->value=='website'){?><input type="text" class="inputElement" data-rule-url="true" name="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value);?>
"/><?php }else{ ?><input type="text" <?php if ($_smarty_tpl->tpl_vars['FIELD']->value=='organizationname'){?> data-rule-required="true" <?php }?> class="inputElement" name="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['COMPANY_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD']->value);?>
"/><?php }?></div></div><?php }?><?php } ?><div class="modal-overlay-footer clearfix"><div class="row clearfix"><div class="textAlignCenter col-lg-12 col-md-12 col-sm-12"><button type="submit" class="btn btn-success saveButton"><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>&nbsp;&nbsp;<a class="cancelLink" data-dismiss="modal" onclick="window.history.back();"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div></div></div></form></div><?php }} ?>