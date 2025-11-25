import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
export const get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: get.url(args, options),
    method: 'get',
})

get.definition = {
    methods: ["get","head"],
    url: '/users/{user}/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
get.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return get.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
get.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
get.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: get.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
const getForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
getForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::get
* @see app/Http/Controllers/UserController.php:261
* @route '/users/{user}/permissions'
*/
getForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: get.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

get.form = getForm

/**
* @see \App\Http\Controllers\UserController::sync
* @see app/Http/Controllers/UserController.php:303
* @route '/users/{user}/permissions'
*/
export const sync = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
})

sync.definition = {
    methods: ["post"],
    url: '/users/{user}/permissions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::sync
* @see app/Http/Controllers/UserController.php:303
* @route '/users/{user}/permissions'
*/
sync.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return sync.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::sync
* @see app/Http/Controllers/UserController.php:303
* @route '/users/{user}/permissions'
*/
sync.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::sync
* @see app/Http/Controllers/UserController.php:303
* @route '/users/{user}/permissions'
*/
const syncForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::sync
* @see app/Http/Controllers/UserController.php:303
* @route '/users/{user}/permissions'
*/
syncForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(args, options),
    method: 'post',
})

sync.form = syncForm

const permissions = {
    get: Object.assign(get, get),
    sync: Object.assign(sync, sync),
}

export default permissions