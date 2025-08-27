<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { ref, computed } from 'vue';

interface AcademicYear {
    id: number;
    name: string;
    state: string;
    university: {
        id: number;
        name: string;
        code: string;
    };
}

interface Props {
    academicYears: AcademicYear[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Planification', href: '/planning' },
];

// État du formulaire
const selectedAcademicYear = ref<number | null>(null);
const isGenerating = ref(false);
const generationProgress = ref(0);

// Formulaire pour la génération
const form = useForm({
    academic_year_id: ''
});

// Filtrer les années académiques actives
const activeAcademicYears = computed(() => {
    return props.academicYears.filter(year => year.state === 'active');
});

// Sélectionner une année académique
const selectAcademicYear = (yearId: number) => {
    selectedAcademicYear.value = yearId;
    form.academic_year_id = yearId.toString();
};

// Générer les horaires
const generateSchedule = async () => {
    if (!form.academic_year_id) {
        alert('Veuillez sélectionner une année académique');
        return;
    }

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
        await form.post(route('planning.generate-schedule'), {
            onSuccess: () => {
                generationProgress.value = 100;
                setTimeout(() => {
                    isGenerating.value = false;
                    generationProgress.value = 0;
                }, 1000);
            },
            onError: () => {
                isGenerating.value = false;
                generationProgress.value = 0;
            }
        });

        clearInterval(progressInterval);
    } catch (error) {
        console.error('Erreur lors de la génération:', error);
        isGenerating.value = false;
        generationProgress.value = 0;
    }
};

// Annuler la génération
const cancelGeneration = () => {
    isGenerating.value = false;
    generationProgress.value = 0;
};
</script>

<template>
    <Head title="Planification automatique" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Titre et description -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    🚀 Planification automatique des horaires
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">
                    Générez automatiquement les sessions de cours selon les règles métier définies
                </p>
            </div>

            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" 
                 class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ (page.props as any).flash.success }}
            </div>

            <!-- Message d'erreur -->
            <div v-if="(page.props as any).flash?.error || form.errors.error" 
                 class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ (page.props as any).flash?.error || form.errors.error }}
            </div>

            <!-- Sélection de l'année académique -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>📅 Sélection de l'année académique</CardTitle>
                    <CardDescription>
                        Choisissez l'année académique pour laquelle générer les horaires
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="year in activeAcademicYears" :key="year.id" 
                             class="border rounded-lg p-4 cursor-pointer transition-all hover:shadow-md"
                             :class="{
                                 'border-blue-500 bg-blue-50': selectedAcademicYear === year.id,
                                 'border-gray-200 hover:border-gray-300': selectedAcademicYear !== year.id
                             }"
                             @click="selectAcademicYear(year.id)">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">{{ year.name }}</div>
                                <div class="text-sm text-gray-600">{{ year.university.name }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ year.university.code }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeAcademicYears.length === 0" class="text-center py-8">
                        <p class="text-gray-500">Aucune année académique active trouvée</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Règles métier -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>⚙️ Règles métier appliquées</CardTitle>
                    <CardDescription>
                        Les horaires sont générés selon ces contraintes
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Durée cours = 1h</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Préférence du site principal du groupe</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">1h de déplacement inter-sites si nécessaire</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Respect des plages horaires du site</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Fallback vers sites autorisés si indisponible</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Pas de collisions (groupe/salle/équipement)</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Capacité salle ≥ effectif du groupe</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm">Matériel requis disponible</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Bouton de génération -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>🎯 Génération des horaires</CardTitle>
                    <CardDescription>
                        Cliquez sur le bouton pour lancer la génération automatique
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="text-center">
                        <Button 
                            @click="generateSchedule"
                            :disabled="!selectedAcademicYear || isGenerating"
                            size="lg"
                            class="px-8 py-3 text-lg"
                            :class="{
                                'bg-blue-600 hover:bg-blue-700': !isGenerating,
                                'bg-gray-400 cursor-not-allowed': isGenerating
                            }">
                            <span v-if="!isGenerating">🚀 Générer les horaires</span>
                            <span v-else>⏳ Génération en cours...</span>
                        </Button>
                    </div>

                    <!-- Barre de progression -->
                    <div v-if="isGenerating" class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Progression</span>
                            <span class="text-sm font-medium">{{ Math.round(generationProgress) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                                 :style="{ width: generationProgress + '%' }"></div>
                        </div>
                        <div class="text-center mt-4">
                            <Button @click="cancelGeneration" variant="outline" size="sm">
                                Annuler
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Informations supplémentaires -->
            <Card>
                <CardHeader>
                    <CardTitle>ℹ️ Informations importantes</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start space-x-2">
                            <span class="text-blue-500">•</span>
                            <span>La génération peut prendre plusieurs minutes selon le nombre de groupes et de cours</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <span class="text-blue-500">•</span>
                            <span>Les sessions existantes ne seront pas modifiées</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <span class="text-blue-500">•</span>
                            <span>Seules les années académiques actives peuvent être planifiées</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <span class="text-blue-500">•</span>
                            <span>Les erreurs de planification seront affichées dans les logs</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
