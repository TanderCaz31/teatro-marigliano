<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

const props = defineProps({
    performances: Array,
    filters: Object,
});

// perserveState and "...props.filters" fed back into useForm together allow to retain the state of the filters after a page reload
const form = useForm({ ...props.filters });
const search = () => form.get(route('performances.index'), { preserveState: true });

const formatDate = (value) =>
    new Date(value).toLocaleString('it-IT', { dateStyle: 'long', timeStyle: 'short' });
</script>

<template>
    <Head title="Prenotazioni" />

    <TheaterLayout>
        <h2>Esibizioni</h2>

        <form @submit.prevent="search">
            <div>
                <label for="search">Titolo</label>
                <input id="search" v-model="form.search" type="text" />
            </div>

            <div>
                <label for="min_duration">Durata minima: {{ form.min_duration }} min</label>
                <input id="min_duration" v-model.number="form.min_duration" type="range" min="40" max="240" step="10" />
            </div>

            <div>
                <label for="max_duration">Durata massima: {{ form.max_duration }} min</label>
                <input id="max_duration" v-model.number="form.max_duration" type="range" min="40" max="240" step="10" />
            </div>

            <div>
                <label>
                    <input v-model="form.past" type="checkbox" />
                    Mostra esibizioni passate
                </label>
            </div>

            <button type="submit" :disabled="form.processing">Cerca</button>
        </form>

        <p v-if="performances.length === 0">Nessuna esibizione corrisponde ai filtri.</p>

        <table v-else>
            <thead>
            <tr>
                <th>Data</th>
                <th>Spettacolo</th>
                <th>Durata</th>
                <th>Sala</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="performance in performances" :key="performance.id">
                <td>{{ formatDate(performance.starts_at) }}</td>
                <td>
                    <Link :href="route('shows.show', performance.show.id)">{{ performance.show.title }}</Link>
                    <span v-if="performance.show.is_featured" class="tag">in evidenza</span>
                </td>
                <td>{{ performance.show.duration_minutes }} min</td>
                <td>{{ performance.venue.name }}</td>
            </tr>
            </tbody>
        </table>
    </TheaterLayout>
</template>
