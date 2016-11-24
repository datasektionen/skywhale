
import 'whatwg-fetch'

import React from 'react'
import ReactDom from 'react-dom'

import Elections from './elections.jsx'
import '../../../public/css/component.css'

const front_container = document.getElementById(window.skywhale_config.container_id || 'election');
ReactDom.render(<Elections />, front_container);