import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
export const map = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: map.url(options),
    method: 'get',
})

map.definition = {
    methods: ["get","head"],
    url: '/banco-proyectos/mapa',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
map.url = (options?: RouteQueryOptions) => {
    return map.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
map.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: map.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
map.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: map.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
const mapForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: map.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: map.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::map
* @see app/Http/Controllers/BancoProyectoController.php:35
* @route '/banco-proyectos/mapa'
*/
mapForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: map.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

map.form = mapForm

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

/**
* @see \App\Http\Controllers\BancoProyectoController::storeDetalle
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
export const storeDetalle = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeDetalle.url(args, options),
    method: 'post',
})

storeDetalle.definition = {
    methods: ["post"],
    url: '/banco-proyectos/{proyectoId}/detalles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::storeDetalle
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
storeDetalle.url = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return storeDetalle.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::storeDetalle
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
storeDetalle.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeDetalle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::storeDetalle
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
const storeDetalleForm = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeDetalle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::storeDetalle
* @see app/Http/Controllers/BancoProyectoController.php:75
* @route '/banco-proyectos/{proyectoId}/detalles'
*/
storeDetalleForm.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeDetalle.url(args, options),
    method: 'post',
})

storeDetalle.form = storeDetalleForm

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
export const updateDetalle = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateDetalle.url(args, options),
    method: 'put',
})

updateDetalle.definition = {
    methods: ["put","post"],
    url: '/banco-proyectos/{proyectoId}/detalles/{detalleId}',
} satisfies RouteDefinition<["put","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateDetalle.url = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions) => {
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

    return updateDetalle.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{detalleId}', parsedArgs.detalleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateDetalle.put = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateDetalle.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateDetalle.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateDetalle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
const updateDetalleForm = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDetalle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateDetalleForm.put = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDetalle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updateDetalle
* @see app/Http/Controllers/BancoProyectoController.php:127
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}'
*/
updateDetalleForm.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDetalle.url(args, options),
    method: 'post',
})

updateDetalle.form = updateDetalleForm

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
export const destroyDetalle = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyDetalle.url(args, options),
    method: 'delete',
})

destroyDetalle.definition = {
    methods: ["delete","post"],
    url: '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete',
} satisfies RouteDefinition<["delete","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyDetalle.url = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions) => {
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

    return destroyDetalle.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{detalleId}', parsedArgs.detalleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyDetalle.delete = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyDetalle.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyDetalle.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroyDetalle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
const destroyDetalleForm = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyDetalle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyDetalleForm.delete = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyDetalle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDetalle
* @see app/Http/Controllers/BancoProyectoController.php:182
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/delete'
*/
destroyDetalleForm.post = (args: { proyectoId: string | number, detalleId: string | number } | [proyectoId: string | number, detalleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyDetalle.url(args, options),
    method: 'post',
})

destroyDetalle.form = destroyDetalleForm

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDocumento
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
export const destroyDocumento = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyDocumento.url(args, options),
    method: 'delete',
})

destroyDocumento.definition = {
    methods: ["delete"],
    url: '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDocumento
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
destroyDocumento.url = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            proyectoId: args[0],
            detalleId: args[1],
            documentoId: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyectoId: args.proyectoId,
        detalleId: args.detalleId,
        documentoId: args.documentoId,
    }

    return destroyDocumento.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{detalleId}', parsedArgs.detalleId.toString())
            .replace('{documentoId}', parsedArgs.documentoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDocumento
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
destroyDocumento.delete = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyDocumento.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDocumento
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
const destroyDocumentoForm = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyDocumento.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyDocumento
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
destroyDocumentoForm.delete = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyDocumento.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyDocumento.form = destroyDocumentoForm

/**
* @see \App\Http\Controllers\BancoProyectoController::storePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
export const storePreviabilizacion = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePreviabilizacion.url(args, options),
    method: 'post',
})

storePreviabilizacion.definition = {
    methods: ["post"],
    url: '/banco-proyectos/{proyectoId}/previabilizaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::storePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
storePreviabilizacion.url = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return storePreviabilizacion.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::storePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
storePreviabilizacion.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePreviabilizacion.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::storePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
const storePreviabilizacionForm = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storePreviabilizacion.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::storePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:229
* @route '/banco-proyectos/{proyectoId}/previabilizaciones'
*/
storePreviabilizacionForm.post = (args: { proyectoId: string | number } | [proyectoId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storePreviabilizacion.url(args, options),
    method: 'post',
})

storePreviabilizacion.form = storePreviabilizacionForm

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
export const updatePreviabilizacion = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatePreviabilizacion.url(args, options),
    method: 'put',
})

updatePreviabilizacion.definition = {
    methods: ["put","post"],
    url: '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}',
} satisfies RouteDefinition<["put","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updatePreviabilizacion.url = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions) => {
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

    return updatePreviabilizacion.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{previabilizacionId}', parsedArgs.previabilizacionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updatePreviabilizacion.put = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatePreviabilizacion.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updatePreviabilizacion.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updatePreviabilizacion.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
const updatePreviabilizacionForm = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePreviabilizacion.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updatePreviabilizacionForm.put = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePreviabilizacion.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::updatePreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:254
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}'
*/
updatePreviabilizacionForm.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePreviabilizacion.url(args, options),
    method: 'post',
})

updatePreviabilizacion.form = updatePreviabilizacionForm

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
export const destroyPreviabilizacion = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyPreviabilizacion.url(args, options),
    method: 'delete',
})

destroyPreviabilizacion.definition = {
    methods: ["delete","post"],
    url: '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete',
} satisfies RouteDefinition<["delete","post"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyPreviabilizacion.url = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions) => {
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

    return destroyPreviabilizacion.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{previabilizacionId}', parsedArgs.previabilizacionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyPreviabilizacion.delete = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyPreviabilizacion.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyPreviabilizacion.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: destroyPreviabilizacion.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
const destroyPreviabilizacionForm = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyPreviabilizacion.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyPreviabilizacionForm.delete = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyPreviabilizacion.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroyPreviabilizacion
* @see app/Http/Controllers/BancoProyectoController.php:282
* @route '/banco-proyectos/{proyectoId}/previabilizaciones/{previabilizacionId}/delete'
*/
destroyPreviabilizacionForm.post = (args: { proyectoId: string | number, previabilizacionId: string | number } | [proyectoId: string | number, previabilizacionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyPreviabilizacion.url(args, options),
    method: 'post',
})

destroyPreviabilizacion.form = destroyPreviabilizacionForm

const BancoProyectoController = { index, map, show, storeDetalle, updateDetalle, destroyDetalle, destroyDocumento, storePreviabilizacion, updatePreviabilizacion, destroyPreviabilizacion }

export default BancoProyectoController