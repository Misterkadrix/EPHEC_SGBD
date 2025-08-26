import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

export function useCrudForm<T extends Record<string, any>>(
  initialData: T,
  resource: string,
  itemId?: number | string
) {
  const form = useForm(initialData);
  const isLoading = ref(false);
  const error = ref<string>('');

  // Pré-remplir le formulaire avec les données existantes
  const populateForm = (data: T) => {
    Object.keys(initialData).forEach(key => {
      if (data[key] !== undefined) {
        // Gestion spéciale pour les dates
        if (key.includes('date') || key.includes('start') || key.includes('end')) {
          form[key] = data[key] ? new Date(data[key]).toISOString().split('T')[0] : '';
        } else {
          form[key] = data[key];
        }
      }
    });
  };

  // Normaliser les données avant envoi
  const normalizeData = (data: T): T => {
    const normalized = { ...data };
    
    // Convertir les dates vides en null
    Object.keys(normalized).forEach(key => {
      if ((key.includes('date') || key.includes('start') || key.includes('end')) && normalized[key] === '') {
        normalized[key] = null;
      }
    });

    return normalized;
  };

  // Sauvegarder (créer ou mettre à jour)
  const save = async () => {
    isLoading.value = true;
    error.value = '';

    try {
      const normalizedData = normalizeData(form.data());
      
      if (itemId) {
        // Mise à jour
        await form.put(`/${resource}/${itemId}`);
      } else {
        // Création
        await form.post(`/${resource}`);
      }
    } catch (err) {
      error.value = 'Erreur lors de la sauvegarde';
      console.error('Save error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  // Réinitialiser le formulaire
  const reset = () => {
    form.reset();
    error.value = '';
  };

  // Valider le formulaire
  const validate = (): boolean => {
    error.value = '';
    
    // Validation de base - vérifier les champs requis
    const requiredFields = Object.keys(initialData).filter(key => 
      initialData[key] !== null && initialData[key] !== undefined
    );
    
    for (const field of requiredFields) {
      if (!form[field] || form[field] === '') {
        error.value = `Le champ ${field} est requis`;
        return false;
      }
    }

    return true;
  };

  // Gestion des erreurs de validation
  const handleValidationErrors = (errors: Record<string, string>) => {
    error.value = Object.values(errors)[0] || 'Erreur de validation';
  };

  return {
    form,
    isLoading,
    error,
    populateForm,
    normalizeData,
    save,
    reset,
    validate,
    handleValidationErrors,
  };
}
