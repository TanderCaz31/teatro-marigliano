<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TheaterLayout from '@/Layouts/TheaterLayout.vue';

const props = defineProps({
    shows: Array,
    venues: Array,
});

// computed() recalculates only when shows changes
// featured shows have their own list
const featuredShows = computed(() => props.shows.filter((show) => show.is_featured));
const otherShows = computed(() => props.shows.filter((show) => !show.is_featured));

const form = useForm({
    show_id: '',
    venue_id: '',
    starts_at: '',
});
</script>

<template>
    <Head title="Nuova esibizione" />

    <TheaterLayout>
        <h2>Nuova esibizione</h2>

        <form @submit.prevent="form.post(route('performances.store'))">
            <div>
                <label for="show">Spettacolo</label>
                <select id="show" v-model="form.show_id">
                    <option value="" disabled>Scegli uno spettacolo</option>
                    <optgroup v-if="featuredShows.length" label="In evidenza">
                        <option v-for="show in featuredShows" :key="show.id" :value="show.id">{{ show.title }}</option>
                    </optgroup>
                    <optgroup label="Altri spettacoli">
                        <option v-for="show in otherShows" :key="show.id" :value="show.id">{{ show.title }}</option>
                    </optgroup>
                </select>
                <p v-if="form.errors.show_id" class="error">{{ form.errors.show_id }}</p>
            </div>

            <div>
                <label for="venue">Sala</label>
                <select id="venue" v-model="form.venue_id">
                    <option value="" disabled>Scegli una sala</option>
                    <option v-for="venue in venues" :key="venue.id" :value="venue.id">
                        {{ venue.name }} ({{ venue.total_seats }} posti)
                    </option>
                </select>
                <p v-if="form.errors.venue_id" class="error">{{ form.errors.venue_id }}</p>
            </div>

            <div>
                <label for="starts_at">Data e ora</label>
                <input id="starts_at" v-model="form.starts_at" type="datetime-local" />
                <p v-if="form.errors.starts_at" class="error">{{ form.errors.starts_at }}</p>
            </div>

            <button type="submit" :disabled="form.processing">Salva</button>
        </form>
    </TheaterLayout>
</template>
