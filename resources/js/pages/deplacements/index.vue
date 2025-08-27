<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ref } from 'vue';
import { Route, Clock, MapPin, Users, Calendar } from 'lucide-vue-next';

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
}

const props = defineProps<Props>();

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
                clearInterval(progressInterval);
                setTimeout(() => {
                    isGenerating.value = false;
                    generationProgress.value = 0;
                }, 500);
            },
            onError: () => {
                clearInterval(progressInterval);
                isGenerating.value = false;
                generationProgress.value = 0;
            }
        });
    } catch (error) {
        console.error('Erreur lors de la génération:', error);
        isGenerating.value = false;
        generationProgress.value = 0;
    }
};

// Déterminer le type de déplacement
const getDeplacementType = (deplacement: Deplacement) => {
    if (!deplacement.session_depart || !deplacement.session_arrivee) {
        return 'Inconnu';
    }
    
    const site_depart = deplacement.session_depart.site?.name || 'Site inconnu';
    const site_arrivee = deplacement.session_arrivee.site?.name || 'Site inconnu';
    
    if (site_depart === site_arrivee) {
        return 'Même site';
    }
    
    return 'Inter-site';
};

// Formater l'heure
const formatTime = (timeString: string) => {
    return new Date(timeString).toLocaleTimeString('fr-FR', {
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
    <AppLayout>
        <Head title="Déplacements" />
        
        <div class="container mx-auto py-6">
            <!-- En-tête -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Déplacements</h1>
                <p class="text-gray-600 mt-2">
                    Gestion des déplacements entre les sessions de cours
                </p>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total</CardTitle>
                        <Route class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                        <p class="text-xs text-muted-foreground">
                            Déplacements enregistrés
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Inter-sites</CardTitle>
                        <MapPin class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.inter_site }}</div>
                        <p class="text-xs text-muted-foreground">
                            Changements de site
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Aujourd'hui</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.aujourd_hui }}</div>
                        <p class="text-xs text-muted-foreground">
                            Déplacements du jour
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Actions -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Génération automatique</CardTitle>
                    <CardDescription>
                        Générer automatiquement tous les déplacements basés sur les sessions existantes
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button 
                        @click="generateDeplacements" 
                        :disabled="isGenerating"
                        class="w-full md:w-auto"
                    >
                        <Route class="h-4 w-4 mr-2" />
                        {{ isGenerating ? 'Génération en cours...' : 'Générer tous les déplacements' }}
                    </Button>
                    
                    <!-- Barre de progression -->
                    <div v-if="isGenerating" class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div 
                                class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                :style="{ width: generationProgress + '%' }"
                            ></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            Progression: {{ Math.round(generationProgress) }}%
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Liste des déplacements -->
            <Card>
                <CardHeader>
                    <CardTitle>Liste des déplacements</CardTitle>
                    <CardDescription>
                        {{ deplacements.total }} déplacement(s) trouvé(s)
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="deplacements.data.length === 0" class="text-center py-8">
                        <Route class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                        <p class="text-gray-500">Aucun déplacement trouvé</p>
                        <p class="text-sm text-gray-400 mt-2">
                            Cliquez sur "Générer tous les déplacements" pour commencer
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <div 
                            v-for="deplacement in deplacements.data" 
                            :key="deplacement.id"
                            class="border rounded-lg p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-2">
                                            <Clock class="h-4 w-4 text-gray-500" />
                                            <span class="font-medium">
                                                {{ formatTime(deplacement.heure_depart) }} → {{ formatTime(deplacement.heure_arrivee) }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <Users class="h-4 w-4 text-gray-500" />
                                            <span class="text-sm text-gray-600">
                                                {{ deplacement.group?.name || 'Groupe inconnu' }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <Route class="h-4 w-4 text-gray-500" />
                                            <span class="text-sm text-gray-600">
                                                {{ getDureeFormatted(deplacement.duree_trajet_minutes) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">Type:</span> 
                                        <span :class="{
                                            'text-blue-600': getDeplacementType(deplacement) === 'Inter-site',
                                            'text-green-600': getDeplacementType(deplacement) === 'Même site',
                                            'text-gray-600': getDeplacementType(deplacement) === 'Inconnu'
                                        }">
                                            {{ getDeplacementType(deplacement) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ formatDate(deplacement.heure_depart) }}
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <Link 
                                        :href="route('deplacements.show', deplacement.id)"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        Voir détails
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="deplacements.last_page > 1" class="mt-6 flex justify-center">
                        <div class="flex space-x-2">
                            <Link 
                                v-for="page in deplacements.last_page" 
                                :key="page"
                                :href="route('deplacements.index', { page })"
                                :class="{
                                    'bg-blue-600 text-white': page === deplacements.current_page,
                                    'bg-gray-200 text-gray-700 hover:bg-gray-300': page !== deplacements.current_page
                                }"
                                class="px-3 py-2 rounded-md text-sm font-medium"
                            >
                                {{ page }}
                            </Link>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
