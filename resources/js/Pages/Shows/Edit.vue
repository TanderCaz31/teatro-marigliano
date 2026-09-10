<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

const props = defineProps({
    show: Object,
});

// this uses useForm like the Create page, but it loads the dats from the incoming props
// the data is then shown in the fields
const form = useForm({
    title: props.show.title,
    description: props.show.description,
    duration_minutes: props.show.duration_minutes,
    is_featured: props.show.is_featured,
});
</script>

<template>
    <Head :title="`Modifica ${show.title}`" />

    <TheaterLayout>
        <h2>Modifica spettacolo</h2>

        <form @submit.prevent="form.put(route('shows.update', show.id))">
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
