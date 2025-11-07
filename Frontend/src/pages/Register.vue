<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4 py-8">
    <div class="w-full max-w-lg">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Inscription</h2>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
              Nom complet *
            </label>
            <input
              id="full_name"
              v-model="form.full_name"
              type="text"
              placeholder="Votre nom complet"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                errors.full_name ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
              @blur="validateField('full_name')"
            />
            <p v-if="errors.full_name" class="mt-2 text-sm text-red-600">{{ errors.full_name }}</p>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="votre@email.com"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                errors.email ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
              @blur="validateField('email')"
            />
            <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <div>
            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
              Téléphone *
            </label>
            <input
              id="phone_number"
              v-model="form.phone_number"
              type="tel"
              placeholder="+33 6 12 34 56 78"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                errors.phone_number ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
              @blur="validateField('phone_number')"
            />
            <p v-if="errors.phone_number" class="mt-2 text-sm text-red-600">{{ errors.phone_number }}</p>
          </div>

          <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
              Adresse *
            </label>
            <textarea
              id="address"
              v-model="form.address"
              placeholder="Votre adresse complète"
              rows="3"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none',
                errors.address ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
              @blur="validateField('address')"
            ></textarea>
            <p v-if="errors.address" class="mt-2 text-sm text-red-600">{{ errors.address }}</p>
          </div>

          <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
              Photo de profil
            </label>
            <input
              id="image"
              type="file"
              accept="image/*"
              @change="handleImageUpload"
              :class="[
                'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100',
                errors.image ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
              ]"
            />
            <p v-if="errors.image" class="mt-2 text-sm text-red-600">{{ errors.image }}</p>
            <div v-if="imagePreview" class="mt-4 flex justify-center">
              <img :src="imagePreview" alt="Preview" class="w-24 h-24 rounded-full object-cover border-4 border-primary-100" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Mot de passe *
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                placeholder="Minimum 6 caractères"
                :class="[
                  'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                  errors.password ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
                ]"
                @blur="validateField('password')"
              />
              <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
            </div>

            <div>
              <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                Confirmer *
              </label>
              <input
                id="confirmPassword"
                v-model="form.confirmPassword"
                type="password"
                placeholder="Répéter le mot de passe"
                :class="[
                  'w-full px-4 py-3 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
                  errors.confirmPassword ? 'border-red-500 bg-red-50' : 'border-gray-300 focus:border-primary-500'
                ]"
                @blur="validateField('confirmPassword')"
              />
              <p v-if="errors.confirmPassword" class="mt-2 text-sm text-red-600">{{ errors.confirmPassword }}</p>
            </div>
          </div>

          <div v-if="authStore.error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600 text-center">{{ authStore.error }}</p>
          </div>

          <button 
            type="submit" 
            :disabled="authStore.loading || !isFormValid"
            class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            {{ authStore.loading ? 'Inscription...' : 'S\'inscrire' }}
          </button>
        </form>

        <div class="mt-8 text-center">
          <p class="text-sm text-gray-600">
            Déjà un compte ? 
            <router-link to="/login" class="text-primary-600 hover:text-primary-700 font-medium">
              Se connecter
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  full_name: '',
  email: '',
  phone_number: '',
  address: '',
  image: null,
  password: '',
  confirmPassword: ''
})

const errors = reactive({
  full_name: '',
  email: '',
  phone_number: '',
  address: '',
  image: '',
  password: '',
  confirmPassword: ''
})

const imagePreview = ref('')

// Validation rules
const validateField = (field) => {
  errors[field] = ''

  switch (field) {
    case 'full_name':
      if (!form.full_name.trim()) {
        errors.full_name = 'Le nom complet est requis'
      } else if (form.full_name.trim().length < 2) {
        errors.full_name = 'Le nom doit contenir au moins 2 caractères'
      }
      break

    case 'email':
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!form.email.trim()) {
        errors.email = 'L\'email est requis'
      } else if (!emailRegex.test(form.email)) {
        errors.email = 'Format d\'email invalide'
      }
      break

    case 'phone_number':
      const phoneRegex = /^[+]?[\d\s\-()]{8,}$/
      if (!form.phone_number.trim()) {
        errors.phone_number = 'Le téléphone est requis'
      } else if (!phoneRegex.test(form.phone_number)) {
        errors.phone_number = 'Format de téléphone invalide'
      }
      break

    case 'address':
      if (!form.address.trim()) {
        errors.address = 'L\'adresse est requise'
      } else if (form.address.trim().length < 10) {
        errors.address = 'L\'adresse doit contenir au moins 10 caractères'
      }
      break

    case 'password':
      if (!form.password) {
        errors.password = 'Le mot de passe est requis'
      } else if (form.password.length < 6) {
        errors.password = 'Le mot de passe doit contenir au moins 6 caractères'
      }
      break

    case 'confirmPassword':
      if (!form.confirmPassword) {
        errors.confirmPassword = 'La confirmation est requise'
      } else if (form.password !== form.confirmPassword) {
        errors.confirmPassword = 'Les mots de passe ne correspondent pas'
      }
      break
  }
}

const validateForm = () => {
  Object.keys(form).forEach(field => {
    if (field !== 'image') {
      validateField(field)
    }
  })
}

const isFormValid = computed(() => {
  return form.full_name.trim() &&
         form.email.trim() &&
         form.phone_number.trim() &&
         form.address.trim() &&
         form.password &&
         form.confirmPassword &&
         !Object.values(errors).some(error => error !== '')
})

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 5 * 1024 * 1024) { // 5MB max
      errors.image = 'L\'image ne doit pas dépasser 5MB'
      return
    }
    
    if (!file.type.startsWith('image/')) {
      errors.image = 'Seuls les fichiers image sont acceptés'
      return
    }

    errors.image = ''
    form.image = file

    // Créer un aperçu de l'image
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleSubmit = async () => {
  validateForm()
  
  if (!isFormValid.value) {
    return
  }

  try {
    const formData = new FormData()
    formData.append('full_name', form.full_name)
    formData.append('email', form.email)
    formData.append('phone_number', form.phone_number)
    formData.append('address', form.address)
    formData.append('password', form.password)
    
    if (form.image) {
      formData.append('image', form.image)
    }

    console.log('📤 Envoi des données d\'inscription au serveur...')
    await authStore.register(formData)
    
    console.log('✅ Inscription réussie, redirection...')
    router.push('/')
  } catch (error) {
    console.error('❌ Erreur lors de l\'inscription:', error)
    
    // L'erreur est déjà gérée par le store et affichée dans authStore.error
    // Pas besoin de traitement supplémentaire ici
  }
}

// Fonction pour tester la connexion au serveur
const testServerConnection = async () => {
  try {
    console.log('🔍 Test de connexion au serveur API...')
    const response = await fetch(`${import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'}/health`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      console.log('✅ Serveur API accessible')
      return true
    } else {
      console.log('⚠️ Serveur API répond mais avec une erreur:', response.status)
      return false
    }
  } catch (error) {
    console.log('❌ Serveur API non accessible:', error.message)
    return false
  }
}

onMounted(async () => {
  authStore.clearError()
  
  // Tester la connexion au serveur au chargement
  const serverAvailable = await testServerConnection()
  if (!serverAvailable) {
    console.warn('⚠️ Le serveur API Laravel ne semble pas être accessible sur http://localhost:8000')
    console.warn('💡 Assurez-vous que votre backend Laravel est démarré avec: php artisan serve')
  }
})
</script>