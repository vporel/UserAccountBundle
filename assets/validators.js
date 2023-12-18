import _ from "vporel/translator"
import validators from "vporel/validators"

export const userValidators = {
    firstName: val => validators.chain(validators.required(val), validators.stringLength(val, 3)),
    lastName: val => validators.chain(validators.required(val), validators.stringLength(val, 3)),
    password: val => validators.chain(validators.required(val), validators.stringLength(val, 6)),
    userName: val => validators.chain(
        validators.required(val),
        validators.stringLength(val, 5),
        validators.regex(val, /^[a-z][a-z0-9-_]{4,}$/, _("Should start with a letter and contains only contains lowercase letters, numbers and (- ou _) (no space)"))
    ),
    email: val => validators.chain(validators.required(val), validators.email(val)),
    phoneNumber: val => validators.regex(val, /^((00|\+)?237)?[62][56789][0-9]{7}$/, _("Invalid format") + " (MTN, Orange Nexttel, Camtel)"),
    confirmPassword: (val, values) => validators.chain(validators.required(val), (val != values.password) ? _("The passwords are different") : "")
}