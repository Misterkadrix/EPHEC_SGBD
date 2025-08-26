<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Group {
    id: number;
    name: string;
    quantity: number;
    min_size: number;
    max_size: number;
    university: {
        name: string;
        code: string;
    };
    academic_year: {
        name: string;
    };
    main_site: {
        name: string;
    };
    created_at: string;
}

interface Props {
    groups: Group[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Groupes', href: '/groups' },
];

const getSizeStatus = (quantity: number, minSize: number, maxSize: number) => {
    if (quantity < minSize) return { color: 'bg-red-100 text-red-800', label: 'Trop petit' };
    if (quantity > maxSize) return { color: 'bg-orange-100 text-orange-800', label: 'Trop grand' };
    return { color: 'bg-green-100 text-green-800', label: 'OK' };
};
</script>

<template>
    <Head title="Groupes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Groupes</h1>
                <Link :href="route('groups.create')">
                    <Button>
                        Créer un groupe
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des groupes -->
            <div v-if="groups.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="group in groups" :key="group.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <CardTitle class="text-lg">{{ group.name }}</CardTitle>
                            <span class="text-xs px-2 py-1 rounded-full" :class="getSizeStatus(group.quantity, group.min_size, group.max_size).color">
                                {{ getSizeStatus(group.quantity, group.min_size, group.max_size).label }}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600">
                                Université: {{ group.university.name }} ({{ group.university.code }})
                            </div>
                            <div class="text-sm text-gray-600">
                                Année: {{ group.academic_year.name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Site principal: {{ group.main_site.name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Taille: <span class="font-semibold">{{ group.quantity }}</span> étudiants
                                <span class="text-xs text-gray-500 ml-2">
                                    ({{ group.min_size }}-{{ group.max_size }})
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">
                                {{ new Date(group.created_at).toLocaleDateString() }}
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('groups.edit', group.id)">
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
            
            <!-- Message si aucun groupe -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun groupe n'a été créé pour le moment.</p>
                <Link :href="route('groups.create')">
                    <Button>
                        Créer votre premier groupe
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
