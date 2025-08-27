<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ArrowLeft, Route, Clock, MapPin, Users, Calendar, Building2, GraduationCap } from 'lucide-vue-next';

interface Deplacement {
    id: number;
    heure_depart: string;
    heure_arrivee: string;
    duree_trajet_minutes: number;
    created_at: string;
    updated_at: string;
    group: any;
    sessionDepart: any;
    sessionArrivee: any;
    siteDepart: any;
    siteArrivee: any;
    roomDepart: any;
    roomArrivee: any;
}

interface Props {
    deplacement: Deplacement;
}

const props = defineProps<Props>();

// Debug pour voir les données reçues
console.log('=== DEBUG SHOW PAGE ===');
console.log('Props:', props);
console.log('Deplacement:', props.deplacement);
console.log('Group:', props.deplacement?.group);
console.log('SessionDepart:', props.deplacement?.sessionDepart);
console.log('SessionDepart keys:', props.deplacement?.sessionDepart ? Object.keys(props.deplacement.sessionDepart) : 'NULL');
console.log('SessionDepart course:', props.deplacement?.sessionDepart?.course);
console.log('SessionDepart site:', props.deplacement?.sessionDepart?.site);
console.log('SessionDepart room:', props.deplacement?.sessionDepart?.room);
console.log('SessionArrivee:', props.deplacement?.sessionArrivee);
console.log('SessionArrivee keys:', props.deplacement?.sessionArrivee ? Object.keys(props.deplacement.sessionArrivee) : 'NULL');
console.log('SiteDepart:', props.deplacement?.siteDepart);
console.log('SiteArrivee:', props.deplacement?.siteArrivee);
console.log('=== FIN DEBUG ===');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Déplacements', href: '/deplacements' },
    { title: `Déplacement #${props.deplacement.id}`, href: `/deplacements/${props.deplacement.id}` },
];

// Computed pour déterminer le type de déplacement
const getDeplacementType = (deplacement: Deplacement) => {
    // Vérification de sécurité
    if (!deplacement?.siteDepart?.id || !deplacement?.siteArrivee?.id) {
        return { type: 'unknown', label: 'Inconnu', color: 'bg-gray-100 text-gray-800' };
    }
    
    if (deplacement.siteDepart.id === deplacement.siteArrivee.id) {
        return { type: 'meme_site', label: 'Même site', color: 'bg-blue-100 text-blue-800' };
    }
    return { type: 'inter_site', label: 'Inter-site', color: 'bg-orange-100 text-orange-800' };
};

// Computed pour la durée formatée
const getDureeFormatted = (minutes: number) => {
    const heures = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    if (heures > 0) {
        return `${heures}h${mins.toString().padStart(2, '0')}`;
    }
    return `${mins}min`;
};

// Computed pour l'heure formatée
const getHeureFormatted = (heure: string) => {
    return new Date(heure).toLocaleTimeString('fr-FR', { 
        hour: '2-digit', 
        minute: '2-digit' 
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
                    <p class="text-gray-600 dark:text-gray-400">
                        Détails du trajet pour {{ deplacement.group?.name || 'Groupe inconnu' }}
                    </p>
                </div>

                <span :class="getDeplacementType(deplacement).color" class="text-lg px-4 py-2 inline-flex items-center rounded-full text-xs font-semibold">
                    {{ getDeplacementType(deplacement).label }}
                </span>
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informations du groupe -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="w-5 h-5" />
                            Groupe
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nom du groupe</p>
                                <p class="text-lg font-semibold text-gray-900">{{ deplacement.group?.name || 'Groupe inconnu' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Effectif</p>
                                <p class="text-lg font-semibold text-gray-900">{{ deplacement.group?.quantity || 'N/A' }} étudiants</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date</p>
                                <p class="text-lg font-semibold text-gray-900">{{ new Date(deplacement.heure_depart).toLocaleDateString('fr-FR') }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Durée du trajet -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="w-5 h-5" />
                            Durée du trajet
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600">
                                {{ getDureeFormatted(deplacement.duree_trajet_minutes) }}
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                Temps estimé pour le déplacement
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Type de déplacement -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Route class="w-5 h-5" />
                            Type de déplacement
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ getDeplacementType(deplacement).label }}
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ (deplacement.siteDepart?.id && deplacement.siteArrivee?.id && deplacement.siteDepart.id === deplacement.siteArrivee.id) ? 'Sur le même site' : 'Entre sites différents' }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Détails du trajet -->
            <Card>
                <CardHeader>
                    <CardTitle>Détails du trajet</CardTitle>
                    <CardDescription>
                        Informations complètes sur le départ et l'arrivée
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Session de départ -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <GraduationCap class="w-6 h-6 text-green-600" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">Départ</h3>
                                    <p class="text-sm text-gray-500">Session qui se termine</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Cours</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ deplacement.sessionDepart?.course?.code || 'Cours inconnu' }}</p>
                                    <p class="text-sm text-gray-600">{{ deplacement.sessionDepart?.course?.title || 'Titre inconnu' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Site</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ deplacement.sessionDepart?.site?.name || 'Site inconnu' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Salle</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ deplacement.sessionDepart?.room?.name || 'Salle inconnue' }}</p>
                                    <p class="text-sm text-gray-600">Capacité: {{ deplacement.sessionDepart?.room?.capacity || 'N/A' }} places</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Heure de fin</p>
                                    <p class="text-lg font-semibold text-green-600">{{ getHeureFormatted(deplacement.heure_depart) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Séparateur -->
                        <div class="hidden lg:flex flex-col items-center justify-center">
                            <div class="w-0.5 h-full bg-gray-300 relative">
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gray-100 p-3 rounded-full border-2 border-gray-300">
                                    <Route class="w-6 h-6 text-gray-600" />
                                </div>
                            </div>
                        </div>

                        <!-- Session d'arrivée -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <GraduationCap class="w-6 h-6 text-purple-600" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">Arrivée</h3>
                                    <p class="text-sm text-gray-500">Session qui commence</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Cours</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ deplacement.sessionArrivee?.course?.code || 'Cours inconnu' }}</p>
                                    <p class="text-sm text-gray-600">{{ deplacement.sessionArrivee?.course?.title || 'Titre inconnu' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Site</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ deplacement.sessionArrivee?.site?.name || 'Site inconnu' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Salle</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ deplacement.sessionArrivee?.room?.name || 'Salle inconnue' }}</p>
                                    <p class="text-sm text-gray-600">Capacité: {{ deplacement.sessionArrivee?.room?.capacity || 'N/A' }} places</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Heure de début</p>
                                    <p class="text-lg font-semibold text-purple-600">{{ getHeureFormatted(deplacement.heure_arrivee) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Actions -->
            <div class="flex justify-center gap-4">
                <Link :href="route('deplacements.index')">
                    <Button variant="outline">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Retour à la liste
                    </Button>
                </Link>
                
                <Link :href="route('course-sessions.show', deplacement.sessionDepart.id)">
                    <Button variant="outline">
                        Voir session de départ
                    </Button>
                </Link>
                
                <Link :href="route('course-sessions.show', deplacement.sessionArrivee.id)">
                    <Button variant="outline">
                        Voir session d'arrivée
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
