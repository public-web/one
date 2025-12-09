import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
export const stats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/api/previabilizacion-social/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
stats.url = (options?: RouteQueryOptions) => {
    return stats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
stats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
stats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
const statsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
statsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PreviabilizacionSocialController::stats
* @see app/Http/Controllers/Api/PreviabilizacionSocialController.php:17
* @route '/api/previabilizacion-social/stats'
*/
statsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

stats.form = statsForm

const PreviabilizacionSocialController = { stats }

export default PreviabilizacionSocialController