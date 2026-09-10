<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

const form = useForm({
    title: '',
    description: '',
    duration_minutes: 90,
    is_featured: false,
});
</script>

<template>
    <Head title="Nuovo spettacolo" />

    <TheaterLayout>
        <h2>Nuovo spettacolo</h2>

        <!-- .prevent stops the browser's native submit so Inertia can be used instead -->
        <form @submit.prevent="form.post(route('shows.store'))">
            <div>
                <label for="title">Titolo</label>
                <input id="title" v-model="form.title" type="text" />
                <p v-if="form.errors.title" class="error">{{ form.errors.title }}</p>
            </div>

            <div>
                <label for="description">Descrizione</label>
                <textarea id="description" v-model="form.description" rows="5"></textarea>
                <p v-if="form.errors.description" class="error">{{ form.errors.description }}</p>
            </div>

            <div>
                <label for="duration">Durata (minuti)</label>
                <input id="duration" v-model.number="form.duration_minutes" type="number" />
                <p v-if="form.errors.duration_minutes" class="error">{{ form.errors.duration_minutes }}</p>
            </div>

            <div>
                <label>
                    <input v-model="form.is_featured" type="checkbox" />
                    In evidenza
                </label>
            </div>

            <button type="submit" :disabled="form.processing">Salva</button>
        </form>
    </TheaterLayout>
</template>
