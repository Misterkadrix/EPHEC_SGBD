<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Holiday {
    id: number;
    name: string;
    date: string;
    year: number;
    university?: {
        name: string;
        code: string;
    };
    created_at: string;
}

interface Props {
    holidays: Holiday[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Fériés', href: '/holidays' },
];

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Fériés" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Fériés</h1>
                <Link :href="route('holidays.create')">
                    <Button>
                        Créer un férié
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des fériés -->
            <div v-if="holidays.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="holiday in holidays" :key="holiday.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <CardTitle class="text-lg">{{ holiday.name }}</CardTitle>
                            <span class="text-xs px-2 py-1 rounded-full" :class="holiday.university ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                                {{ holiday.university ? 'Universitaire' : 'Global' }}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600">
                                <strong>Date:</strong> {{ formatDate(holiday.date) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <strong>Année:</strong> {{ holiday.year }}
                            </div>
                            <div v-if="holiday.university" class="text-sm text-gray-600">
                                <strong>Université:</strong> {{ holiday.university.name }} ({{ holiday.university.code }})
                            </div>
                            <div v-else class="text-sm text-gray-600">
                                <strong>Type:</strong> Férié national/global
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">
                                {{ new Date(holiday.created_at).toLocaleDateString() }}
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('holidays.edit', holiday.id)">
                                    <Button variant="outline" size="sm">Modifier</Button>
                                </Link>
                                <Button variant="outline" size="sm" class="text-red-600 hover:text-red-700">
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Message si aucun férié -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun férié n'a été créé pour le moment.</p>
                <Link :href="route('holidays.create')">
                    <Button>
                        Créer votre premier férié
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
