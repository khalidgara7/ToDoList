<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Inscription</h2>
      
      <form @submit.prevent="handleSubmit" class="auth-form">
        <div class="form-group">
          <label for="full_name">Nom complet *</label>
          <input
            id="full_name"
            v-model="form.full_name"
            type="text"
            :class="{ error: errors.full_name }"
            @blur="validateField('full_name')"
          />
          <span v-if="errors.full_name" class="error-message">{{ errors.full_name }}</span>
        </div>

        <div class="form-group">
          <label for="email">Email *</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            :class="{ error: errors.email }"
            @blur="validateField('email')"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <div class="form-group">
          <label for="phone_number">Téléphone *</label>
          <input
            id="phone_number"
            v-model="form.phone_number"
            type="tel"
            :class="{ error: errors.phone_number }"
            @blur="validateField('phone_number')"
          />
          <span v-if="errors.phone_number" class="error-message">{{ errors.phone_number }}</span>
        </div>

        <div class="form-group">
          <label for="address">Adresse *</label>
          <textarea
            id="address"
            v-model="form.address"
            :class="{ error: errors.address }"
            @blur="validateField('address')"
            rows="3"
          ></textarea>
          <span v-if="errors.address" class="error-message">{{ errors.address }}</span>
        </div>

        <div class="form-group">
          <label for="image">Photo de profil</label>
          <input
            id="image"
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            :class="{ error: errors.image }"
          />
          <span v-if="errors.image" class="error-message">{{ errors.image }}</span>
          <div v-if="imagePreview" class="image-preview">
            <img :src="imagePreview" alt="Preview" />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe *</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            :class="{ error: errors.password }"
            @blur="validateField('password')"
          />
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirmer le mot de passe *</label>
          <input
            id="confirmPassword"
            v-model="form.confirmPassword"
            type="password"
            :class="{ error: errors.confirmPassword }"
            @blur="validateField('confirmPassword')"
          />
          <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
        </div>

        <div v-if="authStore.error" class="error-message global-error">
          {{ authStore.error }}
        </div>

        <button 
          type="submit" 
          class="auth-button"
          :disabled="authStore.loading || !isFormValid"
        >
          {{ authStore.loading ? 'Inscription...' : 'S\'inscrire' }}
        </button>
      </form>

      <p class="auth-link">
        Déjà un compte ? 
        <router-link to="/login">Se connecter</router-link>
      </p>
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

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.auth-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
}

.auth-card h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
  font-size: 28px;
  font-weight: 600;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 500;
  color: #555;
}

.form-group input,
.form-group textarea {
  padding: 12px;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input.error,
.form-group textarea.error {
  border-color: #e74c3c;
}

.error-message {
  color: #e74c3c;
  font-size: 14px;
  margin-top: 5px;
}

.global-error {
  text-align: center;
  padding: 10px;
  background: #fee;
  border-radius: 4px;
}

.image-preview {
  margin-top: 10px;
}

.image-preview img {
  max-width: 150px;
  max-height: 150px;
  border-radius: 8px;
  object-fit: cover;
}

.auth-button {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 15px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.auth-button:hover:not(:disabled) {
  transform: translateY(-2px);
}

.auth-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.auth-link {
  text-align: center;
  margin-top: 20px;
  color: #666;
}

.auth-link a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.auth-link a:hover {
  text-decoration: underline;
}
</style>