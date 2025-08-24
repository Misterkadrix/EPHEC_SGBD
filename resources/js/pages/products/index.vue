<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Product {
    id: number;
    name: string;
    description: string;
    quantite: number;
    created_at: string;
}

interface Props {
    products: Product[];
    success?: string;
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Message de succès -->
            <div v-if="(page.props as any).flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ (page.props as any).flash.success }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Produits</h1>
                <Link :href="route('products.create')">
                    <Button>
                        Créer un produit
                    </Button> 
                </Link>
            </div>
            
            <!-- Liste des produits -->
            <div v-if="products.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="product in products" :key="product.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="text-lg">{{ product.name }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-gray-600 mb-3 line-clamp-3">{{ product.description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Quantité: <span class="font-semibold">{{ product.quantite }}</span>
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ new Date(product.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Message si aucun produit -->
            <div v-else class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-4">Aucun produit n'a été créé pour le moment.</p>
                <Link :href="route('products.create')">
                    <Button>
                        Créer votre premier produit
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
