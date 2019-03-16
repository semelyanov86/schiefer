<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 07:45:28
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Install/Step6.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20079370275c5d33987ded96-15320754%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '57738cd239214e47ff3400392a8a7b6c9edd1e16' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Install/Step6.tpl',
      1 => 1523977545,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20079370275c5d33987ded96-15320754',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'AUTH_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d339880f64',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d339880f64')) {function content_5c5d339880f64($_smarty_tpl) {?>

<form class="form-horizontal" name="step6" method="post" action="index.php">
	<input type=hidden name="module" value="Install" />
	<input type=hidden name="view" value="Index" />
	<input type=hidden name="mode" value="Step7" />
	<input type=hidden name="auth_key" value="<?php echo $_smarty_tpl->tpl_vars['AUTH_KEY']->value;?>
" />

	<div class="row main-container">
		<div class="inner-container">
			<div class="row">
				<div class="col-sm-10">
					<h4><?php echo vtranslate('LBL_ONE_LAST_THING','Install');?>
</h4>
				</div>
				<div class="col-sm-2">
					<a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_7" target="_blank" class="pull-right">
						<img src="<?php echo vimage_path('help.png');?>
" alt="Help-Icon"/>
					</a>
				</div>
			</div>
			<hr>
			<div class="offset2 row">
				<div class="col-sm-2"></div>
				<div class="col-sm-8">
					<table class="config-table input-table">
						<tbody>
							<tr>
								<td>
									<strong><?php echo vtranslate('LBL_YOUR_INDUSTRY','Install');?>
</strong> <span class="no">*</span>
                                                                        
                                                                        
								</td>
								<td>
                                                                        
                                					<select name="industry" class="select2" required="true" style="width:250px;" placeholder=<?php echo vtranslate('LBL_CHOOSE_ONE','Install');?>
>
										<option value=""></option> 
										<option><?php echo vtranslate('LBL_ACCOUNTING','Install');?>
</option> 
										<option><?php echo vtranslate('LBL_ADVERTISING','Install');?>
</option>
										<option><?php echo vtranslate('LBL_AGRICULTURE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_APPAREL_ACCESSORIES','Install');?>
</option>
										<option><?php echo vtranslate('LBL_AUTOMOTIVE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_BANKING_FINANCIAL_SERVICES','Install');?>
</option>
										<option><?php echo vtranslate('LBL_BIOTECHNOLOGY','Install');?>
</option>
										<option><?php echo vtranslate('LBL_CALL_CENTERS','Install');?>
</option>
										<option><?php echo vtranslate('LBL_CAREERS_EMPLOYMENT','Install');?>
</option>
										<option><?php echo vtranslate('LBL_CHEMICAL','Install');?>
</option>
										<option><?php echo vtranslate('LBL_COMPUTER_HARDWARE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_COMPUTER_SOFTWARE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_CONSULTING','Install');?>
</option>
										<option><?php echo vtranslate('LBL_CONSTRUCTION','Install');?>
</option>
										<option><?php echo vtranslate('LBL_EDUCATION','Install');?>
</option>
										<option><?php echo vtranslate('LBL_ENERGY_SERVICES','Install');?>
</option>
										<option><?php echo vtranslate('LBL_ENGINEERING','Install');?>
</option>
										<option><?php echo vtranslate('LBL_ENTERTAINMENT','Install');?>
</option>
										<option><?php echo vtranslate('LBL_FINANCIAL','Install');?>
</option>
										<option><?php echo vtranslate('LBL_FOOD','Install');?>
</option>
										<option><?php echo vtranslate('LBL_GOVERNMENT','Install');?>
</option>
										<option><?php echo vtranslate('LBL_HEALTH_CARE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_INSURANCE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_LEGAL','Install');?>
</option>
										<option><?php echo vtranslate('LBL_LOGISTICS','Install');?>
</option>
										<option><?php echo vtranslate('LBL_MANUFACTURING','Install');?>
</option>
										<option><?php echo vtranslate('LBL_MEDIA_PRODUCTION','Install');?>
</option>
										<option><?php echo vtranslate('LBL_NON_PROFIT','Install');?>
</option>
										<option><?php echo vtranslate('LBL_PHARMACEUTICAL','Install');?>
</option>
										<option><?php echo vtranslate('LBL_REAL_ESTATE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_RENTAL','Install');?>
</option>
										<option><?php echo vtranslate('LBL_RETAIL_WHOLESALE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_SECURITY','Install');?>
</option>
										<option><?php echo vtranslate('LBL_SERVICE','Install');?>
</option>
										<option><?php echo vtranslate('LBL_SPORTS','Install');?>
</option>
										<option><?php echo vtranslate('LBL_TELECOMMUNICATIONS','Install');?>
</option>
										<option><?php echo vtranslate('LBL_TRANSPORTATION','Install');?>
</option>
										<option><?php echo vtranslate('LBL_TRAVEL_TOURISM','Install');?>
</option>
										<option><?php echo vtranslate('LBL_UTILITIES','Install');?>
</option>
										<option><?php echo vtranslate('LBL_OTHER','Install');?>
</option>
									</select>
                                    
                                    
								</td>
							</tr>
							<tr>
								<td colspan="2">
									
                                    <?php echo vtranslate('LBL_WE_COLLECT_INFORMATION','Install');?>

                                    
                                    
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row offset2">
				<div class="col-sm-2"></div>
				<div class="col-sm-8">
					<div class="button-container">
						<input type="button" class="btn btn-large btn-primary" value="<?php echo vtranslate('LBL_NEXT','Install');?>
" name="step7"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<div id="progressIndicator" class="row main-container hide">
	<div class="inner-container">
		<div class="inner-container">
			<div class="row">
				<div class="col-sm-12 welcome-div alignCenter">
					<h3><?php echo vtranslate('LBL_INSTALLATION_IN_PROGRESS','Install');?>
...</h3><br>
					<img src="<?php echo vimage_path('install_loading.gif');?>
"/>
					<h6><?php echo vtranslate('LBL_PLEASE_WAIT','Install');?>
.... </h6>
				</div>
			</div>
		</div>
	</div>
</div><?php }} ?>