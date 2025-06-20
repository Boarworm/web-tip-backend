const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    mode: 'jit',
    content: [
        "./layouts/**/*.{htm,js}",
        "./pages/**/*.{htm,js}",
        "./partials/**/*.{htm,js}",
        "./content/**/*.htm",
        "../../app/blueprints/**/*.yaml",
        '../../plugins/**/*.htm'
    ],
    safelist: [
        'md:col-span-1',
        'md:col-span-2',
        'md:col-span-3',
        'md:col-span-4',
        'md:col-span-5',
        'md:col-span-6',
        'md:col-span-7',
        'md:col-span-8',
        'md:col-span-9',
        'md:col-span-10',
        'md:col-span-11',
        'md:col-span-12',
    ],
    theme: {
        screens: {
            'xs': '475px',
            ...defaultTheme.screens,
        },
        fontSize: {
            'h1': '2.3rem',
            'h1-lg': '2.7rem',
            'h1-xl': '2.9rem',
            'h2': '1.9rem',
            'h2-lg': '2.2rem',
            'h2-xl': '2.5rem',
            'h3': '1.75rem',
            'h3-lg': '1.95rem',
            'h3-xl': '2.15rem',
            'h4': '1.6rem',
            'h4-lg': '1.8rem',
            'h4-xl': '2rem',
            'h5': '1.4rem',
            'h5-lg': '1.5rem',
            'h5-xl': '1.6rem',
            'h6': '1.2rem',
            'h6-lg': '1.3rem',
            'h6-xl': '1.4rem',
            ...defaultTheme.fontSize,
        },
        fontFamily: {
            'sans': ['Commissioner', 'Helvetica', 'Arial', 'sans-serif'],
            'secondary': ['Poppins', 'Helvetica', 'Arial', 'sans-serif'],
        },
        container: {
            center: true,
            padding: {
                DEFAULT: "0.5rem",
                sm: "1.5rem",
                lg: "3rem",
            }
        },
        extend: {
            colors: {
                primary: {
                    lightest: '#E6F7FF',
                    light: '#66C2FF',
                    DEFAULT: '#007BFF',
                    dark: '#0056B3',
                    darkest: '#0e2a47'
                },
                secondary: {
                    lightest: '#FFF0E6',
                    light: '#FFB366',
                    DEFAULT: '#FF6600',
                    dark: '#B34700',
                    darkest: '#803000'
                },
                neutral: {
                    lightest: '#F4F4F4',
                    lighter: '#D9D9D9',
                    light: '#B3B3B3',
                    DEFAULT: '#808080',
                    dark: '#4D4D4D',
                    darker: '#262626',
                    darkest: '#0D0D0D'
                }
            }

            ,
            transitionDuration: {
                DEFAULT: '500ms',
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        'max-width': 'initial',
                        'font-size': 'inherit',
                        'font-weight': 'inherit',
                        a: {
                            color: theme('colors.primary.DEFAULT'),
                            'text-decoration': 'none',
                            '&:hover': {
                                'text-decoration': 'underline',
                            },
                        },
                        p: {
                            'margin-top': '0.5em',
                            'margin-bottom': '1em',
                        },
                        hr: {
                            "border-color": theme('colors.primary.DEFAULT')
                        },
                        h1: {
                            'color': theme('colors.primary.DEFAULT'),
                            'font-weight': '400',
                            'line-height': '1.25',
                            'margin-top': 0,
                            'margin-bottom': "5px",
                        },
                        h2: {
                            'color': theme('colors.primary.DEFAULT'),
                            'font-weight': '400',
                            'line-height': '1.25',
                            'margin-top': 0,
                            'margin-bottom': "5px",
                        },
                        h3: {
                            'color': theme('colors.primary.DEFAULT'),
                            'font-weight': '400',
                            'line-height': '1.25',
                            'margin-top': 0,
                            'margin-bottom': "5px",
                        },
                        h4: {
                            'color': theme('colors.primary.DEFAULT'),
                            'font-weight': '400',
                            'line-height': '1.25',
                            'margin-top': 0,
                            'margin-bottom': "5px",
                        },
                        h5: {
                            'color': theme('colors.primary.DEFAULT'),
                            'font-weight': '400',
                            'line-height': '1.25',
                            'margin-top': 0,
                            'margin-bottom': "5px",
                        },
                        h6: {
                            'color': theme('colors.primary.DEFAULT'),
                            'font-weight': '400',
                            'line-height': '1.25',
                            'margin-top': 0,
                            'margin-bottom': "5px",
                        },
                        '.fr-fic': {
                            width: 'auto',
                            margin: '0 auto',
                            display: 'block'
                        },
                        '.fr-fil': {
                            float: 'left',
                            margin: '8px 15px 15px 0 !important',
                            display: 'inline-block',
                        },
                        '.fr-fir': {
                            float: 'right',
                            margin: '8px 0 15px 15px !important',
                            display: 'inline-block'
                        }
                    }
                }
            }),
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('tailwindcss-3d'),
    ]
}
