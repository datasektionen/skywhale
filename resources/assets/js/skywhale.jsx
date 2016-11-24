const skywhale_url = 'https://val.datasektionen.se/api'

const getJson = path => fetch(skywhale_url + path).then(res => res.json())

const getElections = () => getJson('/elections')


export default getElections

export {
  getElections
}