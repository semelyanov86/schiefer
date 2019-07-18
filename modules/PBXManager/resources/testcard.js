(function(){
  var ui = [
    '<label>Клиент</label><input id="f" placeholder="1112233"/>',
    '<label>Внутренний</label><input id="t" placeholder="202"/>',
    '<label>Задержка (сек)</label><input id="d" class="input-mini" type="number" min="7" value="180"/>',
    //'<label>Существующий?</label><input id="e" type="checkbox"/>'
    '<label>Направление</label>'
      + '<select id="type" value="inbound">'
      + '<option value="inbound">Входящий</option>'
      + '<option value="outbound">Исходящий</option>'
      + '<option value="internal">Внутренний</option>'
      + '</select>'
  ].join('<br/>');

  $('.dial').on('click', function(){
    vtui.dialog(
      'Тестовая карточка:',
      ui,
      [{
      //{label: 'Отмена', 'class': 'btn-warning'},
        label: 'Тест!',
        'class': 'btn-success',
        callback: proceed
      }]
    )
  })

  function proceed() {
    let pbxid = 12345,
      from = $('#f').val() || '7776655',
      to   = $('#t').val() || '1000',
      sec  = $('#d').val() || 10,
      delay = parseInt(sec) * 1000,
      //existing = $('#e').is(':checked')
      direction = $('#type').val() || 'inbound'

    if (direction == 'outbound') {
      [from, to] = [to, from]
    }

    let args = {
      pbxmanagerid : pbxid,
      uuid : 54321,
      customernumber: from,
      callername: null,
      ownername : to,
      answeredby: to,
      direction: direction
    };

    Vtiger_PBXManager_Js.showPBXIncomingCallPopup(args);

    setTimeout(function (){
      Vtiger_PBXManager_Js.removeCallPopup(pbxid);
    }, delay);
  }
})()

