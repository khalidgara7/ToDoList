<template>
  <!-- Modal overlay avec animation de fond -->
  <Transition name="fade">
    <div v-if="isVisible" class="modal-overlay" @click="closeModal">
      <!-- Contenu de la modal avec animation slide-up -->
      <Transition name="slide-up">
        <div v-if="isVisible" class="modal-content" @click.stop>
          <!-- Header de la modal avec design moderne -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">
                {{ isEditing ? 'Modifier la tâche' : 'Nouvelle tâche' }}
              </h2>
              <p class="text-sm text-gray-500 mt-1">
                {{ isEditing ? 'Modifiez les détails de votre tâche' : 'Créez une nouvelle tâche pour rester organisé' }}
              </p>
            </div>
            
            <!-- Bouton de fermeture avec animation -->
            <button 
              @click="closeModal" 
              class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 transform hover:scale-110"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <!-- Formulaire avec design moderne -->
          <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Champ titre avec validation visuelle -->
            <div class="space-y-2">
              <label for="title" class="block text-sm font-medium text-gray-700">
                Titre <span class="text-red-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                placeholder="Titre de la tâche"
                :class="[
                  'input',
                  { 'input-error': errors.title }
                ]"
              />
              <!-- Message d'erreur avec animation -->
              <Transition name="slide-down">
                <p v-if="errors.title" class="text-sm text-red-600 flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ errors.title }}</span>
                </p>
              </Transition>
            </div>

            <!-- Champ description avec textarea moderne -->
            <div class="space-y-2">
              <label for="description" class="block text-sm font-medium text-gray-700">
                Description
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                placeholder="Description de la tâche (optionnel)"
                :class="[
                  'input resize-none',
                  { 'input-error': errors.description }
                ]"
              ></textarea>
              <Transition name="slide-down">
                <p v-if="errors.description" class="text-sm text-red-600 flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ errors.description }}</span>
                </p>
              </Transition>
            </div>

            <!-- Grille pour priorité et date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Sélecteur de priorité avec couleurs -->
              <div class="space-y-2">
                <label for="priority" class="block text-sm font-medium text-gray-700">
                  Priorité
                </label>
                <select id="priority" v-model="form.priority" class="input">
                  <option value="">Sélectionner une priorité</option>
                  <option value="low">🟢 Faible</option>
                  <option value="medium">🟡 Moyenne</option>
                  <option value="high">🟠 Élevée</option>
                  <option value="urgent">🔴 Urgente</option>
                </select>
              </div>

              <!-- Sélecteur de date avec validation -->
              <div class="space-y-2">
                <label for="due_date" class="block text-sm font-medium text-gray-700">
                  Date d'échéance
                </label>
                <input
                  id="due_date"
                  v-model="form.due_date"
                  type="date"
                  :min="today"
                  class="input"
                />
              </div>
            </div>

            <!-- Checkbox pour les tâches en édition -->
            <div v-if="isEditing" class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
              <input
                id="completed"
                type="checkbox"
                v-model="form.completed"
                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2"
              />
              <label for="completed" class="text-sm font-medium text-gray-700 cursor-pointer">
                Marquer cette tâche comme terminée
              </label>
            </div>

            <!-- Aperçu de la priorité sélectionnée -->
            <div v-if="form.priority" class="p-4 rounded-lg border-l-4" :class="[
              {
                'border-l-blue-400 bg-blue-50': form.priority === 'low',
                'border-l-yellow-400 bg-yellow-50': form.priority === 'medium',
                'border-l-orange-400 bg-orange-50': form.priority === 'high',
                'border-l-red-400 bg-red-50': form.priority === 'urgent'
              }
            ]">
              <div class="flex items-center space-x-2">
                <div :class="[
                  'w-2 h-2 rounded-full',
                  {
                    'bg-blue-400': form.priority === 'low',
                    'bg-yellow-400': form.priority === 'medium',
                    'bg-orange-400': form.priority === 'high',
                    'bg-red-400': form.priority === 'urgent'
                  }
                ]"></div>
                <span class="text-sm font-medium" :class="[
                  {
                    'text-blue-800': form.priority === 'low',
                    'text-yellow-800': form.priority === 'medium',
                    'text-orange-800': form.priority === 'high',
                    'text-red-800': form.priority === 'urgent'
                  }
                ]">
                  Priorité {{ getPriorityLabel(form.priority) }}
                </span>
              </div>
            </div>

            <!-- Actions avec boutons modernes -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
              <button 
                type="button" 
                @click="closeModal" 
                class="btn btn-outline flex-1 sm:flex-none order-2 sm:order-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Annuler
              </button>
              
              <button 
                type="submit" 
                :disabled="loading" 
                :class="[
                  'btn flex-1 order-1 sm:order-2',
                  isEditing ? 'btn-secondary' : 'btn-primary'
                ]"
              >
                <!-- Spinner de chargement -->
                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <!-- Icône dynamique -->
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="isEditing" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                
                {{ loading ? 'Enregistrement...' : (isEditing ? 'Modifier la tâche' : 'Créer la tâche') }}
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref, reactive, computed, watch, nextTick } from 'vue'
import { useTasksStore } from '../stores/tasks'

// Props et émissions
const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  task: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'task-saved'])

const tasksStore = useTasksStore()

// État du composant - Variables réactives pour gérer le formulaire
const loading = ref(false)
const errors = reactive({})

// Formulaire réactif avec validation
const form = reactive({
  title: '',
  description: '',
  priority: '',
  due_date: '',
  completed: false
})

// Computed properties
const isEditing = computed(() => !!props.task)

const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

// Réinitialisation du formulaire - Nettoie tous les champs
const resetForm = () => {
  form.title = ''
  form.description = ''
  form.priority = ''
  form.due_date = ''
  form.completed = false
  Object.keys(errors).forEach(key => delete errors[key])
}

// Chargement des données existantes pour l'édition
const loadTaskData = () => {
  if (props.task) {
    form.title = props.task.title || ''
    form.description = props.task.description || ''
    form.priority = props.task.priority || ''
    form.due_date = props.task.due_date ? props.task.due_date.split('T')[0] : ''
    form.completed = props.task.completed || false
  } else {
    resetForm()
  }
}

// Validation côté client avec messages d'erreur détaillés
const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  
  // Validation du titre
  if (!form.title.trim()) {
    errors.title = 'Le titre est obligatoire'
  } else if (form.title.length > 255) {
    errors.title = 'Le titre ne peut pas dépasser 255 caractères'
  }
  
  // Validation de la description
  if (form.description && form.description.length > 1000) {
    errors.description = 'La description ne peut pas dépasser 1000 caractères'
  }
  
  return Object.keys(errors).length === 0
}

// Soumission du formulaire avec gestion d'erreur complète
const submitForm = async () => {
  if (!validateForm()) return
  
  loading.value = true
  
  try {
    // Préparation des données à envoyer
    const taskData = {
      title: form.title.trim(),
      description: form.description.trim(),
      priority: form.priority || null,
      due_date: form.due_date || null,
      completed: form.completed
    }
    
    // Appel à l'API via le store
    if (isEditing.value) {
      await tasksStore.updateTask(props.task.id, taskData)
    } else {
      await tasksStore.createTask(taskData)
    }
    
    // Émission de l'événement de succès
    emit('task-saved', {
      action: isEditing.value ? 'updated' : 'created',
      task: taskData
    })
    
    closeModal()
  } catch (error) {
    console.error('Erreur lors de l\'enregistrement:', error)
    
    // Gestion des erreurs de validation du serveur
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors)
    }
  } finally {
    loading.value = false
  }
}

// Fermeture de la modal avec nettoyage
const closeModal = () => {
  resetForm()
  emit('close')
}

// Utilitaires d'affichage
const getPriorityLabel = (priority) => {
  const labels = {
    low: 'faible',
    medium: 'moyenne',
    high: 'élevée',
    urgent: 'urgente'
  }
  return labels[priority] || priority
}

// Watchers pour la réactivité
watch(() => props.isVisible, (newValue) => {
  if (newValue) {
    nextTick(() => {
      loadTaskData()
      // Focus automatique sur le premier champ
      const titleInput = document.getElementById('title')
      if (titleInput) titleInput.focus()
    })
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 0;
  margin-bottom: 1rem;
}

.modal-header h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.btn-close {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  color: #7f8c8d;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: #f8f9fa;
  color: #2c3e50;
}

.task-form {
  padding: 0 1.5rem 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
}

input[type="text"],
input[type="date"],
textarea,
select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e1e8ed;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s ease;
  box-sizing: border-box;
}

input[type="text"]:focus,
input[type="date"]:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: #3498db;
}

input.error,
textarea.error,
select.error {
  border-color: #e74c3c;
}

.error-message {
  color: #e74c3c;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}

.checkbox-label {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-weight: normal;
  margin-bottom: 0;
}

.checkbox-label input[type="checkbox"] {
  width: auto;
  margin-right: 0.75rem;
  transform: scale(1.2);
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #e1e8ed;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
}

.btn-secondary:hover:not(:disabled) {
  background: #e2e6ea;
  color: #495057;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2980b9;
}

@media (max-width: 768px) {
  .modal-overlay {
    padding: 0.5rem;
  }
  
  .modal-content {
    max-height: 95vh;
  }
  
  .modal-header {
    padding: 1rem 1rem 0;
  }
  
  .task-form {
    padding: 0 1rem 1rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column-reverse;
  }
  
  .btn {
    width: 100%;
  }
}
</style>