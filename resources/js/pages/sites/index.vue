<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Site {
    id: number;
    name: string;
    timezone: string;
    day_start: string;
    day_end: string;
    active_days: string[];
    university: {
        name: string;
        code: string;
    };
    created_at: string;
}

interface Props {
    sites: Site[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sites', href: '/sites' },
];

const destroyForm = useForm({});
const destroy = (id: number) => {
    if (!confirm('Supprimer ce site ?')) return;
    destroyForm.delete(route('sites.destroy', id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Sites" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Sites</h1>
                <Link :href="route('sites.create')">
                    <Button>Créer un site</Button>
                </Link>
            </div>

            <div v-if="sites.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="site in sites" :key="site.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center justify-between">
                            <span>{{ site.name }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-blue-200">{{ site.university.code }}</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2 mb-4">
                            <p class="text-sm text-gray-600">
                                <strong>Université:</strong> {{ site.university.name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Fuseau:</strong> {{ site.timezone }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Horaires:</strong> {{ site.day_start }} - {{ site.day_end }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Jours actifs:</strong> {{ site.active_days.join(', ') }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ new Date(site.created_at).toLocaleDateString() }}</span>
                            <div class="flex gap-2">
                                <Link :href="route('sites.edit', site.id)">
                                    <Button variant="outline">Modifier</Button>
                                </Link>
                                <Button variant="destructive" @click="destroy(site.id)" :disabled="destroyForm.processing">
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun site.</p>
                <Link :href="route('sites.create')">
                    <Button>Créer votre premier site</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
