<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Connexion</h2>
      
      <form @submit.prevent="handleSubmit" class="auth-form">
        <div class="form-group">
          <label for="email">Email *</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="Votre email"
            :class="{ error: errors.email }"
            @blur="validateField('email')"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe *</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Votre mot de passe"
            :class="{ error: errors.password }"
            @blur="validateField('password')"
          />
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" v-model="form.rememberMe" />
            <span class="checkmark"></span>
            Se souvenir de moi
          </label>
        </div>

        <div v-if="authStore.error" class="error-message global-error">
          {{ authStore.error }}
        </div>

        <button 
          type="submit" 
          class="auth-button"
          :disabled="authStore.loading || !isFormValid"
        >
          {{ authStore.loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>

      <div class="auth-links">
        <p class="auth-link">
          Pas encore de compte ? 
          <router-link to="/register">S'inscrire</router-link>
        </p>
        <p class="auth-link">
          <a href="#" @click.prevent="handleForgotPassword">Mot de passe oublié ?</a>
        </p>
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
    
    // Récupérer les informations de l'utilisateur après connexion
    await authStore.fetchUser()
    
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
  max-width: 400px;
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

.form-group input[type="email"],
.form-group input[type="password"] {
  padding: 12px;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input.error {
  border-color: #e74c3c;
}

.checkbox-group {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 14px;
  color: #666;
}

.checkbox-group input[type="checkbox"] {
  margin-right: 8px;
  transform: scale(1.1);
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

.auth-links {
  margin-top: 30px;
}

.auth-link {
  text-align: center;
  margin: 10px 0;
  color: #666;
  font-size: 14px;
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