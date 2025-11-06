<template>
  <div class="home-container">
    <header class="home-header">
      <div class="header-content">
        <h1>Bienvenue sur ToDo App</h1>
        <div class="user-info">
          <span v-if="authStore.currentUser">
            Bonjour, {{ authStore.currentUser.fullName || authStore.currentUser.name }}
          </span>
          <button @click="handleLogout" class="logout-button">
            Déconnexion
          </button>
        </div>
      </div>
    </header>

    <main class="home-main">
      <div class="welcome-card">
        <h2>🎉 Authentification réussie !</h2>
        <p>Vous êtes maintenant connecté à votre compte.</p>
        
        <div v-if="authStore.currentUser" class="user-details">
          <h3>Vos informations :</h3>
          <div class="info-grid">
            <div class="info-item">
              <strong>Nom :</strong> {{ authStore.currentUser.fullName || authStore.currentUser.name }}
            </div>
            <div class="info-item">
              <strong>Email :</strong> {{ authStore.currentUser.email }}
            </div>
            <div v-if="authStore.currentUser.phone" class="info-item">
              <strong>Téléphone :</strong> {{ authStore.currentUser.phone }}
            </div>
            <div v-if="authStore.currentUser.address" class="info-item">
              <strong>Adresse :</strong> {{ authStore.currentUser.address }}
            </div>
          </div>
        </div>

        <!-- Notification Status -->
        <div class="notification-status" v-if="notificationsStore">
          <div class="status-indicator" :class="{ 'connected': notificationsStore.isConnected }">
            <span class="status-dot"></span>
            Notifications en temps réel : {{ notificationsStore.isConnected ? 'Connectées' : 'Déconnectées' }}
          </div>
          <div v-if="notificationsStore.hasUnread" class="unread-notifications">
            {{ notificationsStore.unreadCount }} notification(s) non lue(s)
          </div>
        </div>

        <div class="actions">
          <p>Gérez vos tâches et restez informé en temps réel de toutes vos activités.</p>
          <div class="action-buttons">
            <router-link to="/tasks" class="btn btn-primary">
              📝 Voir mes tâches
            </router-link>
            <router-link to="/notifications" class="btn btn-secondary">
              <span class="notification-badge" v-if="notificationsStore?.hasUnread">
                {{ notificationsStore.unreadCount }}
              </span>
              🔔 Notifications
            </router-link>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useNotificationsStore } from '../stores/notifications'

const router = useRouter()
const authStore = useAuthStore()
const notificationsStore = useNotificationsStore()

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}

onMounted(async () => {
  // Récupérer les informations utilisateur si pas encore chargées
  if (!authStore.currentUser && authStore.token) {
    await authStore.fetchUser()
  }
})
</script>

<style scoped>
.home-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.home-header {
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 20px 0;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content h1 {
  color: #333;
  font-size: 24px;
  font-weight: 600;
  margin: 0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.user-info span {
  color: #666;
  font-weight: 500;
}

.logout-button {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.logout-button:hover {
  background: #c0392b;
}

.home-main {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 20px;
}

.welcome-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.welcome-card h2 {
  color: #2c3e50;
  font-size: 28px;
  margin-bottom: 15px;
}

.welcome-card p {
  color: #666;
  font-size: 16px;
  margin-bottom: 30px;
}

.user-details {
  text-align: left;
  margin: 30px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.user-details h3 {
  color: #2c3e50;
  margin-bottom: 15px;
  text-align: center;
}

.info-grid {
  display: grid;
  gap: 15px;
}

.info-item {
  padding: 10px;
  background: white;
  border-radius: 6px;
  border-left: 4px solid #667eea;
}

.info-item strong {
  color: #2c3e50;
  margin-right: 10px;
}

.actions {
  margin-top: 30px;
  padding: 20px;
  background: #e3f2fd;
  border-radius: 8px;
  border-left: 4px solid #2196f3;
}

.actions p {
  margin: 0 0 20px 0;
  color: #1976d2;
  font-weight: 500;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn-primary:hover {
  background: #2980b9;
  transform: translateY(-2px);
}

.notification-status {
  margin: 1.5rem 0;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  text-align: center;
}

.status-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #dc3545;
}

.status-indicator.connected {
  color: #28a745;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #dc3545;
}

.status-indicator.connected .status-dot {
  background: #28a745;
}

.unread-notifications {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #856404;
  background: #fff3cd;
  padding: 0.5rem;
  border-radius: 4px;
  display: inline-block;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  position: relative;
}

.btn-secondary:hover {
  background: #545b62;
  transform: translateY(-2px);
}

.notification-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .welcome-card {
    padding: 30px 20px;
  }
  
  .action-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn {
    width: 100%;
    max-width: 300px;
  }
}
</style>