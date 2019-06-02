/*+**********************************************************************************
 * Customizable Call Popups
 * Copyright (C) 2011-2017 SalesPlatform Ltd
 * All Rights Reserved.
 * This extension is licensed to be used within one instance of Vtiger CRM.
 * Source code or binaries may not be redistributed unless expressly permitted by SalesPlatform Ltd.
 * If you have any questions or comments, please email: extensions@salesplatform.ru
 ************************************************************************************/

Vtiger_Edit_Js("SPCallPopup_Edit_Js", {}, {
    
    popupContainer : false,
    
    setForm : function(element){
		this.formElement = element;
		return this;
	},
    
    setPopupContainer : function(container) {
        this.popupContainer = container;
    },
    
    getRecordId : function() {
        return $('[name="record"]', this.getForm()).val();
    },
    
    registerChangeCreateType : function() {
        var editViewForm = this.getForm();
        var thisInstance = this;
        $('.createEntity', this.popupContainer).on('change', function() {
            var mappingModule = $(this).val();
            var progressIndicator = $.progressIndicator();
            AppConnector.request({
                module : 'SPCallPopup',
                action : 'RefreshContent',
                mappingModule : mappingModule,
                recordId : thisInstance.getRecordId()
            }).then(
                function(response) {
                    progressIndicator.hide();
                    if(response.success) {
                        $('.popupFormContents', editViewForm).html(response.result);
                        app.changeSelectElementView(editViewForm);
                        app.showSelect2ElementView(editViewForm.find('select.select2'));
                    } else {
                        
                    }
                },
        
                function(error) {
                    progressIndicator.hide();
                }
            );
        });
    },
    
    registerLeavePageWithoutSubmit : function(form){
        /* Override parent for no display alert on page leave */
    },
    
    
    registerValidation : function () {
        var editViewForm = this.getForm();
        this.formValidatorInstance = editViewForm.vtValidate({
            submitHandler : function() {
                var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                app.event.trigger(e);
                if(e.isDefaultPrevented()) {
                    return false;
                }
				window.onbeforeunload = null;
                editViewForm.find('.saveButton').attr('disabled',true);
                
                 
                /* Send request to process popup */
                var progressIndicatorElement = $.progressIndicator({
                    message: app.vtranslate('JS_LBL_SAVE'),
                    blockInfo : {
                        enabled : true
                    }
                });
                
                var popupId = editViewForm.find('[name="record"]').val();
                Vtiger_SPCallPopup_JS.clearError(popupId);
                AppConnector.request($(editViewForm).serialize()).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if(data.success) {
                            Vtiger_SPCallPopup_JS.closePopup(popupId);
                        } else {
                            editViewForm.find('.saveButton').attr('disabled', false);
                            var errorMessage = data.error.message;

                            var errorDisplay = $('<div><div>' + app.vtranslate("JS_ERROR") + ': ' + errorMessage.defaultNotification + '</div></div>');
                            errorDisplay.append('<br>');
                            if(typeof errorMessage.errorDetails.vtigerCoreMessages !== 'undefined') {
                                errorDisplay.append('<div>' + errorMessage.errorDetails.vtigerCoreMessages + '</div>');
                                errorDisplay.append('<br>');
                            }
                            if(typeof errorMessage.errorDetails.lastErrorDetails !== 'undefined') {
                                errorDisplay.append('<div>' + errorMessage.errorDetails.lastErrorDetails + '</div>');
                            }

                            Vtiger_SPCallPopup_JS.displayError(popupId, errorDisplay.html());
                        }
                    },
                    function(error){
                        editViewForm.find('.saveButton').attr('disabled', false);
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        Vtiger_SPCallPopup_JS.displayError(popupId, error.message);
                    }
                );
                
                return false;
            }
        });
    },
    
    registerToggle : function() {
        var thisInstance = this;
        $('.toggleIcon', this.popupContainer).click(function() {
            $('.spMainDataContainer', thisInstance.popupContainer).toggle(500);
            $(this).toggleClass("fa-chevron-circle-up fa-chevron-circle-down");
        });
    },
    
    /**
     * Marks popups as processed without call model save logic
     * 
     * @returns {undefined}
     */
    registerCancelPopup : function() {
        var editViewForm = this.getForm();
        $("#cancelPopup", editViewForm).on('click', function() {
            
            /* Indicate cancel process */
            var popupId = $(editViewForm).find('[name="record"]').val();
            var progressIndicatorElement = $.progressIndicator({
                message: app.vtranslate('JS_LBL_CANCEL'),
                blockInfo : {
                    enabled : true
                }
            });
            
            /* Send search request */
            Vtiger_SPCallPopup_JS.clearError(popupId);
            AppConnector.request({
                module : "SPCallPopup",
                action : "CancelPopup",
                popupId : popupId
            }).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ mode : 'hide'});
                    if(data.success) {
                        Vtiger_SPCallPopup_JS.closePopup(popupId);
                    } else {
                        Vtiger_SPCallPopup_JS.displayError(popupId, app.vtranslate("JS_ERROR") + ': ' + data.error.message);
                    }
                },
                function(error){
                    progressIndicatorElement.progressIndicator({ mode : 'hide'});
                    Vtiger_SPCallPopup_JS.displayError(popupId, app.vtranslate("Error on send request, please, try again"));
                }    
            );  
        });
    },
    
    registerCloseAll : function() {
        var editViewForm = this.getForm();
        $("#cancelAll", editViewForm).on('click', function() {
            var popupId = $(editViewForm).find('[name="record"]').val();
            var progressIndicatorElement = $.progressIndicator({
                message: app.vtranslate('JS_LBL_CANCEL'),
                blockInfo : {
                    enabled : true
                }
            });
            Vtiger_SPCallPopup_JS.clearError(popupId);
            
            AppConnector.request({
                module : "SPCallPopup",
                action : "CancelAllPopups"
            }).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ mode : 'hide'});
                    if(data.success) {
                        Vtiger_SPCallPopup_JS.closeAllPopups();
                    } else {
                        Vtiger_SPCallPopup_JS.displayError(popupId, app.vtranslate("JS_ERROR") + ': ' + data.error.message);
                    }
                },
                function(error){
                    progressIndicatorElement.progressIndicator({ mode : 'hide'});
                    Vtiger_SPCallPopup_JS.displayError(popupId, app.vtranslate("Error on send request, please, try again"));
                }    
            );  
        });
    },
    
    registerAppTriggerEvent : function() {},

    registerEvents : function() {
        this._super();
        this.registerCancelPopup();
        this.registerChangeCreateType();
        this.registerValidation();
        this.registerToggle();
        this.registerCloseAll();
    }
});


/**
 * Wrapper to get incoming calls and use Edit front-end features
 * 
 * @type Object
 */
var Vtiger_SPCallPopup_JS = {
    
    processingCallsList : [],
    processingPopupsCount : 0,
    
    /**
     * Refresh list of displayed popups
     * 
     * @param {type} callsList
     * @returns {undefined}
     */
    refreshCallsList : function(callsList) {
        
        /* Create new and refresh exists popups */
        var thisInstance = this;
        var holdedPopupsIds = [];
        for(var callIndex = 0; callIndex < callsList.length; callIndex++) {
            
            /* Create new popup if not exists */
            var currentCall = callsList[callIndex];
            if(!(currentCall.popupId in this.processingCallsList)) {
                var params = {
                    title: currentCall.direction,
                    text: thisInstance.getPreparedContent(currentCall.popupContents),  
                    width: '55%',
                    addclass:'vtCall',
                    icon: 'vtCall-icon',
                    hide:false,
                    closer:false,
                    type:'info',
                    after_open:function(element) {
                        element.css('z-index', 10000);
                        element.css('overflow-y', 'auto');
                        element.css('max-height', '90%');
                        element.css('min-width', '430px');
                        app.changeSelectElementView(element);
                        app.showSelect2ElementView(element.find('select.select2'));
                        
                        var editView = new SPCallPopup_Edit_Js();
                        editView.setForm($("#EditView", element));
                        editView.setPopupContainer(element);
                        editView.registerEvents();
                    }
                };
                /* Save popup data in processing list */
                this.processingCallsList[currentCall.popupId] = {
                    callStatus : currentCall.callStatus,
                    popupElement : this.showPnotify(params)
                };
                var contactName;

                var link;
                if (document.getElementById('callContact').lastChild.childNodes[0]){
                    link = 'https://scrm.schiefer.co/' + document.getElementById('callContact').lastChild.childNodes[0].attributes.href.nodeValue;
                    contactName = document.getElementById('callContact').innerText;
                }
                if (!link){
                    link = 'https://scrm.schiefer.co/index.php?module=Contacts&view=List&viewname=7&app=MARKETING';
                }
                if (!contactName) {
                    contactName = document.getElementById('phone').innerText;
                }
                Push.create('Incoming call', {
                    body: contactName,
                    icon: '/icon.png',
                    link: link,
                    requireInteraction: true,
                    onClick: function () {
                        var win = window.open(link, '_blank');
                        win.focus();
                        this.close();
                    }
                });
                this.processingPopupsCount++;
            } else {
                Vtiger_SPCallPopup_JS.updatePopupCallStatus(currentCall.popupId, currentCall.callStatus);
            }
            
            holdedPopupsIds.push(currentCall.popupId);
        }
        
        /* Close not holded popups */
        var popupsIds = Object.keys(this.processingCallsList);
        for(var popupIndex = 0; popupIndex < popupsIds.length; popupIndex++) {
            var popupId = popupsIds[popupIndex];
            if($.inArray(popupId, holdedPopupsIds) === -1) {
                Vtiger_SPCallPopup_JS.closePopup(popupId);
            }
        }
    },
    
    showPnotify : function(customParams) {
        return $.pnotify($.extend({
			sticker: false,
			delay: '3000',
			type: 'error',
			pnotify_history: false
		}, customParams));
    },
    
    getPreparedContent : function(htmlContent) {
        if(this.processingPopupsCount >= 1) {
            var wrapper = $('<div>').append(htmlContent);
            $('.spMainDataContainer', wrapper).hide();
            $('.toggleIcon', wrapper).toggleClass("icon-chevron-up icon-chevron-down");
            
            htmlContent = wrapper.html();
        }
        
        return htmlContent;
    },
    
    /**
     * Updates sttaus of popup call
     * 
     * @param {int} popupId
     * @param {string} callStatus
     * @returns {undefined}
     */
    updatePopupCallStatus : function(popupId, callStatus) {
        var popupInfo = this.processingCallsList[popupId];
        $("#callStatus", popupInfo.popupElement).html(callStatus);
    },
    
    /**
     * Removes popup from DOM
     * @param {type} popupId
     * @returns {undefined}
     */
    closePopup : function(popupId) {
        var popupInfo = this.processingCallsList[popupId];
        delete this.processingCallsList[popupId];
        popupInfo.popupElement.remove();
        this.processingPopupsCount--;
    },
    
    /**
     * Closes all popups which are displayed
     * @returns {undefined}
     */
    closeAllPopups : function() {
        for(var popupId in this.processingCallsList) {
            this.closePopup(popupId);
        }
    },
    
    /**
     * Display error in popup
     * 
     * @param {type} popupId
     * @param {type} errorText
     * @returns {undefined}
     */
    displayError : function(popupId, errorText) {
        var popupInfo = this.processingCallsList[popupId];
        if(typeof popupInfo !== 'undefined') {
            $("#popupError", popupInfo.popupElement).html(errorText);
        }
    },
    
    clearError : function(popupId) {
        var popupInfo = this.processingCallsList[popupId];
        if(typeof popupInfo !== 'undefined') {
            $("#popupError", popupInfo.popupElement).html('');
        }
    },
    
    /**
     * Requests info about incoming calls
     * 
     * @returns {undefined}
     */
    getIncomingCalls : function() {
        AppConnector.request({
            module : 'SPCallPopup',
            action : 'GetCalls'
        }).then(function(data){
            if(data.success && data.result) {
                Vtiger_SPCallPopup_JS.refreshCallsList(data.result);
            }
        });
    },
    
    
    /**
     * Register needed actions for incoming calls handling
     * @returns {undefined}
     */
    registerEvents : function(){
        AppConnector.request({
            module : 'SPCallPopup',
            action : 'CheckPollAccess'
        }).then(function(data){
            if(data.result) {
                Vtiger_SPCallPopup_JS.getIncomingCalls(); 
                /*Visibility.every(5000, function() {
                    Vtiger_SPCallPopup_JS.getIncomingCalls(); 
                });*/
                setInterval("Vtiger_SPCallPopup_JS.getIncomingCalls()", 3000);
            }
        });
    }
};

/*
 * Support outgoing click to call - PBXManager vtiger code 
 *
 * @type Object
 */
var Vtiger_PBXManager_Js = {
    
    /**
     * Function registers PBX for Outbound Call
     */
    registerPBXOutboundCall : function(number,record) {
        var params = {
            number : number,
            record : record,
            module  : 'PBXManager',
            action : 'OutgoingCall'
        };
        
        AppConnector.request(params).then(function(data){
            var params;
            if(data.result){
                params = {
                    text :  app.vtranslate('JS_PBX_OUTGOING_SUCCESS'),
                    type : 'info'
                };
            } else {
                params = {
                    text :  app.vtranslate('JS_PBX_OUTGOING_FAILURE'),
                    type : 'error'
                };
            }
            Vtiger_Helper_Js.showPnotify(params);
        });
    }
};

/* Register handling for incoming calls */
$(document).ready(function () {
    Vtiger_SPCallPopup_JS.registerEvents();
});
