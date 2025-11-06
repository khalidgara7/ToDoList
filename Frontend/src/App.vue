<template>
  <!-- Application principale avec navigation moderne -->
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation globale - Affichée uniquement si l'utilisateur est connecté -->
    <AppNavigation v-if="authStore.isAuthenticated" />
    
    <!-- Contenu principal avec transitions -->
    <main :class="{ 'pt-0': !authStore.isAuthenticated }">
      <router-view v-slot="{ Component, route }">
        <Transition
          :name="getTransitionName(route)"
          mode="out-in"
          appear
        >
          <component :is="Component" :key="route.path" />
        </Transition>
      </router-view>
    </main>
    
    <!-- Indicateur de connexion global en bas à droite -->
    <div 
      v-if="authStore.isAuthenticated && notificationsStore" 
      class="fixed bottom-4 left-4 z-40"
    >
      <div :class="[
        'flex items-center space-x-2 px-3 py-2 rounded-full shadow-lg transition-all duration-300',
        notificationsStore.isConnected 
          ? 'bg-green-500 text-white' 
          : 'bg-gray-500 text-white'
      ]">
        <div :class="[
          'w-2 h-2 rounded-full animate-pulse',
          notificationsStore.isConnected ? 'bg-green-200' : 'bg-gray-200'
        ]"></div>
        <span class="text-xs font-medium">
          {{ notificationsStore.isConnected ? 'En ligne' : 'Hors ligne' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'
import { useNotificationsStore } from './stores/notifications'
import AppNavigation from './components/AppNavigation.vue'

const router = useRouter()
const authStore = useAuthStore()
const notificationsStore = useNotificationsStore()

// Déterminer le type de transition basé sur la route
const getTransitionName = (route) => {
  // Transitions spécifiques pour certaines routes
  if (route.path === '/login' || route.path === '/register') {
    return 'slide-up'
  }
  if (route.path === '/tasks' || route.path === '/notifications') {
    return 'fade'
  }
  return 'slide-up'
}

// Initialisation de l'application
onMounted(async () => {
  // Vérifier l'état d'authentification au démarrage
  await authStore.checkAuthState()
  
  // Initialiser les notifications si l'utilisateur est connecté
  if (authStore.isAuthenticated && authStore.currentUser) {
    notificationsStore.initializeEcho(authStore.currentUser.id)
  }
})

// Watcher pour gérer l'état d'authentification
watch(() => authStore.isAuthenticated, (isAuth) => {
  if (isAuth && authStore.currentUser) {
    // Initialiser les notifications lors de la connexion
    notificationsStore.initializeEcho(authStore.currentUser.id)
  } else {
    // Déconnecter les notifications lors de la déconnexion
    notificationsStore.disconnect()
  }
})
</script>
