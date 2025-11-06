<template>
  <!-- Navigation moderne avec Tailwind CSS -->
  <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo et nom de l'application -->
        <div class="flex items-center">
          <router-link 
            to="/" 
            class="flex items-center space-x-3 text-xl font-bold text-gray-900 hover:text-primary-600 transition-colors duration-200"
          >
            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
              📋
            </div>
            <span class="hidden sm:block">ToDo App</span>
          </router-link>
        </div>
        
        <!-- Menu de navigation desktop -->
        <div class="hidden md:flex items-center space-x-1">
          <!-- Lien vers les tâches -->
          <router-link 
            to="/tasks" 
            :class="[
              'flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105',
              $route.path === '/tasks' 
                ? 'bg-primary-100 text-primary-700 shadow-sm' 
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>Tâches</span>
          </router-link>
          
          <!-- Lien vers les notifications avec badge -->
          <router-link 
            to="/notifications" 
            :class="[
              'flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105 relative',
              $route.path === '/notifications' 
                ? 'bg-primary-100 text-primary-700 shadow-sm' 
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            ]"
          >
            <div class="relative">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
              </svg>
              <!-- Badge de notifications non lues avec animation -->
              <span 
                v-if="notificationsStore.hasUnread" 
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold animate-bounce"
                :title="`${notificationsStore.unreadCount} notification(s) non lue(s)`"
              >
                {{ notificationsStore.unreadCount > 99 ? '99+' : notificationsStore.unreadCount }}
              </span>
            </div>
            <span>Notifications</span>
          </router-link>
        </div>
        
        <!-- Section utilisateur desktop -->
        <div class="hidden md:flex items-center space-x-4">
          <!-- Informations utilisateur -->
          <div v-if="authStore.currentUser" class="flex items-center space-x-3">
            <!-- Avatar utilisateur -->
            <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
              {{ getUserInitials(authStore.currentUser.full_name || authStore.currentUser.name) }}
            </div>
            <div class="hidden lg:block">
              <p class="text-sm font-medium text-gray-900">
                {{ authStore.currentUser.full_name || authStore.currentUser.name }}
              </p>
              <p class="text-xs text-gray-500">{{ authStore.currentUser.email }}</p>
            </div>
          </div>
          
          <!-- Bouton de déconnexion -->
          <button 
            @click="handleLogout" 
            class="btn btn-danger px-4 py-2 text-sm font-medium"
            title="Se déconnecter"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden lg:inline">Déconnexion</span>
          </button>
        </div>

        <!-- Bouton menu mobile -->
        <div class="md:hidden flex items-center space-x-2">
          <!-- Badge notifications mobile -->
          <router-link 
            v-if="notificationsStore.hasUnread" 
            to="/notifications" 
            class="relative p-2 text-gray-600 hover:text-primary-600 transition-colors duration-200"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">
              {{ notificationsStore.unreadCount > 9 ? '9+' : notificationsStore.unreadCount }}
            </span>
          </router-link>
          
          <!-- Bouton hamburger animé -->
          <button 
            @click="toggleMenu" 
            :class="[
              'p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 transform hover:scale-110',
              { 'bg-gray-100': isMenuOpen }
            ]"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path 
                v-if="!isMenuOpen"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M4 6h16M4 12h16M4 18h16" 
              />
              <path 
                v-else
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M6 18L18 6M6 6l12 12" 
              />
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Menu mobile avec animation -->
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 transform -translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform -translate-y-2"
      >
        <div v-if="isMenuOpen" class="md:hidden border-t border-gray-200 bg-gray-50">
          <div class="px-2 pt-2 pb-3 space-y-1">
            <!-- Menu tâches mobile -->
            <router-link 
              to="/tasks" 
              @click="closeMenu"
              :class="[
                'flex items-center space-x-3 px-4 py-3 rounded-lg text-base font-medium transition-all duration-200',
                $route.path === '/tasks' 
                  ? 'bg-primary-100 text-primary-700 shadow-sm' 
                  : 'text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm'
              ]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              <span>Mes Tâches</span>
            </router-link>
            
            <!-- Menu notifications mobile -->
            <router-link 
              to="/notifications" 
              @click="closeMenu"
              :class="[
                'flex items-center justify-between px-4 py-3 rounded-lg text-base font-medium transition-all duration-200',
                $route.path === '/notifications' 
                  ? 'bg-primary-100 text-primary-700 shadow-sm' 
                  : 'text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm'
              ]"
            >
              <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                </svg>
                <span>Notifications</span>
              </div>
              <span v-if="notificationsStore.hasUnread" class="badge badge-danger">
                {{ notificationsStore.unreadCount }}
              </span>
            </router-link>
          </div>
          
          <!-- Section utilisateur mobile -->
          <div class="border-t border-gray-200 bg-white">
            <div class="px-4 py-4">
              <div v-if="authStore.currentUser" class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-semibold">
                  {{ getUserInitials(authStore.currentUser.full_name || authStore.currentUser.name) }}
                </div>
                <div>
                  <p class="text-base font-medium text-gray-900">
                    {{ authStore.currentUser.full_name || authStore.currentUser.name }}
                  </p>
                  <p class="text-sm text-gray-500">{{ authStore.currentUser.email }}</p>
                </div>
              </div>
              
              <!-- Bouton déconnexion mobile -->
              <button 
                @click="handleLogout" 
                class="btn btn-danger w-full justify-center py-3"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Se déconnecter
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useNotificationsStore } from '../stores/notifications'

const router = useRouter()
const authStore = useAuthStore()
const notificationsStore = useNotificationsStore()

// État du menu mobile
const isMenuOpen = ref(false)

// Actions du composant
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}

// Utilitaire pour obtenir les initiales de l'utilisateur
const getUserInitials = (name) => {
  if (!name) return 'U'
  return name
    .split(' ')
    .map(word => word.charAt(0).toUpperCase())
    .slice(0, 2)
    .join('')
}

// Fermer le menu mobile lors du changement de route
router.afterEach(() => {
  isMenuOpen.value = false
})
</script>