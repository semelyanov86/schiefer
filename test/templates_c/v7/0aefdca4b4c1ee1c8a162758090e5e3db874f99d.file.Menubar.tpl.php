<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 11:54:57
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Documents/partials/Menubar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2089903785c5d43e10d07d4-64526613%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0aefdca4b4c1ee1c8a162758090e5e3db874f99d' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Documents/partials/Menubar.tpl',
      1 => 1523977545,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2089903785c5d43e10d07d4-64526613',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_MODEL' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d43e10d8aa',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d43e10d8aa')) {function content_5c5d43e10d8aa($_smarty_tpl) {?>

<?php if ($_REQUEST['view']=='Detail'){?>
<div id="modules-menu" class="modules-menu">    
    <ul>
        <li class="active">
            <a href="<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl();?>
">
				<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getModuleIcon();?>

                <span><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
</span>
            </a>
        </li>
    </ul>
</div>
<?php }?><?php }} ?>