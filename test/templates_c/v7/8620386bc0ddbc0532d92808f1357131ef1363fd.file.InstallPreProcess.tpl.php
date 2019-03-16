<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 07:21:22
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Install/InstallPreProcess.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14167708745c5d2df2cb7ee2-51082815%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8620386bc0ddbc0532d92808f1357131ef1363fd' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Install/InstallPreProcess.tpl',
      1 => 1523977545,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14167708745c5d2df2cb7ee2-51082815',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LANGUAGE_STRINGS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d2df2cc0cf',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d2df2cc0cf')) {function content_5c5d2df2cc0cf($_smarty_tpl) {?>

<div id="js_strings" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['LANGUAGE_STRINGS']->value);?>
</div>

<input type="hidden" id="module" value="Install" />
<input type="hidden" id="view" value="Index" />
<div class="container-fluid page-container">
	<div class="row head">
		<div class="col-sm-6">
			<div class="logo">
				<img src="<?php echo vimage_path('logo.png');?>
"/>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="pull-right">
				<h3><?php echo vtranslate('LBL_INSTALLATION_WIZARD','Install');?>
</h3>
			</div>
		</div>
	</div>
<?php }} ?>