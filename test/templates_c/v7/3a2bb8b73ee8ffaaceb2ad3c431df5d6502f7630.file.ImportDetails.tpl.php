<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 19:55:17
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Import/ImportDetails.tpl" */ ?>
<?php /*%%SmartyHeaderCode:951698365c5db475c243b8-59884588%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3a2bb8b73ee8ffaaceb2ad3c431df5d6502f7630' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Import/ImportDetails.tpl',
      1 => 1549611991,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '951698365c5db475c243b8-59884588',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'TYPE' => 0,
    'IMPORT_RECORDS' => 0,
    'LISTVIEW_HEADERS' => 0,
    'LISTVIEW_HEADER' => 0,
    'IMPORT_RESULT_DATA' => 0,
    'RECORD' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5db475c492d',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5db475c492d')) {function content_5c5db475c492d($_smarty_tpl) {?>



<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<?php ob_start();?><?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_tmp1), 0);?>

		<div class="modal-body">
			<div id="popupPageContainer" class="contentsDiv import-details-container">
				<div id="popupContents" class="paddingLeftRight10px">
					<table class="table table-borderless listViewEntriesTable">
						<thead>
							<tr class="listViewHeaders">
								<?php $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS'] = new Smarty_variable($_smarty_tpl->tpl_vars['IMPORT_RECORDS']->value['headers'], null, 0);?>
								<?php $_smarty_tpl->tpl_vars['IMPORT_RESULT_DATA'] = new Smarty_variable($_smarty_tpl->tpl_vars['IMPORT_RECORDS']->value[$_smarty_tpl->tpl_vars['TYPE']->value], null, 0);?>
								<?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
?><th><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value->get('label'),$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value->getModule()->getName());?>
</th><?php } ?>
							</tr>
						</thead>
						<?php  $_smarty_tpl->tpl_vars['RECORD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['IMPORT_RESULT_DATA']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORD']->key => $_smarty_tpl->tpl_vars['RECORD']->value){
$_smarty_tpl->tpl_vars['RECORD']->_loop = true;
?>
							<tr class="listViewEntries">
								<?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
?>
									<td><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value->getName());?>
</td>
								<?php } ?>
							</tr>
						<?php } ?>
					</table>
				</div>
				<input type="hidden" class="triggerEventName" value="<?php echo $_REQUEST['triggerEventName'];?>
"/>
			</div>
		</div>
	</div>
</div>
<?php }} ?>