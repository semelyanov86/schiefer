/*
src = {
  202: {status: 'Ok'},
  205: {status: 'BUSY'}
}
q = new Panel($('#app'))
q.setSrc(src)
q.push(404, {status: 'Fail'})
q.rm(205)
q.push(404, {status: 'Fixed!'})
q.push(606, {status: 'Whee!'})
*/
function Card (ext, opts) {
  opts = opts || {status: 'NA'}
  const tpl = (k, o) => {
    return `<div id="ext${k}" class="exten">
      <label>${k}</label>
      <span class="state">${o.status}<span>
    </div>`
  }

  let $node = $('#ext' + ext),
    fresh = false
  if ($node.length == 0) {
    fresh = true
    $node = $(tpl(ext, opts))
  }
  let $status = $node.find('.state')

  return {
    node: $node,
    fresh: fresh,
    rm: () => $node.remove(),
    setStatus: status => $status.text(status)
  }
}

function Panel ($base) {
  const _ = this
  let Panel = {
    add: div => $base.append(div),
    getSrc: () => _.src
  }

  return {
    root: $base,
    getSrc: Panel.getSrc,
    push: (ext, vals) => {
      _.src[ext] = vals
      let card = new Card(ext, vals)
      if (card.fresh) {
        Panel.add(card.node)
      } else {
        card.setStatus(vals.status)
      }
    },
    rm: ext => {
      let card = new Card(ext)
      card.rm()
      delete(_.src[ext])
    },
    setSrc: x => {
      _.src = x
      $base.html('')
      for (let ext in x) {
        let card = new Card(ext, x[ext])
        Panel.add(card.node)
      }
    }
  }
}
