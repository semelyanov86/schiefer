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
                <div class="pull-left" style="width: 100% !important">
                    <div class="pull-left marginRight10px">
                        <button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                    </div>
                    <div class="pull-left">
                        <button id="cancelPopup" class="btn btn-danger" type="button"><strong>{vtranslate('LBL_CANCEL', $MODULE)}</strong></button>
                    </div>
                    <div class="pull-right">
                        <button id="cancelAll" class="btn btn-danger" type="button"><strong>{vtranslate('LBL_CANCEL_ALL', $MODULE)}</strong></button>
                    </div>
                </div>
                <div id="popupError" class="marginTop15px text-center font14px" style="color: #DA4F49">
                </div>
            </form>
        </div>
    </div>
</div>
{/strip}