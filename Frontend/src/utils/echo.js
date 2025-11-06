import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Configuration Pusher
window.Pusher = Pusher

// Vérifier si les clés Pusher sont disponibles
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER

let echo = null

if (pusherKey && pusherKey !== 'your_pusher_app_key_here') {
  // Mode production avec vraies clés Pusher
  echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster || 'mt1',
    forceTLS: true,
    encrypted: true,
    auth: {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    },
    authorizer: (channel, options) => {
      return {
        authorize: (socketId, callback) => {
          const token = localStorage.getItem('token')
          if (!token) {
            callback(true, null)
            return
          }

          // En production, faire une vraie requête à votre backend pour l'autorisation
          callback(false, {
            auth: `${socketId}:signature`,
          })
        }
      }
    }
  })
} else {
  // Mode développement - Simuler Echo sans Pusher
  console.log('🔧 Mode développement : Pusher désactivé, utilisation de la simulation locale')
  
  echo = {
    // Simuler les méthodes d'Echo pour éviter les erreurs
    private(channel) {
      console.log(`📡 Simulation - Canal privé : ${channel}`)
      return {
        listen(event, callback) {
          console.log(`🎧 Simulation - Écoute de l'événement : ${event}`)
          return this
        },
        notification(callback) {
          console.log('🔔 Simulation - Écoute des notifications')
          return this
        }
      }
    },
    
    channel(channel) {
      console.log(`📡 Simulation - Canal public : ${channel}`)
      return {
        listen(event, callback) {
          console.log(`🎧 Simulation - Écoute de l'événement : ${event}`)
          return this
        }
      }
    },
    
    join(channel) {
      console.log(`👥 Simulation - Rejoindre le canal : ${channel}`)
      return {
        listen(event, callback) {
          console.log(`🎧 Simulation - Écoute de l'événement : ${event}`)
          return this
        },
        here(callback) {
          console.log('👋 Simulation - Utilisateurs présents')
          callback([])
          return this
        },
        joining(callback) {
          console.log('➡️ Simulation - Utilisateur qui rejoint')
          return this
        },
        leaving(callback) {
          console.log('⬅️ Simulation - Utilisateur qui quitte')
          return this
        }
      }
    },
    
    leave(channel) {
      console.log(`🚪 Simulation - Quitter le canal : ${channel}`)
    },
    
    disconnect() {
      console.log('🔌 Simulation - Déconnexion d\'Echo')
    },
    
    // Propriété pour indiquer si c'est une simulation
    isSimulated: true
  }
}

export default echo