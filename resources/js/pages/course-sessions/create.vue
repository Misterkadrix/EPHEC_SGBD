<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed } from 'vue';

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
    form.post(route('course-sessions.store'));
};

const resetForm = () => form.reset();

// Filtrer les données par université sélectionnée
const filteredAcademicYears = computed(() => {
    if (!form.university_id) return [];
    return props.academicYears.filter(year => year.university_id.toString() === form.university_id);
});

const filteredCourses = computed(() => {
    if (!form.university_id) return [];
    return props.courses.filter(course => course.university_id.toString() === form.university_id);
});

const filteredSites = computed(() => {
    if (!form.university_id) return [];
    return props.sites.filter(site => site.university_id.toString() === form.university_id);
});

const filteredRooms = computed(() => {
    if (!form.site_id) return [];
    return props.rooms.filter(room => room.site_id.toString() === form.site_id);
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
