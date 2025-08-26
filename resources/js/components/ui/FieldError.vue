<template>
  <div v-if="error" class="text-sm text-red-600 dark:text-red-400 mt-1">
    {{ error }}
  </div>
</template>

<script setup lang="ts">
interface Props {
  error?: string;
}

defineProps<Props>();
</script>
