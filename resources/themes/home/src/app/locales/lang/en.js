export default {
    hello: 'Hello',
    actions: {
        clear_cache: 'Clear cache',
        go_where: 'Go to {where}',
        refresh: 'Refresh',
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
    pages: {
        _auth: {
            _login: {
                _: 'Login',
            },
        },
    },
}
