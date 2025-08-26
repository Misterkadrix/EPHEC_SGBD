<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Equipment {
    id: number;
    site: {
        name: string;
        university: {
            name: string;
            code: string;
        };
    };
    type: {
        label: string;
    };
    is_mobile: boolean;
    fixed_room?: {
        name: string;
    };
    created_at: string;
}

interface Props {
    equipment: Equipment[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Équipements', href: '/equipment' },
];
</script>

<template>
    <Head title="Équipements" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Équipements</h1>
                <Link :href="route('equipment.create')">
                    <Button>
                        Créer un équipement
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des équipements -->
            <div v-if="equipment.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="item in equipment" :key="item.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="text-lg">{{ item.type.label }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    Site: <span class="font-semibold">{{ item.site.name }}</span>
                                </span>
                                <span class="text-xs px-2 py-1 rounded-full" :class="item.is_mobile ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                                    {{ item.is_mobile ? 'Mobile' : 'Fixe' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                Université: {{ item.site.university.name }} ({{ item.site.university.code }})
                            </div>
                            <div v-if="!item.is_mobile && item.fixed_room" class="text-sm text-gray-600">
                                Salle: {{ item.fixed_room.name }}
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">
                                {{ new Date(item.created_at).toLocaleDateString() }}
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('equipment.edit', item.id)">
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
            
            <!-- Message si aucun équipement -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun équipement n'a été créé pour le moment.</p>
                <Link :href="route('equipment.create')">
                    <Button>
                        Créer votre premier équipement
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
