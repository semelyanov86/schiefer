<?php /* Smarty version Smarty-3.1.7, created on 2019-02-08 18:50:35
         compiled from "/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Vtiger/Footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12892165965c5d340e9ffcf3-97356290%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c2fcfabf85abdb198710192d7877a4d9c70b9c51' => 
    array (
      0 => '/var/www/html/vtigercrm/includes/runtime/../../layouts/v7/modules/Vtiger/Footer.tpl',
      1 => 1549640771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12892165965c5d340e9ffcf3-97356290',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5c5d340ea11e0',
  'variables' => 
  array (
    'MODULE' => 0,
    'VTIGER_VERSION' => 0,
    'LANGUAGE_STRINGS' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5c5d340ea11e0')) {function content_5c5d340ea11e0($_smarty_tpl) {?>

<footer class="app-footer">
        
        <div class="pull-right footer-icons">
            <small><?php echo vtranslate('LBL_CONNECT_WITH_US',$_smarty_tpl->tpl_vars['MODULE']->value);?>
&nbsp;</small>
            <!-- SalesPlatform begin #5822 -->
            <!-- <a href="http://community.salesplatform.ru/"><img src="layouts/vlayout/skins/images/forum.png"></a>
            &nbsp;<a href="https://twitter.com/salesplatformru"><img src="layouts/vlayout/skins/images/twitter.png"></a> -->
            <a href="http://community.salesplatform.ru/" target="_blank" title="<?php echo vtranslate('Community',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><i class="fa fa-comments"></i></a>
            <a href="https://twitter.com/salesplatformru" target="_blank" title="Twitter"><i class="fa fa-twitter"></i></a>
            <a href="https://vk.com/salesplatform" target="_blank" title="Vk"><i class="fa fa-vk"></i></a>
            <a href="https://youtube.com/salesplatform" target="_blank" title="YouTube"><i class="fa fa-youtube-play"></i></a>
            <!-- SalesPlatform end -->
        </div>
        
	<p>
		
                
                
                

            <?php echo vtranslate('POWEREDBY');?>
 <?php echo $_smarty_tpl->tpl_vars['VTIGER_VERSION']->value;?>
 &nbsp;
            &copy; 2004 - <?php echo date('Y');?>
&nbsp&nbsp;
            <a href="//www.vtiger.com" target="_blank">vtiger.com</a>
            &nbsp;|&nbsp;
            
            &copy; 2011 - <?php echo date('Y');?>
&nbsp&nbsp;
            <a href="//salesplatform.ru/" target="_blank">SalesPlatform.ru</a>
            
	</p>
</footer>
</div>
<div id='overlayPage'>
	<!-- arrow is added to point arrow to the clicked element (Ex:- TaskManagement),
	any one can use this by adding "show" class to it -->
	<div class='arrow'></div>
	<div class='data'>
	</div>
</div>
<div id='helpPageOverlay'></div>
<div id="js_strings" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['LANGUAGE_STRINGS']->value);?>
</div>
<div class="modal myModal fade"></div>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('JSResources.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>

</html><?php }} ?>