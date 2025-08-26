<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface University {
    id: number;
    name: string;
    code: string;
}

interface Holiday {
    id: number;
    name: string;
    date: string;
    year: number;
    university_id?: number;
}

interface Props {
    holiday: Holiday;
    universities: University[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Fériés', href: '/holidays' },
    { title: 'Edit', href: `/holidays/${props.holiday.id}/edit` },
];

const form = useForm({
    name: '',
    date: '',
    year: '',
    university_id: '',
});

// Initialiser le formulaire avec les données existantes
const initializeForm = () => {
    if (props.holiday) {
        form.name = props.holiday.name || '';
        form.date = props.holiday.date || '';
        form.year = props.holiday.year?.toString() || '';
        form.university_id = props.holiday.university_id?.toString() || '';
        
        console.log('Férié initialisé:', props.holiday);
        console.log('Formulaire initialisé:', form.data());
    }
};

onMounted(() => {
    initializeForm();
});

const submit = () => {
    form.put(route('holidays.update', props.holiday.id));
};

// Extraire l'année de la date sélectionnée
const updateYear = () => {
    if (form.date) {
        const selectedDate = new Date(form.date);
        form.year = selectedDate.getFullYear().toString();
    }
};
</script>

<template>
    <Head title="Edit Holiday" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier le férié</CardTitle>
                    <CardDescription>Mettez à jour les informations du férié</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nom du férié</Label>
                            <Input 
                                id="name" 
                                v-model="form.name" 
                                type="text" 
                                placeholder="ex: Noël, Pâques, Fête nationale" 
                                :class="{ 'border-red-500': form.errors.name }" 
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="date">Date</Label>
                            <Input 
                                id="date" 
                                v-model="form.date" 
                                type="date" 
                                :class="{ 'border-red-500': form.errors.date }"
                                @change="updateYear"
                            />
                            <div v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="year">Année</Label>
                            <Input 
                                id="year" 
                                v-model="form.year" 
                                type="number" 
                                min="2000" 
                                max="2100" 
                                :class="{ 'border-red-500': form.errors.year }" 
                            />
                            <div v-if="form.errors.year" class="text-red-500 text-sm">{{ form.errors.year }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="university">Université (optionnel)</Label>
                            <select 
                                v-model="form.university_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.university_id }"
                            >
                                <option value="">Férié global/national</option>
                                <option v-for="univ in universities" :key="univ.id" :value="univ.id">
                                    {{ univ.name }} ({{ univ.code }})
                                </option>
                            </select>
                            <div v-if="form.errors.university_id" class="text-red-500 text-sm">{{ form.errors.university_id }}</div>
                            <p class="text-xs text-gray-500">Laissez vide pour un férié global, ou sélectionnez une université pour un férié spécifique</p>
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
