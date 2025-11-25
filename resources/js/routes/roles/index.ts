import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import permissions from './permissions'
/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/roles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:76
* @route '/roles'
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
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:131
* @route '/roles'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/roles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:131
* @route '/roles'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:131
* @route '/roles'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:131
* @route '/roles'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:131
* @route '/roles'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\RoleController::destroyMany
* @see app/Http/Controllers/RoleController.php:369
* @route '/roles/delete-many'
*/
export const destroyMany = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroyMany.url(options),
    method: 'post',
})

destroyMany.definition = {
    methods: ["post"],
    url: '/roles/delete-many',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RoleController::destroyMany
* @see app/Http/Controllers/RoleController.php:369
* @route '/roles/delete-many'
*/
destroyMany.url = (options?: RouteQueryOptions) => {
    return destroyMany.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::destroyMany
* @see app/Http/Controllers/RoleController.php:369
* @route '/roles/delete-many'
*/
destroyMany.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroyMany.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::destroyMany
* @see app/Http/Controllers/RoleController.php:369
* @route '/roles/delete-many'
*/
const destroyManyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyMany.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::destroyMany
* @see app/Http/Controllers/RoleController.php:369
* @route '/roles/delete-many'
*/
destroyManyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyMany.url(options),
    method: 'post',
})

destroyMany.form = destroyManyForm

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
export const show = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/roles/{role}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
show.url = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { role: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            role: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role: typeof args.role === 'object'
        ? args.role.id
        : args.role,
    }

    return show.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
show.get = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
show.head = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
const showForm = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
showForm.get = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::show
* @see app/Http/Controllers/RoleController.php:303
* @route '/roles/{role}'
*/
showForm.head = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
export const update = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","post"],
    url: '/roles/{role}',
} satisfies RouteDefinition<["put","post"]>

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
update.url = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { role: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            role: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role: typeof args.role === 'object'
        ? args.role.id
        : args.role,
    }

    return update.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
update.put = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
update.post = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
const updateForm = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
updateForm.put = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:188
* @route '/roles/{role}'
*/
updateForm.post = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
export const destroy = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete","post"],
    url: '/roles/{role}/delete',
} satisfies RouteDefinition<["delete","post"]>

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
destroy.url = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { role: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            role: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role: typeof args.role === 'object'
        ? args.role.id
        : args.role,
    }

    return destroy.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
destroy.delete = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
destroy.post = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
const destroyForm = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
destroyForm.delete = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:258
* @route '/roles/{role}/delete'
*/
destroyForm.post = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, options),
    method: 'post',
})

destroy.form = destroyForm

const roles = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    destroyMany: Object.assign(destroyMany, destroyMany),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    permissions: Object.assign(permissions, permissions),
}

export default roles