<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

const props = defineProps({
    show: Object,
});

const formatDate = (value) =>
    new Date(value).toLocaleString('it-IT', { dateStyle: 'long', timeStyle: 'short' });

const destroy = () => {
    if (confirm('Eliminare lo spettacolo? Verranno eliminate anche le esibizioni collegate.')) {
        router.delete(route('shows.destroy', props.show.id));
    }
};
</script>

<template>
    <Head :title="show.title" />

    <TheaterLayout>
        <h2>{{ show.title }}</h2>

        <p>
            Durata: {{ show.duration_minutes }} minuti
            <span v-if="show.is_featured" class="tag">in evidenza</span>
        </p>

        <p v-if="show.description">{{ show.description }}</p>

        <h2>Esibizioni</h2>

        <p v-if="show.performances.length === 0">Nessuna esibizione in programma.</p>

        <table v-else>
            <thead>
            <tr>
                <th>Data</th>
                <th>Sala</th>
                <th>Capienza</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="performance in show.performances" :key="performance.id">
                <td>{{ formatDate(performance.starts_at) }}</td>
                <td>{{ performance.venue.name }}</td>
                <td>{{ performance.capacity }}</td>
            </tr>
            </tbody>
        </table>

        <!-- TODO admin-only once the role column exists -->
        <p v-if="$page.props.auth.user" class="actions">
            <Link :href="route('shows.edit', show.id)">Modifica</Link>
            <button @click="destroy">Elimina</button>
        </p>
    </TheaterLayout>
</template>
