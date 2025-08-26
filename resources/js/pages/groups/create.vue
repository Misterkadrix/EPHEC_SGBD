<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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

interface Props {
    universities: University[];
    academicYears: AcademicYear[];
    sites: Site[];
}

const props = defineProps<Props>();

// Debug: Afficher les données reçues
console.log('Props reçues:', {
    universities: props.universities,
    academicYears: props.academicYears,
    sites: props.sites
});

// Debug: Vérifier la structure des données
console.log('Structure des universités:', props.universities.map(u => ({ id: u.id, name: u.name, code: u.code })));
console.log('Structure des années académiques:', props.academicYears.map(a => ({ id: a.id, name: a.name, university_id: a.university_id })));
console.log('Structure des sites:', props.sites.map(s => ({ id: s.id, name: s.name, university_id: s.university_id })));

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Groupes', href: '/groups' },
    { title: 'Create', href: '/groups/create' },
];

const form = useForm({
    university_id: '',
    academic_year_id: '',
    name: '',
    quantity: '',
    main_site_id: '',
    min_size: '20',
    max_size: '40',
});

const submit = () => {
    form.post(route('groups.store'));
};

const resetForm = () => form.reset();

const cancel = () => {
    // Empêcher la validation du formulaire et naviguer directement
    form.clearErrors();
    router.visit('/groups');
};

// Filtrer les années académiques et sites par université sélectionnée
const filteredAcademicYears = computed(() => {
    if (!form.university_id) return [];
    
    // Convertir en array normal et forcer la comparaison des types
    const academicYearsArray = Array.from(props.academicYears);
    const universityId = parseInt(form.university_id);
    
    const filtered = academicYearsArray.filter(year => year.university_id === universityId);
    
    console.log('Filtering academic years:', {
        university_id: form.university_id,
        university_id_type: typeof form.university_id,
        university_id_parsed: universityId,
        academicYears: academicYearsArray,
        academicYears_sample: academicYearsArray.slice(0, 2),
        filtered: filtered
    });
    
    return filtered;
});

const filteredSites = computed(() => {
    if (!form.university_id) return [];
    
    // Convertir en array normal et forcer la comparaison des types
    const sitesArray = Array.from(props.sites);
    const universityId = parseInt(form.university_id);
    
    const filtered = sitesArray.filter(site => site.university_id === universityId);
    
    console.log('Filtering sites:', {
        university_id: form.university_id,
        university_id_type: typeof form.university_id,
        university_id_parsed: universityId,
        sites: sitesArray,
        sites_sample: sitesArray.slice(0, 2),
        filtered: filtered
    });
    
    return filtered;
});

// Watcher pour voir quand l'université change
watch(() => form.university_id, (newValue, oldValue) => {
    console.log('Université changée:', { oldValue, newValue });
    console.log('Form data après changement:', form.data());
    
    if (newValue) {
        console.log('Recherche des années académiques pour université:', newValue);
        
        // Convertir en array normal et forcer la comparaison des types
        const academicYearsArray = Array.from(props.academicYears);
        const universityId = parseInt(newValue);
        const years = academicYearsArray.filter(year => year.university_id === universityId);
        console.log('Années académiques trouvées:', years);
        
        console.log('Recherche des sites pour université:', newValue);
        const sitesArray = Array.from(props.sites);
        const sites = sitesArray.filter(site => site.university_id === universityId);
        console.log('Sites trouvés:', sites);
    }
});
</script>

<template>
    <Head title="Create Group" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer un groupe</CardTitle>
                    <CardDescription>Renseignez les informations du groupe</CardDescription>
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
                            submit-text="Créer"
                            show-reset
                            @cancel="cancel"
                            @reset="resetForm"
                        />
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
