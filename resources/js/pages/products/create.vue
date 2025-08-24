<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Create Product',
        href: '/products/create',
    },
];

// Formulaire avec validation
const form = useForm({
    name: '',
    description: '',
    quantite: '',
});

// Fonction pour soumettre le formulaire
const submit = () => {
    form.post(route('products.store'), {
        onSuccess: () => {
            // Redirection après succès
            form.reset();
        },
        onError: (errors) => {
            console.error('Erreurs de validation:', errors);
        },
    });
};

// Fonction pour réinitialiser le formulaire
const resetForm = () => {
    form.reset();
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer un nouveau produit</CardTitle>
                    <CardDescription>
                        Remplissez les informations du produit ci-dessous
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Champ Nom -->
                        <div class="space-y-2">
                            <Label for="name">Nom du produit</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Entrez le nom du produit"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Champ Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Entrez la description du produit"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Champ Quantité -->
                        <div class="space-y-2">
                            <Label for="quantite">Quantité</Label>
                            <Input
                                id="quantite"
                                v-model="form.quantite"
                                type="number"
                                min="0"
                                placeholder="Entrez la quantité"
                                :class="{ 'border-red-500': form.errors.quantite }"
                            />
                            <div v-if="form.errors.quantite" class="text-red-500 text-sm">
                                {{ form.errors.quantite }}
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-3">
                            <Button
                                type="button"
                                variant="outline"
                                @click="resetForm"
                                :disabled="form.processing"
                            >
                                Réinitialiser
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Création...</span>
                                <span v-else>Créer le produit</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
