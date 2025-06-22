import { useState } from 'react'
import Table from 'react-bootstrap/Table'
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import FormOriginalTexts from './FormOriginalTexts'

function OriginalTexts(props) {
  return (
    <>
      <Container>
        <Row>
          <Col md={6}>
            <h2>Original Texts</h2>
          </Col>
          <Col md={6}>
            <FormOriginalTexts languages={props?.languages} sync={props.sync} />
          </Col>
        </Row>
      </Container>
      <Table striped bordered hover>
        <thead>
          <tr>
            <th>#</th>
            <th>Text</th>
            <th>Language</th>
            <th>Created at</th>
            <th>Updated at</th>
          </tr>
        </thead>
        <tbody>
          {props?.originalTexts.map(function (value) {
            return (
              <tr>
                <td>{value?.id}</td>
                <td>{value?.text}</td>
                <td>
                  <strong>{value?.language_code}</strong> / {value?.language_name}
                </td>
                <td>{value?.created_at}</td>
                <td>{value?.updated_at}</td>
              </tr>
            )
          })}
        </tbody>
      </Table>
    </>
  )
}

export default OriginalTexts
