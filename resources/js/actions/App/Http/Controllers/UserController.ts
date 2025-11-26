import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:31
* @route '/users'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\UserController::store
* @see app/Http/Controllers/UserController.php:72
* @route '/users'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/users',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::store
* @see app/Http/Controllers/UserController.php:72
* @route '/users'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::store
* @see app/Http/Controllers/UserController.php:72
* @route '/users'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::store
* @see app/Http/Controllers/UserController.php:72
* @route '/users'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::store
* @see app/Http/Controllers/UserController.php:72
* @route '/users'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
export const update = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","post"],
    url: '/users/{user}',
} satisfies RouteDefinition<["put","post"]>

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
update.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
update.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
update.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
const updateForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
updateForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::update
* @see app/Http/Controllers/UserController.php:93
* @route '/users/{user}'
*/
updateForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
export const destroy = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete","post"],
    url: '/users/{user}',
} satisfies RouteDefinition<["delete","post"]>

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
destroy.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
destroy.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
destroy.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
const destroyForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
destroyForm.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::destroy
* @see app/Http/Controllers/UserController.php:118
* @route '/users/{user}'
*/
destroyForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, options),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\UserController::restore
* @see app/Http/Controllers/UserController.php:131
* @route '/users/{id}/restore'
*/
export const restore = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

restore.definition = {
    methods: ["post"],
    url: '/users/{id}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::restore
* @see app/Http/Controllers/UserController.php:131
* @route '/users/{id}/restore'
*/
restore.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return restore.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::restore
* @see app/Http/Controllers/UserController.php:131
* @route '/users/{id}/restore'
*/
restore.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::restore
* @see app/Http/Controllers/UserController.php:131
* @route '/users/{id}/restore'
*/
const restoreForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::restore
* @see app/Http/Controllers/UserController.php:131
* @route '/users/{id}/restore'
*/
restoreForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

restore.form = restoreForm

/**
* @see \App\Http\Controllers\UserController::forceDelete
* @see app/Http/Controllers/UserController.php:145
* @route '/users/{id}/force'
*/
export const forceDelete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: forceDelete.url(args, options),
    method: 'delete',
})

forceDelete.definition = {
    methods: ["delete"],
    url: '/users/{id}/force',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\UserController::forceDelete
* @see app/Http/Controllers/UserController.php:145
* @route '/users/{id}/force'
*/
forceDelete.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return forceDelete.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::forceDelete
* @see app/Http/Controllers/UserController.php:145
* @route '/users/{id}/force'
*/
forceDelete.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: forceDelete.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\UserController::forceDelete
* @see app/Http/Controllers/UserController.php:145
* @route '/users/{id}/force'
*/
const forceDeleteForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceDelete.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::forceDelete
* @see app/Http/Controllers/UserController.php:145
* @route '/users/{id}/force'
*/
forceDeleteForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceDelete.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

forceDelete.form = forceDeleteForm

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
export const activityLogs = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activityLogs.url(args, options),
    method: 'get',
})

activityLogs.definition = {
    methods: ["get","head"],
    url: '/users/{user}/activity-logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
activityLogs.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return activityLogs.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
activityLogs.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activityLogs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
activityLogs.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activityLogs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
const activityLogsForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activityLogs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
activityLogsForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activityLogs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::activityLogs
* @see app/Http/Controllers/UserController.php:165
* @route '/users/{user}/activity-logs'
*/
activityLogsForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activityLogs.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

activityLogs.form = activityLogsForm

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
export const getPermissions = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPermissions.url(args, options),
    method: 'get',
})

getPermissions.definition = {
    methods: ["get","head"],
    url: '/users/{user}/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
getPermissions.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getPermissions.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
getPermissions.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPermissions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
getPermissions.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPermissions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
const getPermissionsForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPermissions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
getPermissionsForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPermissions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::getPermissions
* @see app/Http/Controllers/UserController.php:258
* @route '/users/{user}/permissions'
*/
getPermissionsForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPermissions.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getPermissions.form = getPermissionsForm

/**
* @see \App\Http\Controllers\UserController::syncDirectPermissions
* @see app/Http/Controllers/UserController.php:300
* @route '/users/{user}/permissions'
*/
export const syncDirectPermissions = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncDirectPermissions.url(args, options),
    method: 'post',
})

syncDirectPermissions.definition = {
    methods: ["post"],
    url: '/users/{user}/permissions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::syncDirectPermissions
* @see app/Http/Controllers/UserController.php:300
* @route '/users/{user}/permissions'
*/
syncDirectPermissions.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return syncDirectPermissions.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::syncDirectPermissions
* @see app/Http/Controllers/UserController.php:300
* @route '/users/{user}/permissions'
*/
syncDirectPermissions.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncDirectPermissions.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::syncDirectPermissions
* @see app/Http/Controllers/UserController.php:300
* @route '/users/{user}/permissions'
*/
const syncDirectPermissionsForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: syncDirectPermissions.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::syncDirectPermissions
* @see app/Http/Controllers/UserController.php:300
* @route '/users/{user}/permissions'
*/
syncDirectPermissionsForm.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: syncDirectPermissions.url(args, options),
    method: 'post',
})

syncDirectPermissions.form = syncDirectPermissionsForm

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/users/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::exportMethod
* @see app/Http/Controllers/UserController.php:324
* @route '/users/export'
*/
exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
export const downloadTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadTemplate.url(options),
    method: 'get',
})

downloadTemplate.definition = {
    methods: ["get","head"],
    url: '/users/import/template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
downloadTemplate.url = (options?: RouteQueryOptions) => {
    return downloadTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
downloadTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
downloadTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadTemplate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
const downloadTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
downloadTemplateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::downloadTemplate
* @see app/Http/Controllers/UserController.php:362
* @route '/users/import/template'
*/
downloadTemplateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

downloadTemplate.form = downloadTemplateForm

/**
* @see \App\Http\Controllers\UserController::importMethod
* @see app/Http/Controllers/UserController.php:379
* @route '/users/import'
*/
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/users/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::importMethod
* @see app/Http/Controllers/UserController.php:379
* @route '/users/import'
*/
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::importMethod
* @see app/Http/Controllers/UserController.php:379
* @route '/users/import'
*/
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::importMethod
* @see app/Http/Controllers/UserController.php:379
* @route '/users/import'
*/
const importMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::importMethod
* @see app/Http/Controllers/UserController.php:379
* @route '/users/import'
*/
importMethodForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importMethod.url(options),
    method: 'post',
})

importMethod.form = importMethodForm

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
export const importExportHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: importExportHistory.url(options),
    method: 'get',
})

importExportHistory.definition = {
    methods: ["get","head"],
    url: '/users/import-export-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
importExportHistory.url = (options?: RouteQueryOptions) => {
    return importExportHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
importExportHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: importExportHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
importExportHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: importExportHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
const importExportHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: importExportHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
importExportHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: importExportHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::importExportHistory
* @see app/Http/Controllers/UserController.php:440
* @route '/users/import-export-history'
*/
importExportHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: importExportHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

importExportHistory.form = importExportHistoryForm

/**
* @see \App\Http\Controllers\UserController::archiveOperation
* @see app/Http/Controllers/UserController.php:482
* @route '/users/operations/{id}'
*/
export const archiveOperation = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: archiveOperation.url(args, options),
    method: 'delete',
})

archiveOperation.definition = {
    methods: ["delete"],
    url: '/users/operations/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\UserController::archiveOperation
* @see app/Http/Controllers/UserController.php:482
* @route '/users/operations/{id}'
*/
archiveOperation.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return archiveOperation.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::archiveOperation
* @see app/Http/Controllers/UserController.php:482
* @route '/users/operations/{id}'
*/
archiveOperation.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: archiveOperation.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\UserController::archiveOperation
* @see app/Http/Controllers/UserController.php:482
* @route '/users/operations/{id}'
*/
const archiveOperationForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: archiveOperation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::archiveOperation
* @see app/Http/Controllers/UserController.php:482
* @route '/users/operations/{id}'
*/
archiveOperationForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: archiveOperation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

archiveOperation.form = archiveOperationForm

/**
* @see \App\Http\Controllers\UserController::restoreOperation
* @see app/Http/Controllers/UserController.php:500
* @route '/users/operations/{id}/restore'
*/
export const restoreOperation = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreOperation.url(args, options),
    method: 'post',
})

restoreOperation.definition = {
    methods: ["post"],
    url: '/users/operations/{id}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::restoreOperation
* @see app/Http/Controllers/UserController.php:500
* @route '/users/operations/{id}/restore'
*/
restoreOperation.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return restoreOperation.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::restoreOperation
* @see app/Http/Controllers/UserController.php:500
* @route '/users/operations/{id}/restore'
*/
restoreOperation.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreOperation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::restoreOperation
* @see app/Http/Controllers/UserController.php:500
* @route '/users/operations/{id}/restore'
*/
const restoreOperationForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restoreOperation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::restoreOperation
* @see app/Http/Controllers/UserController.php:500
* @route '/users/operations/{id}/restore'
*/
restoreOperationForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restoreOperation.url(args, options),
    method: 'post',
})

restoreOperation.form = restoreOperationForm

/**
* @see \App\Http\Controllers\UserController::forceDeleteOperation
* @see app/Http/Controllers/UserController.php:521
* @route '/users/operations/{id}/force'
*/
export const forceDeleteOperation = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: forceDeleteOperation.url(args, options),
    method: 'delete',
})

forceDeleteOperation.definition = {
    methods: ["delete"],
    url: '/users/operations/{id}/force',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\UserController::forceDeleteOperation
* @see app/Http/Controllers/UserController.php:521
* @route '/users/operations/{id}/force'
*/
forceDeleteOperation.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return forceDeleteOperation.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::forceDeleteOperation
* @see app/Http/Controllers/UserController.php:521
* @route '/users/operations/{id}/force'
*/
forceDeleteOperation.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: forceDeleteOperation.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\UserController::forceDeleteOperation
* @see app/Http/Controllers/UserController.php:521
* @route '/users/operations/{id}/force'
*/
const forceDeleteOperationForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceDeleteOperation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::forceDeleteOperation
* @see app/Http/Controllers/UserController.php:521
* @route '/users/operations/{id}/force'
*/
forceDeleteOperationForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceDeleteOperation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

forceDeleteOperation.form = forceDeleteOperationForm

const UserController = { index, store, update, destroy, restore, forceDelete, activityLogs, getPermissions, syncDirectPermissions, exportMethod, downloadTemplate, importMethod, importExportHistory, archiveOperation, restoreOperation, forceDeleteOperation, export: exportMethod, import: importMethod }

export default UserController