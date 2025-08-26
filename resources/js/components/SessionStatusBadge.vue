<template>
  <div class="flex flex-col gap-2">
    <!-- Badge de statut -->
    <div class="flex items-center gap-2">
      <span 
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="statusClasses"
      >
        {{ status.label }}
      </span>
      <span class="text-xs text-gray-500">{{ status.description }}</span>
    </div>

    <!-- Permissions de modification -->
    <div v-if="!canModify.can_modify" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-2">
      <div class="font-medium mb-1">🚫 Modifications non autorisées :</div>
      <ul class="list-disc list-inside space-y-1">
        <li v-if="!canModify.can_change_room">Changement de salle non autorisé</li>
        <li v-if="!canModify.can_change_groups">Modification des groupes non autorisée</li>
        <li v-if="!canModify.can_change_schedule">Changement d'horaire non autorisé</li>
        <li v-if="!canModify.can_delete">Suppression non autorisée</li>
      </ul>
      <div class="mt-2 text-red-700 font-medium">{{ canModify.reason }}</div>
    </div>

    <!-- Permissions complètes -->
    <div v-else class="text-xs text-green-600 bg-green-50 border border-green-200 rounded-md p-2">
      <div class="font-medium mb-1">✅ Modifications autorisées :</div>
      <ul class="list-disc list-inside space-y-1">
        <li>Changement de salle</li>
        <li>Modification des groupes</li>
        <li>Changement d'horaire</li>
        <li>Suppression</li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface SessionStatus {
  status: 'future' | 'ongoing' | 'recently_ended' | 'past';
  label: string;
  description: string;
  can_modify: boolean;
}

interface CanModify {
  can_modify: boolean;
  can_delete: boolean;
  can_change_room: boolean;
  can_change_groups: boolean;
  can_change_schedule: boolean;
  reason: string;
  status: string;
}

interface Props {
  status: SessionStatus;
  canModify: CanModify;
}

const props = defineProps<Props>();

const statusClasses = computed(() => {
  switch (props.status.status) {
    case 'future':
      return 'bg-green-100 text-green-800';
    case 'ongoing':
      return 'bg-blue-100 text-blue-800';
    case 'recently_ended':
      return 'bg-yellow-100 text-yellow-800';
    case 'past':
      return 'bg-gray-100 text-gray-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
});
</script>
