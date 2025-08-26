<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Course {
    id: number;
    code: string;
    title: string;
    description?: string;
    university: {
        name: string;
        code: string;
    };
    created_at: string;
}

interface Props {
    courses: Course[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cours', href: '/courses' },
];
</script>

<template>
    <Head title="Cours" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Cours</h1>
                <Link :href="route('courses.create')">
                    <Button>
                        Créer un cours
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des cours -->
            <div v-if="courses.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="course in courses" :key="course.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <CardTitle class="text-lg">{{ course.title }}</CardTitle>
                            <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ course.code }}</span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600">
                                Université: {{ course.university.name }} ({{ course.university.code }})
                            </div>
                            <div v-if="course.description" class="text-sm text-gray-600 line-clamp-3">
                                {{ course.description }}
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">
                                {{ new Date(course.created_at).toLocaleDateString() }}
                            </span>
                            <div class="flex space-x-2">
                                <Link :href="route('courses.edit', course.id)">
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
            
            <!-- Message si aucun cours -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun cours n'a été créé pour le moment.</p>
                <Link :href="route('courses.create')">
                    <Button>
                        Créer votre premier cours
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
