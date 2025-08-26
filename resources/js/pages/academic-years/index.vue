<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface AcademicYear {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    state: 'planned' | 'active' | 'archived';
    university: {
        name: string;
        code: string;
    };
    created_at: string;
}

interface Props {
    academicYears: AcademicYear[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Années académiques', href: '/academic-years' },
];

const getStateColor = (state: string) => {
    switch (state) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'planned': return 'bg-blue-100 text-blue-800';
        case 'archived': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStateLabel = (state: string) => {
    switch (state) {
        case 'active': return 'Active';
        case 'planned': return 'Planifiée';
        case 'archived': return 'Archivée';
        default: return state;
    }
};
</script>

<template>
    <Head title="Années académiques" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Années académiques</h1>
                <Link :href="route('academic-years.create')">
                    <Button>
                        Créer une année académique
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des années académiques -->
            <div v-if="academicYears.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="year in academicYears" :key="year.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <CardTitle class="text-lg">{{ year.name }}</CardTitle>
                            <span class="text-xs px-2 py-1 rounded-full" :class="getStateColor(year.state)">
                                {{ getStateLabel(year.state) }}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600">
                                Université: {{ year.university.name }} ({{ year.university.code }})
                            </div>
                            <div class="text-sm text-gray-600">
                                Période: {{ new Date(year.start_date).toLocaleDateString() }} - {{ new Date(year.end_date).toLocaleDateString() }}
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">
                                {{ new Date(year.created_at).toLocaleDateString() }}
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('academic-years.edit', year.id)">
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
            
            <!-- Message si aucune année académique -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucune année académique n'a été créée pour le moment.</p>
                <Link :href="route('academic-years.create')">
                    <Button>
                        Créer votre première année académique
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
