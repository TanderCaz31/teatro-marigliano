<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

defineProps({
    shows: Array,
});
</script>

<template>
    <Head title="Spettacoli" />

    <TheaterLayout>
        <h2>Catalogo spettacoli</h2>

        <!-- TODO admin-only once the role column exists -->
        <p v-if="$page.props.auth.user">
            <Link :href="route('shows.create')">Aggiungi uno spettacolo</Link>
        </p>

        <p v-if="shows.length === 0">Nessuno spettacolo in cartellone.</p>

        <table v-else>
            <thead>
            <tr>
                <th>Titolo</th>
                <th>Durata</th>
                <th>Esibizioni</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="show in shows" :key="show.id">
                <td>
                    <Link :href="route('shows.show', show.id)">{{ show.title }}</Link>
                    <span v-if="show.is_featured" class="tag">in evidenza</span>
                </td>
                <td>{{ show.duration_minutes }} min</td>
                <td>{{ show.performances_count }}</td> <!-- exists thanks to "withCount()" on ShowController -->
            </tr>
            </tbody>
        </table>
    </TheaterLayout>
</template>
