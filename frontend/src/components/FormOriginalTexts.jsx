import { useState } from 'react'
import Table from 'react-bootstrap/Table'
import Button from 'react-bootstrap/Button'
import Modal from 'react-bootstrap/Modal'
import Form from 'react-bootstrap/Form'
import axios from 'axios'

function FormOriginalTexts(props) {
  const [show, setShow] = useState(false)
  const handleClose = () => setShow(false)
  const handleShow = () => setShow(true)

  const addNewOriginalText = (event) => {
    event.preventDefault()
    event.stopPropagation()

    const form = new FormData(event.currentTarget)
    axios
      .post('http://localhost:8200/api/original-text', form)
      .then(function (response) {
        handleClose()
        props?.sync()
      })
      .catch(function (error) {
        console.log(error)
      })
  }

  return (
    props?.languages?.length > 0 && (
      <>
        <Button variant='primary' onClick={handleShow}>
          Add new original text
        </Button>
        <Modal show={show} onHide={handleClose}>
          <Modal.Body>
            <Form onSubmit={addNewOriginalText}>
              <Form.Group className='mb-3' controlId='exampleForm.ControlInput1'>
                <Form.Label>Original text</Form.Label>
                <Form.Control
                  name='text'
                  type='text'
                  placeholder='Lorem Ipsum is simply dummy text of the printing...'
                />
              </Form.Group>
              <Form.Group className='mb-3' controlId='exampleForm.ControlTextarea1'>
                <Form.Label>Language</Form.Label>
                <Form.Select selected name='language_id' aria-label='Default select example'>
                  <option disabled>Select a language</option>
                  {props?.languages.map(function (value) {
                    return <option value={value?.id}>{value?.language_name}</option>
                  })}
                </Form.Select>
              </Form.Group>
              <Button type='submit' variant='primary'>
                Save
              </Button>
            </Form>
          </Modal.Body>
        </Modal>
      </>
    )
  )
}

export default FormOriginalTexts
