import { createTheme } from "@mui/material";

export const THEME = _THEME
 
export const MUI_THEME = createTheme({
    palette: {
      primary: {
        main: THEME.primary_color, 
      },
      secondary: {
        main: THEME.secondary_color,  
      },
    },
});