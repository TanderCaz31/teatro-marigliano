<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

defineProps({
    tickets: Array,
    showAll: Boolean,
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
        <h2>{{ showAll ? 'Tutti i biglietti' : 'I miei biglietti' }}</h2>

        <p v-if="$page.props.auth.user?.role === 'admin'">
            <Link v-if="showAll" :href="route('tickets.index')">Mostra solo i miei biglietti</Link>
            <Link v-else :href="route('tickets.index', { showAll: 1 })">Mostra tutti i biglietti</Link>
        </p>

        <p v-if="tickets.length === 0">
            {{ showAll ? 'Nessun biglietto prenotato.' : 'Non hai ancora prenotato un biglietto.' }}
        </p>

        <table v-else>
            <thead>
            <tr>
                <th>Spettacolo</th>
                <th>Data</th>
                <th>Sala</th>
                <th>Posto</th>
                <th v-if="showAll">Utente</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="ticket in tickets" :key="ticket.id">
                <td>{{ ticket.performance.show.title }}</td>
                <td>{{ formatDate(ticket.performance.starts_at) }}</td>
                <td>{{ ticket.performance.venue.name }}</td>
                <td>{{ ticket.seat_code }}</td>
                <td v-if="showAll">{{ ticket.user.name }}</td>
                <td>
                    <button v-if="isUpcoming(ticket.performance.starts_at)" @click="cancel(ticket)">Annulla</button>
                </td>
            </tr>
            </tbody>
        </table>
    </TheaterLayout>
</template>
