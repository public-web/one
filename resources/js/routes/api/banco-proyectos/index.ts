import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
*/
export const stats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/api/banco-proyectos/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
*/
stats.url = (options?: RouteQueryOptions) => {
    return stats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
*/
stats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
*/
stats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
*/
const statsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
*/
statsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::stats
* @see app/Http/Controllers/Api/BancoProyectoController.php:276
* @route '/api/banco-proyectos/stats'
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

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/banco-proyectos/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::exportMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:313
* @route '/api/banco-proyectos/export'
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
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
export const template = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: template.url(options),
    method: 'get',
})

template.definition = {
    methods: ["get","head"],
    url: '/api/banco-proyectos/template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
template.url = (options?: RouteQueryOptions) => {
    return template.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
template.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: template.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
template.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: template.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
const templateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: template.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
templateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: template.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::template
* @see app/Http/Controllers/Api/BancoProyectoController.php:335
* @route '/api/banco-proyectos/template'
*/
templateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: template.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

template.form = templateForm

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::importMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:402
* @route '/api/banco-proyectos/import'
*/
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/api/banco-proyectos/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::importMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:402
* @route '/api/banco-proyectos/import'
*/
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::importMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:402
* @route '/api/banco-proyectos/import'
*/
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::importMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:402
* @route '/api/banco-proyectos/import'
*/
const importMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::importMethod
* @see app/Http/Controllers/Api/BancoProyectoController.php:402
* @route '/api/banco-proyectos/import'
*/
importMethodForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importMethod.url(options),
    method: 'post',
})

importMethod.form = importMethodForm

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::restore
* @see app/Http/Controllers/Api/BancoProyectoController.php:224
* @route '/api/banco-proyectos/{id}/restore'
*/
export const restore = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

restore.definition = {
    methods: ["post"],
    url: '/api/banco-proyectos/{id}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::restore
* @see app/Http/Controllers/Api/BancoProyectoController.php:224
* @route '/api/banco-proyectos/{id}/restore'
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
* @see \App\Http\Controllers\Api\BancoProyectoController::restore
* @see app/Http/Controllers/Api/BancoProyectoController.php:224
* @route '/api/banco-proyectos/{id}/restore'
*/
restore.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::restore
* @see app/Http/Controllers/Api/BancoProyectoController.php:224
* @route '/api/banco-proyectos/{id}/restore'
*/
const restoreForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::restore
* @see app/Http/Controllers/Api/BancoProyectoController.php:224
* @route '/api/banco-proyectos/{id}/restore'
*/
restoreForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

restore.form = restoreForm

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::forceDelete
* @see app/Http/Controllers/Api/BancoProyectoController.php:251
* @route '/api/banco-proyectos/{id}/force'
*/
export const forceDelete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: forceDelete.url(args, options),
    method: 'delete',
})

forceDelete.definition = {
    methods: ["delete"],
    url: '/api/banco-proyectos/{id}/force',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::forceDelete
* @see app/Http/Controllers/Api/BancoProyectoController.php:251
* @route '/api/banco-proyectos/{id}/force'
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
* @see \App\Http\Controllers\Api\BancoProyectoController::forceDelete
* @see app/Http/Controllers/Api/BancoProyectoController.php:251
* @route '/api/banco-proyectos/{id}/force'
*/
forceDelete.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: forceDelete.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::forceDelete
* @see app/Http/Controllers/Api/BancoProyectoController.php:251
* @route '/api/banco-proyectos/{id}/force'
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
* @see \App\Http\Controllers\Api\BancoProyectoController::forceDelete
* @see app/Http/Controllers/Api/BancoProyectoController.php:251
* @route '/api/banco-proyectos/{id}/force'
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
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/banco-proyectos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::index
* @see app/Http/Controllers/Api/BancoProyectoController.php:25
* @route '/api/banco-proyectos'
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
* @see \App\Http\Controllers\Api\BancoProyectoController::store
* @see app/Http/Controllers/Api/BancoProyectoController.php:75
* @route '/api/banco-proyectos'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/banco-proyectos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::store
* @see app/Http/Controllers/Api/BancoProyectoController.php:75
* @route '/api/banco-proyectos'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::store
* @see app/Http/Controllers/Api/BancoProyectoController.php:75
* @route '/api/banco-proyectos'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::store
* @see app/Http/Controllers/Api/BancoProyectoController.php:75
* @route '/api/banco-proyectos'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::store
* @see app/Http/Controllers/Api/BancoProyectoController.php:75
* @route '/api/banco-proyectos'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
export const show = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/banco-proyectos/{proyecto}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
show.url = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proyecto: args }
    }

    if (Array.isArray(args)) {
        args = {
            proyecto: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyecto: args.proyecto,
    }

    return show.definition.url
            .replace('{proyecto}', parsedArgs.proyecto.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
show.get = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
show.head = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
const showForm = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
showForm.get = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::show
* @see app/Http/Controllers/Api/BancoProyectoController.php:126
* @route '/api/banco-proyectos/{proyecto}'
*/
showForm.head = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
export const update = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/banco-proyectos/{proyecto}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
update.url = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proyecto: args }
    }

    if (Array.isArray(args)) {
        args = {
            proyecto: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyecto: args.proyecto,
    }

    return update.definition.url
            .replace('{proyecto}', parsedArgs.proyecto.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
update.put = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
update.patch = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
const updateForm = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
updateForm.put = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::update
* @see app/Http/Controllers/Api/BancoProyectoController.php:145
* @route '/api/banco-proyectos/{proyecto}'
*/
updateForm.patch = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::destroy
* @see app/Http/Controllers/Api/BancoProyectoController.php:198
* @route '/api/banco-proyectos/{proyecto}'
*/
export const destroy = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/banco-proyectos/{proyecto}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::destroy
* @see app/Http/Controllers/Api/BancoProyectoController.php:198
* @route '/api/banco-proyectos/{proyecto}'
*/
destroy.url = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proyecto: args }
    }

    if (Array.isArray(args)) {
        args = {
            proyecto: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proyecto: args.proyecto,
    }

    return destroy.definition.url
            .replace('{proyecto}', parsedArgs.proyecto.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::destroy
* @see app/Http/Controllers/Api/BancoProyectoController.php:198
* @route '/api/banco-proyectos/{proyecto}'
*/
destroy.delete = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::destroy
* @see app/Http/Controllers/Api/BancoProyectoController.php:198
* @route '/api/banco-proyectos/{proyecto}'
*/
const destroyForm = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BancoProyectoController::destroy
* @see app/Http/Controllers/Api/BancoProyectoController.php:198
* @route '/api/banco-proyectos/{proyecto}'
*/
destroyForm.delete = (args: { proyecto: string | number } | [proyecto: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const bancoProyectos = {
    stats: Object.assign(stats, stats),
    export: Object.assign(exportMethod, exportMethod),
    template: Object.assign(template, template),
    import: Object.assign(importMethod, importMethod),
    restore: Object.assign(restore, restore),
    forceDelete: Object.assign(forceDelete, forceDelete),
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default bancoProyectos