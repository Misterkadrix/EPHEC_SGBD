<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';
import { Route, Clock, MapPin, Users, Calendar } from 'lucide-vue-next';

interface Deplacement {
    id: number;
    heure_depart: string;
    heure_arrivee: string;
    duree_trajet_minutes: number;
    created_at: string;
    updated_at: string;
    group: any; // Simplifié pour l'instant
    sessionDepart: any; // Simplifié pour l'instant
    sessionArrivee: any; // Simplifié pour l'instant
    siteDepart: any; // Simplifié pour l'instant
    siteArrivee: any; // Simplifié pour l'instant
    roomDepart: any; // Simplifié pour l'instant
    roomArrivee: any; // Simplifié pour l'instant
}

interface Props {
    deplacements: {
        data: Deplacement[];
        current_page: number;
        last_page: number;
        total: number;
    };
    stats: {
        total: number;
        inter_site: number;
        aujourd_hui: number;
    };
    filters: {
        date?: string;
        group_id?: string;
        inter_site?: boolean;
    };
}

const props = defineProps<Props>();

// Debug complet
console.log('=== DEBUG FRONTEND ===');
console.log('Props:', props);
console.log('Deplacements:', props.deplacements);
console.log('Deplacements data:', props.deplacements?.data);
console.log('Deplacements data length:', props.deplacements?.data?.length);
console.log('Premier déplacement:', props.deplacements?.data?.[0]);
console.log('Type du premier:', typeof props.deplacements?.data?.[0]);
console.log('Clés du premier:', props.deplacements?.data?.[0] ? Object.keys(props.deplacements?.data?.[0]) : 'NULL');
console.log('=== FIN DEBUG ===');



const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Déplacements', href: '/deplacements' },
];

// État du formulaire de génération
const isGenerating = ref(false);
const generationProgress = ref(0);

// Formulaire pour la génération
const form = useForm({});

// Formater la durée en heures et minutes
const getDureeFormatted = (minutes: number) => {
    const heures = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    if (heures > 0) {
        return `${heures}h${mins}min`;
    }
    
    return `${mins}min`;
};

// Générer tous les déplacements
const generateDeplacements = async () => {
    isGenerating.value = true;
    generationProgress.value = 0;

    try {
        // Simuler une progression
        const progressInterval = setInterval(() => {
            if (generationProgress.value < 90) {
                generationProgress.value += Math.random() * 10;
            }
        }, 200);

        // Envoyer la requête
        await form.post(route('deplacements.generate'), {
            onSuccess: () => {
                generationProgress.value = 100;
                setTimeout(() => {
                    isGenerating.value = false;
                    generationProgress.value = 0;
                    // Recharger la page pour voir les nouveaux déplacements
                    window.location.reload();
                }, 1000);
            },
            onError: () => {
                isGenerating.value = false;
                generationProgress.value = 0;
            },
        });

        clearInterval(progressInterval);
    } catch (error) {
        console.error('Erreur lors de la génération:', error);
        isGenerating.value = false;
        generationProgress.value = 0;
    }
};

// Computed pour déterminer le type de déplacement
const getDeplacementType = (deplacement: Deplacement) => {
    // Vérifier que les propriétés existent
    if (!deplacement.sessionDepart?.site || !deplacement.sessionArrivee?.site) {
        return { type: 'unknown', label: 'Inconnu', color: 'bg-gray-100 text-gray-800' };
    }
    
    if (deplacement.sessionDepart?.site?.id === deplacement.sessionArrivee?.site?.id) {
        return { type: 'meme_site', label: 'Même site', color: 'bg-blue-100 text-blue-800' };
    }
    return { type: 'inter_site', label: 'Inter-site', color: 'bg-orange-100 text-orange-800' };
};


</script>

<template>
    <Head title="Déplacements" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 overflow-x-auto">
            <!-- En-tête avec statistiques -->
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Déplacements des étudiants
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Gestion des trajets entre les différents sites et salles
                    </p>
                </div>
                
                <!-- Bouton de génération -->
                <div class="flex items-center gap-4">
                    <Button 
                        @click="generateDeplacements" 
                        :disabled="isGenerating"
                        class="bg-green-600 hover:bg-green-700"
                    >
                        <Route class="w-4 h-4 mr-2" />
                        {{ isGenerating ? 'Génération...' : 'Générer tous les déplacements' }}
                    </Button>
                </div>
            </div>

            <!-- Barre de progression -->
            <div v-if="isGenerating" class="w-full">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div 
                        class="bg-green-600 h-2.5 rounded-full transition-all duration-300"
                        :style="{ width: generationProgress + '%' }"
                    ></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Génération en cours... {{ Math.round(generationProgress) }}%</p>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total déplacements</CardTitle>
                        <Route class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Tous les déplacements</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Déplacements inter-sites</CardTitle>
                        <MapPin class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ props.stats?.inter_site || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Entre sites différents</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Aujourd'hui</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ props.stats?.aujourd_hui || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Déplacements du jour</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Liste des déplacements -->
            <Card>
                <CardHeader>
                    <CardTitle>Liste des déplacements</CardTitle>
                    <CardDescription>
                        {{ props.deplacements?.total || 0 }} déplacements au total
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="text-center py-4 bg-gray-100 rounded-lg mb-4">
                        <p class="text-sm text-gray-600">
                            <strong>DEBUG:</strong> 
                            Déplacements reçus: {{ props.deplacements?.data?.length || 'undefined' }} | 
                            Total: {{ props.deplacements?.total || 'undefined' }} | 
                            Stats: {{ props.stats?.total || 'undefined' }}
                        </p>
                    </div>

                    <div v-if="!props.deplacements?.data || props.deplacements.data.length === 0" class="text-center py-8">
                        <Route class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <p class="text-gray-500">Aucun déplacement trouvé</p>
                        <p class="text-sm text-gray-400 mt-2">
                            Cliquez sur "Générer tous les déplacements" pour commencer
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <div 
                            v-for="(deplacement, index) in (props.deplacements?.data || [])" 
                            :key="deplacement?.id || index"
                            class="border rounded-lg p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Informations du groupe -->
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <Users class="w-4 h-4 text-blue-600" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900">{{ deplacement.group?.name || 'Groupe inconnu' }}</p>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ deplacement.sessionDepart?.site?.id === deplacement.sessionArrivee?.site?.id ? 'Même site' : 'Inter-site' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Trajet -->
                                <div class="flex-1 flex items-center gap-3">
                                    <!-- Session de départ -->
                                    <div class="text-center min-w-0">
                                        <div class="bg-green-100 p-2 rounded-lg">
                                            <p class="font-semibold text-green-800">{{ deplacement.sessionDepart?.course?.code || 'Cours inconnu' }}</p>
                                            <p class="text-xs text-green-600">{{ deplacement.sessionDepart?.course?.title || 'Titre inconnu' }}</p>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ deplacement.sessionDepart?.site?.name || 'Site inconnu' }}</p>
                                        <p class="text-xs text-gray-500">{{ deplacement.sessionDepart?.room?.name || 'Salle inconnue' }}</p>
                                        <p class="text-sm font-medium text-gray-900">{{ new Date(deplacement.heure_depart).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}</p>
                                    </div>

                                    <!-- Flèche et durée -->
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-8 h-0.5 bg-gray-300"></div>
                                        <div class="bg-gray-100 p-2 rounded-full">
                                            <Clock class="w-4 h-4 text-gray-600" />
                                        </div>
                                        <div class="w-8 h-0.5 bg-gray-300"></div>
                                        <p class="text-xs text-gray-600 font-medium">
                                            {{ getDureeFormatted(deplacement.duree_trajet_minutes) }}
                                        </p>
                                    </div>

                                    <!-- Session d'arrivée -->
                                    <div class="text-center min-w-0">
                                        <div class="bg-purple-100 p-2 rounded-lg">
                                            <p class="font-semibold text-purple-800">{{ deplacement.sessionArrivee?.course?.code || 'Cours inconnu' }}</p>
                                            <p class="text-xs text-purple-600">{{ deplacement.sessionArrivee?.course?.title || 'Titre inconnu' }}</p>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ deplacement.sessionArrivee?.site?.name || 'Site inconnu' }}</p>
                                        <p class="text-xs text-gray-500">{{ deplacement.sessionArrivee?.room?.name || 'Salle inconnue' }}</p>
                                        <p class="text-sm font-medium text-gray-900">{{ new Date(deplacement.heure_arrivee).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}</p>
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">{{ new Date(deplacement.heure_depart).toLocaleDateString('fr-FR') }}</p>
                                    <Link 
                                        :href="route('deplacements.show', deplacement?.id || 0)"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        Voir détails
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="props.deplacements?.last_page && props.deplacements.last_page > 1" class="mt-6 flex justify-center">
                        <div class="flex gap-2">
                            <Button 
                                v-for="page in props.deplacements.last_page" 
                                :key="page"
                                :variant="page === props.deplacements?.current_page ? 'default' : 'outline'"
                                size="sm"
                                :href="route('deplacements.index', { page })"
                                as="a"
                            >
                                {{ page }}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
