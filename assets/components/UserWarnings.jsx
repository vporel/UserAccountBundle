import React, { useState, Fragment }  from "react";
import { userSignal } from "~/globals";
import ValidateEmail from "./ValidateEmail";

/**
 * The email validation is managed internally
 */
export default function UserWarnings({onChangeProfilePhoto, checkEmail = true, checkProfilePhoto = true}){
    const [validateEmailVisible, setValidateEmailVisible] = useState(false)
    return <div id="user-warnings">
        {checkEmail && !userSignal.value.emailValidated && <Fragment>
            <div class="alert alert-warning">
                Votre adresse email n'a pas été vérifiée. Vous pourriez ne pas recevoir les mails vous concernant. {" "}
                <a class="text-decoration-underline" onClick={() => setValidateEmailVisible(true)}>Cliquez-ici</a> pour la vérification.
            </div>
            <ValidateEmail show={validateEmailVisible} onHide={() => setValidateEmailVisible(false)} />
        </Fragment>}
        {checkProfilePhoto && userSignal.value.avatarFile == null &&
            <div class="alert alert-warning mt-1">
                Vous n'avez pas de photo de profil personnalisée.
                 <a onClick={onChangeProfilePhoto} className="text-decoration-underline">Cliquez-ici</a> pour ajouter une photo.
            </div>
        }
    </div>
}