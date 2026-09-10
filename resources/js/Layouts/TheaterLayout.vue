<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="theater">
        <header>
            <h1><Link :href="route('shows.index')">Teatro Marigliano</Link></h1>

            <nav>
                <Link :href="route('shows.index')">Spettacoli</Link>

                <template v-if="$page.props.auth.user"> <!-- different behaviour based on user auth -->
                    <Link :href="route('profile.edit')">{{ $page.props.auth.user.name }}</Link>
                    <Link :href="route('logout')" method="post" as="button">Esci</Link>
                </template>
                <template v-else>
                    <Link :href="route('login')">Accedi</Link>
                    <Link :href="route('register')">Registrati</Link>
                </template>
            </nav>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
