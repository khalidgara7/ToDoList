import axios from 'axios'

// Configuration de base d'Axios
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api',
  timeout: 15000, // Augmenté à 15 secondes
  headers: {
    'Accept': 'application/json'
    // Ne pas forcer Content-Type ici pour permettre FormData
  }
})

// Intercepteur de requête pour ajouter le token JWT et gérer Content-Type
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    // Définir Content-Type uniquement pour les données JSON
    if (!(config.data instanceof FormData)) {
      config.headers['Content-Type'] = 'application/json'
    }
    // Pour FormData, laisser le navigateur définir automatiquement le Content-Type avec boundary
    
    return config
  },
  (error) => {
    console.error('Erreur de configuration de requête:', error)
    return Promise.reject(error)
  }
)

// Intercepteur de réponse pour gérer les erreurs et l'expiration du token
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Gestion des erreurs réseau
    if (!error.response) {
      // Erreur de connexion réseau
      console.error('Erreur réseau - Serveur non accessible:', error.message)
      error.message = 'Impossible de se connecter au serveur. Vérifiez que l\'API backend est démarrée.'
    } else {
      // Erreur HTTP du serveur
      const status = error.response.status
      
      if (status === 401) {
        // Token expiré ou invalide
        console.log('Token expiré, redirection vers login')
        localStorage.removeItem('token')
        
        // Éviter la redirection en boucle si on est déjà sur login
        if (!window.location.pathname.includes('/login')) {
          window.location.href = '/login'
        }
      } else if (status === 422) {
        // Erreurs de validation
        console.error('Erreurs de validation:', error.response.data)
      } else if (status === 500) {
        // Erreur serveur
        console.error('Erreur serveur interne:', error.response.data)
        error.message = 'Erreur du serveur. Veuillez réessayer plus tard.'
      }
    }
    
    return Promise.reject(error)
  }
)

export default api
