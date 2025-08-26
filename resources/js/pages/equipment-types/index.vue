<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmDeleteDialog from '@/components/ui/ConfirmDeleteDialog.vue';
import { ref } from 'vue';

interface EquipmentType {
    id: number;
    label: string;
    created_at: string;
}

interface Props {
    equipmentTypes: EquipmentType[];
}

const props = defineProps<Props>();
const page = usePage();

// État pour la suppression
const showDeleteDialog = ref(false);
const equipmentTypeToDelete = ref<EquipmentType | null>(null);
const deleteForm = useForm({});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Types d\'équipements', href: '/equipment-types' },
];

const openDeleteDialog = (equipmentType: EquipmentType) => {
    equipmentTypeToDelete.value = equipmentType;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    if (!equipmentTypeToDelete.value) return;
    
    deleteForm.delete(route('equipment-types.destroy', equipmentTypeToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
            equipmentTypeToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Types d'équipements" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Types d'équipements</h1>
                <Link :href="route('equipment-types.create')">
                    <Button>
                        Créer un type d'équipement
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des types d'équipements -->
            <div v-if="equipmentTypes.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="type in equipmentTypes" :key="type.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="text-lg">{{ type.label }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                ID: <span class="font-semibold">{{ type.id }}</span>
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('equipment-types.edit', type.id)">
                                    <Button variant="outline" size="sm">Modifier</Button>
                                </Link>
                                <Button 
                                    variant="outline" 
                                    size="sm" 
                                    class="text-red-600 hover:text-red-700"
                                    @click="openDeleteDialog(type)"
                                >
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-400">
                            {{ new Date(type.created_at).toLocaleDateString() }}
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Message si aucun type d'équipement -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun type d'équipement n'a été créé pour le moment.</p>
                <Link :href="route('equipment-types.create')">
                    <Button>
                        Créer votre premier type d'équipement
                    </Button>
                </Link>
            </div>
        </div>
        
        <!-- Dialogue de confirmation de suppression -->
        <ConfirmDeleteDialog
            :open="showDeleteDialog"
            :item-name="equipmentTypeToDelete?.label || ''"
            :is-loading="deleteForm.processing"
            @update:open="showDeleteDialog = $event"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
