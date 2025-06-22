import { useState } from 'react'
import Table from 'react-bootstrap/Table'
import Button from 'react-bootstrap/Button'
import Modal from 'react-bootstrap/Modal'
import Form from 'react-bootstrap/Form'
import axios from 'axios'

function FormTranslations(props) {
  const [show, setShow] = useState(false)
  const handleClose = () => setShow(false)
  const handleShow = () => setShow(true)
  const onDelete = () => {
    if (props?.data?.translation_id) {
      axios
        .delete(`http://localhost:8200/api/translator/${props?.data?.translation_id}`)
        .then(function (response) {
          handleClose()
          props?.sync()
        })
        .catch(function (error) {
          console.log(error)
        })
    }
  }
  const addNewOriginalText = (event) => {
    event.preventDefault()
    event.stopPropagation()

    const form = new FormData(event.currentTarget)
    const data = Array.from(form.entries()).reduce(
      (memo, [key, value]) => ({
        ...memo,
        [key]: value,
      }),
      {},
    )
    if (!props?.data?.translation_id) {
      axios
        .post('http://localhost:8200/api/translator', data)
        .then(function (response) {
          handleClose()
          props?.sync()
        })
        .catch(function (error) {
          console.log(error)
        })
    } else {
      const data = Array.from(form.entries()).reduce(
        (memo, [key, value]) => ({
          ...memo,
          [key]: value,
        }),
        {},
      )
      axios
        .put(`http://localhost:8200/api/translator/${props?.data?.translation_id}`, data)
        .then(function (response) {
          handleClose()
          props?.sync()
        })
        .catch(function (error) {
          console.log(error)
        })
    }
  }

  return (
    props?.languages?.length > 0 && (
      <>
        <Button variant='primary' onClick={handleShow}>
          {props?.data ? 'Edit' : 'Add new translation'}
        </Button>
        {props?.data && (
          <Button variant='danger' onClick={onDelete}>
            Delete
          </Button>
        )}
        <Modal show={show} onHide={handleClose}>
          <Modal.Body>
            <Form onSubmit={addNewOriginalText}>
              <Form.Group className='mb-3' controlId='exampleForm.ControlInput1'>
                <Form.Label>Original text</Form.Label>
                <Form.Control
                  name='text'
                  type='text'
                  placeholder='Lorem Ipsum is simply dummy text of the printing...'
                  defaultValue={props?.data?.text}
                />
              </Form.Group>
              {!props?.data?.translation_id && props?.originalTexts && (
                <Form.Group className='mb-3' controlId='exampleForm.ControlTextarea1'>
                  <Form.Label>Language</Form.Label>
                  <Form.Select name='text_id' aria-label='Default select example'>
                    <option selected disabled>
                      Select a original text
                    </option>
                    {props?.originalTexts.map(function (value) {
                      return <option value={value?.id}>{value?.text}</option>
                    })}
                  </Form.Select>
                </Form.Group>
              )}
              {!props?.data?.translation_id && (
                <Form.Group className='mb-3' controlId='exampleForm.ControlTextarea1'>
                  <Form.Label>Language</Form.Label>
                  <Form.Select name='language_id' aria-label='Default select example'>
                    <option selected disabled>
                      Select a language
                    </option>
                    {props?.languages.map(function (value) {
                      return (
                        <option selected={props?.data?.language_id == value?.id} value={value?.id}>
                          {value?.language_name}
                        </option>
                      )
                    })}
                  </Form.Select>
                </Form.Group>
              )}
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

export default FormTranslations
