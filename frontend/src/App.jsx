import { useState, useEffect } from 'react'
import './App.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import Languages from './components/Languages'
import OriginalTexts from './components/OriginalTexts'
import Translations from './components/Translations'
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import axios from 'axios'
import Form from 'react-bootstrap/Form'

function App() {
  const [count, setCount] = useState(0)
  const [languages, setLanguages] = useState([])
  const [language, setLanguage] = useState(null)
  const [originalTexts, setOriginalTexts] = useState([])
  const [translations, setTranslations] = useState([])

  useEffect(() => {
    syncData()
  }, [])

  useEffect(() => {
    getTranslatedTexts()
  }, [language])

  const syncData = () => {
    setLanguage([])
    setOriginalTexts([])
    setTranslations([])
    getLanguages()
    getOriginalTexts()
    getTranslatedTexts()
  }

  const onChangeLanguage = (event) => {
    setLanguage(event?.target?.value)
  }

  const getLanguages = () => {
    axios
      .get('http://localhost:8200/api/languages')
      .then(function (response) {
        setLanguages(response?.data?.texts)
      })
      .catch(function (error) {
        console.log(error)
      })
  }

  const getTranslatedTexts = () => {
    if (!language) {
      return true
    }
    setTranslations([])
    axios
      .get(`http://localhost:8200/api/translator/${language}`)
      .then(function (response) {
        setTranslations(response?.data?.texts)
      })
      .catch(function (error) {
        console.log(error)
      })
  }

  const getOriginalTexts = () => {
    axios
      .get('http://localhost:8200/api/original-texts')
      .then(function (response) {
        setOriginalTexts(response?.data?.texts)
      })
      .catch(function (error) {
        console.log(error)
      })
  }
  return (
    <>
      <Container>
        <Row>
          <Col md={5}>
            <Languages sync={syncData} languages={languages} />
          </Col>
          <Col md={7}>
            <OriginalTexts languages={languages} sync={syncData} originalTexts={originalTexts} />
          </Col>
          <Col md={12}>
            <Form.Group className='mb-3' controlId='exampleForm.ControlTextarea1'>
              <Form.Label>Language</Form.Label>
              <Form.Select
                name='language_id'
                onChange={onChangeLanguage}
                aria-label='Default select example'
              >
                <option disabled selected>
                  Select a language
                </option>
                {languages.map(function (value) {
                  return <option value={value?.id}>{value?.language_name}</option>
                })}
              </Form.Select>
            </Form.Group>
            <Translations
              languages={languages}
              sync={syncData}
              translations={translations}
              originalTexts={originalTexts}
            />
          </Col>
        </Row>
      </Container>
    </>
  )
}

export default App
