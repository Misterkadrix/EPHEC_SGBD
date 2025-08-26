<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface University { id: number; code: string; name: string; }

const props = defineProps<{ university: University }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Universities', href: '/universities' },
    { title: 'Edit', href: `/universities/${props.university.id}/edit` },
];

const form = useForm({
    code: props.university.code,
    name: props.university.name,
});

const submit = () => {
    form.put(route('universities.update', props.university.id));
};
</script>

<template>
    <Head title="Edit University" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier l'université</CardTitle>
                    <CardDescription>Mettez à jour les informations</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="code">Code</Label>
                            <Input id="code" v-model="form.code" type="text" :class="{ 'border-red-500': form.errors.code }" />
                            <div v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label for="name">Nom</Label>
                            <Input id="name" v-model="form.name" type="text" :class="{ 'border-red-500': form.errors.name }" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Enregistrement...</span>
                                <span v-else>Enregistrer</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
