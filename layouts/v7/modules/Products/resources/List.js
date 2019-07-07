
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
    makeRequest: function (params) {
        app.helper.showProgress();
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
                                                console.log(data);
                                                $('*[data-id="' + data.recordId + '"]').find('[data-name="qtyinstock"]').find('.value').html(data.qtyinstock);
                                                $('*[data-id="' + data.recordId + '"]').find('[data-name="cf_1501"]').find('.value').html(data.cf_1501);
                                                $('*[data-id="' + data.recordId + '"]').find('[data-name="cf_1503"]').find('.value').html(data.cf_1503);
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