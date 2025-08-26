<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface University {
    id: number;
    code: string;
    name: string;
    created_at: string;
}

interface Props {
    universities: University[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Universities', href: '/universities' },
];

const destroyForm = useForm({});
const destroy = (id: number) => {
    if (!confirm('Supprimer cette université ?')) return;
    destroyForm.delete(route('universities.destroy', id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Universities" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Universités</h1>
                <Link :href="route('universities.create')">
                    <Button>Créer une université</Button>
                </Link>
            </div>

            <div v-if="universities.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="u in universities" :key="u.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center justify-between">
                            <span>{{ u.name }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-200">{{ u.code }}</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ new Date(u.created_at).toLocaleDateString() }}</span>
                            <div class="flex gap-2">
                                <Link :href="route('universities.edit', u.id)">
                                    <Button variant="outline">Modifier</Button>
                                </Link>
                                <Button variant="destructive" @click="destroy(u.id)" :disabled="destroyForm.processing">
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucune université.</p>
                <Link :href="route('universities.create')">
                    <Button>Créer votre première université</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
