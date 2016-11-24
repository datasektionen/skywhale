import React from 'react'

import getElections from './skywhale.jsx'

export default class Elections extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      election: null,
      hash: location.hash
    };

    window.onhashchange = e => this.setState({hash: location.hash})
  }

  componentDidMount() {
    this.getElections()
  }

  getElections(type) {
    getElections().then(json => (
      this.setState({election: json.length === 0 ? null : json[0]})
    )
    )
  }

  render() {
    if (this.state.election === null) {
      return <div>Inga val just nu.</div>
    }
    return (
      <div className="outer">
        <h3>{this.state.election.name}</h3>
        <p><a href="https://val.datasektionen.se">Gå till valsidan</a> om du vill se mer detaljer.</p>
        <p>{this.state.election.description}</p>
        <ul className="elections">
            {this.state.election.positions.map(position => <Position key={position.identifier} position={position} />)}
        </ul>
      </div>
    )
  }
}

class Position extends React.Component {
  render() {

    var imgStyle = (imgUrl) => ({
      backgroundImage: 'url(https://zfinger.datasektionen.se/user/'+imgUrl+'/image/100)'
    });

    return (
      <li>
        <h3>{this.props.position.title}</h3>
        <p></p>
        <ul>
          {this.props.position.nominees.filter(nominee => nominee.status !== 'declined').map(
            nominee => 
              <li key={nominee.uuid} className={nominee.status}>
                <div className="crop" style={imgStyle(nominee.kth_username)}></div>
                &nbsp;{nominee.status === 'accepted' ? 'Accepterat: ' : (nominee.status === 'declined' ? 'Tackat nej: ' : '')}
                {nominee.name} 
              </li>
          )}
        </ul>
      </li>
    )
  }
}