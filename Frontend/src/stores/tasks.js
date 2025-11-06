import { defineStore } from 'pinia'
import api from '../utils/axios'
import { useNotificationsStore } from './notifications'

export const useTasksStore = defineStore('tasks', {
  state: () => ({
    tasks: [],
    loading: false,
    error: null,
    currentTask: null
  }),

  getters: {
    completedTasks: (state) => state.tasks.filter(task => task.completed),
    pendingTasks: (state) => state.tasks.filter(task => !task.completed),
    tasksCount: (state) => state.tasks.length,
    completedTasksCount: (state) => state.completedTasks.length
  },

  actions: {
    async fetchTasks() {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.get('/tasks')
        this.tasks = response.data.data || response.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des tâches'
        console.error('Erreur fetchTasks:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async createTask(taskData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.post('/tasks', taskData)
        const newTask = response.data.data || response.data
        this.tasks.unshift(newTask)
        
        // Déclencher une notification locale pour la nouvelle tâche
        const notificationsStore = useNotificationsStore()
        notificationsStore.addTaskNotification(newTask)
        
        return newTask
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la création de la tâche'
        console.error('Erreur createTask:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateTask(taskId, taskData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.put(`/tasks/${taskId}`, taskData)
        const updatedTask = response.data.data || response.data
        
        const index = this.tasks.findIndex(task => task.id === taskId)
        if (index !== -1) {
          const oldTask = { ...this.tasks[index] }
          this.tasks[index] = updatedTask
          
          // Déclencher une notification locale pour la tâche mise à jour
          const notificationsStore = useNotificationsStore()
          
          // Notification spéciale si la tâche vient d'être terminée
          if (!oldTask.completed && updatedTask.completed) {
            notificationsStore.addNotification({
              type: 'task-completed',
              title: '🎉 Tâche terminée !',
              message: `Félicitations ! La tâche "${updatedTask.title}" a été marquée comme terminée.`,
              data: { task: updatedTask, previousTask: oldTask }
            })
          } else {
            notificationsStore.addTaskUpdateNotification(updatedTask)
          }
        }
        
        return updatedTask
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la mise à jour de la tâche'
        console.error('Erreur updateTask:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteTask(taskId) {
      this.loading = true
      this.error = null
      
      try {
        const taskToDelete = this.tasks.find(task => task.id === taskId)
        await api.delete(`/tasks/${taskId}`)
        this.tasks = this.tasks.filter(task => task.id !== taskId)
        
        // Déclencher une notification locale pour la tâche supprimée
        if (taskToDelete) {
          const notificationsStore = useNotificationsStore()
          notificationsStore.addNotification({
            type: 'task-deleted',
            title: '🗑️ Tâche supprimée',
            message: `La tâche "${taskToDelete.title}" a été supprimée avec succès.`,
            data: { task: taskToDelete }
          })
        }
        
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression de la tâche'
        console.error('Erreur deleteTask:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async toggleTaskStatus(taskId) {
      const task = this.tasks.find(t => t.id === taskId)
      if (!task) return
      
      try {
        const updatedTask = await this.updateTask(taskId, {
          ...task,
          completed: !task.completed
        })
        return updatedTask
      } catch (error) {
        console.error('Erreur toggleTaskStatus:', error)
        throw error
      }
    },

    setCurrentTask(task) {
      this.currentTask = task
    },

    clearCurrentTask() {
      this.currentTask = null
    },

    clearError() {
      this.error = null
    }
  }
})