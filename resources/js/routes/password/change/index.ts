import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/password/change',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PasswordChangeController::show
* @see app/Http/Controllers/PasswordChangeController.php:26
* @route '/password/change'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\PasswordChangeController::update
* @see app/Http/Controllers/PasswordChangeController.php:45
* @route '/password/change'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/password/change',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PasswordChangeController::update
* @see app/Http/Controllers/PasswordChangeController.php:45
* @route '/password/change'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PasswordChangeController::update
* @see app/Http/Controllers/PasswordChangeController.php:45
* @route '/password/change'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PasswordChangeController::update
* @see app/Http/Controllers/PasswordChangeController.php:45
* @route '/password/change'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PasswordChangeController::update
* @see app/Http/Controllers/PasswordChangeController.php:45
* @route '/password/change'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const change = {
    show: Object.assign(show, show),
    update: Object.assign(update, update),
}

export default change