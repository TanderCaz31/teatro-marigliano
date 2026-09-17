<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// The password is cleared after every attempt, successful or not
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="Accedi" />

    <TheaterLayout>
        <h2>Accedi</h2>

        <p v-if="status">{{ status }}</p>

        <form @submit.prevent="submit">
            <div>
                <label for="email">Email</label>
                <input id="email" v-model="form.email" type="email" autofocus autocomplete="username" />
                <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password">Password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="current-password" />
                <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
            </div>

            <div>
                <label>
                    <input v-model="form.remember" type="checkbox" />
                    Ricordami
                </label>
            </div>

            <p class="actions">
                <button type="submit" :disabled="form.processing">Accedi</button>
                <Link v-if="canResetPassword" :href="route('password.request')">Password dimenticata?</Link>
                <Link :href="route('register')">Non hai un account? Registrati</Link>
            </p>
        </form>
    </TheaterLayout>
</template>
