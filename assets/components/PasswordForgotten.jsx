import _ from "vporel/translator"
import React, { Fragment, useCallback, useContext, useState}  from "react"
import { checkPasswordResetCode, sendPasswordResetCode, resetPassword as serverResetPassword } from "../api"
import { Modal } from "react-bootstrap"
import { ButtonPrimary } from "vporel/components/Button"
import { InputAdornment, TextField } from "@mui/material"
import validators from "vporel/validators"
import ToastContext from "vporel/contexts/ToastContext"
import { userValidators } from "../validators"


export default function PasswordForgotten({show, onHide, onPasswordChanged}){
    const {toastSuccess, toastError, toastWarning} = useContext(ToastContext)
    const [codeSent, setCodeSent] = useState(false)
    const [loading, setLoading] = useState(false)
    const [code, setCode] = useState("")
    const [codeValidated, setCodeValidated] = useState(false)
    const [email, setEmail] = useState("")
    const [password, setPassword] = useState("")

    const sendResetCode = useCallback(async () => {
        const emailError = validators.email(email)
        if(emailError != ""){
            toastWarning(emailError)
            return
        }
        setLoading(true)
        const response = await sendPasswordResetCode(email)
        if(response.status == 1) setCodeSent(true)
        else toastError(response.errorMessage)
        setLoading(false)
    }, [email, toastWarning])

    const checkCode = useCallback(async () => {
        setLoading(true)
        const response = await checkPasswordResetCode(code)
        if(response.status == 1) setCodeValidated(true)
        else toastError("Code incorrect")
        setLoading(false)
    }, [code, toastError])

    const resetPassword = useCallback(async () => {
        const passwordError = userValidators.password(password)
        if(passwordError != ""){
            toastWarning(passwordError)
            return
        }
        setLoading(true)
        const response = await serverResetPassword(email, code, password)
        if(response.status == 1){
            toastSuccess(_("Password reset"))
            onHide() 
            if(onPasswordChanged) onPasswordChanged()
        }else toastError("Code incorrect")
        setLoading(false)
    }, [email, code, password, onHide, toastWarning, toastSuccess, toastError, onPasswordChanged])
    
    return <Modal show={show} onHide={onHide} centered contentClassName="border-0">
        <Modal.Header closeButton className="border-bottom-0">
            <Modal.Title className="mb-0">{_("Password reset")}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {!codeSent 
                ? <TextField fullWidth label={_("Your email address")} value={email} 
                    onChange={e => setEmail(e.target.value)}
                    InputProps={{endAdornment: <InputAdornment position="end"><i className="fas fa-envelope"></i></InputAdornment>}}
                />
                : (!codeValidated 
                    ? <Fragment>
                        <p>{_("A code has been sent to")} <span className="text-primary">{email}</span></p>
                        <TextField fullWidth label={_("Code")} value={code} onChange={e => {setCode(e.target.value)}}/>
                    </Fragment>
                    : <TextField fullWidth label={_("Your new password")} value={password} 
                        onChange={e => setPassword(e.target.value)}
                        InputProps={{endAdornment: <InputAdornment position="end"><i className="fas fa-key"></i></InputAdornment>}}
                    />
                )
            }
        </Modal.Body>
        <Modal.Footer className="border-top-0 pt-0">
            <ButtonPrimary 
                loading={loading} 
                onClick={!codeSent ? sendResetCode : (!codeValidated ? checkCode : resetPassword)}
            >{_("Validate")}</ButtonPrimary>
        </Modal.Footer>
    </Modal>
}