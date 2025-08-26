<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface Site {
    id: number;
    name: string;
    university: {
        name: string;
        code: string;
    };
}

interface Room {
    id: number;
    site_id: number;
    name: string;
    capacity: number;
    description?: string;
}

interface Props {
    room: Room;
    sites: Site[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Salles', href: '/rooms' },
    { title: 'Edit', href: `/rooms/${props.room.id}/edit` },
];

const form = useForm({
    site_id: props.room.site_id.toString(),
    name: props.room.name,
    capacity: props.room.capacity.toString(),
    description: props.room.description || '',
});

const submit = () => {
    form.put(route('rooms.update', props.room.id));
};
</script>

<template>
    <Head title="Edit Room" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier la salle</CardTitle>
                    <CardDescription>Mettez à jour les informations de la salle</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="site">Site</Label>
                            <select 
                                v-model="form.site_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.site_id }"
                            >
                                <option value="">Sélectionnez un site</option>
                                <option v-for="site in sites" :key="site.id" :value="site.id">
                                    {{ site.name }} - {{ site.university.name }} ({{ site.university.code }})
                                </option>
                            </select>
                            <div v-if="form.errors.site_id" class="text-red-500 text-sm">{{ form.errors.site_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="name">Nom de la salle</Label>
                            <Input id="name" v-model="form.name" type="text" :class="{ 'border-red-500': form.errors.name }" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="capacity">Capacité</Label>
                            <Input id="capacity" v-model="form.capacity" type="number" min="1" :class="{ 'border-red-500': form.errors.capacity }" />
                            <div v-if="form.errors.capacity" class="text-red-500 text-sm">{{ form.errors.capacity }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description (optionnel)</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
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
