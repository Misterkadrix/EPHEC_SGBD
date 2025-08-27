<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Route, Clock, MapPin, Users, Building2, GraduationCap } from 'lucide-vue-next';

interface Deplacement {
    id: number;
    heure_depart: string;
    heure_arrivee: string;
    duree_trajet_minutes: number;
    created_at: string;
    updated_at: string;
    group: any;
    session_depart: any;
    session_arrivee: any;
}

interface Props {
    deplacement: Deplacement;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Déplacements', href: '/deplacements' },
    { title: `Déplacement #${props.deplacement.id}`, href: `/deplacements/${props.deplacement.id}` },
];

// Formater l'heure
const formatTime = (heure: string) => {
    return new Date(heure).toLocaleTimeString('fr-FR', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};

// Formater la date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Déplacement #${deplacement.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 overflow-x-auto">
            <!-- En-tête avec bouton retour -->
            <div class="flex items-center gap-4">
                <Link :href="route('deplacements.index')">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Retour aux déplacements
                    </Button>
                </Link>
                
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Déplacement #{{ deplacement.id }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Détails du trajet entre deux sessions
                    </p>
                </div>
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informations du groupe -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="w-5 h-5 text-blue-600" />
                            Groupe
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ deplacement.group?.name || 'Groupe inconnu' }}
                        </div>
                        <!-- <p class="text-sm text-gray-600 mt-1">
                            {{ deplacement || 'Site principal inconnu' }}
                        </p> -->
                    </CardContent>
                </Card>

                <!-- Durée du trajet -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="w-5 h-5 text-green-600" />
                            Durée du trajet
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ deplacement.duree_trajet_minutes }} min
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            Temps de déplacement
                        </p>
                    </CardContent>
                </Card>

                <!-- Date -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Route class="w-5 h-5 text-purple-600" />
                            Date
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-lg font-medium text-purple-600">
                            {{ formatDate(deplacement.heure_depart) }}
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ formatTime(deplacement.heure_depart) }} - {{ formatTime(deplacement.heure_arrivee) }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Détails du trajet -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Session de départ -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-green-700">
                            <Building2 class="w-5 h-5" />
                            Départ
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                {{ deplacement?.session_depart?.room?.name || 'Cours inconnu' }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ deplacement.session_depart?.course?.title || 'Titre inconnu' }}
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <MapPin class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-700">
                                {{ deplacement.session_depart?.site?.name || 'Site inconnu' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <Building2 class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-700">
                                {{ deplacement.session_depart?.room?.name || 'Salle inconnue' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <Clock class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-700 font-medium">
                                {{ formatTime(deplacement.heure_depart) }}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Session d'arrivée -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-purple-700">
                            <GraduationCap class="w-5 h-5" />
                            Arrivée
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                {{ deplacement.session_arrivee?.course?.code || 'Cours inconnu' }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ deplacement.session_arrivee?.course?.title || 'Titre inconnu' }}
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <MapPin class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-700">
                                {{ deplacement.session_arrivee?.site?.name || 'Site inconnu' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <Building2 class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-700">
                                {{ deplacement.session_arrivee?.room?.name || 'Salle inconnue' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <Clock class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-700 font-medium">
                                {{ formatTime(deplacement.heure_arrivee) }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>