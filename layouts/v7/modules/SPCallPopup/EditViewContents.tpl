{*<!--
/*+**********************************************************************************
 * Customizable Call Popups
 * Copyright (C) 2011-2017 SalesPlatform Ltd
 * All Rights Reserved.
 * This extension is licensed to be used within one instance of Vtiger CRM.
 * Source code or binaries may not be redistributed unless expressly permitted by SalesPlatform Ltd.
 * If you have any questions or comments, please email: extensions@salesplatform.ru
 ************************************************************************************/
-->*}
{strip}
    
<!-- Call status -->
<span class="fa fa-chevron-circle-up cursorPointer toggleIcon" style="position: absolute; top: 12px; right: 12px;"></span>
<div class="popupContainerContents">
    <div class="container-fluid" style="color: black; font-size: 14px;">
        <div style="margin-bottom: 2px;"> 
            <span class="span3" style="margin-left: 0px">{vtranslate('LBL_PHONE', $MODULE)}:&nbsp;</span> 
            <span id="phone">{$CALL_MODEL->get('customernumber')}</span>
        </div>
        <div style="margin-bottom: 2px;"> 
            <span class="span3">{vtranslate('LBL_CLIENT', $MODULE)}:&nbsp;</span> 
            <span id="callContact">
                {assign var=CALL_CLIENT value=$POPUP->getClientModel()}
                {if empty($CALL_CLIENT)}
                    {vtranslate('LBL_NEW_POPUP_CONTACT', $MODULE)}
                {else}
                    <span data-field-type="reference">
                        <a href="{$CALL_CLIENT->getDetailViewUrl()}" target="_blank">
                            {$CALL_CLIENT->getName()}
                        </a>   
                    </span>
                {/if}
            </span>
        </div>
    </div>
            
    <div class="spMainDataContainer">
        <div class="container-fluid" style="color: black; font-size: 14px;">
            <div style="margin-bottom: 2px;"> 
                <span class="span3">{vtranslate('LBL_CALL_USER', $MODULE)}:&nbsp;</span> 
                <span>{$CALL_USER->get('last_name')}</span> 
                <i class="icon-headphones" style="float: none; margin-left: 5px"></i>
            </div>
            <div style="margin-bottom: 2px;">
                <span class="span3">{vtranslate('LBL_CALL_STATUS', $MODULE)}:&nbsp;</span> 
                <span id="callStatus">{$POPUP->getCallStatusDisplay()}&nbsp;</span>
            </div>
            <div style="margin-bottom: 2px;">
                <span class="span3">{vtranslate('LBL_CALL_DATE', $MODULE)}:&nbsp;</span> 
                <span>{$CALL_MODEL->get('starttime')}</span>
            </div>
        </div>    

        <!-- Form fields -->
        <div class="container-fluid editViewContainer" style="padding-bottom: 0px;">
            <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
                {if empty($CALL_CLIENT)}
                    <div style="margin-bottom: 30px;">
                        <h4 class="fieldBlockHeader">{vtranslate('LBL_CREATE_NEW', $MODULE)}</h4>
                        <div style="margin-left: 15px;">
                            {foreach item=MODULE_NAME from=$MAPPIGNS_MODULES}
                                {vtranslate('SINGLE_'|cat:$MODULE_NAME, $MODULE_NAME)}&nbsp;<input type="radio" {if $MODULE_NAME eq $MAPPING_MODULE} checked {/if} name="client_type" class="createEntity" value="{$MODULE_NAME}"/>
                                &nbsp;&nbsp;&nbsp;
                            {/foreach}
                        </div>
                    </div>
                {/if}

                {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
                    <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
                {/if}

                <input type="hidden" name="module" value="{$MODULE}" />
                <input type="hidden" name="action" value="MarkPopupProcessed" />
                <input type="hidden" name="record" value="{$RECORD_ID}" />
                <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
                <input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />

                <div class="popupFormContents">
                    {include file="PopupContents.tpl"|@vtemplate_path:$MODULE}
                </div>
{/strip}