<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Room {
    id: number;
    name: string;
    capacity: number;
    description?: string;
    site: {
        name: string;
        university: {
            name: string;
            code: string;
        };
    };
    created_at: string;
}

interface Props {
    rooms: Room[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Salles', href: '/rooms' },
];

const destroyForm = useForm({});
const destroy = (id: number) => {
    if (!confirm('Supprimer cette salle ?')) return;
    destroyForm.delete(route('rooms.destroy', id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Salles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Salles</h1>
                <Link :href="route('rooms.create')">
                    <Button>Créer une salle</Button>
                </Link>
            </div>

            <div v-if="rooms.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="room in rooms" :key="room.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center justify-between">
                            <span>{{ room.name }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-green-200">{{ room.capacity }} places</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2 mb-4">
                            <p class="text-sm text-gray-600">
                                <strong>Site:</strong> {{ room.site.name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Université:</strong> {{ room.site.university.name }} ({{ room.site.university.code }})
                            </p>
                            <p v-if="room.description" class="text-sm text-gray-600">
                                <strong>Description:</strong> {{ room.description }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ new Date(room.created_at).toLocaleDateString() }}</span>
                            <div class="flex gap-2">
                                <Link :href="route('rooms.edit', room.id)">
                                    <Button variant="outline">Modifier</Button>
                                </Link>
                                <Button variant="destructive" @click="destroy(room.id)" :disabled="destroyForm.processing">
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucune salle.</p>
                <Link :href="route('rooms.create')">
                    <Button>Créer votre première salle</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
