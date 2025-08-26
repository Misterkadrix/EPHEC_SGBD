<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface Site {
    id: number;
    name: string;
    university: {
        name: string;
        code: string;
    };
}

interface EquipmentType {
    id: number;
    label: string;
}

interface Room {
    id: number;
    name: string;
}

interface Props {
    sites: Site[];
    equipmentTypes: EquipmentType[];
    rooms: Room[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Équipements', href: '/equipment' },
    { title: 'Create', href: '/equipment/create' },
];

const form = useForm({
    site_id: '',
    type_id: '',
    is_mobile: true,
    fixed_room_id: '',
});

const submit = () => {
    form.post(route('equipment.store'));
};

const resetForm = () => form.reset();
</script>

<template>
    <Head title="Create Equipment" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer un équipement</CardTitle>
                    <CardDescription>Renseignez les informations de l'équipement</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="site">Site</Label>
                            <select 
                                v-model="form.site_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.site_id }"
                            >
                                <option value="">Sélectionnez un site</option>
                                <option v-for="site in sites" :key="site.id" :value="site.id">
                                    {{ site.name }} - {{ site.university.name }} ({{ site.university.code }})
                                </option>
                            </select>
                            <div v-if="form.errors.site_id" class="text-red-500 text-sm">{{ form.errors.site_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="type">Type d'équipement</Label>
                            <select 
                                v-model="form.type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.type_id }"
                            >
                                <option value="">Sélectionnez un type</option>
                                <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                                    {{ type.label }}
                                </option>
                            </select>
                            <div v-if="form.errors.type_id" class="text-red-500 text-sm">{{ form.errors.type_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label>Type d'équipement</Label>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-2">
                                    <input
                                        type="radio"
                                        v-model="form.is_mobile"
                                        :value="true"
                                        class="text-blue-600"
                                    />
                                    <span>Mobile</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input
                                        type="radio"
                                        v-model="form.is_mobile"
                                        :value="false"
                                        class="text-blue-600"
                                    />
                                    <span>Fixe</span>
                                </label>
                            </div>
                            <div v-if="form.errors.is_mobile" class="text-red-500 text-sm">{{ form.errors.is_mobile }}</div>
                        </div>

                        <div v-if="!form.is_mobile" class="space-y-2">
                            <Label for="room">Salle fixe</Label>
                            <select 
                                v-model="form.fixed_room_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.fixed_room_id }"
                            >
                                <option value="">Sélectionnez une salle</option>
                                <option v-for="room in rooms" :key="room.id" :value="room.id">
                                    {{ room.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.fixed_room_id" class="text-red-500 text-sm">{{ form.errors.fixed_room_id }}</div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <Button type="button" variant="outline" @click="resetForm" :disabled="form.processing">Réinitialiser</Button>
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Création...</span>
                                <span v-else>Créer</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
