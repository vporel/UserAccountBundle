import { post } from "vporel/api"

export async function sendPasswordResetCode(email){
    return await post("/api/account/send-password-reset-code", {email})
}

export async function checkPasswordResetCode(code){
    return await post("/api/account/check-password-reset-code", {code})
}

export async function resetPassword(email, code, password){
    return await post("/api/account/reset-password", {email, code, password})
}

export async function sendEmailValidationCode(){
    return await post("/api/account/send-email-validation-code")
}

export async function validateEmail(code){
    return await post("/api/account/validate-email", {code})
}

export async function changeEmail(email){
    return await post("/api/account/change-email", {email})
}