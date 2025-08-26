<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface CourseSession {
    id: number;
    start_at: string;
    end_at: string;
    course: {
        code: string;
        title: string;
    };
    site: {
        name: string;
        university: {
            name: string;
            code: string;
        };
    };
    room: {
        name: string;
    };
    academic_year: {
        name: string;
    };
    created_at: string;
}

interface Props {
    sessions: CourseSession[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sessions de cours', href: '/course-sessions' },
];

const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleString('fr-FR', {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getDuration = (startAt: string, endAt: string) => {
    const start = new Date(startAt);
    const end = new Date(endAt);
    const diffMs = end.getTime() - start.getTime();
    const diffHours = Math.round(diffMs / (1000 * 60 * 60));
    return `${diffHours}h`;
};
</script>

<template>
    <Head title="Sessions de cours" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Sessions de cours</h1>
                <Link :href="route('course-sessions.create')">
                    <Button>
                        Créer une session
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des sessions de cours -->
            <div v-if="sessions.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="session in sessions" :key="session.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <CardTitle class="text-lg">{{ session.course.title }}</CardTitle>
                            <span class="text-sm font-mono bg-blue-100 px-2 py-1 rounded">{{ session.course.code }}</span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600">
                                <strong>Date et heure:</strong><br>
                                {{ formatDateTime(session.start_at) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <strong>Durée:</strong> {{ getDuration(session.start_at, session.end_at) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <strong>Site:</strong> {{ session.site.name }} - {{ session.site.university.name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <strong>Salle:</strong> {{ session.room.name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <strong>Année:</strong> {{ session.academic_year.name }}
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">
                                {{ new Date(session.created_at).toLocaleDateString() }}
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('course-sessions.edit', session.id)">
                                    <Button variant="outline" size="sm">Modifier</Button>
                                </Link>
                                <Button variant="outline" size="sm" class="text-red-600 hover:text-red-700">
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Message si aucune session -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucune session de cours n'a été créée pour le moment.</p>
                <Link :href="route('course-sessions.create')">
                    <Button>
                        Créer votre première session
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
