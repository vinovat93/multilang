import { useState } from 'react'
import Table from 'react-bootstrap/Table'
import FormTranslations from './FormTranslations'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import Container from 'react-bootstrap/Container'

function Translations(props) {
  console.log('props', props)
  return (
    <>
      <Container>
        <Row>
          <Col md={6}>
            <h2>Translations</h2>
          </Col>
          <Col md={6}>
            <FormTranslations
              languages={props?.languages}
              originalTexts={props?.originalTexts}
              sync={props.sync}
            />
          </Col>
        </Row>
      </Container>
      <Table striped bordered hover>
        <thead>
          <tr>
            <th>#</th>
            <th>Original text</th>
            <th>Original text language</th>
            <th>Translated text</th>
          </tr>
        </thead>
        <tbody>
          {props?.translations.map(function (value) {
            return (
              <tr>
                <td>{value?.translation_id ? value?.translation_id : '#'}</td>
                <td>{value?.original_text}</td>
                <td>
                  {value?.o_language_code}/{value?.o_language_name}
                </td>
                <td>
                  {value?.translation_id ? (
                    value?.text
                  ) : (
                    <span className='no-translation'>No translation</span>
                  )}
                </td>
                <td>
                  {value?.translation_id && (
                    <FormTranslations
                      data={value}
                      sync={props?.sync}
                      languages={props?.languages}
                    />
                  )}
                </td>
              </tr>
            )
          })}
        </tbody>
      </Table>
    </>
  )
}

export default Translations
