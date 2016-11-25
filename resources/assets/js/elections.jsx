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
        <ul className="elections" style={{listStyleType:'none',padding:'0',margin:'0'}}>
            {this.state.election.positions.map(position => <Position key={position.identifier} position={position} />)}
        </ul>
      </div>
    )
  }
}

class Position extends React.Component {
  render() {

    var imgStyle = (imgUrl) => ({
      backgroundRepeat: 'no-repeat',
      backgroundPosition: '50% 0',
      backgroundSize: '40px',
      borderRadius: '50%',
      width: '30px',
      height: '30px',
      display: 'inline-block',
      verticalAlign: 'middle',
      margin: '3px 5px 5px 0px',
      backgroundImage: 'url(https://zfinger.datasektionen.se/user/'+imgUrl+'/image/100)'
    });

    return (
      <li>
        <h3>{this.props.position.title}</h3>
        <p></p>
        <ul style={{listStyleType:'none',padding:'0',margin:'0'}}>
          {this.props.position.nominees.filter(nominee => nominee.status !== 'declined').map(
            nominee => 
              <li style={{listStyleType:'none',padding:'0',margin:'0'}} key={nominee.uuid} className={nominee.status}>
                <div style={imgStyle(nominee.kth_username)}></div>
                &nbsp;{nominee.status === 'accepted' ? 'Accepterat: ' : (nominee.status === 'declined' ? 'Tackat nej: ' : '')}
                {nominee.name} 
              </li>
          )}
        </ul>
      </li>
    )
  }
}