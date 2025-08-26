<template>
  <div class="flex justify-end gap-3 mt-6">
    <Button 
      type="button"
      variant="outline" 
      @click="$emit('cancel')"
      :disabled="isLoading"
    >
      Annuler
    </Button>
    <Button 
      type="submit"
      :disabled="isLoading || !isValid"
    >
      <span v-if="isLoading">Enregistrement...</span>
      <span v-else>{{ submitText }}</span>
    </Button>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface Props {
  isLoading?: boolean;
  isValid?: boolean;
  submitText?: string;
}

interface Emits {
  (e: 'cancel'): void;
}

withDefaults(defineProps<Props>(), {
  isLoading: false,
  isValid: true,
  submitText: 'Enregistrer'
});

defineEmits<Emits>();
</script>
