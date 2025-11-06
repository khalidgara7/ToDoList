<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header moderne avec statistiques -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <!-- Titre et actions -->
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div class="mb-4 lg:mb-0">
              <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifications</h1>
              <p class="text-gray-600">Restez informé de toutes vos activités</p>
            </div>
            
            <!-- Actions principales -->
            <div class="flex flex-wrap gap-3">
              <button 
                v-if="notificationsStore.hasUnread" 
                @click="markAllAsRead" 
                class="btn btn-outline"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tout marquer comme lu
              </button>
              
              <button @click="clearRead" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Supprimer les lues
              </button>
              
              <button @click="simulateNotification" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                </svg>
                Test notification
              </button>
            </div>
          </div>

          <!-- Statistiques en cards modernes -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <!-- Total des notifications -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-blue-100 text-sm font-medium">Total</p>
                  <p class="text-2xl font-bold">{{ notificationsStore.notifications.length }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-lg p-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Notifications non lues -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-red-100 text-sm font-medium">Non lues</p>
                  <p class="text-2xl font-bold">{{ notificationsStore.unreadCount }}</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-lg p-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Statut de connexion -->
            <div :class="[
              'rounded-xl p-4 text-white',
              notificationsStore.isConnected 
                ? 'bg-gradient-to-r from-green-500 to-green-600' 
                : 'bg-gradient-to-r from-gray-500 to-gray-600'
            ]">
              <div class="flex items-center justify-between">
                <div>
                  <p :class="notificationsStore.isConnected ? 'text-green-100' : 'text-gray-100'" class="text-sm font-medium">
                    Connexion temps réel
                  </p>
                  <p class="text-2xl font-bold">
                    {{ notificationsStore.isConnected ? 'Connecté' : 'Déconnecté' }}
                  </p>
                </div>
                <div :class="[
                  'rounded-lg p-2',
                  notificationsStore.isConnected ? 'bg-green-400 bg-opacity-30' : 'bg-gray-400 bg-opacity-30'
                ]">
                  <div :class="[
                    'w-3 h-3 rounded-full animate-pulse',
                    notificationsStore.isConnected ? 'bg-green-200' : 'bg-gray-200'
                  ]"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtres et contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Section des filtres -->
      <div class="bg-white rounded-xl shadow-card p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
          <!-- Filtre par type -->
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Type de notification</label>
            <select v-model="filterType" class="input">
              <option value="all">Tous les types</option>
              <option value="task-created">✅ Tâches créées</option>
              <option value="task-updated">📝 Tâches mises à jour</option>
              <option value="task-completed">🎉 Tâches terminées</option>
              <option value="info">ℹ️ Information</option>
              <option value="success">✅ Succès</option>
              <option value="warning">⚠️ Attention</option>
              <option value="error">❌ Erreur</option>
            </select>
          </div>
          
          <!-- Filtre par statut -->
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select v-model="filterStatus" class="input">
              <option value="all">Toutes</option>
              <option value="unread">🔴 Non lues</option>
              <option value="read">✅ Lues</option>
            </select>
          </div>
          
          <!-- Bouton pour effacer les filtres -->
          <div class="flex items-end">
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
      </div>

      <!-- Liste des notifications -->
      <div class="space-y-4">
        <!-- État vide avec design moderne -->
        <div v-if="!filteredNotifications.length" class="text-center py-16">
          <div class="mx-auto h-24 w-24 text-gray-400 mb-6">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-5 5v-5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4" />
            </svg>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">
            {{ notificationsStore.notifications.length === 0 ? 'Aucune notification' : 'Aucune notification trouvée' }}
          </h3>
          <p class="text-gray-600 mb-8 max-w-md mx-auto">
            {{ notificationsStore.notifications.length === 0 
                ? 'Vous n\'avez encore reçu aucune notification. Les notifications apparaîtront ici quand vous effectuerez des actions.' 
                : 'Essayez de modifier vos filtres pour voir d\'autres notifications.' }}
          </p>
          <button @click="simulateNotification" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Tester les notifications
          </button>
        </div>

        <!-- Liste des notifications avec animations -->
        <div v-else class="space-y-3">
          <TransitionGroup name="slide-up" tag="div">
            <div
              v-for="notification in filteredNotifications"
              :key="notification.id"
              :class="[
                'card group cursor-pointer border-l-4 transition-all duration-300',
                {
                  'border-l-blue-400 bg-blue-50': !notification.read,
                  'border-l-gray-300 bg-white': notification.read,
                  'border-l-green-400': notification.type === 'task-created' || notification.type === 'success',
                  'border-l-yellow-400': notification.type === 'task-updated' || notification.type === 'warning',
                  'border-l-purple-400': notification.type === 'task-completed',
                  'border-l-red-400': notification.type === 'error',
                  'border-l-blue-400': notification.type === 'info'
                }
              ]"
              @click="markAsRead(notification.id)"
            >
              <div class="card-body">
                <div class="flex items-start space-x-4">
                  <!-- Icône de notification avec couleur dynamique -->
                  <div :class="[
                    'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white text-lg font-semibold',
                    {
                      'bg-green-500': notification.type === 'task-created' || notification.type === 'success',
                      'bg-yellow-500': notification.type === 'task-updated' || notification.type === 'warning',
                      'bg-purple-500': notification.type === 'task-completed',
                      'bg-red-500': notification.type === 'error',
                      'bg-blue-500': notification.type === 'info' || !notification.type
                    }
                  ]">
                    {{ getNotificationIcon(notification.type) }}
                  </div>
                  
                  <!-- Contenu de la notification -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-2">
                      <h3 :class="[
                        'text-lg font-semibold transition-colors duration-200',
                        notification.read ? 'text-gray-600' : 'text-gray-900'
                      ]">
                        {{ notification.title }}
                      </h3>
                      
                      <!-- Badge "non lu" et temps -->
                      <div class="flex items-center space-x-2 ml-4">
                        <span v-if="!notification.read" class="badge badge-primary animate-pulse">
                          Non lu
                        </span>
                        <span class="text-xs text-gray-500 whitespace-nowrap">
                          {{ formatTime(notification.created_at) }}
                        </span>
                      </div>
                    </div>
                    
                    <!-- Message de la notification -->
                    <p :class="[
                      'text-sm mb-3 transition-colors duration-200',
                      notification.read ? 'text-gray-500' : 'text-gray-700'
                    ]">
                      {{ notification.message }}
                    </p>
                    
                    <!-- Détails de la tâche si disponible -->
                    <div v-if="notification.data?.task" class="p-3 rounded-lg border-l-3" :class="[
                      {
                        'border-l-green-300 bg-green-50': notification.type === 'task-created',
                        'border-l-yellow-300 bg-yellow-50': notification.type === 'task-updated',
                        'border-l-purple-300 bg-purple-50': notification.type === 'task-completed',
                        'border-l-blue-300 bg-blue-50': !notification.type || notification.type === 'info'
                      }
                    ]">
                      <div class="flex items-center justify-between">
                        <div class="flex-1">
                          <p class="font-medium text-sm text-gray-900">
                            📋 {{ notification.data.task.title }}
                          </p>
                          <div class="flex items-center space-x-3 mt-1">
                            <!-- Badge de priorité -->
                            <span v-if="notification.data.task.priority" :class="[
                              'badge text-xs',
                              {
                                'badge-success': notification.data.task.priority === 'low',
                                'badge-warning': notification.data.task.priority === 'medium',
                                'bg-orange-100 text-orange-800': notification.data.task.priority === 'high',
                                'badge-danger': notification.data.task.priority === 'urgent'
                              }
                            ]">
                              {{ getPriorityLabel(notification.data.task.priority) }}
                            </span>
                            
                            <!-- Statut de la tâche -->
                            <span :class="[
                              'badge text-xs',
                              notification.data.task.completed ? 'badge-success' : 'badge-primary'
                            ]">
                              {{ notification.data.task.completed ? '✅ Terminée' : '⏳ En cours' }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Bouton de suppression avec animation -->
                  <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <button 
                      @click.stop="removeNotification(notification.id)" 
                      class="btn-icon text-red-600 hover:bg-red-100 transform hover:scale-110 transition-all duration-200"
                      title="Supprimer cette notification"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>
      </div>
    </div>

    <!-- Toast notifications modernes -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <TransitionGroup name="slide-down" tag="div">
        <div
          v-for="toast in recentToasts"
          :key="toast.id"
          :class="[
            'max-w-sm bg-white rounded-xl shadow-lg border-l-4 p-4 transform transition-all duration-300',
            {
              'border-green-400': toast.type === 'success' || toast.type === 'task-created',
              'border-red-400': toast.type === 'error',
              'border-yellow-400': toast.type === 'warning' || toast.type === 'task-updated',
              'border-purple-400': toast.type === 'task-completed',
              'border-blue-400': toast.type === 'info'
            }
          ]"
        >
          <div class="flex items-start space-x-3">
            <div :class="[
              'flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-white text-sm font-bold',
              {
                'bg-green-500': toast.type === 'success' || toast.type === 'task-created',
                'bg-red-500': toast.type === 'error',
                'bg-yellow-500': toast.type === 'warning' || toast.type === 'task-updated',
                'bg-purple-500': toast.type === 'task-completed',
                'bg-blue-500': toast.type === 'info'
              }
            ]">
              {{ getNotificationIcon(toast.type) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-900">{{ toast.title }}</p>
              <p class="text-sm text-gray-600 mt-1">{{ toast.message }}</p>
            </div>
            <button @click="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useNotificationsStore } from '../stores/notifications'
import { useAuthStore } from '../stores/auth'

const notificationsStore = useNotificationsStore()
const authStore = useAuthStore()

// État des filtres - Variables réactives pour filtrer les notifications
const filterType = ref('all')
const filterStatus = ref('all')

// Toasts en temps réel pour les nouvelles notifications
const recentToasts = ref([])

// Computed - Filtrage intelligent des notifications
const filteredNotifications = computed(() => {
  let notifications = notificationsStore.notifications

  // Filtrage par type de notification
  if (filterType.value !== 'all') {
    notifications = notifications.filter(n => n.type === filterType.value)
  }

  // Filtrage par statut de lecture
  if (filterStatus.value !== 'all') {
    notifications = notifications.filter(n => {
      if (filterStatus.value === 'read') return n.read
      if (filterStatus.value === 'unread') return !n.read
      return true
    })
  }

  return notifications
})

// Vérification si des filtres sont actifs
const hasActiveFilters = computed(() => {
  return filterType.value !== 'all' || filterStatus.value !== 'all'
})

// Actions principales pour la gestion des notifications
const markAsRead = (notificationId) => {
  notificationsStore.markAsRead(notificationId)
}

const markAllAsRead = () => {
  notificationsStore.markAllAsRead()
}

const removeNotification = (notificationId) => {
  notificationsStore.removeNotification(notificationId)
}

const clearRead = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications lues ?')) {
    notificationsStore.clearRead()
  }
}

const clearFilters = () => {
  filterType.value = 'all'
  filterStatus.value = 'all'
}

const simulateNotification = () => {
  const types = ['info', 'success', 'warning', 'error']
  const randomType = types[Math.floor(Math.random() * types.length)]
  notificationsStore.simulateNotification(randomType)
}

// Utilitaires pour l'affichage - Fonctions d'aide pour formater les données
const getNotificationIcon = (type) => {
  const icons = {
    'task-created': '✅',
    'task-updated': '📝',
    'task-completed': '🎉',
    'task-deleted': '🗑',
    'info': 'ℹ️',
    'success': '✅',
    'warning': '⚠️',
    'error': '❌'
  }
  return icons[type] || '🔔'
}

const getPriorityLabel = (priority) => {
  const labels = {
    low: 'Faible',
    medium: 'Moyenne',
    high: 'Élevée',
    urgent: 'Urgente'
  }
  return labels[priority] || priority
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date

  // Formatage intelligent des dates relatives
  if (diff < 60000) return 'À l\'instant'
  if (diff < 3600000) return `Il y a ${Math.floor(diff / 60000)} min`
  if (diff < 86400000) return `Il y a ${Math.floor(diff / 3600000)}h`
  
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Gestion des toasts pour les nouvelles notifications
const showToast = (notification) => {
  const toast = {
    id: `toast-${notification.id}`,
    type: notification.type,
    title: notification.title,
    message: notification.message
  }
  
  recentToasts.value.push(toast)
  
  // Suppression automatique après 5 secondes
  setTimeout(() => {
    removeToast(toast.id)
  }, 5000)
}

const removeToast = (toastId) => {
  const index = recentToasts.value.findIndex(toast => toast.id === toastId)
  if (index > -1) {
    recentToasts.value.splice(index, 1)
  }
}

// Watcher pour détecter les nouvelles notifications et afficher des toasts
watch(() => notificationsStore.notifications.length, (newLength, oldLength) => {
  if (newLength > oldLength && oldLength > 0) {
    // Une nouvelle notification a été ajoutée
    const latestNotification = notificationsStore.notifications[0]
    showToast(latestNotification)
  }
})

// Lifecycle hooks - Initialisation et nettoyage
onMounted(() => {
  // Initialiser Echo si l'utilisateur est connecté
  if (authStore.currentUser) {
    notificationsStore.initializeEcho(authStore.currentUser.id)
  }
})

onUnmounted(() => {
  // Nettoyer les connexions Echo lors de la destruction du composant
  notificationsStore.disconnect()
})
</script>