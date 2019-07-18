$(function ($) {
  'use strict'
  window.extList = {}
  $.get('index.php?module=PBXManager&action=States').then((response) => {
    let table = getTab(response.result)
    $('#extPanel').html(table)
    bindClick()
  })

  function bindClick() {
    let $anchors = $('.amicall')
    $anchors.click(e => {
      e.preventDefault()
      let a = e.currentTarget
      let url = getHost()
      console.log('clck', a.innerText, url)
    })
  }

  function getHost() {
    if (extList.host) {
      console.log('Host is defined')
      return extList.host
    }
    extList.host = [pbxSocket.config.host, pbxSocket.config.port].join(':')

    return extList.host
  }

  function getTab(o) {
    if (!o) {
      return ''
    }
    return '<table class="span10 table table-striped table-condensed">'
      + Object.keys(o).map(function (x) {
        return '<tr><td><b>'
          + x + '</b></td><td>'
          + o[x].status
          + '</td><td>'
          + (o[x].call_id?getAnchorInfo(o[x].call_id):'--')
          + '</td></tr>'
      }).join('')
      + '</table>'
  }

  function getAnchorInfo(uuid)
  {
    return '<a href="#" class="amicall" target="_blank">' + uuid + '</a>'
  }
})

