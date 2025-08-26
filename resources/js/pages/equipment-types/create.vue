<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import FormActions from '@/components/ui/FormActions.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Types d\'équipements', href: '/equipment-types' },
    { title: 'Create', href: '/equipment-types/create' },
];

const form = useForm({
    label: '',
});

const submit = () => {
    form.post(route('equipment-types.store'));
};

const resetForm = () => form.reset();

const cancel = () => {
    // Empêcher la validation du formulaire et naviguer directement
    form.clearErrors();
    router.visit('/equipment-types');
};
</script>

<template>
    <Head title="Create Equipment Type" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Créer un type d'équipement</CardTitle>
                    <CardDescription>Renseignez le label du type d'équipement</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="label">Label</Label>
                            <Input 
                                id="label" 
                                v-model="form.label" 
                                type="text" 
                                placeholder="ex: PC lab, Baffles, Projecteur" 
                                :class="{ 'border-red-500': form.errors.label }" 
                            />
                            <div v-if="form.errors.label" class="text-red-500 text-sm">{{ form.errors.label }}</div>
                        </div>

                        <FormActions
                            :is-loading="form.processing"
                            :is-valid="form.label"
                            submit-text="Créer"
                            show-reset
                            @cancel="cancel"
                            @reset="resetForm"
                        />
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
