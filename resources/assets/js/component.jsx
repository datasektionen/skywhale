import 'whatwg-fetch'

import React from 'react'
import ReactDom from 'react-dom'

import Elections from './elections.jsx'

const front_container = document.getElementById('elections');
ReactDom.render(<Elections />, front_container);

export default class App extends React.Component {
  render() {
    return (
      <Election />
    )
  }
}
