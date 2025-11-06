import { defineStore } from 'pinia'
import echo from '../utils/echo'

export const useNotificationsStore = defineStore('notifications', {
  state: () => ({
    notifications: [],
    isConnected: false,
    echoChannel: null,
  }),

  getters: {
    unreadCount: (state) => state.notifications.filter(n => !n.read).length,
    hasUnread: (state) => state.notifications.some(n => !n.read),
  },

  actions: {
    initializeEcho(userId) {
      try {
        // Vérifier si Echo est en mode simulation
        if (echo.isSimulated) {
          console.log('🔧 Mode simulation : Connexion Echo simulée')
          this.isConnected = true
          return
        }

        // Mode production avec vraie connexion Pusher
        this.echoChannel = echo.private(`notifications.${userId}`)
        
        this.echoChannel.notification((notification) => {
          console.log('📥 Nouvelle notification reçue:', notification)
          this.addNotification(notification)
        })

        this.isConnected = true
        console.log(`✅ Echo connecté pour l'utilisateur ${userId}`)
      } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation d\'Echo:', error)
        this.isConnected = false
        
        // Basculer en mode simulation en cas d'erreur
        console.log('🔧 Basculement en mode simulation')
        this.isConnected = true
      }
    },

    disconnect() {
      if (echo.isSimulated) {
        console.log('🔧 Mode simulation : Déconnexion Echo simulée')
        this.isConnected = false
        return
      }

      try {
        if (this.echoChannel) {
          echo.disconnect()
          this.echoChannel = null
        }
        this.isConnected = false
        console.log('🔌 Echo déconnecté')
      } catch (error) {
        console.error('❌ Erreur lors de la déconnexion d\'Echo:', error)
        this.isConnected = false
      }
    },

    simulateNotification(type = 'info') {
      const notifications = {
        info: {
          title: 'Information',
          message: 'Ceci est une notification d\'information de test',
          type: 'info'
        },
        success: {
          title: 'Succès',
          message: 'Action réalisée avec succès !',
          type: 'success'
        },
        warning: {
          title: 'Attention',
          message: 'Veuillez vérifier cette information',
          type: 'warning'
        },
        error: {
          title: 'Erreur',
          message: 'Une erreur est survenue',
          type: 'error'
        },
        'task-created': {
          title: 'Nouvelle tâche',
          message: 'Une nouvelle tâche a été créée',
          type: 'task-created',
          data: {
            task: {
              title: 'Tâche de test',
              priority: 'medium',
              completed: false
            }
          }
        },
        'task-updated': {
          title: 'Tâche mise à jour',
          message: 'Une tâche a été modifiée',
          type: 'task-updated',
          data: {
            task: {
              title: 'Tâche modifiée',
              priority: 'high',
              completed: false
            }
          }
        },
        'task-completed': {
          title: 'Tâche terminée',
          message: 'Félicitations ! Une tâche a été terminée',
          type: 'task-completed',
          data: {
            task: {
              title: 'Tâche terminée',
              priority: 'low',
              completed: true
            }
          }
        }
      }

      const notification = notifications[type] || notifications.info
      this.addNotification(notification)
    },

    addNotification(notificationData) {
      const notification = {
        id: Date.now() + Math.random(),
        title: notificationData.title,
        message: notificationData.message,
        type: notificationData.type || 'info',
        data: notificationData.data || null,
        read: false,
        created_at: new Date().toISOString(),
      }

      // Ajouter au début du tableau
      this.notifications.unshift(notification)
      
      // Limiter le nombre de notifications (garder les 100 dernières)
      if (this.notifications.length > 100) {
        this.notifications = this.notifications.slice(0, 100)
      }

      console.log('📝 Notification ajoutée:', notification.title)
    },

    markAsRead(notificationId) {
      const notification = this.notifications.find(n => n.id === notificationId)
      if (notification) {
        notification.read = true
        console.log('✅ Notification marquée comme lue:', notification.title)
      }
    },

    markAllAsRead() {
      this.notifications.forEach(notification => {
        notification.read = true
      })
      console.log('✅ Toutes les notifications marquées comme lues')
    },

    removeNotification(notificationId) {
      const index = this.notifications.findIndex(n => n.id === notificationId)
      if (index > -1) {
        const notification = this.notifications[index]
        this.notifications.splice(index, 1)
        console.log('🗑️ Notification supprimée:', notification.title)
      }
    },

    clearRead() {
      const unreadCount = this.notifications.filter(n => !n.read).length
      this.notifications = this.notifications.filter(n => !n.read)
      console.log(`🧹 ${this.notifications.length - unreadCount} notifications lues supprimées`)
    },

    clearAll() {
      const count = this.notifications.length
      this.notifications = []
      console.log(`🧹 ${count} notifications supprimées`)
    }
  }
})