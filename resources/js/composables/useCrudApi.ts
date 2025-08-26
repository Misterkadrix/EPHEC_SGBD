import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useCrudApi(resource: string) {
  const isLoading = ref(false);
  const error = ref<string>('');

  const list = async () => {
    isLoading.value = true;
    error.value = '';
    try {
      await router.visit(`/${resource}`);
    } catch (err) {
      error.value = 'Erreur lors du chargement de la liste';
      console.error('List error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const get = async (id: number | string) => {
    isLoading.value = true;
    error.value = '';
    try {
      await router.visit(`/${resource}/${id}/edit`);
    } catch (err) {
      error.value = 'Erreur lors du chargement de l\'élément';
      console.error('Get error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const create = async (data: any) => {
    isLoading.value = true;
    error.value = '';
    try {
      await router.post(`/${resource}`, data);
    } catch (err) {
      error.value = 'Erreur lors de la création';
      console.error('Create error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const update = async (id: number | string, data: any) => {
    isLoading.value = true;
    error.value = '';
    try {
      await router.put(`/${resource}/${id}`, data);
    } catch (err) {
      error.value = 'Erreur lors de la mise à jour';
      console.error('Update error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const remove = async (id: number | string) => {
    isLoading.value = true;
    error.value = '';
    try {
      await router.delete(`/${resource}/${id}`);
    } catch (err) {
      error.value = 'Erreur lors de la suppression';
      console.error('Delete error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const goToList = () => {
    router.visit(`/${resource}`);
  };

  const goToCreate = () => {
    router.visit(`/${resource}/create`);
  };

  const goToEdit = (id: number | string) => {
    router.visit(`/${resource}/${id}/edit`);
  };

  return {
    isLoading,
    error,
    list,
    get,
    create,
    update,
    remove,
    goToList,
    goToCreate,
    goToEdit,
  };
}
