import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/articulos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::index
* @see app/Http/Controllers/ArticuloController.php:10
* @route '/articulos'
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
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/articulos/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::create
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\ArticuloController::store
* @see app/Http/Controllers/ArticuloController.php:16
* @route '/articulos'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/articulos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ArticuloController::store
* @see app/Http/Controllers/ArticuloController.php:16
* @route '/articulos'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::store
* @see app/Http/Controllers/ArticuloController.php:16
* @route '/articulos'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ArticuloController::store
* @see app/Http/Controllers/ArticuloController.php:16
* @route '/articulos'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ArticuloController::store
* @see app/Http/Controllers/ArticuloController.php:16
* @route '/articulos'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
export const show = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/articulos/{articulo}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
show.url = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { articulo: args }
    }

    if (Array.isArray(args)) {
        args = {
            articulo: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        articulo: args.articulo,
    }

    return show.definition.url
            .replace('{articulo}', parsedArgs.articulo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
show.get = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
show.head = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
const showForm = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
showForm.get = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::show
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}'
*/
showForm.head = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
export const edit = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/articulos/{articulo}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
edit.url = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { articulo: args }
    }

    if (Array.isArray(args)) {
        args = {
            articulo: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        articulo: args.articulo,
    }

    return edit.definition.url
            .replace('{articulo}', parsedArgs.articulo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
edit.get = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
edit.head = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
const editForm = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
editForm.get = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ArticuloController::edit
* @see app/Http/Controllers/ArticuloController.php:0
* @route '/articulos/{articulo}/edit'
*/
editForm.head = (args: { articulo: string | number } | [articulo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
export const update = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/articulos/{articulo}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
update.url = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { articulo: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { articulo: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            articulo: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        articulo: typeof args.articulo === 'object'
        ? args.articulo.id
        : args.articulo,
    }

    return update.definition.url
            .replace('{articulo}', parsedArgs.articulo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
update.put = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
update.patch = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
const updateForm = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
updateForm.put = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ArticuloController::update
* @see app/Http/Controllers/ArticuloController.php:28
* @route '/articulos/{articulo}'
*/
updateForm.patch = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\ArticuloController::destroy
* @see app/Http/Controllers/ArticuloController.php:45
* @route '/articulos/{articulo}'
*/
export const destroy = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/articulos/{articulo}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ArticuloController::destroy
* @see app/Http/Controllers/ArticuloController.php:45
* @route '/articulos/{articulo}'
*/
destroy.url = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { articulo: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { articulo: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            articulo: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        articulo: typeof args.articulo === 'object'
        ? args.articulo.id
        : args.articulo,
    }

    return destroy.definition.url
            .replace('{articulo}', parsedArgs.articulo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ArticuloController::destroy
* @see app/Http/Controllers/ArticuloController.php:45
* @route '/articulos/{articulo}'
*/
destroy.delete = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ArticuloController::destroy
* @see app/Http/Controllers/ArticuloController.php:45
* @route '/articulos/{articulo}'
*/
const destroyForm = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ArticuloController::destroy
* @see app/Http/Controllers/ArticuloController.php:45
* @route '/articulos/{articulo}'
*/
destroyForm.delete = (args: { articulo: number | { id: number } } | [articulo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ArticuloController = { index, create, store, show, edit, update, destroy }

export default ArticuloController