<script setup>
import { Head } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

defineProps({
    venues: Array,
});

const formatDate = (value) =>
    new Date(value).toLocaleString('it-IT', { dateStyle: 'long', timeStyle: 'short' });
</script>

<template>
    <Head title="Le nostre sale" />

    <TheaterLayout>
        <section v-for="venue in venues" :key="venue.id">
            <h2>{{ venue.name }}</h2>

            <p>
                {{ venue.total_seats }} posti su {{ venue.rows }} file - {{ venue.upcoming_performances_count }} in programma
            </p>

            <p v-if="venue.upcoming_performances.length === 0">Nessuna esibizione in programma.</p>

            <table v-else>
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Spettacolo</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="performance in venue.upcoming_performances" :key="performance.id">
                    <td>{{ formatDate(performance.starts_at) }}</td>
                    <td>{{ performance.show.title }}</td>
                </tr>
                </tbody>
            </table>
        </section>
    </TheaterLayout>
</template>
