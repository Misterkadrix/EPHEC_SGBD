<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import FormActions from '@/components/ui/FormActions.vue';
import FieldError from '@/components/ui/FieldError.vue';

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
    start_date: props.academicYear.start_date ? new Date(props.academicYear.start_date).toISOString().split('T')[0] : '',
    end_date: props.academicYear.end_date ? new Date(props.academicYear.end_date).toISOString().split('T')[0] : '',
    state: props.academicYear.state,
});

// Validation des dates
const validateDates = (): boolean => {
    if (!form.start_date || !form.end_date) return false;
    const startDate = new Date(form.start_date);
    const endDate = new Date(form.end_date);
    return startDate < endDate;
};

const isFormValid = computed(() => {
    return form.university_id && form.name && form.start_date && form.end_date && validateDates();
});

const submit = () => {
    if (!isFormValid.value) {
        return;
    }
    form.put(route('academic-years.update', props.academicYear.id));
};

const cancel = () => {
    // Réinitialiser le formulaire avant de naviguer
    form.reset();
    router.visit('/academic-years');
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
                                <FieldError :error="form.errors.start_date" />
                            <FieldError v-if="form.start_date && form.end_date && !validateDates()" error="La date de début doit être antérieure à la date de fin" />
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

                        <FormActions
                            :is-loading="form.processing"
                            :is-valid="isFormValid"
                            submit-text="Enregistrer"
                            @cancel="cancel"
                        />
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
