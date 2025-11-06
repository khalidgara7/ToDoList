<template>
  <!-- Card principale avec hover effects et transitions -->
  <div :class="[
    'card group cursor-pointer transform transition-all duration-300 border-l-4',
    {
      'border-l-green-400 bg-green-50': task.completed,
      'border-l-blue-400': !task.completed && task.priority === 'low',
      'border-l-yellow-400': !task.completed && task.priority === 'medium', 
      'border-l-orange-400': !task.completed && task.priority === 'high',
      'border-l-red-400': !task.completed && task.priority === 'urgent',
      'border-l-gray-400': !task.completed && !task.priority
    }
  ]"
  @click="$emit('toggle-complete', task.id)">
    
    <div class="card-body">
      <!-- Header avec titre et actions -->
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1 min-w-0">
          <h3 :class="[
            'text-lg font-semibold transition-all duration-200',
            {
              'text-gray-500 line-through': task.completed,
              'text-gray-900': !task.completed
            }
          ]">
            {{ task.title }}
          </h3>
          
          <!-- Badge de priorité -->
          <div v-if="task.priority" class="mt-1">
            <span :class="[
              'badge text-xs font-medium',
              {
                'badge-success': task.priority === 'low',
                'badge-warning': task.priority === 'medium',
                'bg-orange-100 text-orange-800': task.priority === 'high',
                'badge-danger': task.priority === 'urgent'
              }
            ]">
              {{ getPriorityLabel(task.priority) }}
            </span>
          </div>
        </div>
        
        <!-- Actions buttons avec animations -->
        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
          <!-- Bouton toggle complete/incomplete -->
          <button 
            @click.stop="toggleComplete" 
            :class="[
              'btn-icon text-sm transform hover:scale-110 transition-all duration-200',
              {
                'text-green-600 hover:bg-green-100': task.completed,
                'text-gray-600 hover:bg-gray-100': !task.completed
              }
            ]"
            :title="task.completed ? 'Marquer comme non terminée' : 'Marquer comme terminée'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="task.completed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
          
          <!-- Bouton éditer -->
          <button 
            @click.stop="editTask" 
            class="btn-icon text-blue-600 hover:bg-blue-100 text-sm transform hover:scale-110 transition-all duration-200"
            title="Modifier la tâche"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          
          <!-- Bouton supprimer -->
          <button 
            @click.stop="deleteTask" 
            class="btn-icon text-red-600 hover:bg-red-100 text-sm transform hover:scale-110 transition-all duration-200"
            title="Supprimer la tâche"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Description -->
      <p v-if="task.description" :class="[
        'text-sm mb-4 line-clamp-2 transition-colors duration-200',
        {
          'text-gray-400': task.completed,
          'text-gray-600': !task.completed
        }
      ]">
        {{ task.description }}
      </p>
      
      <!-- Footer avec métadonnées -->
      <div class="flex items-center justify-between text-xs text-gray-500 mt-4 pt-4 border-t border-gray-100">
        <!-- Date d'échéance -->
        <div v-if="task.due_date" class="flex items-center space-x-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span :class="[
            'font-medium',
            {
              'text-red-500': isOverdue && !task.completed,
              'text-yellow-500': isDueSoon && !task.completed && !isOverdue,
              'text-gray-500': task.completed || (!isOverdue && !isDueSoon)
            }
          ]">
            {{ formatDate(task.due_date) }}
          </span>
        </div>
        
        <!-- Status badge -->
        <div class="flex items-center space-x-2">
          <span :class="[
            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
            {
              'bg-green-100 text-green-800': task.completed,
              'bg-blue-100 text-blue-800': !task.completed
            }
          ]">
            <span :class="[
              'w-2 h-2 rounded-full mr-1',
              {
                'bg-green-400': task.completed,
                'bg-blue-400': !task.completed
              }
            ]"></span>
            {{ task.completed ? 'Terminée' : 'En cours' }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Indicateur de progression visuel -->
    <div class="h-1 bg-gray-100">
      <div :class="[
        'h-full transition-all duration-500 ease-out',
        {
          'bg-green-400 w-full': task.completed,
          'bg-blue-400 w-1/4': !task.completed && task.priority === 'low',
          'bg-yellow-400 w-2/4': !task.completed && task.priority === 'medium',
          'bg-orange-400 w-3/4': !task.completed && task.priority === 'high',
          'bg-red-400 w-full': !task.completed && task.priority === 'urgent',
          'bg-gray-300 w-1/4': !task.completed && !task.priority
        }
      ]"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTasksStore } from '../stores/tasks'

// Props et émissions d'événements
const props = defineProps({
  task: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'toggle-complete'])

const tasksStore = useTasksStore()

// Computed properties pour les dates et statuts
const isOverdue = computed(() => {
  if (!props.task.due_date || props.task.completed) return false
  return new Date(props.task.due_date) < new Date()
})

const isDueSoon = computed(() => {
  if (!props.task.due_date || props.task.completed) return false
  const dueDate = new Date(props.task.due_date)
  const today = new Date()
  const diffTime = dueDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays <= 3 && diffDays >= 0
})

// Actions handlers avec gestion d'erreur
const toggleComplete = async () => {
  try {
    await tasksStore.toggleTaskStatus(props.task.id)
    emit('toggle-complete', props.task.id)
  } catch (error) {
    console.error('Erreur lors du changement de statut:', error)
  }
}

const editTask = () => {
  emit('edit', props.task)
}

const deleteTask = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
    emit('delete', props.task.id)
  }
}

// Utilitaires pour l'affichage
const getPriorityLabel = (priority) => {
  const labels = {
    low: 'Faible',
    medium: 'Moyenne', 
    high: 'Élevée',
    urgent: 'Urgente'
  }
  return labels[priority] || priority
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const today = new Date()
  const diffTime = date - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 0) return "Aujourd'hui"
  if (diffDays === 1) return "Demain" 
  if (diffDays === -1) return "Hier"
  if (diffDays < -1) return `Il y a ${Math.abs(diffDays)} jours`
  if (diffDays > 1) return `Dans ${diffDays} jours`
  
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short'
  })
}
</script>