<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Simple Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-900">Mes Tâches</h1>
          <button 
            @click="openCreateModal" 
            class="bg-blau-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nouvelle tâche</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tasks Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="tasksStore.loading && !tasksStore.tasks.length" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <p class="mt-4 text-gray-600">Chargement des tâches...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="!tasksStore.tasks.length" class="text-center py-12">
        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune tâche pour le moment</h3>
        <p class="text-gray-600 mb-6">Commencez par créer votre première tâche !</p>
        <button @click="openCreateModal" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
          Créer ma première tâche
        </button>
      </div>

      <!-- Tasks Table -->
      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Titre
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Description
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Priorité
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date limite
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="task in tasksStore.tasks" :key="task.id" class="hover:bg-gray-50">
              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleTaskStatus(task.id)"
                  :class="[
                    'flex items-center justify-center w-6 h-6 rounded-full border-2 transition-all duration-200',
                    task.completed 
                      ? 'bg-green-500 border-green-500 text-white' 
                      : 'border-gray-300 hover:border-green-500'
                  ]"
                >
                  <svg v-if="task.completed" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </td>

              <!-- Title -->
              <td class="px-6 py-4">
                <div :class="['font-medium', task.completed ? 'text-gray-500 line-through' : 'text-gray-900']">
                  {{ task.title }}
                </div>
              </td>

              <!-- Description -->
              <td class="px-6 py-4">
                <div :class="['text-sm max-w-xs truncate', task.completed ? 'text-gray-400' : 'text-gray-600']">
                  {{ task.description || '-' }}
                </div>
              </td>

              <!-- Priority -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                  {
                    'bg-red-100 text-red-800': task.priority === 'urgent',
                    'bg-orange-100 text-orange-800': task.priority === 'high',
                    'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                    'bg-green-100 text-green-800': task.priority === 'low'
                  }
                ]">
                  {{ getPriorityLabel(task.priority) }}
                </span>
              </td>

              <!-- Due Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ task.due_date ? formatDate(task.due_date) : '-' }}
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <button
                    @click="editTask(task)"
                    class="text-primary-600 hover:text-primary-900 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteTask(task.id)"
                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de création/édition -->
    <TaskForm
      :is-visible="showTaskModal"
      :task="selectedTask"
      @close="closeTaskModal"
      @task-saved="onTaskSaved"
    />

    <!-- Toast notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <TransitionGroup name="slide-down" tag="div">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'max-w-sm bg-white rounded-lg shadow-lg border-l-4 p-4 transform transition-all duration-300',
            {
              'border-green-400': toast.type === 'success',
              'border-red-400': toast.type === 'error',
              'border-yellow-400': toast.type === 'warning',
              'border-blue-400': toast.type === 'info'
            }
          ]"
        >
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <div :class="[
                'w-4 h-4 rounded-full flex items-center justify-center text-white text-xs',
                {
                  'bg-green-400': toast.type === 'success',
                  'bg-red-400': toast.type === 'error', 
                  'bg-yellow-400': toast.type === 'warning',
                  'bg-blue-400': toast.type === 'info'
                }
              ]">
                {{ getToastIcon(toast.type) }}
              </div>
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-gray-900">{{ toast.message }}</p>
            </div>
            <button @click="removeToast(toast.id)" class="ml-4 text-gray-400 hover:text-gray-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useTasksStore } from '../stores/tasks'
import TaskForm from '../components/TaskForm.vue'

const router = useRouter()
const authStore = useAuthStore()
const tasksStore = useTasksStore()

// État du composant
const showTaskModal = ref(false)
const selectedTask = ref(null)
const toasts = ref([])

// Vérification de l'authentification
const checkAuth = () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return false
  }
  return true
}

// Gestion des tâches
const loadTasks = async () => {
  if (!checkAuth()) return
  
  try {
    await tasksStore.fetchTasks()
  } catch (error) {
    console.error('Erreur lors du chargement des tâches:', error)
    showToast('Erreur lors du chargement des tâches', 'error')
  }
}

const openCreateModal = () => {
  selectedTask.value = null
  showTaskModal.value = true
}

const editTask = (task) => {
  selectedTask.value = task
  showTaskModal.value = true
}

const deleteTask = async (taskId) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) return
  
  try {
    await tasksStore.deleteTask(taskId)
    showToast('Tâche supprimée avec succès', 'success')
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    showToast('Erreur lors de la suppression de la tâche', 'error')
  }
}

const toggleTaskStatus = async (taskId) => {
  try {
    await tasksStore.toggleTaskStatus(taskId)
    const task = tasksStore.tasks.find(t => t.id === taskId)
    const message = task?.completed ? 'Tâche marquée comme terminée' : 'Tâche marquée comme en cours'
    showToast(message, 'info')
  } catch (error) {
    console.error('Erreur lors du changement de statut:', error)
    showToast('Erreur lors du changement de statut', 'error')
  }
}

const closeTaskModal = () => {
  showTaskModal.value = false
  selectedTask.value = null
}

const onTaskSaved = (event) => {
  const { action } = event
  const message = action === 'created' ? 'Tâche créée avec succès' : 'Tâche modifiée avec succès'
  showToast(message, 'success')
}

// Utilitaires
const getPriorityLabel = (priority) => {
  const labels = {
    urgent: 'Urgente',
    high: 'Élevée',
    medium: 'Moyenne',
    low: 'Faible'
  }
  return labels[priority] || priority
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR')
}

// Système de notifications toast
const showToast = (message, type = 'info') => {
  const toast = {
    id: Date.now() + Math.random(),
    message,
    type
  }
  
  toasts.value.push(toast)
  
  setTimeout(() => {
    removeToast(toast.id)
  }, 4000)
}

const removeToast = (toastId) => {
  const index = toasts.value.findIndex(toast => toast.id === toastId)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

const getToastIcon = (type) => {
  const icons = {
    success: '✓',
    error: '✕',
    warning: '!',
    info: 'i'
  }
  return icons[type] || icons.info
}

// Lifecycle hooks
onMounted(() => {
  loadTasks()
})

watch(() => authStore.isAuthenticated, (newValue) => {
  if (newValue) {
    loadTasks()
  }
})
</script>