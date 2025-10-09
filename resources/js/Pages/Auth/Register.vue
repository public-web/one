<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { useForm, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account. A temporary password will be provided.">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>Nota:</strong> Se generará una contraseña temporal automáticamente.
                        Deberá cambiarla en su primer inicio de sesión por seguridad.
                    </p>
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="3" :disabled="form.processing" data-test="register-user-button">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="login()" class="underline underline-offset-4" :tabindex="4">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
