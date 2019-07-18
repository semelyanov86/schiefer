/*jshint esversion:6, asi:true */
/*globals Vtiger_PBXManager_Js, socket */

var pinIO = function (options) {
    'use strict'

    function extend(obj, src) {
        for (var key in src) {
            if (src.hasOwnProperty(key)) obj[key] = src[key];
        }
        return obj;
    }

    var defaultOptions = {
        host: "",
        port: "",
        userID: 0,
        exten: 0,
        path: "/"
    }

    var config = extend(defaultOptions, options);


    var self = this;

    self.onConnect = function () {
        console.log((new Date()).toLocaleString() + " Connect");
        self.socket.send({action: "open", exten: config.exten})
    };

    self.onMessage = function (data) {
        if (!!data.admin) {
            Vtiger_PBXManager_Js.toPanel(data)
        }
        data.pbxid = data.target + data.call_id;
        data.ts = (new Date).toLocaleString()
        data.type = data.type || '-NA-'
        switch (data.event) {
            case 'raiseCard':
                self.onRaiseCard(data);
                break;
            case 'hideCard':
                self.onHideCard(data);
                break;
        }
    }

    self.onRaiseCard = function (data) {
        if (typeof Vtiger_PBXManager_Js === 'undefined') {
            console.log('Vtiger_PBXManager_Js not loaded')
            return
        }
        Vtiger_PBXManager_Js.showPBXIncomingCallPopup({
            tscard: data.ts,
            tscall: new Date(data.created_at * 1000).toLocaleString(),
            tscallsrc: data.created_at,
            pbxmanagerid: data.pbxid,
            msgid: data.uuid,
            uuid: data.call_id,
            customernumber: data.from,
            ownername: data.to,
            direction: data.type,
            dst: data.target,
            /*
            //callername : null,
            answeredby: data.to
            customertype: 'Contacts',
            callername: data.from,
            */
        })
    }

    self.onHideCard = function (data) {
        Vtiger_PBXManager_Js.removeCallPopup(data.pbxid);
    }

    self.onCDR = function (data) {

    }

    return {
        self: this,
        config: config,
        init: function () {
            var c = 0
            self.socket = io(config.host + ':' + config.port, {
                'path': config.path,
                'transports': ['websocket'],
                'reconnectionDelay': 1000,
                'reconnectionDelayMax' : 5000,
                'reconnectionAttempts': 5
            });
            self.socket.on('connect', function () {
                c = 0;
                self.onConnect();
            });

            self.socket.on('message', function (data, fn) {
                self.onMessage(data);

                if (!!fn) fn(200);
            });

            self.socket.on('disconnect', function () {
                console.log((new Date()).toLocaleString() + " Disconnect");
            });
            self.socket.on('connect_error', function(err) {
                console.log(`#${c} connecting error`)
                c++
            })
        },

    }
}

