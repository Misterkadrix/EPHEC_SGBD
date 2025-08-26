<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmDeleteDialog from '@/components/ui/ConfirmDeleteDialog.vue';

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

// État pour le dialogue de suppression
const showDeleteDialog = ref(false);
const siteToDelete = ref<Site | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sites', href: '/sites' },
];

const destroyForm = useForm({});

// Fonction pour formater les jours actifs de manière sécurisée
const formatActiveDays = (activeDays: any): string => {
    if (!activeDays) return 'Aucun jour défini';
    
    // Si c'est une chaîne JSON, essayer de la parser
    if (typeof activeDays === 'string') {
        try {
            const parsed = JSON.parse(activeDays);
            if (Array.isArray(parsed)) {
                return parsed.join(', ');
            }
        } catch (e) {
            console.warn('Impossible de parser active_days:', activeDays);
        }
        return activeDays;
    }
    
    // Si c'est un tableau, l'utiliser directement
    if (Array.isArray(activeDays)) {
        return activeDays.join(', ');
    }
    
    return String(activeDays);
};

const openDeleteDialog = (site: Site) => {
    siteToDelete.value = site;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    if (!siteToDelete.value) return;
    
    destroyForm.delete(route('sites.destroy', siteToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
            siteToDelete.value = null;
        },
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
                                <strong>Jours actifs:</strong> {{ formatActiveDays(site.active_days) }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ new Date(site.created_at).toLocaleDateString() }}</span>
                            <div class="flex gap-2">
                                <Link :href="route('sites.edit', site.id)">
                                    <Button variant="outline">Modifier</Button>
                                </Link>
                                <Button variant="destructive" @click="openDeleteDialog(site)" :disabled="destroyForm.processing">
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
        
        <!-- Dialogue de confirmation de suppression -->
        <ConfirmDeleteDialog
            :open="showDeleteDialog"
            :item-name="siteToDelete?.name || ''"
            :is-loading="destroyForm.processing"
            @update:open="showDeleteDialog = $event"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
