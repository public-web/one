import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import documentos from './documentos'
/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
export const store = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/banco-proyectos/{proyectoId}/detalles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
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
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
store.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
const storeForm = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::store
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
storeForm.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
export const update = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","post"],
    url: '/banco-proyectos/{proyectoId}/detalles/{detalleId}',
} satisfies RouteDefinition<["put","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
update.url = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            proyectoId: args[0],
            detalleId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyectoId: args.proyectoId,
        detalleId: args.detalleId,
    }

    return update.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{detalleId}', parsedArgs.detalleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
update.put = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
update.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::update
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
const updateForm = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateForm.put = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateForm.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
export const destroy = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete","post"],
    url: '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete',
} satisfies RouteDefinition<["delete","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroy.url = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            proyectoId: args[0],
            detalleId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyectoId: args.proyectoId,
        detalleId: args.detalleId,
    }

    return destroy.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{detalleId}', parsedArgs.detalleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroy.delete = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroy.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
const destroyForm = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyForm.delete = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyForm.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, options),
    method: 'post',
})

destroy.form = destroyForm

const detalles = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    documentos: Object.assign(documentos, documentos),
}

export default detalles