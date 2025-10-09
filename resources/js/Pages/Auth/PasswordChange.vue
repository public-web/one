<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    isFirstLogin?: boolean;
}>();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    router.post('/password/change', form.data(), {
        onFinish: () => {
            form.reset('current_password', 'password', 'password_confirmation');
        },
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            // Set form errors
            Object.keys(errors).forEach((key) => {
                form.setError(key, errors[key]);
            });
        },
    });
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <Head title="Cambio de Contraseña Requerido" />

        <Card class="w-full max-w-md">
            <CardHeader class="text-center">
                <CardTitle class="text-2xl font-bold text-gray-900"> Cambio de Contraseña Requerido </CardTitle>
                <CardDescription>
                    <template v-if="isFirstLogin"> Por seguridad, debe cambiar su contraseña temporal antes de continuar. </template>
                    <template v-else> Debe actualizar su contraseña para continuar. </template>
                </CardDescription>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <Label for="current_password">Contraseña Actual</Label>
                        <div class="relative">
                            <Input
                                id="current_password"
                                v-model="form.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                class="mt-1 pr-10"
                                required
                                autofocus
                                autocomplete="current-password"
                            />
                            <button
                                type="button"
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
                            >
                                <Eye v-if="!showCurrentPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.current_password" />
                    </div>

                    <div>
                        <Label for="password">Nueva Contraseña</Label>
                        <div class="relative">
                            <Input
                                id="password"
                                v-model="form.password"
                                :type="showNewPassword ? 'text' : 'password'"
                                class="mt-1 pr-10"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                @click="showNewPassword = !showNewPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
                            >
                                <Eye v-if="!showNewPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <Label for="password_confirmation">Confirmar Nueva Contraseña</Label>
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="mt-1 pr-10"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
                            >
                                <Eye v-if="!showConfirmPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <Button type="submit" class="w-full" :disabled="form.processing">
                        {{ form.processing ? 'Actualizando...' : 'Actualizar Contraseña' }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
