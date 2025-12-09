import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
export const destroy = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
destroy.url = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{proyectoId}', parsedArgs.proyectoId.toString())
            .replace('{detalleId}', parsedArgs.detalleId.toString())
            .replace('{documentoId}', parsedArgs.documentoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
destroy.delete = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BancoProyectoController::destroy
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
const destroyForm = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/BancoProyectoController.php:206
* @route '/banco-proyectos/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}'
*/
destroyForm.delete = (args: { proyectoId: string | number, detalleId: string | number, documentoId: string | number } | [proyectoId: string | number, detalleId: string | number, documentoId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const documentos = {
    destroy: Object.assign(destroy, destroy),
}

export default documentos