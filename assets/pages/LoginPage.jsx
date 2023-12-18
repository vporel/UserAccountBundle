import { Checkbox, FormControlLabel, FormGroup, InputAdornment, TextField, ThemeProvider, Box } from '@mui/material'
import { THEME, MUI_THEME } from '../globals'
import React, { useState } from 'react'
import PasswordForgotten from '../components/PasswordForgotten'
import { ToastContextProvider } from 'vporel/contexts/ToastContext'

export default function LoginPage(){
    //constants defined in the page header
    const TARGET_PATH = _TARGET_PATH
    const CSRF_TOKEN = _CSRF_TOKEN
    const ERROR = _ERROR 
    const LAST_USERNAME = _LAST_USERNAME
    const SIGN_UP_URL = _SIGN_UP_URL
    const [userName, setUserName] = useState(LAST_USERNAME)
    const [passwordForgottenVisible, setPasswordForgottenVisible] = useState(false)

    return <ThemeProvider theme={MUI_THEME}>
        <ToastContextProvider>
            <div className="container">
            <div className="form-content p-3 p-sm-4 p-md-5">
                <div className="text-center">
                    <a href="/"><img src="/favicons/favicon-512x512.png" alt="" width="50" /></a>
                    <h4 className='mt-1'>Lahotte à votre service</h4>
                </div>
                <Box className="title" sx={{"&::before": {background: THEME.primary_color}}}>Connexion</Box>
                <form method="post"className="mt-5">
                    <div>
                        {ERROR != "" && <div className="alert alert-danger mt-3">{ERROR}</div>}
                        <input type="hidden" name="_target_path" value={TARGET_PATH} />
                        <input type="hidden" name="_csrf_token" value={CSRF_TOKEN} />
                        <TextField fullWidth value={userName} onChange={e => setUserName(e.target.value)} name="userName" label="Nom d'utilisateur" InputProps={{startAdornment: <InputAdornment position='start'><i className='fas fa-user' /></InputAdornment>}} />
                        <TextField fullWidth className='mt-3' name="password" type='password' label="Mot de passe" InputProps={{startAdornment: <InputAdornment position='start'><i className='fas fa-key' /></InputAdornment>}} />
                        <div className="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row mt-1">
                            <FormGroup>
                                <FormControlLabel name='_remember_me' control={<Checkbox defaultChecked />} label="Rester connecté" />
                            </FormGroup>
                            <a onClick={() => setPasswordForgottenVisible(true)}>Mot de passe oublié ?</a>
                            <PasswordForgotten show={passwordForgottenVisible} onHide={() => setPasswordForgottenVisible(false)}/>
                        </div>
                    </div>
                    <div className="d-flex justify-content-between align-items-center mt-5">
                        <div><a href={SIGN_UP_URL} className="fw-bold" style={{color: THEME.primary_color, textDecoration: "none"}}>Créer un compte</a></div>
                        <input type="submit" value="Connexion" className="btn btn-primary" style={{background: THEME.primary_color, borderColor: THEME.primary_color}}/>
                    </div>
                </form>
            </div>
            </div>
        </ToastContextProvider>
    </ThemeProvider>
}