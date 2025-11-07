<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Connexion</h2>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="Votre email"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                errors.email ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
              @blur="validateField('email')"
            />
            <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Mot de passe *
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="Votre mot de passe"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                errors.password ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
              @blur="validateField('password')"
            />
            <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
          </div>

          <div class="flex items-center">
            <input 
              id="rememberMe"
              type="checkbox" 
              v-model="form.rememberMe"
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
            />
            <label for="rememberMe" class="ml-2 block text-sm text-gray-700">
              Se souvenir de moi
            </label>
          </div>

          <div v-if="authStore.error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600 text-center">{{ authStore.error }}</p>
          </div>

          <button 
            type="submit" 
            :disabled="authStore.loading || !isFormValid"
            class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            {{ authStore.loading ? 'Connexion...' : 'Se connecter' }}
          </button>
        </form>

        <div class="mt-8 space-y-4 text-center">
          <p class="text-sm text-gray-600">
            Pas encore de compte ? 
            <router-link to="/register" class="text-primary-600 hover:text-primary-700 font-medium">
              S'inscrire
            </router-link>
          </p>
          <p class="text-sm">
            <a href="#" @click.prevent="handleForgotPassword" class="text-primary-600 hover:text-primary-700">
              Mot de passe oublié ?
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
  rememberMe: false
})

const errors = reactive({
  email: '',
  password: ''
})

// Validation rules
const validateField = (field) => {
  errors[field] = ''

  switch (field) {
    case 'email':
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!form.email.trim()) {
        errors.email = 'L\'email est requis'
      } else if (!emailRegex.test(form.email)) {
        errors.email = 'Format d\'email invalide'
      }
      break

    case 'password':
      if (!form.password) {
        errors.password = 'Le mot de passe est requis'
      } else if (form.password.length < 6) {
        errors.password = 'Le mot de passe doit contenir au moins 6 caractères'
      }
      break
  }
}

const validateForm = () => {
  validateField('email')
  validateField('password')
}

const isFormValid = computed(() => {
  return form.email.trim() &&
         form.password &&
         !Object.values(errors).some(error => error !== '')
})

const handleSubmit = async () => {
  validateForm()
  
  if (!isFormValid.value) {
    return
  }

  try {
    await authStore.login({
      email: form.email,
      password: form.password,
      rememberMe: form.rememberMe
    })
    
    router.push('/')
  } catch (error) {
    console.error('Erreur lors de la connexion:', error)
  }
}

const handleForgotPassword = () => {
  // Ici vous pouvez implémenter la logique de récupération de mot de passe
  alert('Fonctionnalité de récupération de mot de passe à implémenter')
}

onMounted(() => {
  authStore.clearError()
})
</script>

<style scoped>
</style>