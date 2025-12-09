<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Previabilización Social', href: '/previabilizacion-social/dashboard' },
];

const goToBancoProyectos = () => {
    router.visit('/banco-proyectos');
};
</script>

<template>
    <Head title="Dashboard - Previabilización Social" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Previabilización Social</h1>
                <p class="text-muted-foreground mt-2">
                    Gestión de previabilizaciones para proyectos del banco de proyectos
                </p>
            </div>

            <!-- Main Card - Access to Banco de Proyectos -->
            <Card class="border-2 hover:border-primary transition-colors cursor-pointer" @click="goToBancoProyectos">
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-primary/10 rounded-lg">
                            <svg
                                class="w-12 h-12 text-primary"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <CardTitle class="text-2xl">Banco de Proyectos</CardTitle>
                            <CardDescription class="text-base mt-1">
                                Accede a la lista de proyectos para gestionar previabilizaciones sociales
                            </CardDescription>
                        </div>
                        <div>
                            <Button size="lg">
                                Ir a Proyectos →
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                        <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg">
                            <div class="p-2 bg-blue-100 rounded">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ver proyectos</p>
                                <p class="font-semibold text-gray-900">Consulta completa</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg">
                            <div class="p-2 bg-green-100 rounded">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Crear</p>
                                <p class="font-semibold text-gray-900">Previabilizaciones</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-purple-50 rounded-lg">
                            <div class="p-2 bg-purple-100 rounded">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Gestionar</p>
                                <p class="font-semibold text-gray-900">Registros</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle>¿Qué es una Previabilización Social?</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">
                            La previabilización social es el proceso mediante el cual se evalúa y registra la
                            información relacionada con la priorización y participación de las juntas de acción
                            comunal en los proyectos del banco de proyectos.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Funcionalidades Disponibles</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ul class="space-y-2 text-muted-foreground">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Ver todos los proyectos del banco</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Crear nuevas previabilizaciones sociales</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Editar y actualizar registros existentes</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Consultar información detallada de cada proyecto</span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
