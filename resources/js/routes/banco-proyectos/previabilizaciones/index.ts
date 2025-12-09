import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
export const store = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/banco-proyectos/{proyectoId}/previabilizaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
store.url = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proyectoId: args }
    }

    if (Array.isArray(args)) {
        args = {
            proyectoId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyectoId: args.proyectoId,
    }

    return store.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
store.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
const storeForm = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
storeForm.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
export const update = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","post"],
    url: '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}',
} satisfies RouteDefinition<["put","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
update.url = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            proyectoId: args[0],
            previabilizacionId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyectoId: args.proyectoId,
        previabilizacionId: args.previabilizacionId,
    }

    return update.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{previabilizacionId}', parsedArgs.previabilizacionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
update.put = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
update.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
const updateForm = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updateForm.put = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updateForm.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
export const destroy = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete","post"],
    url: '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete',
} satisfies RouteDefinition<["delete","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroy.url = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            proyectoId: args[0],
            previabilizacionId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyectoId: args.proyectoId,
        previabilizacionId: args.previabilizacionId,
    }

    return destroy.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{previabilizacionId}', parsedArgs.previabilizacionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroy.delete = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroy.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
const destroyForm = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyForm.delete = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyForm.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, options),
    method: 'post',
})

destroy.form = destroyForm

const previabilizaciones = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default previabilizaciones