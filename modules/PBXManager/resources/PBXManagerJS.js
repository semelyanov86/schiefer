/**
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */

/*jshint esversion:6, asi:true */
/*globals $,jQuery, window, Vtiger_Helper_Js */

var Vtiger_PBXManager_Js = {
    DBG : false,

    /**
     * User/project options
     */
    opts : {},

    defaults : {
      lang: 'ru_ru',
      actions: "Leads,Contacts,Accounts",
      buttons: "close,toggle,info",
      hideDelay: 5,
      internal: true,
      max: 4,
      autoCompleteLimit: 15,
      queueType: "revolve",
      related: "default",
      storeCalls: false,
      useStorage: false,
      syncFwdList: false,
      showTarget: true,
      lookupUsers: false,
      allowIcon: true,
      allowClose: true,
      allowDrag: true,
      allowToggle: true,
      allowAssign: false,
      allowForward: true,
      detailed: true
    },

    /**
     * Max phone number length
     */
    maxLength: 6,

    /**
     * turn off cards for active tab
     */
    disableCards : false,

    /**
     * Limiting types
     *   next - drops all new beyound limit
     *   revolve - removes oldest appends new one
     */
    limitType : 'revolve',

    /**
     * Fold cards after this limit
     * I like oddity
     */
    maxPopups : 1,

    /**
     * Skip cards
     */
    cardsHardLimit : 3,

    /**
     * card css class marker
     */
    baseClass : '.vtCall',

    /**
     * Remember all incoming calls
     */
    calls : {},

    // Deprecation
    callsPollFunctionId : false,

    /**
     * global saving permission
     */
    allowSave : true,

    /**
     * Save entity actions
     * Move to setype?
     * Quick or Edit
     */
    createAction: 'Edit',

    /**
     * setypes
     */
    createTypes: {
      'Leads': {
        'active': 1,
        'label':  apptrans('Leads'),
        'crm': {
          name : 'lastname',
          phone: 'phone'
        }
      },
      'Contacts': {
        'active': 0,
        'label':  apptrans('Contacts'),
        'crm': {
          name : 'firstname',
          phone: 'mobile'
        }
      },
      'Accounts': {
        'active': 0,
        'label':  apptrans('Accounts'),
        'crm': {
          name : 'accountname',
          phone: 'phone'
        }
      }
    },

    /**
     * crm users phone info
     */
    phoneList : {},

    /**
     * cache redirect html options
     */
    redirectOpts : false,

    /**
     * drag ui presence flag
     */
    dragLoaded: false,

    /**
     * template for call details
     */
    techInfo: '<small class="info">[ %S% ]</small>',

    getUiCtl: function (vtVersion) {
      const data = {
        6: V6ui,
        7: V7ui
      }
      let ctlV = (vtVersion in data)? vtVersion : 7
      return new data[ctlV]()
    },

    /**
     * Load required libraries
     * @param object pbx configuration
     */
    preLoad: function (pbx) {
      let _ = this,
        base = 'modules/PBXManager/'

      if (typeof pbx == 'undefined') {
        _.dbg('No configuration loaded')
        return
      }

      // TODO feature detection
      let uiVersion = pbx.vtmajor
      if (!('vtui' in window)) vtui = new VTUI(_.getUiCtl(uiVersion))

      if (!pbx.exten) {
        _.dbg('No user extension defined', pbx.exten)
        return
      }

      $('head').append('<link rel="stylesheet" href="' + base + 'resources/card.css"/>');
      if (uiVersion == 6) {
        $('head').append('<link rel="stylesheet" href="' + base + 'vendor/fontawesome/font-awesome.min.css"/>');
      }
      _.opts = Object.assign({}, _.defaults, pbx.card)

      if (_.opts.allowIcon) {
        PBXIcon = window.PBXIcon || initPbxIcon(pbx.exten)
      }

      if (_.opts.max == 0) {
        _.disableCards = true
        _.dbg('Cards disabled')
        return
      }

      if (!pbx.host) return _.dbg('No WS host defined')

      // TODO glue alltogether
      $.when(
        $.getScript(base + 'vendor/socket.io/socket.io.js'),
        $.getScript(base + 'resources/pin.io.js')
      ).done(function (...args) {
        var socketCfg = {
          host: pbx.host,
          port: pbx.port,
          path: "/ws",
          showcard : true,
          exten: pbx.exten
        };
        pbxSocket = new pinIO(socketCfg)
        pbxSocket.init()
        if (typeof pbxSocket == 'undefined') {
          _.dbg('Unable to create WS connection')
          return
        }
      }).fail(e=>_.dbg('Some error', e))
    },

    /**
     * Function display the PBX popup
     */
    showPBXIncomingCallPopup : function(record) {
        var _ = this;
        if (_.disableCards) return;

        if (record.direction == 'internal' && _.opts.internal == false) {
          _.calls[pbxid] = 'Skipped';
          return
        }
        var pbxid = record.pbxmanagerid;

        // Prevent duplicates
        if (pbxid in _.calls) {
          return _.dbg(`Card ${pbxid} already shown`)
        }
        _.calls[pbxid] = _.opts.storeCalls? record : {};

        let allowNext = _.borderConditions(pbxid)
        if (!allowNext) {
          return;
        }

        // TODO move to ui
        var types = {
          inbound : {
            icon: 'fa-angle-double-down',
            type: 'info',
            title: pbxtrans('INCOMINGCALL')
          },
          outbound : {
            icon: 'fa-angle-double-up',
            type: 'success',
            title: pbxtrans('OUTGOINGCALL')
          },
          internal : {
            icon: 'fa-retweet',
            type: 'error',
            title: pbxtrans('INTERNALCALL')
          }
        }
        var wIcon  = (record.direction in types) ? types[record.direction].icon : 'fa-question';
        var wType  = (record.direction in types) ? types[record.direction].type : 'error';
        var wTitle = (record.direction in types) ? types[record.direction].title : apptrans('Unknown');
        var ui = _.getCardTemplate(
          pbxid,
          record.customernumber,
          record.ownername
        );
        var card = vtui.showCard({
          title: wTitle + ' '
              + (_.techInfo.replace('%S%', (new Date).toLocaleTimeString())),
          text: ui,
          width: '245px',
          //min_height: '75px',
          addclass: _.baseClass.replace('.', '')
              + (wType == 'error'?' alert-warning':''),
          icon: 'm-0 fa ' + wIcon,
          hide: false,
          closer: true,
          sticker: true,
          type: wType,
          after_open: function(pn) {
            //_.calls[record.pbxmanagerid].card = pn
            _.init($(pn), record)
          }
        })
    },

    /**
     * Init card according to options
     */
    init: function ($main, record) {
      var _ = this;
      var $container = $main.find('.ui-pnotify-container');
      if (_.opts.allowAssign) {
        let $auto = $container.find('.tgtcon')
        _.initAutocomplete($auto, $container)
      }

      /*
      $(_.getDropdown()).insertAfter('.ui-pnotify-sticker')
      $main.find('.dropdown-toggle').dropdown()
      $main.data('info', record);
      */

      if (!_.opts.allowClose) {
        var $close = $main.find('.ui-pnotify-closer');
        $close.off();
        $close.click(function(){$container.toggleClass('folded')});
      }

      if (_.opts.allowToggle) {
        var $toggle = $main.find('.ui-pnotify-title');
        $toggle.click(function(){$container.toggleClass('folded')});
      }

      // technical info
      $main.find('.info').click(function() {
        bootbox.alert(_.toList(record))
      })

      _.findRelated($container, record)

      var pbxid = record.pbxmanagerid
      var uuid = record.uuid
      // TODO get rid of ids
      if (_.opts.allowForward) {
        _.fillForward($container, record)
        $main.find('.forwarding').removeClass('hide')
        var $fwdSelect = $main.find('select.forward-list')
        var $fwdInput = $main.find('input.forwardto')
        var selectedForward = $fwdSelect.val()
        if (selectedForward) $fwdInput.val(selectedForward);

        $('#redirect_' + pbxid).click(function(e) {
          var redirTarget = $fwdSelect.val();
          if (redirTarget == '') {
            bootbox.alert(pbxtrans('REQUIREFWDEXTEN'));
            return false;
          }

          _.requestRedirect(uuid, redirTarget)
        });

        $fwdSelect.bind('change', function(e) {
          $fwdInput.val(e.currentTarget.value);
        });
      }

      if (_.opts.allowDrag) {
        if (typeof $.fn.draggable != 'function') {
          $.getScript('/kcfinder/js/jquery.drag.js',function(){
            $main.draggable();
          })
        } else {
          $main.draggable({revert:false});
        }
      }

      // fold unwanted cards
      if (_.limitType != 'next') return
      if ($(_.baseClass + ' > div:not(.folded)').length > _.maxPopups)
        $container.toggleClass('folded')
    },

    initAutocomplete: function ($node, $container) {
      const _ = this,
        $btn = $node.parent().find('button'),
        $phone = $node.parent().parent().find('.tgtphone'),
        $card = $container
      $node.autocomplete({
        minLength: 2,
        source: (req, res) => {
          $btn.disable()
          $.get('/index.php', {
            module: 'PBXManager',
            action: 'SmartLookup',
            search_value: req.term
          }).then(data => {
            if (!data.result || data.result.length == 0) {
              res([{label: 'No data', value: false}])
              return
            }
            let items = data.result
            //undefined? items.splice(_.autoCompleteLimit)
            items.splice(15)
            res(items)
          })
        },
        select: (evt, ui) => {
          if (!ui.item.id) {
            return false
          }
          $btn.off()
          // Accept customer phone
          $btn.click(function (e) {
            const tgtPhone = $phone.val()
            if (tgtPhone.length == 0) {
                $btn.disable()
                return
            }
            // extracted to acceptEntityPhone
            AppConnector.request({
              module: 'PBXManager',
              action: 'SavePhone',
              id: ui.item.id,
              phone: tgtPhone
            }).then(function (data) {
              console.log(data)
              if (!data.success || ('error' in data)) {
                Vtiger_Helper_Js.showPnotify({
                  title: 'Failed to save ' + tgtPhone,
                  type: 'info'
                })
                $btn.off()
                $btn.disable()
                return
              }
              $card.find('.newentity').remove()
              // TODO extract wrapper
              _.customerLookupDeep(tgtPhone).then(function (data) {
                let valid = data.result &&
                  data.result instanceof Array &&
                  data.result.length > 0
                // Saved but no info?
                if (!valid) {
                  _.renderCreate($card)
                  return
                }

                let html = ''
                data.result.map(x => html += _.showRelations(x))
                $card.find('.crmdata')
                  .append(html)
                  .removeClass('hide')
              })
              // hide create, clear related, insert related
            })
          })

          $btn.enable()
        }
      })
    },

    acceptEntityPhone: function ($card, $btn, id, phone) {
      const _ = this
      AppConnector.request({
        module: 'PBXManager',
        action: 'SavePhone',
        id: id,
        phone: tgtPhone
      }).then(function (data) {
        console.log(data)
        if (!data.success || ('error' in data)) {
          Vtiger_Helper_Js.showPnotify({
            title: 'Failed to save ' + tgtPhone,
            type: 'info'
          })
          $btn.off()
          $btn.disable()
          return
        }
        $card.find('.newentity').remove()
        // TODO extract wrapper
        _.customerLookupDeep(tgtPhone).then(function (data) {
          let valid = data.result &&
            data.result instanceof Array &&
            data.result.length > 0
          // Saved but no info?
          if (!valid) {
            _.renderCreate($card)
            return
          }

          let html = ''
          data.result.map(x => html += _.showRelations(x))
          $card.find('.crmdata')
            .append(html)
            .removeClass('hide')
        })
        // hide create, clear related, insert related
      })
    },

    /**
     * Async forward list fill
     */
    fillForward: function ($container, record) {
      let _ = this
      if (_.redirectOpts) {
          return $container.find('.forward-list').html(_.redirectOpts);
      }

      _.getUsers(
        record.pbxmanagerid,
        record.ownername,
        record.answeredby
      ).then(
        function (res) {
          if (!res || Object.keys(res).length == 0) return

          _.phoneList = res
          let htmlUsers = `<option selected disabled value="">${apptrans('User')}</option>`;
          /*
          var htmlUsers = '<optgroup label="Пользователь">';
          htmlUsers += '</optgroup>'
          */
          for (let id in _.phoneList) {
            let selected
              = (_.phoneList[id] == record.ownername && record.ownername != user)
                ?'selected':''
            htmlUsers += `<option value="${id}" ${selected}>${_.phoneList[id]}</option>`
          }
          _.redirectOpts = htmlUsers;

          if (_.opts.lookupUsers) {
            let PhoneA = record.customernumber.toString(),
              PhoneB = record.ownername.toString(),
              pbxid = record.pbxmanagerid,
              $crm = $container.find('.crmdata'),
              crmExtens = Object.keys(_.phoneList),
              showCrm = false
            if (crmExtens.includes(PhoneA)) {
              showCrm = true
              $crm.append(`<a class="inverted" href="#">${PhoneA} : ${_.phoneList[PhoneA]}</a>`)
              //$crm.append(`<div>${PhoneA} : ${_.phoneList[PhoneA]}</div>`)
            }

            // internal 2
            if (crmExtens.includes(PhoneB)) {
              showCrm = true
              $crm.append(`<a class="inverted" href="#">${PhoneB} : ${_.phoneList[PhoneB]}</a>`)
              //$crm.append(`<div>${PhoneB} : ${_.phoneList[PhoneB]}</div>`)
            }

            if (showCrm) $crm.removeClass('hide')
          }

          return $container.find('.forward-list').html(_.redirectOpts);
        },
        function (jqXHR, textStatus) {
          console.log("Failed to get users.", textStatus);
        })
    },

    validate: function () {

    },

    /**
     * TBD Choose between new window / quick create
     */
    createEntity: function() {
      if (!this.validate()) {
        return _.dbg('Invalid params')
      }

      switch (_.createType) {
        case 'quick':
        case 'instant':
        default:
      }
    },

    quickUrl: function (setype, args) {

    },

    dbg: function (...args) {
      if (!this.DBG) return
      console.log(...args)
    },

    /**
     * PINstudio begin @binizik
     *
     * @param recordid
     * @param ownername?
     * @param user
     * @return html options
     */
    getUsers: function (recordId, ownername, user) {
        return $.post(
          "index.php",
          {
            module: 'PBXManager',
            action: 'IncomingCallPoll',
            mode: 'getUsers'
          }
        )
    },

    getSourceSiteList: function () {
      var htmlUsers = '<option value="">Сайт источник</option>';

      $.ajax({
        type: "POST",
        url: "index.php",
        data: "module=PBXManager&action=IncomingCallPoll&mode=getSourceSiteList",
        dataType: "json",
        async: false,
        success: function (res) {
          var selected = '';
          for (id in res) {
            htmlUsers += '<option value="' + res[id] + '">' + res[id] + '</option>';
          }
        },
        error: function (jqXHR, textStatus) {
          console.log("Failed source site list. " + textStatus);
        }
      });

      return htmlUsers;
    },

    /**
     * return Promise?
     */
    requestRedirect: function (uuid, target) {
      var _ = this
      var params = {
        'module'   : 'PBXManager',
        'action'   : 'RedirectCall',
        'recordid' : uuid,
        'to'       : target
      }
      $.ajax({
        type: "POST",
        url: "index.php",
        data: $.param(params),
        success: function (res) {
          let msg = ''
          if (res.result == 'Success') {
            msg = `${pbxtrans('FWDPEND')} ${_.phoneList[target]} :  ${target}`
            _.removeCallPopup(uuid)
          } else {
            msg = `${pbxtrans('FWDERR')} ${res}`
          }
          bootbox.alert(msg)
        },
        error: function (jqXHR, textStatus) {
          bootbox.alert(pbxtrans('FWDERR'))
        }
      });
    },

    /**
     * Main content
     */
    getCardTemplate: function (pbxid, customernumber, to) {
      /* TODO preloader
      <div class="preloader text-center"><img src="layouts/v7/skins/images/loading.gif"/></div>
       */
      let _ = this,
        tgt = _.opts.showTarget?` &gt;&gt;&gt; <span>${to}</span>`:'',

        setCustomer = `<div class="input-group mb-1" style="float: none">
          <input class="form-control tgtcon" type="text" placeholder="Поиск"/>
          <span class="input-group-btn">
            <button class="btn btn-success accept" disabled>
              <i class="fa fa-check"></i>
            </button>
          </span>
        </div>`,

        newEntity = `<div class="hide newentity pv-1 bb1">
          <h4 class="pb-1">${pbxtrans('CREATE')}:</h4>
          <input class="form-control tgtname mb-1" type="text" placeholder="${pbxtrans('Description')}"/>
          <input class="form-control tgtphone" readonly value="${customernumber}" type="text" placeholder="${apptrans('Phone')}"/>
          <h5 class="alert-danger hide span3" id="alert_msg" style="margin-left: 0px;">${apptrans('JS_PBX_FILL_ALL_FIELDS')}</h5><br>
          <div class="create"></div>
        </div>`,

        forwarding = `<div class="forwarding hide pv-1 bb1">
          <h4>${pbxtrans('FWDTO')}:</h4>
          <select recordid="${pbxid}" class="mb-1 forward-list form-control" id="redirect_to_user_${pbxid}">
            <option selected disabled value="">${apptrans('User')}</option>
          </select>
          <div class="input-group">
            <input class="form-control forwardto" id="redirect_to_${pbxid}" type="text" placeholder="${pbxtrans('EXT')}"/>
            <span class="input-group-btn">
              <button recordid="${pbxid}" id="redirect_${pbxid}" class="btn btn-success forward">
                <i class="fa fa-phone"></i>
              </button>
            </span>
          </div>
        </div>`

      return `<div class="row-fluid pbxcall" id="pbxcall_${pbxid}" callid="${pbxid}">
        <h4 class="pv-1 textAlignCenter"><span>${customernumber}</span>${tgt}</h4>
        ${_.opts.allowAssign?setCustomer:''}
        <div class="crmdata hide pv-1 bb1"></div>
        ${newEntity}
        ${_.opts.allowForward?forwarding:''}
      </div>`.replace(/\n/gm,"");
    },

    /**
     * Card specific actions
     */
    getDropdown: function () {
      return `<div class="dropdown pull-right">
        <a href="#" class="dropdown-toggle icon-align-left" data-toggle="dropdown"></a>
        <ul class="dropdown-menu" style="left:auto; right:0">
          <li><a href="#">Call info</a></li>
          <li><a href="#">Toggle</a></li>
          <li><a href="#">Pause</a></li>
          <li class="divider"></li>
          <li><a href="#">Add to blacklist</a></li>
          <li><a href="#">Close</a></li>
        </ul>
      </div>`.replace(/\n/gm,"")
    },

    /**
     * Create crmentity based on which button was pressed
     * TODO get rid of pbx action, use respective module
     *
     * @param event
     * @param websocket data ?
     */
    createRecord: function(e, record) {
      var pbxmanagerid = jQuery(e.currentTarget).attr('recordid');
      var name = jQuery('#name_'+pbxmanagerid+'').val();
      //last_name = jQuery('#last_name_'+pbxmanagerid+'').val(),
      //var moduleName = jQuery('#module_'+pbxmanagerid+'').val();
      var moduleName  = jQuery(e.currentTarget).data('mod');

      var number = jQuery('.from','#pbxcall_'+pbxmanagerid+'').text();
      var args = {
        'module'   : 'PBXManager',
        'action'   : 'IncomingCallPoll',
        'mode'     : 'createRecord',
        'modulename': moduleName,
        'number'   : encodeURIComponent(number),
        'name'     : encodeURIComponent(name),
        //'last_name': encodeURIComponent(last_name),
        'callid'   : record.sourceuuid
      }
      var url = 'index.php?' + $.param(args);
      AppConnector.request(url).then(function(data) {
        if (data.success && data.result) {
           jQuery('#contactsave_'+pbxmanagerid+'').hide();
           window.open('/index.php?module='+moduleName+'&view=Detail&record=' + data.result); // PINstudio @binizik
        }
      });
    },

    /**
     * Show related Entities
     *
     * @param card container
     * @param WS data
     *
     * @return void
     */
    findRelated: function($container, record) {
      var _ = this,
        PhoneA = record.customernumber.toString(),
        PhoneB = record.ownername.toString(),
        pbxid = record.pbxmanagerid,
        $crmdata = $container.find('.crmdata'),
        crmExtens = Object.keys(_.phoneList),
        showCrm = false

      // external 1 - from
      if (PhoneA.length > _.maxLength) {
        if (_.opts.detailed) {
          _.customerLookupDeep(PhoneA).then(function (data) {
            let valid = data.result &&
              (data.result instanceof Array) &&
              (data.result.length > 0)
            if (!valid) {
              _.renderCreate($container)
              return
            }

            let html = ''
            data.result.map(x => html += _.showRelations(x))
            $crmdata
              .append(html)
              .removeClass('hide')
          })
        } else {
          _.customerLookup(PhoneA).then(function (data) {
            _.calls[pbxid].related = data.result;
            if (!data.result) {
              _.renderCreate($container)
              return
            }
            _.processLookupResult($crmdata, data.result)
          })
        }
      }

      // external 2 - to
      if (PhoneB.length > _.maxLength) {
        _.customerLookup(PhoneB).then(function (data) {
          if (!data.result) {
            return
          }
          _.processLookupResult($crmdata, data.result)
        })
      }

      // TODO link to user settings
      // internal 1
      if (_.opts.lookupUsers) {
        if (crmExtens.includes(PhoneA)) {
          showCrm = true
          //$crmdata.append(`<div>${PhoneA} : ${_.phoneList[PhoneA]}</div>`)
          $crmdata.append(`<a class="inverted" href="#">${PhoneA} : ${_.phoneList[PhoneA]}</a>`)
        }

        // internal 2
        if (crmExtens.includes(PhoneB)) {
          showCrm = true
          $crmdata.append(`<a class="inverted" href="#">${PhoneB} : ${_.phoneList[PhoneB]}</a>`)
        }

        if (showCrm) $crmdata.removeClass('hide')
      }
    },

    renderCreate: function ($container) {
      let _ = this
      if (!_.allowSave) {
        return
      }
      let $btns = $container.find('div.create')
      _.renderSaveBtns($btns)
      $container.find('.newentity').removeClass('hide');
      _.initSaveBtns($btns)
    },

    /**
     * add save buttons according to options
     * @param $node parent element for buttons
     * @return void
     */
    renderSaveBtns: function ($btns) {
      let _ = this
      let html = Object.keys(_.createTypes).reduce((a,x)=>{
        if (!_.createTypes[x].active) return a
        return a +
          `<button class="btn btn-success" data-mod="${x}">${pbxtrans('NEW')} ${_.createTypes[x].label}</button>`
      }, '')

      $btns.html(html)
    },

    /**
     * Bind events
     * TODO two field arg is bad decision. update
     */
    initSaveBtns: function ($controls) {
      let _ = this
      $controls.find('button').each((i,x)=>{
        $(x).click((e)=>{
          let $inputs = $controls.parent()
          _.saveBtnHandler(
            x,
            $inputs.find('.tgtname').val(),
            $inputs.find('.tgtphone').val()
          )
        })
      })
    },

    /**
     * save Handler. fill params and take actions
     * QuickCreate / Redirect to edit page
     *
     * @param node  - dom element (not jq)
     * @param name  - name input value
     * @param phone - phone input value
     *
     * @returns void
     */
    saveBtnHandler: function (node, name, phone) {
      let _ = this,
        tgtModule = node.dataset['mod'],
        tokens = name.split(' '),
        gotFIO = (tokens.length > 1) && ['Contacts', 'Leads'].includes(tgtModule),
        params = {
          module: tgtModule,
        }

      // validate
      // TODO single space entityName.replace(/\s+/, ' ')
      if (gotFIO) {
        // expecting F + I
        params[_.createTypes[tgtModule].crm.name] = tokens[1]
        params.lastname = tokens[0]
      } else {
        params[_.createTypes[tgtModule].crm.name] = name
      }

      params[_.createTypes[tgtModule].crm.phone] = phone

      switch (_.createAction) {
        case 'Quick':
          params.action = 'SaveAjax'
          AppConnector.request(params).then(result => {
            _.dbg(result)
            // TODO append/reload .crmdata
          })
        break
        case 'Edit':
        default:
          params.view = 'Edit'
          window.open('index.php?' + $.param(params))
      }

      return _.dbg(params)
      bootbox.alert(JSON.stringify(params))
    },

    /**
     * Crm find user with extension
     *
     * @param {phone}
     *
     * @returns Deferred
     */
    extenLookup : function (phone) {
      return AppConnector.request({
        module : 'PBXManager',
        action : 'Lookup',
        mode   : 'exten',
        phone  : phone
      })
    },

    /**
     * Crm find customer with phone
     *
     * @param {phone}
     *
     * @returns Deferred
     */
    customerLookup : function (phone) {
      return AppConnector.request({
        module : 'PBXManager',
        action : 'Lookup',
        phone : phone
      })
    },

    /**
     * Crm find customer with phone
     *
     * @param {phone}
     *
     * @returns Deferred
     */
    customerLookupDeep : function (phone) {
      return AppConnector.request({
        module : 'PBXManager',
        action : 'GetRelated',
        phone : phone
      })
    },

    /**
     * merge, validate?
     * lookup specific
     * simple linear links to entities
     */
    processLookupResult : function ($crmdata, entities) {
      //this.showRelated(pbxid, (entities instanceof Array)?entities:[entities])
      if (!entities) return
      let _ = this,
        data = (entities instanceof Array)?entities:[entities],
        html = data.map(function(x,i){
          return _.toHref(x)
        }).join("\n")

      $crmdata
        .append(html)
        .removeClass('hide')
    },

    processDeepResults: function ($card, data) {
    },

    /**
     * Display related specific
     * convert response data to anchors
     *
     * @param {pbxid} call id
     * @param {data}  lookup results
     *
     * @returns void
     */
    showRelated : function (pbxid, data) {
    },

    /**
     * Complex hierarchical data
     */
    showRelations: function (entity) {
      let html = '<hr/>',
        icon = this.getIcon(entity.setype)
      html += this.anchor(entity.link, icon + entity.label, {css: 'inverted'})
      html += this.props2list(entity.props)
      html += '<div class="linked">'
      if ('related' in entity) {
        let relIco = this.getIcon(entity.related.setype)
        html += this.anchor(entity.related.link, relIco + entity.related.label)
        html += this.props2list(entity.related.props)
      }
      if ('owner' in entity) {
        let label = (typeof entity.owner == 'object')
          ? this.anchor(entity.owner.link, entity.owner.label)
          : entity.owner
        html += `<hr/><b>${apptrans('Assigned To')}</b>: ${label}`
      }
      html += '</div>'

      return html
    },

    getIcon: function (modname) {
      let map = {
        'Accounts': 'fa-bank',
        'Contacts': 'fa-user',
        'Leads': 'fa-indent'
      }
      let gotIcon = (modname in map)
      if (!gotIcon) return ''

      return `<i class="fa ${map[modname]}"></i> `
    },

    /**
     * TODO fa image?
     *
     * @param obj
     *   id
     *   setype
     *   name
     *
     * @return html anchor
     */
    toHref: function (o) {
      let args = {
          module : o.setype,
          view   : 'Detail',
          record : o.id
        },
        params = 'index.php?' + $.param(args),
        icon = this.getIcon(o.setype)

      return this.anchor(params, icon + o.name)
    },

    anchor: function (url, label, opts) {
      let args = opts || {}
      let css = ''
      if ('css' in args) {
        css = `class="${args.css}"`
      }
      return `<a href="${url}" target="_blank" ${css}>${label}</a>`
    },

    /**
     * Convert plain object to keyvalue table
     */
    toList: function (o) {
      return '<table class="table-condensed">'
        + Object.keys(o).map(function (x) {
          return `<tr><td><b>${x}</b></td><td>${o[x]}</td></tr>`
        }).join('')
        + '</table>'
    },

    props2list: function (props) {
      let data = {}
      Object.keys(props).map(x => data[props[x].label] = props[x].value)

      return this.toList(data)
    },

    /**
     * Display public properties
     */
    showOptions: function () {
      // TODO wrap in vtui
      bootbox.alert(this.toList({
        'Макс. длинна внутреннего' : this.maxLength,
        'Лимит карточек' : this.maxPopups,
        'Тип ограничения' : this.limitType,
        'Сохранение разрешено' : this.allowSave,
        'Типы Сущностей' : this.getActive().join(),
        'Зарегистрировано внтуренних' : Object.keys(this.phoneList).length,
        'Обработано карточек' : Object.keys(this.calls).length,
      }))
    },

    /**
     * Active crm types
     */
    getActive: function () {
      let _ = this
      return Object.keys(_.createTypes).filter(x => {
        return _.createTypes[x].active == 1
      })
    },

    /**
     * Take actions before showing another card
     *
     * @param pbxid save message
     *
     * @return bool allow next card open or not
     */
    borderConditions: function (pbxid) {
      var _ = this,
        allow = true,
        $cards = $(_.baseClass)

      switch (_.limitType) {
        // aka first in first out, stack, etc
        case 'revolve':
          if ($cards.length < _.maxPopups) {
            break
          }
          // remove first
          $cards.each(function (i, x) {
            if (i > ($cards.length - _.maxPopups)) return
            let $tgt = $(x)
            _.pendRemove($tgt)
          })
          allow = true
          break
        // first in last out - wait until first call is ended
        case 'filo':
          /*
          if ($cards.length < _.maxPopups) {
            break
          }
          allow following cards folded til hardLimit reached

          */
        case 'next':
        default:
          if ($cards.length < _.cardsHardLimit) {
            break
          }
          let msg = 'Limit reached ' + _.cardsHardLimit
          _.calls[pbxid].rejected = msg
          _.dbg(msg, pbxid)
          allow = false
      }

      return allow
    },

    /**
     * TODO Remove from DOM + from window.data(pnotify)
     */
    pendRemove: function($tgt) {
      // Change appearence
      $tgt.css('filter', 'grayscale()')
      //$tgt.find('.ui-pnotify-container').addClass('folded')
      let pNode = $tgt[0]
      setTimeout(()=>{
        // pnotify storage
        let notices = $(window).data('pnotify')
        if (!notices) return
        // find in storage
        let idx = $.inArray(pNode, notices.map((x,i)=>x[0]))
        let exists = (idx != -1) && notices[idx]
        if (exists) {
            notices[idx].pnotify_remove()
        }
        if ($tgt.length == 1){
            $tgt.remove()
        }
      }, Vtiger_PBXManager_Js.opts.hideDelay * 1000)
    },

    phoneLookup : function (phone) {
      $.getScript('resources/render.js')
      phone = phone || '79140059149'
      AppConnector.request({
        module: 'PBXManager',
        action: 'Phone',
        phone: phone
      }).then(x => app.modal({
        title: 'Записи о номере ' + phone,
        body: R.table(x.result)
      }))
    },

    setExten: function (v) {
      return AppConnector.request({
        exten: v,
        module: 'PBXManager',
        action: 'SetExten'
      })
    },

    /**
     * Function registers PBX for popups
     */
    registerPBXCall : function() {
      Vtiger_PBXManager_Js.requestPBXgetCalls();
    },

// ---------- admin panel ----------

    /**
     * Appends call info to admin panel
     * @param {object} data ws call info
     * @return void
     */
    toPanel: function (data) {
      let isPbx = (app.getParentModuleName() == 'Settings') &&
        (app.getModuleName() == 'PBXManager')
      if (!isPbx) return

      let rLimit = 13,
        $panel = this.getPanel(),
        tabts = new Date().toLocaleString(),
        row = this.toRow([
          tabts,
          data.type,
          data.target,
          data.event,
          data.from,
          data.to,
          data.call_id
        ]),
        $rowHandle = $(row)
      $panel.append($rowHandle)
      if ($panel.find('tr').length > rLimit) {
        $panel.find('tr:nth-child(2)').remove();
      }
    },

    /**
     * Retrieve panels table node, jQuery
     * appends new one to body in case if node doesnt exist
     * @return {ref} jquery table reference
     */
    getPanel: function () {
      let $panel = $('#panel table')
      if ($panel.length == 1) {
          return $panel
      }

      let html = `<div id="panel"><table class="table">
        <tr>
          <th>timestamp</th>
          <th>type</th>
          <th>target</th>
          <th>event</th>
          <th>from</th>
          <th>to</th>
          <th>uuid</th>
        </tr>
        </table></div>`
      $('body').append(html)
      let $node = $('#panel')
      $node.find('th').click(e=>$node.toggleClass('compact'))
      return $node.find('table')
    },

    /**
     * Wraps array in a table row
     * @param {array} data array of values
     * @return {str} html row
     */
    toRow: function (a) {
      return '<tr>'
        + a.map(x=>`<td>${x}</td>`).join('')
        + '</tr>'
    },

// ------------- Deprecated below this line ------------------
    /**
     * Function registers PBX for Outbound Call
     */
    registerPBXOutboundCall : function(number,record) {
      Vtiger_PBXManager_Js.makeOutboundCall(number,record);
    },

    /**
     * Function request for PBX popups
     * @deprecated
     */
    requestPBXgetCalls : function() {
      var thisInstance = this;
      console.log('Poll #' + thisInstance.callsPollFunctionId);
      var url = 'index.php?module=PBXManager&action=IncomingCallPoll&mode=searchIncomingCalls';
      AppConnector.request(url).then(function(data){
        if (data.success && data.result) {
          for (var i=0; i< data.result.length; i++) {
            var record  = data.result[i];
            if(jQuery('#pbxcall_'+record.pbxmanagerid+'').size()== 0 )
              Vtiger_PBXManager_Js.showPBXIncomingCallPopup(record);
            else
              Vtiger_PBXManager_Js.updatePBXIncomingCallPopup(record);
          }
        }
      });
      Vtiger_PBXManager_Js.removeCompletedCallPopup();
    },

    /**
     * Function to update the popup with answeredby, hide contactsave option e.t.c.,
     */
    updatePBXIncomingCallPopup: function(record){
      if (record.answeredby!=null){
        jQuery('#answeredbyname','#pbxcall_'+record.pbxmanagerid+'').text(record.answeredby);
        jQuery('#answeredby','#pbxcall_'+record.pbxmanagerid+'').show();
      }

      if (record.customer!=null && record.customer!=''){
        jQuery('#caller','#pbxcall_'+record.pbxmanagerid+'')
          .html(app.vtranslate('JS_PBX_CALL_FROM') + ' :&nbsp;<a href="index.php?module='+record.customertype+'&view=Detail&record='+record.customer+'">'+record.callername+'</a>');
        jQuery('#contactsave_'+record.pbxmanagerid+'').hide();
      }

      //To remove the popup for all users except answeredby (new record)
      if(record.user) {
        if(record.user != record.current_user_id) {
          Vtiger_PBXManager_Js.removeCallPopup(record.pbxmanagerid);
        }
      }
    },

    /**
     * Function to remove the call popup which is completed
     * @deprecated
     */
    removeCompletedCallPopup: function() {
      var callid = null;
      var pbxcall = jQuery('.pbxcall');
      for(var i=0; i<pbxcall.length; i++){
        callid = pbxcall[i].getAttribute('callid');
        var url = 'index.php?module=PBXManager&action=IncomingCallPoll&mode=getCallStatus&callid='+encodeURIComponent(callid)+'';
        AppConnector.request(url).then(function(data){
          if (!data.result) {
            return
          }
          if (data.result!='in-progress' && data.result!='ringing'){
            Vtiger_PBXManager_Js.removeCallPopup(callid);
          }
        });
      }
    },

    /**
     * Find and queue card removal
     * @param string callid
     */
    removeCallPopup: function(callid) {
      const _ = this
      // TODO process status (comments, etc)
      $('#pbxcall_'+callid+'').each((i,x) => {
        let $container = $(x).closest(_.baseClass)
        _.pendRemove($container);
      })
    },

    /**
     * To get contents holder based on the view
     */
    getContentHolder:function(view){
      if (view == 'List')
        return jQuery('.listViewContentDiv');
      else
        return jQuery('.detailViewContainer');
    },

    /**
     * Function to forward call to number
     */
    makeOutboundCall : function(number, record){
      AppConnector.request({
        'number' : number,
        'record' : record,
        'module'  : 'PBXManager',
        'action' : 'OutgoingCall'
      }).then(function(data){
        if (data.result) {
          params = {
            'text' :  pbxtrans('OUTGOINGOK'),
            'type' : 'info'
          }
        } else {
          params = {
            'text' :  pbxtrans('OUTGOINGFAIL'),
            'type' : 'error'
          }
        }
        // TODO v7ui
        Vtiger_Helper_Js.showPnotify(params);
      });
    },

    //SalesPlatform.ru begin
    /**
     * @deprecated
     */
    registerPollHooks : function() {
      var thisInstance = this;
      $(window).on('blur', function() {
        if(thisInstance.callsPollFunctionId !== false) {
          console.log("PBX pause #" + thisInstance.callsPollFunctionId);
          clearInterval(thisInstance.callsPollFunctionId);
          thisInstance.callsPollFunctionId = false;
        }
      });

      $(window).on('focus', function() {
        if (thisInstance.callsPollFunctionId === false) {
          Vtiger_PBXManager_Js.registerPBXCall();
          thisInstance.callsPollFunctionId = setInterval("Vtiger_PBXManager_Js.registerPBXCall()", 4000);
          console.log("PBX restart #" + thisInstance.callsPollFunctionId);
        }
      });
    },
    //SalesPlatform.ru end

     /**
      * Function to register required events
      */
     registerEvents : function(){
       return false;
       var thisInstance = this;
       //for polling
       var url = 'index.php?module=PBXManager&action=IncomingCallPoll&mode=checkPermissionForPolling';
       AppConnector.request(url).then(function(data){
         if(data.result) {
           //SalesPlatform.ru begin
           if(document.hasFocus()
             //&& thisInstance.callsPollFunctionId === false
           ) {
             Vtiger_PBXManager_Js.registerPBXCall();
             thisInstance.callsPollFunctionId = setInterval("Vtiger_PBXManager_Js.registerPBXCall()", 4000);
             console.log("PBX init #"+thisInstance.callsPollFunctionId);
           }
           thisInstance.registerPollHooks();
           //Vtiger_PBXManager_Js.registerPBXCall();
           //setInterval("Vtiger_PBXManager_Js.registerPBXCall()", 3000);
           //SalesPlatform.ru end
         }
       });
    }
}

$(function() {
  $.get('index.php?module=PBXManager&action=getCfg')
    .done(cfg => Vtiger_PBXManager_Js.preLoad(cfg))
    .fail(e => console.log('No PBX configuration loaded'))
})

/**
 * Factory, es6
 * require fontawesome, popover
 */
function initPbxIcon(exten) {
  //'use strict'
  if (!exten) return;

  let template = vtui.getIconTpl(exten)
  let iconhandler = vtui.getIconHandler()
  var $node = iconhandler.before(template)

  var _ = {
    $pbx : $('.pbx'),
    state : false,
    shown : false
  }

  if (!('popover' in _.$pbx)) return

  _.$pbx.popover({
    title: 'Введите данные',
    html: true,
    //selector: 'pbxSettings',
    placement : 'bottom',
    trigger: 'manual',
    content: `<button id="cardreset" class="btn btn-success row-fluid form-control">Remove all popups</button>
      <button id="carddisable" class="btn btn-info row-fluid form-control">Toggle popups <span id="cardstate"></span></button>
      <button id="showdetail" class="btn btn-info row-fluid form-control">Display Details</button>
      <button id="showopts" class="btn btn-info row-fluid form-control">Show Info</button>
      <button id="settings" class="btn btn-info row-fluid form-control">Settings</button>
      <button id="promptexten" class="btn btn-warning row-fluid form-control">Set phone</button>`
      //<button id="showtgt" class="btn btn-info row-fluid form-control">Display PhoneB</button>
  })

  _.$pbx.click((e)=>{
    _.$pbx.popover('toggle')
    _.shown = !_.shown

    // Prevent double initialization
    if (!_.shown) {
      return
    }

    var $extraClose = $('.popover-title').append('<div class="extraClose"><span class="icon-remove"></span></div>')
    $('.extraClose').click(function(){off()})
    let txtStatus = _.state?'Disabled':'Enabled'
    $('#cardstate').text(txtStatus)

    $('#cardreset').click(function(e){
      off()
      $(window).data('pnotify').forEach(x=>{x.pnotify_remove()})
      $.pnotify_remove_all()
    })

    $('#carddisable').click(function(e){
      off()
      let state = !Vtiger_PBXManager_Js.disableCards
      _.state = state
      Vtiger_PBXManager_Js.disableCards = state
      let txtStatus = state?'Disabled':'Enabled'
      vtui.notify('Cards are ' + txtStatus)
    })

    $('#showtgt').click(function(e){
      off()
      Vtiger_PBXManager_Js.opts.showTarget = !Vtiger_PBXManager_Js.opts.showTarget
      vtui.notify('Toggle Phone B')
    })

    $('#showdetail').click(function(e){
      off()
      Vtiger_PBXManager_Js.opts.detailed = !Vtiger_PBXManager_Js.opts.detailed
      vtui.notify('Toggle Detailed CRM info')
    })

    $('#showopts').click(function(e){
      off()
      Vtiger_PBXManager_Js.showOptions()
    })

    $('#settings').click(function(e){
      off()
      // TODO update according to version
      window.location = '/index.php?module=PBXManager&parent=Settings&view=Index'
    })

    $('#promptexten').click(function(e){
      off()
      bootbox.prompt('Please enter your extension', function(x) {
        if (!x) return;

        Vtiger_PBXManager_Js.setExten(x)
          .then(y => {
            vtui.notify('Reloading page to reflect changes')
            setTimeout(() => window.location.reload(false), 2000)
          })
          .fail(z =>
            vtui.notify('Error updating extension')
          )
      })
    })
  })

  function off()
  {
    _.shown = false
    _.$pbx.popover('hide')
  }

  return _
}

function pbxtrans(k)
{
  let current = Vtiger_PBXManager_Js.opts.lang || 'en_us';
  let langs = {
    'ru_ru' : {
      'CREATE' : 'Создать',
      'INCOMINGCALL' : 'Входящий',
      'OUTGOINGCALL' : 'Исходящий',
      'INTERNALCALL' : 'Внутренний',
      'NEW' : '',
      'EXT' : 'Номер',
      'FWDTO' : 'Переадресовать',
      'FWDERR' : 'Во время переадресации произошла ошибка',
      'FWDPEND' : 'Звонок был переведен.',
      'OUTGOINGOK': 'Отправлен запрос на создание звонка',
      'OUTGOINGFAIL': 'Невозможно создать звонок',
      'REQUIREFWDEXTEN' : 'Укажите номер для перевода',
      'Description': 'Наименование / ФИO'
    },
    'en_us' : {
      'CREATE' : 'Create',
      'INCOMINGCALL' : 'Incoming',
      'OUTGOINGCALL' : 'Outgoing',
      'INTERNALCALL' : 'Internal',
      'NEW' : 'New',
      'EXT' : 'Extension',
      'FWDTO' : 'Forward to',
      'FWDERR' : 'Error forwarding',
      'FWDPEND' : 'Forwarding',
      'OUTGOINGOK': 'Outgoing call request is successful',
      'OUTGOINGFAIL': 'Unable to make call',
      'REQUIREFWDEXTEN' : 'Please, specify an extension number'
    },
    'de_de' : {
      'CREATE' : 'Create',
      'INCOMINGCALL' : 'Incoming',
      'OUTGOINGCALL' : 'Outgoing',
      'INTERNALCALL' : 'Internal',
      'NEW' : 'New',
      'EXT' : 'Extension',
      'FWDTO' : 'Forward to',
      'FWDERR' : 'Error forwarding',
      'FWDPEND' : 'Forwarding',
      'OUTGOINGOK': 'Outgoing call request is successful',
      'OUTGOINGFAIL': 'Unable to make call',
      'REQUIREFWDEXTEN' : 'Please, specify an extension number'
    }
  }

  return (langs[current] && (k in langs[current]))? langs[current][k]: k
}

function apptrans(k)
{
  return app.vtranslate(k)
}

class VTUI {
  constructor (ngn) {
    this.ngn = ngn
  }

  notify (msg) {
    this.ngn.notify(msg)
  }

  alert (msg) {
    this.ngn.alert(msg)
  }

  getIconTpl (exten) {
    return this.ngn.getIconTpl(exten)
  }

  getIconHandler () {
    return this.ngn.getIconHandler()
  }

  showCard (args) {
    return this.ngn.showCard(args)
  }

  dialog (title, ui, buttons) {
    return this.ngn.dialog(title, ui, buttons)
  }
}

class V6ui {
  notify (msg) {
    Vtiger_Helper_Js.showPnotify({
      text: msg,
      type: 'info'
    })
  }

  alert (msg) {
    app.showModalWindow({
      'data': msg,
      'css' : {'padding': '20px', 'max-width':'400px'}
    });
  }

  getIconTpl (exten) {
    return `<i class="fa fa-phone-square pbx" title="Page: ${(new Date).toLocaleString()}"> ${exten}</i>`
  }

  getIconHandler () {
    return $('#headerLinksBig > span:first-child')
  }

  showCard (params) {
    Vtiger_Helper_Js.showPnotify(params)
  }

  dialog (title, ui, buttons) {
    bootbox.dialog(
      ui,
      buttons,
      {
        header: title,
        headerCloseButton: true
      }
    )
  }
}

class V7ui {
  notify (msg) {
    Vtiger_Helper_Js.showPnotify({title: msg})
  }

  alert (msg) {
    bootbox.alert({'message': msg});
  }

  getIconTpl (exten) {
    return `<li class="cursorPointer">
      <i class="fa fa-phone-square pbx pv-1" title="Page: ${(new Date).toLocaleString()}"> ${exten}</i>
    </li>`
  }

  getIconHandler () {
    return $('#navbar .navbar-nav>li:first-child')
  }

  showCard (params) {
    // TODO v7org notify
    return $.pnotify($.extend({
      sticker: false,
      delay: '3000',
      type: 'error',
      pnotify_history: false
    }, params));
  }

  dialog (title, ui, buttons) {
    bootbox.dialog({
      title: title,
      message: ui,
      buttons: buttons,
      headerCloseButton: true
    })
  }
}
