import React  from "react"
import {createRoot} from "react-dom/client"
import LoginPage from "./pages/LoginPage"
import "./style.scss"

const loginPage = createRoot(document.getElementById("login-page"))

loginPage.render(<LoginPage />)