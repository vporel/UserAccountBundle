import _ from "vporel/translator"
import React, { Fragment, useCallback, useContext, useState}  from "react"
import { sendEmailValidationCode, validateEmail, changeEmail as serverChangeEmail } from "../api"
import { Modal } from "react-bootstrap"
import { ButtonLight, ButtonPrimary } from "vporel/components/Button"
import { InputAdornment, TextField } from "@mui/material"
import { userSignal } from "~/globals"
import FlexCenter from "vporel/components/display/FlexCenter"
import { signal } from "@preact/signals"
import validators from "vporel/validators"
import ToastContext from "vporel/contexts/ToastContext"

const time = signal(60)
let timer 

export default function ValidateEmail({show, onHide, onValidated}){
    const {toastSuccess, toastError} = useContext(ToastContext)
    const [codeSent, setCodeSent] = useState(false)
    const [changeEmail, setChangeEmail] = useState(false)
    const [loading, setLoading] = useState(false)
    const [code, setCode] = useState("")
    const [newEmail, setNewEmail] = useState("")
    const [error, setError] = useState("")
    const [canResend, setCanResend] = useState(false)

    const startResendCountDown = useCallback(() => {
        time.value = 60
        timer = setInterval(() => {
            time.value--
            if(time.value == 0){
                clearInterval(timer)
                setCanResend(true)
            }
        }, 1000)
    }, [setCanResend])

    const sendCode = useCallback(async () => {
        setLoading(true)
        const response = await sendEmailValidationCode()
        if(response.status == 1){ 
            setCodeSent(true)
            setChangeEmail(false) 
            setCanResend(false)
            startResendCountDown()
        }
        setLoading(false)
    }, [setCodeSent, setChangeEmail, setLoading, setCanResend, startResendCountDown])

    const saveNewEmail = useCallback(async () => {
        if(error != "") return;
        setLoading(true)
        const response = await serverChangeEmail(newEmail)
        if(response.status == 1){
            setChangeEmail(false) 
            userSignal.value.email = newEmail
        }else toastError(response.errorMessage)
        setLoading(false)
    }, [error, setChangeEmail, newEmail, setLoading])

    const validate = useCallback(async () => {
        setLoading(true)
        setError("")
        const response = await validateEmail(code)
        if(response.status == 1){
            toastSuccess(_("Email validated"))
            userSignal.value.emailValidated = true
            onHide() 
            if(onValidated) onValidated()
        }else{
            setError(_("Code incorrect"))
        }
        setLoading(false)
    }, [code, onHide, toastSuccess, onValidated])
    
    return <Modal show={show} onHide={onHide} centered contentClassName="border-0">
        <Modal.Header closeButton className="border-bottom-0">
            <Modal.Title className="mb-0">{_("Email validation")}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {!codeSent 
                ?(!changeEmail 
                    ? <Fragment>
                        <p>{_("Your email address is")} <span className="text-primary">{userSignal.value.email}</span></p>
                        <FlexCenter>
                            <ButtonPrimary onClick={sendCode} loading={loading}>{_("I confirm")}</ButtonPrimary>
                            <ButtonLight onClick={() => setChangeEmail(true)}>{_("Change")}</ButtonLight>
                        </FlexCenter>
                    </Fragment>
                    : <Fragment>
                        <TextField fullWidth label={_("New address")} value={newEmail} onChange={e => {
                            const value = e.target.value
                            setError(validators.email(value))
                            setNewEmail(value)
                        }} helperText={error} error={error != ""} InputProps={{endAdornment: <InputAdornment position="end"><i className="fas fa-envelope"></i></InputAdornment>}}/>
                    </Fragment>
                ): <Fragment>
                    <p>{_("A validation code has been sent to")} <span className="text-primary">{userSignal.value.email}</span></p>
                    <TextField fullWidth label={_("Code")} value={code} onChange={e => {setCode(e.target.value)}} helperText={error} error={error != ""}/>
                    {!canResend
                        ? <span className="d-block mt-2">{_("You will be able to ask a new code in")} {time.value} {_("second") + (time.value > 1 ? "s": "")}</span>
                        : <a onClick={sendCode}  className="d-block mt-2">{_("Resend the code")}</a>
                    }
                </Fragment>
            }
        </Modal.Body>
        {codeSent &&
            <Modal.Footer className="border-top-0 pt-0">
                <ButtonPrimary loading={loading} onClick={validate}>{_("Validate")}</ButtonPrimary>
            </Modal.Footer>
        }
        {changeEmail &&
            <Modal.Footer className="border-top-0 pt-0">
                <ButtonPrimary loading={loading} onClick={saveNewEmail}>{_("Validate")}</ButtonPrimary>
            </Modal.Footer>
        }
    </Modal>
}