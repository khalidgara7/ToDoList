import { defineStore } from 'pinia'
import axios from '../utils/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user
  },

  actions: {
    // Vérifier l'état d'authentification au démarrage de l'application
    async checkAuthState() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        try {
          await this.fetchUser()
        } catch (error) {
          console.error('Token invalide, déconnexion automatique:', error)
          this.logout()
        }
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/register', userData)
        
        if (response.data.token) {
          this.token = response.data.token
          this.user = response.data.user
          localStorage.setItem('token', this.token)
          
          // Initialiser les notifications Echo après la connexion
          this.initializeNotifications()
        }
        
        return response.data
      } catch (error) {
        console.error('Erreur lors de l\'inscription:', error)
        
        // Gestion des différents types d'erreurs
        if (!error.response) {
          // Erreur de réseau (serveur non accessible)
          this.error = 'Impossible de se connecter au serveur. Assurez-vous que le backend Laravel est démarré sur http://localhost:8000'
        } else if (error.response.status === 422) {
          // Erreurs de validation du serveur
          const validationErrors = error.response.data.errors
          if (validationErrors) {
            // Prendre le premier message d'erreur
            const firstError = Object.values(validationErrors)[0]
            this.error = Array.isArray(firstError) ? firstError[0] : firstError
          } else {
            this.error = error.response.data.message || 'Erreurs de validation'
          }
        } else if (error.response.status === 409) {
          // Conflit (email déjà utilisé)
          this.error = 'Cet email est déjà utilisé. Veuillez choisir un autre email.'
        } else {
          // Autres erreurs serveur
          this.error = error.response?.data?.message || error.message || 'Erreur lors de l\'inscription'
        }
        
        throw error
      } finally {
        this.loading = false
      }
    },

    async login(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/login', credentials)
        
        // Handle the response structure from your backend
        if (response.data.status === 'success' && response.data.authorisation) {
          this.token = response.data.authorisation.token
          this.user = response.data.user
          localStorage.setItem('token', this.token)
          
          // Initialiser les notifications Echo après la connexion
          this.initializeNotifications()
        } else if (response.data.token) {
          // Fallback for different response structure
          this.token = response.data.token
          this.user = response.data.user
          localStorage.setItem('token', this.token)
          
          // Initialiser les notifications Echo après la connexion
          this.initializeNotifications()
        }
        
        return response.data
      } catch (error) {
        console.error('Erreur lors de la connexion:', error)
        
        // Gestion des différents types d'erreurs
        if (!error.response) {
          // Erreur de réseau (serveur non accessible)
          this.error = 'Impossible de se connecter au serveur. Assurez-vous que le backend Laravel est démarré sur http://localhost:8000'
        } else if (error.response.status === 401) {
          // Identifiants incorrects
          this.error = 'Email ou mot de passe incorrect'
        } else if (error.response.status === 422) {
          // Erreurs de validation
          const validationErrors = error.response.data.errors
          if (validationErrors) {
            const firstError = Object.values(validationErrors)[0]
            this.error = Array.isArray(firstError) ? firstError[0] : firstError
          } else {
            this.error = error.response.data.message || 'Erreurs de validation'
          }
        } else {
          // Autres erreurs
          this.error = error.response?.data?.message || error.message || 'Erreur lors de la connexion'
        }
        
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        // Déconnecter les notifications Echo avant de se déconnecter
        this.disconnectNotifications()
        
        await axios.post('/logout')
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      try {
        const response = await axios.get('/user')
        this.user = response.data
        
        // Initialiser les notifications si l'utilisateur est récupéré
        if (this.user) {
          this.initializeNotifications()
        }
      } catch (error) {
        console.error('Erreur lors de la récupération de l\'utilisateur:', error)
        this.logout()
      }
    },

    // Initialiser les notifications Echo
    initializeNotifications() {
      if (this.user) {
        try {
          // Import dynamique pour éviter les erreurs de circularité
          import('./notifications').then(({ useNotificationsStore }) => {
            const notificationsStore = useNotificationsStore()
            notificationsStore.initializeEcho(this.user.id)
          })
        } catch (error) {
          console.error('Erreur lors de l\'initialisation des notifications:', error)
        }
      }
    },

    // Déconnecter les notifications Echo
    disconnectNotifications() {
      try {
        // Import dynamique pour éviter les erreurs de circularité
        import('./notifications').then(({ useNotificationsStore }) => {
          const notificationsStore = useNotificationsStore()
          notificationsStore.disconnect()
        })
      } catch (error) {
        console.error('Erreur lors de la déconnexion des notifications:', error)
      }
    },

    clearError() {
      this.error = null
    }
  }
})