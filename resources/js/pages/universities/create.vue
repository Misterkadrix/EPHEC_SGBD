<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Universities', href: '/universities' },
    { title: 'Create', href: '/universities/create' },
];

const form = useForm({
    code: '',
    name: '',
});

const submit = () => {
    form.post(route('universities.store'));
};

const resetForm = () => form.reset();
</script>

<template>
    <Head title="Create University" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer une université</CardTitle>
                    <CardDescription>Renseignez le code et le nom</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="code">Code</Label>
                            <Input id="code" v-model="form.code" type="text" placeholder="KAD" :class="{ 'border-red-500': form.errors.code }" />
                            <div v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label for="name">Nom</Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="Université" :class="{ 'border-red-500': form.errors.name }" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
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
