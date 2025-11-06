<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header moderne avec statistiques -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <!-- Titre et bouton d'ajout -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes Tâches</h1>
              <p class="text-gray-600">Gérez vos tâches efficacement</p>
            </div>
            
            <!-- Bouton d'ajout principal - visible sur desktop -->
            <button 
              @click="openCreateModal" 
              class="hidden sm:flex btn btn-primary px-6 py-3 text-base font-semibold"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nouvelle tâche
            </button>
          </div>

          <!-- Statistiques en cards -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-blue-100 text-sm font-medium">Total</p>
                  <p class="text-2xl font-bold">{{ tasksStore.tasksCount }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-lg p-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl p-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-yellow-100 text-sm font-medium">En cours</p>
                  <p class="text-2xl font-bold">{{ tasksStore.pendingTasks.length }}</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-lg p-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-green-100 text-sm font-medium">Terminées</p>
                  <p class="text-2xl font-bold">{{ tasksStore.completedTasksCount }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-lg p-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-xl shadow-card p-6 mb-6">
        <!-- Barre de recherche moderne -->
        <div class="relative mb-4">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher une tâche..."
            class="input pl-10 pr-4 py-3 text-base"
          />
        </div>
        
        <!-- Filtres -->
        <div class="flex flex-col sm:flex-row gap-4">
          <select v-model="filterStatus" class="input">
            <option value="all">Toutes les tâches</option>
            <option value="pending">En cours</option>
            <option value="completed">Terminées</option>
          </select>
          
          <select v-model="filterPriority" class="input">
            <option value="all">Toutes les priorités</option>
            <option value="urgent">Urgente</option>
            <option value="high">Élevée</option>
            <option value="medium">Moyenne</option>
            <option value="low">Faible</option>
          </select>
          
          <button 
            v-if="hasActiveFilters" 
            @click="clearFilters" 
            class="btn btn-outline whitespace-nowrap"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Effacer les filtres
          </button>
        </div>
      </div>

      <!-- Liste des tâches -->
      <div class="space-y-4">
        <!-- État de chargement -->
        <div v-if="tasksStore.loading && !tasksStore.tasks.length" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          <p class="mt-4 text-gray-600">Chargement des tâches...</p>
        </div>

        <!-- État vide -->
        <div v-else-if="!filteredTasks.length && !tasksStore.loading" class="text-center py-12">
          <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            {{ tasksStore.tasks.length === 0 ? 'Aucune tâche pour le moment' : 'Aucune tâche trouvée' }}
          </h3>
          <p class="text-gray-600 mb-6">
            {{ tasksStore.tasks.length === 0 
                ? 'Commencez par créer votre première tâche !' 
                : 'Essayez de modifier vos filtres de recherche.' }}
          </p>
          <button v-if="tasksStore.tasks.length === 0" @click="openCreateModal" class="btn btn-primary">
            Créer ma première tâche
          </button>
        </div>

        <!-- Grille de tâches responsive -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TransitionGroup name="slide-up" tag="div" class="contents">
            <TaskCard
              v-for="task in filteredTasks"
              :key="task.id"
              :task="task"
              @edit="editTask"
              @delete="deleteTask"
              @toggle-complete="onTaskToggle"
              class="animate-slide-up"
            />
          </TransitionGroup>
        </div>
      </div>
    </div>

    <!-- Bouton flottant pour mobile -->
    <button 
      @click="openCreateModal"
      class="fixed bottom-6 right-6 sm:hidden bg-primary-600 hover:bg-primary-700 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-110 z-40"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
    </button>

    <!-- Modal de création/édition -->
    <TaskForm
      :is-visible="showTaskModal"
      :task="selectedTask"
      @close="closeTaskModal"
      @task-saved="onTaskSaved"
    />

    <!-- Notifications toast modernes -->
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
                'w-5 h-5 rounded-full flex items-center justify-center text-white text-xs',
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useTasksStore } from '../stores/tasks'
import TaskCard from '../components/TaskCard.vue'
import TaskForm from '../components/TaskForm.vue'

const router = useRouter()
const authStore = useAuthStore()
const tasksStore = useTasksStore()

// État du composant - Variables réactives pour gérer l'interface
const showTaskModal = ref(false)
const selectedTask = ref(null)
const searchQuery = ref('')
const filterStatus = ref('all')
const filterPriority = ref('all')
const toasts = ref([])

// Vérification de l'authentification avant d'accéder aux tâches
const checkAuth = () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return false
  }
  return true
}

// Computed - Filtrage intelligent des tâches basé sur la recherche et les filtres
const filteredTasks = computed(() => {
  let tasks = tasksStore.tasks

  // Filtrage par recherche textuelle (titre et description)
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    tasks = tasks.filter(task =>
      task.title.toLowerCase().includes(query) ||
      (task.description && task.description.toLowerCase().includes(query))
    )
  }

  // Filtrage par statut (terminé/en cours)
  if (filterStatus.value !== 'all') {
    tasks = tasks.filter(task => {
      if (filterStatus.value === 'completed') return task.completed
      if (filterStatus.value === 'pending') return !task.completed
      return true
    })
  }

  // Filtrage par niveau de priorité
  if (filterPriority.value !== 'all') {
    tasks = tasks.filter(task => task.priority === filterPriority.value)
  }

  // Tri par date de création (plus récent en premier)
  return tasks.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// Vérification si des filtres sont actifs pour afficher le bouton de réinitialisation
const hasActiveFilters = computed(() => {
  return searchQuery.value || filterStatus.value !== 'all' || filterPriority.value !== 'all'
})

// Gestion des tâches - Actions principales
const loadTasks = async () => {
  if (!checkAuth()) return
  
  try {
    await tasksStore.fetchTasks()
  } catch (error) {
    console.error('Erreur lors du chargement des tâches:', error)
    showToast('Erreur lors du chargement des tâches', 'error')
  }
}

// Ouverture du modal pour créer une nouvelle tâche
const openCreateModal = () => {
  selectedTask.value = null
  showTaskModal.value = true
}

// Ouverture du modal pour éditer une tâche existante
const editTask = (task) => {
  selectedTask.value = task
  showTaskModal.value = true
}

// Suppression d'une tâche avec gestion d'erreur
const deleteTask = async (taskId) => {
  try {
    await tasksStore.deleteTask(taskId)
    showToast('Tâche supprimée avec succès', 'success')
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    showToast('Erreur lors de la suppression de la tâche', 'error')
  }
}

// Gestion du changement de statut des tâches
const onTaskToggle = (taskId) => {
  const task = tasksStore.tasks.find(t => t.id === taskId)
  if (task) {
    const message = task.completed ? 'Tâche marquée comme terminée' : 'Tâche marquée comme en cours'
    showToast(message, 'info')
  }
}

// Fermeture du modal de création/édition
const closeTaskModal = () => {
  showTaskModal.value = false
  selectedTask.value = null
}

// Callback après sauvegarde d'une tâche
const onTaskSaved = (event) => {
  const { action } = event
  const message = action === 'created' ? 'Tâche créée avec succès' : 'Tâche modifiée avec succès'
  showToast(message, 'success')
}

// Réinitialisation de tous les filtres
const clearFilters = () => {
  searchQuery.value = ''
  filterStatus.value = 'all'
  filterPriority.value = 'all'
}

// Système de notifications toast - Affichage des messages temporaires
const showToast = (message, type = 'info') => {
  const toast = {
    id: Date.now() + Math.random(),
    message,
    type
  }
  
  toasts.value.push(toast)
  
  // Suppression automatique après 4 secondes
  setTimeout(() => {
    removeToast(toast.id)
  }, 4000)
}

// Suppression manuelle d'un toast
const removeToast = (toastId) => {
  const index = toasts.value.findIndex(toast => toast.id === toastId)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

// Icônes pour les différents types de toast
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

// Watcher pour recharger les tâches si l'utilisateur se connecte
watch(() => authStore.isAuthenticated, (newValue) => {
  if (newValue) {
    loadTasks()
  }
})
</script>