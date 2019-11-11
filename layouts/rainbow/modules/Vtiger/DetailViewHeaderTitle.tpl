{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="record-header clearfix">
			{if !$MODULE}
				{assign var=MODULE value=$MODULE_NAME}
			{/if}
			<div class="recordImage bg_{$MODULE} hidden-sm hidden-xs ">
				<div class="name">

					{assign var=IMAGE_DETAILS value=$RECORD->getImageDetails()}
						{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
							{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
								<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}">
							{/if}
						{/foreach}
						<span><strong><i class="ti-{strtolower($MODULE)}"></i></strong></span>

				</div>  
			</div>

			<div class="recordBasicInfo">
				<div class="info-row">
					<h4>
						{if $MODULE == 'Contacts'}
							<span class="recordLabel  pushDown" title="{$RECORD->getDisplayValue('salutationtype')}&nbsp;{$RECORD->getName()}">
					  <a href="http://sams.schiefer.co/kunden/{$RECORD->get('cf_1137')}" target="_blank">
					 {assign var=COUNTER value=0}
						  {foreach item=NAME_FIELD from=$MODULE_MODEL->getNameFields()}
							  {assign var=FIELD_MODEL value=$MODULE_MODEL->getField($NAME_FIELD)}
							  {if $FIELD_MODEL->getPermissions()}
								  <span class="{$NAME_FIELD}">
							   {if $RECORD->getDisplayValue('salutationtype') && $FIELD_MODEL->getName() eq 'firstname'}
								   {$RECORD->getDisplayValue('salutationtype')}&nbsp;
							   {/if}
									  {trim($RECORD->get($NAME_FIELD))}
						   </span>
								  {if $COUNTER eq 0 && ($RECORD->get($NAME_FIELD))}&nbsp;{assign var=COUNTER value=$COUNTER+1}{/if}
							  {/if}
						  {/foreach}
					  </a>
				  </span>
						{else}
							<span class="recordLabel pushDown" title="{$RECORD->getName()}">
							{foreach item=NAME_FIELD from=$MODULE_MODEL->getNameFields()}
								{assign var=FIELD_MODEL value=$MODULE_MODEL->getField($NAME_FIELD)}
								{if $FIELD_MODEL->getPermissions()}
									<span class="{$NAME_FIELD}">{$RECORD->get($NAME_FIELD)}</span>&nbsp;
								{/if}
							{/foreach}
						</span>
						{/if}
					</h4>
				</div>
				{include file="DetailViewHeaderFieldsView.tpl"|vtemplate_path:$MODULE}
			</div>
		</div>
	</div>
{/strip}