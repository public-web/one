import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
export const history = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

history.definition = {
    methods: ["get","head"],
    url: '/users/import-export-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
history.url = (options?: RouteQueryOptions) => {
    return history.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
history.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
history.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: history.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
const historyForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
historyForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::history
* @see app/Http/Controllers/UserController.php:443
* @route '/users/import-export-history'
*/
historyForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

history.form = historyForm

const importExport = {
    history: Object.assign(history, history),
}

export default importExport