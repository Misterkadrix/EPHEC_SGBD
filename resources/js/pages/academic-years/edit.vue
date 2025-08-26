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

interface AcademicYear {
    id: number;
    university_id: number;
    name: string;
    start_date: string;
    end_date: string;
    state: 'planned' | 'active' | 'archived';
}

interface Props {
    academicYear: AcademicYear;
    universities: University[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Années académiques', href: '/academic-years' },
    { title: 'Edit', href: `/academic-years/${props.academicYear.id}/edit` },
];

const form = useForm({
    university_id: props.academicYear.university_id.toString(),
    name: props.academicYear.name,
    start_date: props.academicYear.start_date,
    end_date: props.academicYear.end_date,
    state: props.academicYear.state,
});

const submit = () => {
    form.put(route('academic-years.update', props.academicYear.id));
};
</script>

<template>
    <Head title="Edit Academic Year" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier l'année académique</CardTitle>
                    <CardDescription>Mettez à jour les informations de l'année académique</CardDescription>
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
                            <Label for="name">Nom de l'année</Label>
                            <Input 
                                id="name" 
                                v-model="form.name" 
                                type="text" 
                                placeholder="ex: 2025-2026" 
                                :class="{ 'border-red-500': form.errors.name }" 
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="start_date">Date de début</Label>
                                <Input 
                                    id="start_date" 
                                    v-model="form.start_date" 
                                    type="date" 
                                    :class="{ 'border-red-500': form.errors.start_date }" 
                                />
                                <div v-if="form.errors.start_date" class="text-red-500 text-sm">{{ form.errors.start_date }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="end_date">Date de fin</Label>
                                <Input 
                                    id="end_date" 
                                    v-model="form.end_date" 
                                    type="date" 
                                    :class="{ 'border-red-500': form.errors.end_date }" 
                                />
                                <div v-if="form.errors.end_date" class="text-red-500 text-sm">{{ form.errors.end_date }}</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="state">Statut</Label>
                            <select 
                                v-model="form.state"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.state }"
                            >
                                <option value="planned">Planifiée</option>
                                <option value="active">Active</option>
                                <option value="archived">Archivée</option>
                            </select>
                            <div v-if="form.errors.state" class="text-red-500 text-sm">{{ form.errors.state }}</div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Enregistrement...</span>
                                <span v-else>Enregistrer</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
