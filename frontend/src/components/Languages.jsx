import { useState, useEffect } from 'react'
import Table from 'react-bootstrap/Table'
import FormLanguages from './FormLanguage'
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'

function Languages(props) {
  return (
    <>
      <Container>
        <Row>
          <Col md={6}>
            <h2>Languages</h2>
          </Col>
          <Col md={6}>
            <FormLanguages sync={props.sync} />
          </Col>
        </Row>
      </Container>
      <Table striped bordered hover>
        <thead>
          <tr>
            <th>#</th>
            <th>Code</th>
            <th>Languages</th>
          </tr>
        </thead>
        <tbody>
          {props?.languages.map(function (value) {
            return (
              <tr>
                <td>{value?.id}</td>
                <td>{value?.language_code}</td>
                <td>{value?.language_name}</td>
              </tr>
            )
          })}
        </tbody>
      </Table>
    </>
  )
}

export default Languages
