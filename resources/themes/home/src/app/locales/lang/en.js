export default {
    hello: 'Hello',
    def: {
        datetime: {
            short_date_0: '{yyyy}-{mm}-{dd}',
            short_date_1: '{mm}/{dd}/{yyyy}',
            short_date_2: '{dd}/{mm}/{yyyy}',
            short_date_3: '{dd}-{sm}-{yy}',
            short_month_0: '{yyyy}-{mm}',
            short_month_1: '{mm}/{yyyy}',
            short_month_2: '{mm}/{yyyy}',
            short_month_3: '{sm}-{yy}',
            long_date_0: '{lm} {dd}, {yyyy}',
            long_date_1: '{dd} {lm}, {yyyy}',
            long_date_2: '{ld}, {lm} {dd}, {yyyy}',
            long_date_3: '{ld}, {dd} {lm}, {yyyy}',
            short_time_0: '{hh2}:{ii}',
            short_time_1: '{h}:{ii} {ut}',
            short_time_2: '{h}:{ii} {lt}',
            short_time_3: '{hh}:{ii} {ut}',
            short_time_4: '{hh}:{ii} {lt}',
            long_time_0: '{hh2}:{ii}:{ss}',
            long_time_1: '{h}:{ii}:{ss} {ut}',
            long_time_2: '{h}:{ii}:{ss} {lt',
            long_time_3: '{hh}:{ii}:{ss} {ut}',
            long_time_4: '{hh}:{ii}:{ss} {lt}',
        },
    },
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
                login_failed_with: 'Sorry, you did not successfully login with {provider}',
            },
            _register: {
                _: 'Create an account',
                register_with: 'Register with {provider}',
                registering_with: 'Registering with {provider}',
            },
            _verify_email: {
                _: 'Verify email address',
                desc: 'Please check your e-mail at <span class="text-info">{email}</span> for a guide on verification.<br>Or if you wish us to send you another e-mail, please click the <strong>Resend</strong> button.',
                done: 'Your e-mail address <span class="text-info">{email}</span> is verified!',
                help: 'You can change your e-mail address before sending in case it was wrong',
                succeed: 'A guide on verification has been sent to your e-mail',
                waiting: 'Please wait while your e-mail address <span class="text-info">{email}</span> is being verified...',
            },
        },
    },
}
