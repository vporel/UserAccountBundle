import FlexCenter from 'vporel/components/display/FlexCenter'
import { ButtonPrimary } from '.vporel/components/Button'
import { useState } from 'react'
import { ToastContextProvider } from 'vporel/contexts/ToastContext'
import { ThemeProvider } from '@mui/material'
import { MUI_THEME } from '../globals'
import ValidateEmail from '../components/ValidateEmail'

export default function EmailValidationPage(){
    const [promptVisible, setPromptVisible] = useState(false)

    return <ThemeProvider theme={MUI_THEME}>
        <ToastContextProvider>
            <FlexCenter style={{width: "100%", height: "100vh"}}>
                <FlexCenter className='p-4 flex-column align-items-center text-center -w-90 -w-md-50' style={{background: "white", boxShadow: "2px 2px 5px rgba(0, 0, 0, .2)", borderRadius: "10px"}}>
                    <FlexCenter className="bg-primary bg-2 fs-4 text-white" style={{width: 60, height: 60, borderRadius: "50%"}}>
                        <i className="fas fa-envelope" />
                    </FlexCenter>
                    <h3 className='mt-2'>Vérification de votre adresse email</h3>
                    <p>
                        Chèr(e) client(e), pour garantir la sécurité de votre compte, 
                        veuillez cliquer sur le bouton ci-dessous afin de vérifier l'adresse e-mail associée 
                        à votre compte. Merci de votre collaboration !
                    </p>
                    <ButtonPrimary onClick={() => setPromptVisible(true)}>Vérifier</ButtonPrimary>
                    <ValidateEmail show={promptVisible} onHide={() => setPromptVisible(false)} onValidated={() => window.location = "/"} />
                    <a href="/deconnexion" className='mt-2'>Me déconnecter</a>
                </FlexCenter>
            </FlexCenter>
        </ToastContextProvider>
    </ThemeProvider>
}