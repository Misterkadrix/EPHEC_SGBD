<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import Calendar from '../components/SimpleCalendar.vue';

const page = usePage();
const calendarData = (page.props as any).calendarData;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 overflow-x-auto">
            <!-- Titre et description -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    SGBD_KAD - Tableau de bord
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Gestion des sessions de cours et planning universitaire
                </p>
            </div>

            <!-- Calendrier principal -->
            <div class="w-full">
                <div v-if="calendarData && calendarData.universities && calendarData.universities.length > 0">
                    <Calendar 
                        :universities="calendarData.universities"
                        :groups="calendarData.groups || []"
                        :sessions="calendarData.sessions || []"
                        :deplacements="calendarData.deplacements || []"
                    />
                </div>
                <div v-else class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Chargement du calendrier...</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
