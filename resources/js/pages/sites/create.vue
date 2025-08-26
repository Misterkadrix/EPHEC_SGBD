<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface University {
    id: number;
    name: string;
    code: string;
}

interface Props {
    universities: University[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sites', href: '/sites' },
    { title: 'Create', href: '/sites/create' },
];

const form = useForm({
    university_id: '',
    name: '',
    timezone: 'Europe/Brussels',
    day_start: '08:00',
    day_end: '18:00',
    active_days: ['MO', 'TU', 'WE', 'TH', 'FR'],
});

const submit = () => {
    form.post(route('sites.store'));
};

const resetForm = () => form.reset();

const days = [
    { value: 'MO', label: 'Lundi' },
    { value: 'TU', label: 'Mardi' },
    { value: 'WE', label: 'Mercredi' },
    { value: 'TH', label: 'Jeudi' },
    { value: 'FR', label: 'Vendredi' },
    { value: 'SA', label: 'Samedi' },
    { value: 'SU', label: 'Dimanche' },
];
</script>

<template>
    <Head title="Create Site" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer un site</CardTitle>
                    <CardDescription>Renseignez les informations du site</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="university">Université</Label>
                            <select 
                                v-model="form.university_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.university_id }"
                            >
                                <option value="">Sélectionnez une université</option>
                                <option v-for="univ in universities" :key="univ.id" :value="univ.id">
                                    {{ univ.name }} ({{ univ.code }})
                                </option>
                            </select>
                            <div v-if="form.errors.university_id" class="text-red-500 text-sm">{{ form.errors.university_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="name">Nom du site</Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="Campus principal" :class="{ 'border-red-500': form.errors.name }" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="timezone">Fuseau horaire</Label>
                            <Input id="timezone" v-model="form.timezone" type="text" placeholder="Europe/Brussels" :class="{ 'border-red-500': form.errors.timezone }" />
                            <div v-if="form.errors.timezone" class="text-red-500 text-sm">{{ form.errors.timezone }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="day_start">Heure de début</Label>
                                <Input id="day_start" v-model="form.day_start" type="time" :class="{ 'border-red-500': form.errors.day_start }" />
                                <div v-if="form.errors.day_start" class="text-red-500 text-sm">{{ form.errors.day_start }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="day_end">Heure de fin</Label>
                                <Input id="day_end" v-model="form.day_end" type="time" :class="{ 'border-red-500': form.errors.day_end }" />
                                <div v-if="form.errors.day_end" class="text-red-500 text-sm">{{ form.errors.day_end }}</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Jours actifs</Label>
                            <div class="grid grid-cols-4 gap-2">
                                <label v-for="day in days" :key="day.value" class="flex items-center space-x-2">
                                    <input
                                        type="checkbox"
                                        :value="day.value"
                                        v-model="form.active_days"
                                        class="rounded border-gray-300"
                                    />
                                    <span class="text-sm">{{ day.label }}</span>
                                </label>
                            </div>
                            <div v-if="form.errors.active_days" class="text-red-500 text-sm">{{ form.errors.active_days }}</div>
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
