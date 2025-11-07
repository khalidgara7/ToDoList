# 📋 Full Stack To-Do List Application

Une application de gestion de tâches moderne et interactive développée avec Laravel (API REST) et Vue 3, offrant une expérience utilisateur fluide avec des notifications en temps réel.

## 🎯 Objectifs du Projet

Cette application To-Do List a été conçue pour démontrer les compétences en développement Full Stack, en intégrant les dernières technologies web pour créer une solution complète de gestion de tâches avec :
- Une API REST robuste et sécurisée
- Une interface utilisateur moderne et responsive
- Des notifications en temps réel
- Un système d'authentification sécurisé

## 🛠️ Technologies Utilisées

### Backend
- **Laravel 10+** - Framework PHP pour l'API REST
- **JWT Auth** - Authentification sécurisée par tokens
- **Pusher** - Notifications et mises à jour en temps réel
- **MySQL** - Base de données relationnelle
- **PHP 8.1+** - Langage de programmation backend

### Frontend
- **Vue 3** - Framework JavaScript progressif
- **Pinia** - Gestionnaire d'état moderne pour Vue
- **Vue Router** - Routage côté client
- **Axios** - Client HTTP pour les requêtes API
- **TailwindCSS** - Framework CSS utilitaire
- **Shadcn UI** - Composants UI modernes et accessibles

## ✨ Fonctionnalités Principales

- 🔐 **Authentification JWT** - Inscription, connexion et gestion des sessions
- ✅ **CRUD des Tâches** - Créer, lire, modifier et supprimer des tâches
- 🔄 **Notifications Temps Réel** - Mises à jour instantanées via Pusher
- 📱 **Interface Responsive** - Optimisée pour tous les écrans
- 🎨 **UI Moderne** - Design élégant avec TailwindCSS et Shadcn UI
- 🚀 **Performance** - Chargement rapide et interactions fluides
- 🔒 **Sécurité** - Protection CSRF, validation des données, etc.

## 📁 Structure du Projet

```
ToDoList/
├── backend/                    # API Laravel
│   ├── app/
│   │   ├── Http/Controllers/   # Contrôleurs API
│   │   ├── Models/            # Modèles Eloquent
│   │   └── ...
│   ├── config/                # Configuration Laravel
│   ├── database/              # Migrations et seeders
│   └── routes/                # Routes API
│
├── frontend/                  # Application Vue 3
│   ├── src/
│   │   ├── components/        # Composants Vue
│   │   ├── views/            # Pages de l'application
│   │   ├── stores/           # Stores Pinia
│   │   ├── router/           # Configuration des routes
│   │   └── assets/           # Assets statiques
│   ├── public/               # Fichiers publics
│   └── package.json          # Dépendances npm
│
└── README.md                 # Documentation du projet
```

## 🚀 Installation et Configuration

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- Node.js 18+ et npm
- MySQL 8.0+
- Git

### 1. Clonage du Repository

```bash
git clone https://github.com/khalidgara7/ToDoList.git
cd ToDoList
```

### 2. Configuration du Backend (Laravel)

#### Installation des dépendances
```bash
cd backend
composer install
```

#### Configuration de l'environnement
```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Générer la clé JWT
php artisan jwt:secret
```

#### Configuration du fichier .env (Backend)
```env
APP_NAME="ToDoList API"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todolist_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

# Configuration Pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=your_cluster

# Configuration JWT
JWT_SECRET=your_jwt_secret
JWT_TTL=60
```

#### Migration de la base de données
```bash
# Créer la base de données
mysql -u root -p -e "CREATE DATABASE todolist_db"

# Exécuter les migrations
php artisan migrate

# (Optionnel) Seed de données de test
php artisan db:seed
```

#### Lancement du serveur backend
```bash
php artisan serve
# Le serveur sera accessible sur http://localhost:8000
```

### 3. Configuration du Frontend (Vue 3)

#### Installation des dépendances
```bash
cd ../frontend
npm install
```

#### Configuration de l'environnement
```bash
# Créer le fichier d'environnement
cp .env.example .env.local
```

#### Configuration du fichier .env.local (Frontend)
```env
# URL de l'API Backend
VITE_API_URL=http://localhost:8000/api

# Configuration Pusher (Frontend)
VITE_PUSHER_APP_KEY=your_pusher_key
VITE_PUSHER_APP_CLUSTER=your_cluster

# Autres configurations
VITE_APP_NAME="ToDoList App"
```

#### Lancement du serveur frontend
```bash
npm run dev
# Le serveur sera accessible sur http://localhost:5173
```

### 4. Test de l'Application

1. **Backend** : Visitez `http://localhost:8000/api` pour vérifier l'API
2. **Frontend** : Visitez `http://localhost:5173` pour accéder à l'application
3. **Test complet** : Créez un compte, ajoutez des tâches et testez les notifications en temps réel

## 📸 Captures d'Écran

### Page d'Accueil
*[À ajouter - Capture d'écran de la page d'accueil]*

### Interface de Gestion des Tâches
*[À ajouter - Capture d'écran du dashboard principal]*

### Authentification
*[À ajouter - Capture d'écran des pages de connexion/inscription]*

### Version Mobile
*[À ajouter - Capture d'écran de la version responsive]*

## 🔧 Commandes Utiles

### Backend (Laravel)
```bash
# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Créer un nouveau contrôleur
php artisan make:controller TaskController --api

# Créer une nouvelle migration
php artisan make:migration create_tasks_table
```

### Frontend (Vue 3)
```bash
# Développement avec hot reload
npm run dev

# Build de production
npm run build

# Prévisualisation du build
npm run preview

# Linting du code
npm run lint
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez votre branche de fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commitez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 👨‍💻 Auteur

**Khalid EL Mati**

- 🐱 GitHub: [@khalidgara7](https://github.com/khalidgara7)
- 💼 LinkedIn: [Khalid EL Mati](https://www.linkedin.com/in/khalid-el-mati)
- 📧 Email: [khalidgara8@gmail.com](mailto:votre-email@exemple.com)

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

<div align="center">
  <p>Développé avec ❤️ par Khalid EL Mati</p>
  <p>⭐ N'hésitez pas à donner une étoile si ce projet vous a plu !</p>
</div>
