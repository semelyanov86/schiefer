{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
-->*}
{strip}
    <div id="sendMessageContainer" class='modal-dialog modal-lg'>
        <div class="modal-content">
            <form class="form-horizontal" id="massSave" method="post" action="index.php">
                {assign var=HEADER_TITLE value={vtranslate('Quick Create', $MODULE)}|cat:" "|cat:{vtranslate($SINGLE_MODULE, $MODULE)}}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
                <div class="modal-body">

                    <input type="hidden" name="module" value="{$MODULE}" />
                    <input type="hidden" name="source" value="{$PARENT_MODEL->getName()}" />
                    <input type="hidden" name="view" value="MassActionAjax" />
                    <input type="hidden" name="mode" value="saveAjax" />

                    <div class="modal-body tabbable">

                        <hr>
                        <table class="table table-borderless">
                            <tr>
                                {assign var=COUNTER value=0}
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                                {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                                {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                                {assign var="refrenceListCount" value=count($refrenceList)}
                                {if $FIELD_MODEL->isEditable() eq true}
                                {if $FIELD_MODEL->get('uitype') eq "19"}
                                {if $COUNTER eq '1'}
                                <td></td><td></td></tr><tr>
                                {assign var=COUNTER value=0}
                                {/if}
                                {/if}
                                {if $COUNTER eq 2}
                            </tr><tr>
                                {assign var=COUNTER value=1}
                                {else}
                                {assign var=COUNTER value=$COUNTER+1}
                                {/if}
                                <td class="fieldLabel alignMiddle">
                                    {if $isReferenceField eq "reference"}
                                        {if $refrenceListCount > 1}
                                            {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                            {assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                            {if !empty($REFERENCED_MODULE_STRUCTURE)}
                                                {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCTURE->get('name')}
                                            {/if}
                                            <select style="width: 140px;" class="select2 referenceModulesList">
                                                {foreach key=index item=value from=$refrenceList}
                                                    <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $value)}</option>
                                                {/foreach}
                                            </select>
                                        {else}
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                        {/if}
                                    {else if $FIELD_MODEL->get('uitype') eq "83"}
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                                        {if $TAXCLASS_DETAILS}
                                            {assign 'taxCount' count($TAXCLASS_DETAILS)%2}
                                            {if $taxCount eq 0}
                                                {if $COUNTER eq 2}
                                                    {assign var=COUNTER value=1}
                                                {else}
                                                    {assign var=COUNTER value=2}
                                                {/if}
                                            {/if}
                                        {/if}
                                    {else}
                                        {if $MODULE eq 'Documents' && $FIELD_MODEL->get('label') eq 'File Name'}
                                            {assign var=FILE_LOCATION_TYPE_FIELD value=$RECORD_STRUCTURE['LBL_FILE_INFORMATION']['filelocationtype']}
                                            {if $FILE_LOCATION_TYPE_FIELD}
                                                {if $FILE_LOCATION_TYPE_FIELD->get('fieldvalue') eq 'E'}
                                                    {vtranslate("LBL_FILE_URL", $MODULE)}&nbsp;<span class="redColor">*</span>
                                                {else}
                                                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                                {/if}
                                            {else}
                                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                            {/if}
                                        {else}
                                            {if $FIELD_MODEL->getName() eq 'qtyinstock'}
                                                Gramm
                                            {else}
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                            {/if}
                                        {/if}
                                    {/if}
                                    &nbsp;{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                </td>
                                {if $FIELD_MODEL->get('uitype') neq '83'}
                                    <td class="fieldValue" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                                    </td>
                                {/if}
                                {/if}
                                {/foreach}
                                {*If their are odd number of fields in edit then border top is missing so adding the check*}
                                {if $COUNTER is odd}
                                    <td></td>
                                    <td></td>
                                {/if}
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    {if $BUTTON_NAME neq null}
                        {assign var=BUTTON_LABEL value=$BUTTON_NAME}
                    {else}
                        {assign var=BUTTON_LABEL value={vtranslate('LBL_SAVE', $MODULE)}}
                    {/if}
                    <button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-success" type="submit" name="saveButton"><strong>{$BUTTON_LABEL}</strong></button>
                    <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
            </form>
        </div>
    </div>
{/strip}