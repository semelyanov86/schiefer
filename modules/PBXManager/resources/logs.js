(function(){
  'use strict';
  initCells();
  $('#clearLogs').click(function(){
    var params = {
      'module': 'PBXManager',
      'action': 'Logs',
      'mode'  : 'clean'
    };
    AppConnector.request(params)
      .done(function(x){ console.log('Table is clean')})
      .fail(function(e){ console.log(e)})
  });

  var $filter = $('#filterResults')
  $filter.click(function(){
    $filter.text('Searching...');
    $filter.disable();
    var val = $('[name = filter]').val();
    if (val == ''
      || val === 'undefined'
      || val.length <3
    ) {
      console.log('Invalid: ' + val);
      return;
    }
    var params = {
      'module': 'PBXManager',
      'action': 'Logs',
      'mode'  : 'show',
      'filter': val
    };
    var $container = $('#logsContainer');
    AppConnector.request(params)
      .done(function(x){
        if (!(x.result instanceof Object && ('data' in x.result))){
          $container.html('Not found');
          console.log(x);
          return;
        }

        $container.html(x.result.data);
        initCells();
      })
      .fail(function(e){
        $container.html('Not found');
        console.log(e)
      })
      .always(function(e){
        $filter.text('Search');
        $filter.enable();
      })

  });

  function initCells()
  {
    var $container = $('#logsContainer');
    var cells = $container.find('td:nth-child(even)')
    if (cells.length < 1) return;

    cells.each(function (i,x){
      x.onclick = display;
    });
  }

  function display(e)
  {
    var data = '<h2>Event Info</h2><div class="wrap">';
    try {
      var inf = JSON.parse(this.innerText);
    } catch(e) {
      return;
    }
    data += '<table class="table logInfo table-condensed">';
    for (var k in inf){
      data += `<tr><td>${k}</td><td>${inf[k]}</td></tr>`;
    }
    data += '</table>'
    data += '</div><hr/><button data-dismiss="modal" class="btn pull-right">Ok</button>';
    vtui.alert(data);
  }
})()

