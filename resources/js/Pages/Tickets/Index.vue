<script setup>
import { Head, router } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

defineProps({
    tickets: Array,
});

const formatDate = (dateToFormat) =>
    new Date(dateToFormat).toLocaleString('it-IT', { dateStyle: 'long', timeStyle: 'short' });

const isUpcoming = (dateToCheck) => new Date(dateToCheck) > new Date();

const cancel = (ticket) => {
    if (confirm('Annullare la prenotazione?')) {
        router.delete(route('tickets.destroy', ticket.id));
    }
};
</script>

<template>
    <Head title="I miei biglietti" />

    <TheaterLayout>
        <h2>I miei biglietti</h2>

        <p v-if="tickets.length === 0">Non hai ancora prenotato nessun biglietto.</p>

        <table v-else>
            <thead>
            <tr>
                <th>Spettacolo</th>
                <th>Data</th>
                <th>Sala</th>
                <th>Posto</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="ticket in tickets" :key="ticket.id">
                <td>{{ ticket.performance.show.title }}</td>
                <td>{{ formatDate(ticket.performance.starts_at) }}</td>
                <td>{{ ticket.performance.venue.name }}</td>
                <td>{{ ticket.seat_code }}</td>
                <td>
                    <button v-if="isUpcoming(ticket.performance.starts_at)" @click="cancel(ticket)">Annulla</button>
                </td>
            </tr>
            </tbody>
        </table>
    </TheaterLayout>
</template>
