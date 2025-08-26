<template>
  <Dialog :open="open" @update:open="$emit('update:open', $event)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Confirmer la suppression</DialogTitle>
        <DialogDescription>
          Êtes-vous sûr de vouloir supprimer <strong>{{ itemName }}</strong> ? 
          Cette action est irréversible.
        </DialogDescription>
      </DialogHeader>
      
      <div class="flex justify-end gap-3 mt-6">
        <Button 
          variant="outline" 
          @click="$emit('update:open', false)"
          :disabled="isLoading"
        >
          Annuler
        </Button>
        <Button 
          variant="destructive" 
          @click="confirmDelete"
          :disabled="isLoading"
        >
          <span v-if="isLoading">Suppression...</span>
          <span v-else>Supprimer</span>
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface Props {
  open: boolean;
  itemName: string;
  isLoading?: boolean;
}

interface Emits {
  (e: 'update:open', value: boolean): void;
  (e: 'confirm'): void;
}

defineProps<Props>();

const emit = defineEmits<Emits>();

const confirmDelete = () => {
  emit('confirm');
};
</script>
