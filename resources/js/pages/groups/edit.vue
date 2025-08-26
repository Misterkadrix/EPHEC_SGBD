<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed } from 'vue';
import FormActions from '@/components/ui/FormActions.vue';

interface University {
    id: number;
    name: string;
    code: string;
}

interface AcademicYear {
    id: number;
    name: string;
    university_id: number;
}

interface Site {
    id: number;
    name: string;
    university_id: number;
}

interface Group {
    id: number;
    university_id: number;
    academic_year_id: number;
    name: string;
    quantity: number;
    main_site_id: number;
    min_size: number;
    max_size: number;
}

interface Props {
    group: Group;
    universities: University[];
    academicYears: AcademicYear[];
    sites: Site[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Groupes', href: '/groups' },
    { title: 'Edit', href: `/groups/${props.group.id}/edit` },
];

const form = useForm({
    university_id: props.group.university_id.toString(),
    academic_year_id: props.group.academic_year_id.toString(),
    name: props.group.name,
    quantity: props.group.quantity.toString(),
    main_site_id: props.group.main_site_id.toString(),
    min_size: props.group.min_size.toString(),
    max_size: props.group.max_size.toString(),
});

const submit = () => {
    form.put(route('groups.update', props.group.id));
};

const cancel = () => {
    // Réinitialiser le formulaire avant de naviguer
    form.reset();
    router.visit('/groups');
};

// Filtrer les années académiques et sites par université sélectionnée
const filteredAcademicYears = computed(() => {
    if (!form.university_id) return [];
    return props.academicYears.filter(year => year.university_id.toString() === form.university_id);
});

const filteredSites = computed(() => {
    if (!form.university_id) return [];
    return props.sites.filter(site => site.university_id.toString() === form.university_id);
});
</script>

<template>
    <Head title="Edit Group" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier le groupe</CardTitle>
                    <CardDescription>Mettez à jour les informations du groupe</CardDescription>
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
                            <Label for="academic_year">Année académique</Label>
                            <select 
                                v-model="form.academic_year_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.academic_year_id }"
                                :disabled="!form.university_id"
                            >
                                <option value="">Sélectionnez une année académique</option>
                                <option v-for="year in filteredAcademicYears" :key="year.id" :value="year.id">
                                    {{ year.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.academic_year_id" class="text-red-500 text-sm">{{ form.errors.academic_year_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="name">Nom du groupe</Label>
                            <Input 
                                id="name" 
                                v-model="form.name" 
                                type="text" 
                                placeholder="ex: Groupe A, Section 1" 
                                :class="{ 'border-red-500': form.errors.name }" 
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="quantity">Taille du groupe</Label>
                            <Input 
                                id="quantity" 
                                v-model="form.quantity" 
                                type="number" 
                                min="1" 
                                placeholder="ex: 35" 
                                :class="{ 'border-red-500': form.errors.quantity }" 
                            />
                            <div v-if="form.errors.quantity" class="text-red-500 text-sm">{{ form.errors.quantity }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="main_site">Site principal</Label>
                            <select 
                                v-model="form.main_site_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.main_site_id }"
                                :disabled="!form.university_id"
                            >
                                <option value="">Sélectionnez un site</option>
                                <option v-for="site in filteredSites" :key="site.id" :value="site.id">
                                    {{ site.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.main_site_id" class="text-red-500 text-sm">{{ form.errors.main_site_id }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="min_size">Taille minimale</Label>
                                <Input 
                                    id="min_size" 
                                    v-model="form.min_size" 
                                    type="number" 
                                    min="1" 
                                    :class="{ 'border-red-500': form.errors.min_size }" 
                                />
                                <div v-if="form.errors.min_size" class="text-red-500 text-sm">{{ form.errors.min_size }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="max_size">Taille maximale</Label>
                                <Input 
                                    id="max_size" 
                                    v-model="form.max_size" 
                                    type="number" 
                                    min="1" 
                                    :class="{ 'border-red-500': form.errors.max_size }" 
                                />
                                <div v-if="form.errors.max_size" class="text-red-500 text-sm">{{ form.errors.max_size }}</div>
                            </div>
                        </div>

                        <FormActions
                            :is-loading="form.processing"
                            :is-valid="form.university_id && form.academic_year_id && form.name && form.quantity && form.main_site_id"
                            submit-text="Enregistrer"
                            @cancel="cancel"
                        />
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
