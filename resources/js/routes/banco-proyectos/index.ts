import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import detalles from './detalles'
import previabilizaciones from './previabilizaciones'
/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/banco-proyectos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::index
* @see app/Http/Controllers/BancoProyectoController.php:19
* @route '/banco-proyectos'
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
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
export const mapa = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mapa.url(options),
    method: 'get',
})

mapa.definition = {
    methods: ["get","head"],
    url: '/banco-proyectos/mapa',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapa.url = (options?: RouteQueryOptions) => {
    return mapa.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapa.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mapa.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapa.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mapa.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
const mapaForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mapa.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapaForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mapa.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::mapa
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapaForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mapa.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mapa.form = mapaForm

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/banco-proyectos/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::show
* @see app/Http/Controllers/BancoProyectoController.php:45
* @route '/banco-proyectos/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const bancoProyectos = {
    index: Object.assign(index, index),
    mapa: Object.assign(mapa, mapa),
    show: Object.assign(show, show),
    detalles: Object.assign(detalles, detalles),
    previabilizaciones: Object.assign(previabilizaciones, previabilizaciones),
}

export default bancoProyectos