<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmDeleteDialog from '@/components/ui/ConfirmDeleteDialog.vue';
import { ref } from 'vue';

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

// État pour la suppression
const showDeleteDialog = ref(false);
const universityToDelete = ref<University | null>(null);
const destroyForm = useForm({});

const openDeleteDialog = (university: University) => {
    universityToDelete.value = university;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    if (!universityToDelete.value) return;
    
    destroyForm.delete(route('universities.destroy', universityToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
            universityToDelete.value = null;
        },
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
                                <Button variant="destructive" @click="openDeleteDialog(u)" :disabled="destroyForm.processing">
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
        
        <!-- Dialogue de confirmation de suppression -->
        <ConfirmDeleteDialog
            :open="showDeleteDialog"
            :item-name="universityToDelete?.name || ''"
            :is-loading="destroyForm.processing"
            @update:open="showDeleteDialog = $event"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
