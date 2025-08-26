<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed, onMounted, watch, nextTick } from 'vue';
import FormActions from '@/components/ui/FormActions.vue';
import SessionStatusBadge from '@/components/SessionStatusBadge.vue';

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

interface CourseSession {
    id: number;
    academic_year_id: number;
    course_id: number;
    site_id: number;
    room_id: number;
    start_at: string;
    end_at: string;
    academicYear?: {
        id: number;
        name: string;
        university_id: number;
        university?: {
            id: number;
            name: string;
            code: string;
        };
    };
    course?: {
        id: number;
        code: string;
        title: string;
        university_id: number;
        university?: {
            id: number;
            name: string;
            code: string;
        };
    };
    site?: {
        id: number;
        name: string;
        university_id: number;
        university?: {
            id: number;
            name: string;
            code: string;
        };
    };
    room?: {
        id: number;
        name: string;
        site_id: number;
        site?: {
            id: number;
            name: string;
        };
    };
    validation_status?: {
        status: 'future' | 'ongoing' | 'recently_ended' | 'past';
        label: string;
        description: string;
        can_modify: boolean;
    };
    can_modify?: {
        can_modify: boolean;
        can_delete: boolean;
        can_change_room: boolean;
        can_change_groups: boolean;
        can_change_schedule: boolean;
        reason: string;
        status: string;
    };
}

interface Props {
    session: CourseSession;
    universities: University[];
    academicYears: AcademicYear[];
    courses: Course[];
    sites: Site[];
    rooms: Room[];
}

const props = defineProps<Props>();

// Debug: Afficher les données reçues
console.log('Props reçues dans edit:', {
    session: props.session,
    universities: props.universities,
    academicYears: props.academicYears,
    courses: props.courses,
    sites: props.sites,
    rooms: props.rooms
});

// Debug détaillé de la session
if (props.session) {
    console.log('🔍 Détails de la session:', {
        id: props.session.id,
        academic_year_id: props.session.academic_year_id,
        course_id: props.session.course_id,
        site_id: props.session.site_id,
        room_id: props.session.room_id,
        start_at: props.session.start_at,
        end_at: props.session.end_at,
        academicYear: props.session.academicYear,
        course: props.session.course,
        site: props.session.site,
        room: props.session.room
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sessions de cours', href: '/course-sessions' },
    { title: 'Edit', href: `/course-sessions/${props.session?.id || 'unknown'}/edit` },
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



// Initialiser le formulaire directement avec les données disponibles
const initializeForm = () => {
    if (!props.session) return;
    
    console.log('🚀 Initialisation du formulaire avec session:', props.session);
    
    // Initialiser tous les champs
    form.academic_year_id = props.session.academic_year_id?.toString() || '';
    form.course_id = props.session.course_id?.toString() || '';
    form.site_id = props.session.site_id?.toString() || '';
    form.room_id = props.session.room_id?.toString() || '';
    
    // Formater les dates pour l'input datetime-local (YYYY-MM-DDTHH:MM)
    if (props.session.start_at) {
        const startDate = new Date(props.session.start_at);
        if (!isNaN(startDate.getTime())) {
            form.start_at = startDate.toISOString().slice(0, 16);
            console.log('📅 Date de début formatée:', form.start_at);
        }
    }
    if (props.session.end_at) {
        const endDate = new Date(props.session.end_at);
        if (!isNaN(endDate.getTime())) {
            form.end_at = endDate.toISOString().slice(0, 16);
            console.log('📅 Date de fin formatée:', form.end_at);
        }
    }
    
    // Déterminer l'université
    if (props.academicYears && props.academicYears.length > 0) {
        const academicYear = props.academicYears.find(year => year.id === props.session.academic_year_id);
        if (academicYear && academicYear.university_id) {
            form.university_id = academicYear.university_id.toString();
            console.log('🏫 Université déterminée:', form.university_id);
        }
    }
    
    if (!form.university_id && props.courses && props.courses.length > 0) {
        const course = props.courses.find(c => c.id === props.session.course_id);
        if (course && course.university_id) {
            form.university_id = course.university_id.toString();
            console.log('🏫 Université déterminée depuis cours:', form.university_id);
        }
    }
    
    console.log('✅ Formulaire initialisé:', form.data());
};

// Initialiser au chargement
onMounted(() => {
    console.log('🔄 onMounted - Initialisation du formulaire');
    
    if (!props.session) {
        console.error('❌ Session non trouvée dans les props');
        return;
    }
    
    // Initialiser immédiatement
    initializeForm();
    
    // Vérifier que l'initialisation a fonctionné
    console.log('🔍 Vérification après initialisation:', {
        university_id: form.university_id,
        academic_year_id: form.academic_year_id,
        course_id: form.course_id,
        site_id: form.site_id,
        room_id: form.room_id,
        start_at: form.start_at,
        end_at: form.end_at
    });
});

// Watcher pour les changements de session
watch(() => props.session, (newSession) => {
    if (newSession) {
        console.log('📝 Session mise à jour, réinitialisation du formulaire');
        initializeForm();
    }
}, { immediate: true });

const submit = () => {
    form.put(route('course-sessions.update', props.session.id));
};

const cancel = () => {
    // Réinitialiser le formulaire avant de naviguer
    form.reset();
    router.visit('/course-sessions');
};

// Filtrer les données par université sélectionnée
const filteredAcademicYears = computed(() => {
    if (!form.university_id) return [];
    
    const filtered = props.academicYears.filter(year => year.university_id.toString() === form.university_id);
    console.log('Années académiques filtrées:', {
        university_id: form.university_id,
        total: props.academicYears.length,
        filtered: filtered.length,
        data: filtered
    });
    
    return filtered;
});

const filteredCourses = computed(() => {
    if (!form.university_id) return [];
    
    const filtered = props.courses.filter(course => course.university_id.toString() === form.university_id);
    console.log('Cours filtrés:', {
        university_id: form.university_id,
        total: props.courses.length,
        filtered: filtered.length,
        data: filtered
    });
    
    return filtered;
});

const filteredSites = computed(() => {
    if (!form.university_id) return [];
    
    const filtered = props.sites.filter(site => site.university_id.toString() === form.university_id);
    console.log('Sites filtrés:', {
        university_id: form.university_id,
        total: props.sites.length,
        filtered: filtered.length,
        data: filtered
    });
    
    return filtered;
});

const filteredRooms = computed(() => {
    if (!form.site_id) return [];
    
    const filtered = props.rooms.filter(room => room.site_id.toString() === form.site_id);
    console.log('Salles filtrées:', {
        site_id: form.site_id,
        total: props.rooms.length,
        filtered: filtered.length,
        data: filtered
    });
    
    return filtered;
});

// Calculer automatiquement l'heure de fin (1h après le début)
const updateEndTime = () => {
    if (form.start_at) {
        const startTime = new Date(form.start_at);
        const endTime = new Date(startTime.getTime() + 60 * 60 * 1000); // +1h
        form.end_at = endTime.toISOString().slice(0, 16);
    }
};
</script>

<template>
    <Head title="Edit Course Session" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier la session de cours</CardTitle>
                    <CardDescription>Mettez à jour les informations de la session</CardDescription>
                    
                    <!-- Badge de statut de la session -->
                    <div v-if="session.validation_status" class="mt-4">
                        <SessionStatusBadge 
                            :status="session.validation_status" 
                            :can-modify="session.can_modify" 
                        />
                    </div>
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
                            submit-text="Enregistrer"
                            @cancel="cancel"
                        />
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
