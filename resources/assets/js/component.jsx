import 'whatwg-fetch'

import React from 'react'
import ReactDom from 'react-dom'

import Elections from './elections.jsx'

var elemId;
if (window.skywhale_config.container_id) {
	elemId = window.skywhale_config.container_id;
} else {
	elemId = 'election';
}

const front_container = document.getElementById(elemId);
ReactDom.render(<Elections />, front_container);

export default class App extends React.Component {
  render() {
    return (
      <Election />
    )
  }
}
