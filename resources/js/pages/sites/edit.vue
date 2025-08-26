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

interface Site {
    id: number;
    university_id: number;
    name: string;
    timezone: string;
    day_start: string;
    day_end: string;
    active_days: string[];
}

interface Props {
    site: Site;
    universities: University[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sites', href: '/sites' },
    { title: 'Edit', href: `/sites/${props.site.id}/edit` },
];

const form = useForm({
    university_id: props.site.university_id.toString(),
    name: props.site.name,
    timezone: props.site.timezone,
    day_start: props.site.day_start,
    day_end: props.site.day_end,
    active_days: props.site.active_days,
});

const submit = () => {
    form.put(route('sites.update', props.site.id));
};

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
    <Head title="Edit Site" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier le site</CardTitle>
                    <CardDescription>Mettez à jour les informations du site</CardDescription>
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
                            <Input id="name" v-model="form.name" type="text" :class="{ 'border-red-500': form.errors.name }" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="timezone">Fuseau horaire</Label>
                            <Input id="timezone" v-model="form.timezone" type="text" :class="{ 'border-red-500': form.errors.timezone }" />
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
