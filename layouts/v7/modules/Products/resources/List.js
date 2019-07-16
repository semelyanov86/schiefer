Vtiger_List_Js("Products_List_Js", {
    addOrder: function() {
        var params =  {
            module: 'Products',
            mode: 'showSendOrder',
            view: 'MassActionAjax',
            source: 'SalesOrder'
        };
        this.makeRequest(params);

    },
    addPurchase: function () {
        var params =  {
            module: 'Products',
            mode: 'showSendOrder',
            view: 'MassActionAjax',
            source: 'PurchaseOrder'
        };
        this.makeRequest(params);
    },
    addInventur: function() {
        var params =  {
            module: 'Products',
            mode: 'showSendOrder',
            view: 'MassActionAjax',
            source: 'Products'
        };
        this.makeRequest(params);
    },
    registerSearchContact: function(){
        if (!jQuery('#Products_editView_fieldName_cf_1137')) {
            return false;
        }
        var modal = jQuery('#productModal');
        modal.on('keyup', '#Products_editView_fieldName_cf_1137', function (e) {
            var target = e.currentTarget;
            var params = {};
            params.data = {
                module: 'Products',
                view: 'MassActionAjax',
                mode: 'quickSearch',
                number: e.currentTarget.value,
            };
            app.request.post(params).then(
                function (err,data) {
/*                    var newdata = $.map(data, function(dataItem) {
                        return { value: dataItem.value, data: dataItem.label };
                    });*/
                    console.log(data);
                    if(!err) {
                        jQuery('#Products_editView_fieldName_cf_1137').autocomplete({
                            source: data,
                            minLength: 2,
                            select: function(event, ui) {
                                var curdata = ui.item.data;
                                document.getElementById('kuddenresult').innerText = 'Kundenname: ' + curdata;
                            }
                        });
                    }
                },
                function (data, err) {
                }
            );
        });
    },
    registerSelectNummer: function(){
        if (!jQuery('#Products_editView_fieldName_cf_1137')) {
            return false;
        }
        var modal = jQuery('#productModal');
        modal.on('change', '#Products_editView_fieldName_cf_1137', function (e) {
            var target = e.currentTarget;
            var params = {};
            params.data = {
                module: 'Products',
                view: 'MassActionAjax',
                mode: 'quickSearchCurrent',
                number: e.currentTarget.value,
            };
            app.request.post(params).then(
                function (err,data) {
                    console.log(data, err);
                    if(!err) {
                        document.getElementById('kuddenresult').innerText = 'Kundenname: ' + data;
                    } else {
                        document.getElementById('kuddenresult').innerText = 'Error: ' + err.message;
                    }
                },
                function (data, err) {
                }
            );
        });
    },
    makeRequest: function (params) {
        app.helper.showProgress();
        var self = this;
        app.request.post({data: params}).then(
            function (err, data) {
                if (err === null) {
                    if(data != 'notShow') {
                        var params = {};
                        params.data = data;
                        params.css = {'width':'20%','text-align':'left'};
                        params.overlayCss = {'opacity':'0.2'};
                        app.helper.showModal(data, {'width': '900px',
                            'cb': function (wizardContainer) {
                                app.helper.hideProgress();
                                var form = jQuery('form', wizardContainer);
                                self.registerSearchContact();
                                self.registerSelectNummer();
                                form.submit(function (e) {
                                    var date = new Date;
                                    e.preventDefault();
                                    var paramData = $( this ).serializeFormData();
                                    app.helper.showProgress();
                                    app.request.post({data: paramData}).then(
                                        function (err, data) {
                                            if (!err) {
                                                app.helper.showSuccessNotification({'message': data.state});
                                                form.find("input[type=text], textarea").val("");
                                                var trobj = $('*[data-id="' + data.recordId + '"]');
                                                if (trobj.length > 0) {
                                                    trobj.find('[data-name="qtyinstock"]').find('.value').html(data.qtyinstock);
                                                    trobj.find('[data-name="cf_1501"]').find('.value').html(data.cf_1501);
                                                    trobj.find('[data-name="cf_1503"]').find('.value').html(data.cf_1503);
                                                }

                                                // app.helper.hideModal();
                                            } else {
                                                app.helper.showSuccessNotification({'title': 'Error', 'message': err.message});
                                                // app.helper.showErrorMessage(err.message);
                                            }
                                            app.helper.hideProgress();
                                        }
                                    );
                                    //thisInstance.createStep2(type);
                                });
                            }});
                    }

                }
            }
        );
    }
}, {

});