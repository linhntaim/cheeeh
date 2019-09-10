export default {
    hello: 'Hello',
    actions: {
        back: 'Back',
        back_where: 'Back to {where}',
        clear_cache: 'Clear cache',
        go_where: 'Go to {where}',
        login: 'Login',
        logout: 'Logout',
        logging_out: 'Logging out...',
        refresh: 'Refresh',
        register: 'Register',
        retype: 'Retype',
        retype_what: 'Retype {what}',
    },
    components: {
        home_cover: {
            title: 'Show your inspiration.',
            subtitle: 'Cheeeh is the place where you can hold and spread the love of your photos.',
        },
    },
    error: {
        back_to_root: 'Back to root',
        clear_cache_help: 'If you think something does not work properly, please try to clear cache at first!',
        bad_request: {
            _: 'Bad request',
            desc: 'It looks like your request is not successfully handled...',
        },
        connection_lost: {
            _: 'Connection lost',
            desc: 'It looks like you cannot connect to our service...',
        },
        not_found: {
            _: 'Page not found',
            desc: 'It looks like you visit a page that does not exist...',
        },
        unauthenticated: {
            _: 'Unauthenticated',
            desc: 'It looks like you are not logging in...',
        },
        unauthorized: {
            _: 'Unauthorized',
            desc: 'It looks like you are not authorized to perform this request...',
        },
    },
    master: {
        auth: {
            desc: 'Joining <span class="text-gradient-base">Cheeeh</span> means joining the world of photography. With our services, you can store, edit and publish your photos or keep them private as you want.',
            title_1: 'Your inspiration.',
            title_2: 'Your passion.',
        },
        main_footer: {
            about: 'About',
            blog: 'Blog',
            help: 'Help',
            language: 'Language',
            privacy: 'Privacy',
            terms: 'Terms',
        },
    },
    pages: {
        display_name: 'Display name',
        email_address: 'Email address',
        password: 'Password',
        password_lc: 'password',
        start_free: 'Start for free',
        _auth: {
            create_account: 'Don\'t have an account? Create one!',
            forgot_password: 'Forgot password?',
            has_account: 'Already have an account? Login!',
            _forgot_password: {
                _: 'Get a new password',
                submit: 'Get password',
                succeed: 'A link for resetting password has been sent to your email',
            },
            _login: {
                _: 'Login',
                login_with: 'Login with {provider}',
            },
            _register: {
                _: 'Create an account',
            },
        },
    },
}
