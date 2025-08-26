<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
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

interface Course {
    id: number;
    code: string;
    title: string;
    university_id: number;
}

interface Site {
    id: number;
    name: string;
    university_id: number;
}

interface Room {
    id: number;
    name: string;
    site_id: number;
}

interface Props {
    universities: University[];
    academicYears: AcademicYear[];
    courses: Course[];
    sites: Site[];
    rooms: Room[];
}

const props = defineProps<Props>();

// Debug: Afficher les données reçues
console.log('Props reçues dans create:', {
    universities: props.universities,
    academicYears: props.academicYears,
    courses: props.courses,
    sites: props.sites,
    rooms: props.rooms
});

// Debug: Vérifier la structure des données
console.log('Structure des universités:', props.universities.map(u => ({ id: u.id, name: u.name, code: u.code })));
console.log('Structure des années académiques:', props.academicYears.map(a => ({ id: a.id, name: a.name, university_id: a.university_id })));
console.log('Structure des cours:', props.courses.map(c => ({ id: c.id, code: c.code, title: c.title, university_id: c.university_id })));
console.log('Structure des sites:', props.sites.map(s => ({ id: s.id, name: s.name, university_id: s.university_id })));
console.log('Structure des salles:', props.rooms.map(r => ({ id: r.id, name: r.name, site_id: r.site_id })));

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sessions de cours', href: '/course-sessions' },
    { title: 'Create', href: '/course-sessions/create' },
];

const form = useForm({
    university_id: '',
    academic_year_id: '',
    course_id: '',
    site_id: '',
    room_id: '',
    start_at: '',
    end_at: '',
});

const submit = () => {
    // Convertir le format de date pour correspondre au backend
    if (form.start_at) {
        const startDate = new Date(form.start_at);
        form.start_at = startDate.toISOString().slice(0, 19).replace('T', ' ');
    }
    
    if (form.end_at) {
        const endDate = new Date(form.end_at);
        form.end_at = endDate.toISOString().slice(0, 19).replace('T', ' ');
    }
    
    console.log('Dates formatées pour envoi:', {
        start_at: form.start_at,
        end_at: form.end_at
    });
    
    form.post(route('course-sessions.store'));
};

const resetForm = () => form.reset();

const cancel = () => {
    // Empêcher la validation du formulaire et naviguer directement
    form.clearErrors();
    router.visit('/course-sessions');
};

// Filtrer les données par université sélectionnée
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

const filteredCourses = computed(() => {
    if (!form.university_id) return [];
    
    // Convertir en array normal et forcer la comparaison des types
    const coursesArray = Array.from(props.courses);
    const universityId = parseInt(form.university_id);
    
    const filtered = coursesArray.filter(course => course.university_id === universityId);
    
    console.log('Filtering courses:', {
        university_id: form.university_id,
        university_id_type: typeof form.university_id,
        university_id_parsed: universityId,
        courses: coursesArray,
        courses_sample: coursesArray.slice(0, 2),
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

const filteredRooms = computed(() => {
    if (!form.site_id) return [];
    
    // Convertir en array normal et forcer la comparaison des types
    const roomsArray = Array.from(props.rooms);
    const siteId = parseInt(form.site_id);
    
    const filtered = roomsArray.filter(room => room.site_id === siteId);
    
    console.log('Filtering rooms:', {
        site_id: form.site_id,
        site_id_type: typeof form.site_id,
        site_id_parsed: siteId,
        rooms: roomsArray,
        rooms_sample: roomsArray.slice(0, 2),
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
        
        console.log('Recherche des cours pour université:', newValue);
        const coursesArray = Array.from(props.courses);
        const courses = coursesArray.filter(course => course.university_id === universityId);
        console.log('Cours trouvés:', courses);
        
        console.log('Recherche des sites pour université:', newValue);
        const sitesArray = Array.from(props.sites);
        const sites = sitesArray.filter(site => site.university_id === universityId);
        console.log('Sites trouvés:', sites);
    }
});

// Watcher pour voir quand le site change
watch(() => form.site_id, (newValue, oldValue) => {
    console.log('Site changé:', { oldValue, newValue });
    
    if (newValue) {
        console.log('Recherche des salles pour site:', newValue);
        
        // Convertir en array normal et forcer la comparaison des types
        const roomsArray = Array.from(props.rooms);
        const siteId = parseInt(newValue);
        const rooms = roomsArray.filter(room => room.site_id === siteId);
        console.log('Salles trouvées:', rooms);
    }
});

// Calculer automatiquement l'heure de fin (1h après le début)
const updateEndTime = () => {
    if (form.start_at) {
        const startTime = new Date(form.start_at);
        const endTime = new Date(startTime.getTime() + 60 * 60 * 1000); // +1h
        // Format pour input datetime-local: YYYY-MM-DDTHH:MM
        form.end_at = endTime.toISOString().slice(0, 16);
        
        console.log('Heure de fin calculée:', {
            start_at: form.start_at,
            end_at: form.end_at,
            startTime: startTime,
            endTime: endTime
        });
    }
};
</script>

<template>
    <Head title="Create Course Session" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer une session de cours</CardTitle>
                    <CardDescription>Planifiez une nouvelle session de cours</CardDescription>
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
                            <Label for="course">Cours</Label>
                            <select 
                                v-model="form.course_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.course_id }"
                                :disabled="!form.university_id"
                            >
                                <option value="">Sélectionnez un cours</option>
                                <option v-for="course in filteredCourses" :key="course.id" :value="course.id">
                                    {{ course.code }} - {{ course.title }}
                                </option>
                            </select>
                            <div v-if="form.errors.course_id" class="text-red-500 text-sm">{{ form.errors.course_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="site">Site</Label>
                            <select 
                                v-model="form.site_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.site_id }"
                                :disabled="!form.university_id"
                            >
                                <option value="">Sélectionnez un site</option>
                                <option v-for="site in filteredSites" :key="site.id" :value="site.id">
                                    {{ site.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.site_id" class="text-red-500 text-sm">{{ form.errors.site_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="room">Salle</Label>
                            <select 
                                v-model="form.room_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.room_id }"
                                :disabled="!form.site_id"
                            >
                                <option value="">Sélectionnez une salle</option>
                                <option v-for="room in filteredRooms" :key="room.id" :value="room.id">
                                    {{ room.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.room_id" class="text-red-500 text-sm">{{ form.errors.room_id }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="start_at">Date et heure de début</Label>
                                <Input 
                                    id="start_at" 
                                    v-model="form.start_at" 
                                    type="datetime-local" 
                                    :class="{ 'border-red-500': form.errors.start_at }"
                                    @change="updateEndTime"
                                />
                                <div v-if="form.errors.start_at" class="text-red-500 text-sm">{{ form.errors.start_at }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="end_at">Date et heure de fin</Label>
                                <Input 
                                    id="end_at" 
                                    v-model="form.end_at" 
                                    type="datetime-local" 
                                    :class="{ 'border-red-500': form.errors.end_at }" 
                                />
                                <div v-if="form.errors.end_at" class="text-red-500 text-sm">{{ form.errors.end_at }}</div>
                            </div>
                        </div>

                        <FormActions
                            :is-loading="form.processing"
                            :is-valid="form.university_id && form.academic_year_id && form.course_id && form.site_id && form.room_id && form.start_at && form.end_at"
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
