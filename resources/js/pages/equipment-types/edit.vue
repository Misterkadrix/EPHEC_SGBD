<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface EquipmentType {
    id: number;
    label: string;
}

interface Props {
    equipmentType: EquipmentType;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Types d\'équipements', href: '/equipment-types' },
    { title: 'Edit', href: `/equipment-types/${props.equipmentType.id}/edit` },
];

const form = useForm({
    label: props.equipmentType.label,
});

const submit = () => {
    form.put(route('equipment-types.update', props.equipmentType.id));
};
</script>

<template>
    <Head title="Edit Equipment Type" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Modifier le type d'équipement</CardTitle>
                    <CardDescription>Mettez à jour le label du type d'équipement</CardDescription>
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
