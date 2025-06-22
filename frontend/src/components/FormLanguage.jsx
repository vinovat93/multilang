import { useState } from 'react'
import Table from 'react-bootstrap/Table'
import Button from 'react-bootstrap/Button'
import Modal from 'react-bootstrap/Modal'
import Form from 'react-bootstrap/Form'
import axios from 'axios'

function FormLanguages(props) {
  const [show, setShow] = useState(false)

  const handleClose = () => setShow(false)
  const handleShow = () => setShow(true)

  const addNewLanguage = (event) => {
    event.preventDefault()
    event.stopPropagation()

    const form = new FormData(event.currentTarget)
    axios
      .post('http://localhost:8200/api/language', form)
      .then(function (response) {
        handleClose()
        props?.sync()
      })
      .catch(function (error) {
        console.log(error)
      })
  }

  return (
    <>
      <Button variant='primary' onClick={handleShow}>
        Add new language
      </Button>
      <Modal show={show} onHide={handleClose}>
        <Modal.Body>
          <Form onSubmit={addNewLanguage}>
            <Form.Group className='mb-3' controlId='exampleForm.ControlInput1'>
              <Form.Label>Languages code</Form.Label>
              <Form.Control name='language_code' type='text' placeholder='EN' />
            </Form.Group>
            <Form.Group className='mb-3' controlId='exampleForm.ControlTextarea1'>
              <Form.Label>Languages</Form.Label>
              <Form.Control name='language_name' type='text' placeholder='Languages' />
            </Form.Group>
            <Button type='submit' variant='primary'>
              Save
            </Button>
          </Form>
        </Modal.Body>
      </Modal>
    </>
  )
}

export default FormLanguages
