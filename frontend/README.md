# Frontend ALT Platform - Vue.js + Vite


## Stack Technique

- **Vue 3** - Framework JavaScript progressif
- **Vite** - Outil de build ultra-rapide
- **Vue Router** - Routage côté client
- **Pinia** - State management
- **Axios** - Requêtes HTTP
- **Socket.io-client** - WebSocket temps réel

## 📁 Structure du projet

```
frontend/
├── src/
│   ├── assets/
│   │   └── css/
│   │       └── style.css          # Styles globaux
│   ├── router/
│   │   └── index.js               # Configuration routes
│   ├── views/
│   │   ├── Home.vue               
│   │   ├── Chat.vue               
│   │   ├── Login.vue              
│   │   ├── Marketplace.vue        
│   │   ├── Social.vue             
│   │   └── Avatar.vue             
│   ├── App.vue                   
│   └── main.js                    
├── index.html
├── vite.config.js
├── package.json
└── Dockerfile
```

## 🐳 Lancement avec Docker

### Démarrer tous les services
```bash
docker-compose up -d
```

### Voir les logs frontend
```bash
docker-compose logs -f frontend
```

### Arrêter les services
```bash
docker-compose down
```

## 💻 Lancement en local (sans Docker)

### Installation
```bash
cd frontend
npm install
```

### Dev server
```bash
npm run dev
```

L'application sera accessible sur `http://localhost:5173`

### Build production
```bash
npm run build
```

### Preview production
```bash
npm run preview
```

## 🌐 Endpoints API

Le frontend se connecte aux microservices suivants :

- **Gateway** : `http://localhost:6090`
- **Auth** : `http://localhost:6081`
- **Chat** : `http://localhost:6084`
- **Marketplace** : `http://localhost:6082`
- **Avatar** : `http://localhost:6083`
- **Social** : `http://localhost:6085`
- **WebSocket** : `ws://localhost:3001`

## 📄 Pages disponibles

- `/` - Page d'accueil
- `/chat` - Chat temps réel avec WebSocket
- `/login` - Connexion utilisateur
- `/marketplace` - Liste des produits
- `/social` - Feed social (posts)
- `/avatar` - Galerie des avatars

## 🔧 Configuration

Les variables d'environnement sont définies dans `docker-compose.yml` :

```yaml
environment:
  - NODE_ENV=development
  - VITE_API_GATEWAY=http://localhost:6090
  - VITE_WS_URL=ws://localhost:3001
```

## 🎨 Personnalisation

### Thème
Les couleurs sont définies dans `src/assets/css/style.css` :

```css
:root {
  --primary-color: #0d6efd;
  --success-color: #198754;
  --danger-color: #dc3545;
}
```

### Routing
Ajouter une route dans `src/router/index.js` :

```javascript
{
  path: '/nouvelle-page',
  name: 'NouvellePage',
  component: () => import('../views/NouvellePage.vue')
}
```

## 📝 Notes

- Le chat utilise WebSocket pour la communication temps réel
- Les tokens JWT sont stockés dans localStorage
- Hot Module Replacement (HMR) activé en dev
- Build optimisé pour la production avec code splitting

## 🐛 Debugging

### Frontend ne démarre pas
```bash
# Supprimer node_modules et réinstaller
docker-compose down
docker-compose up --build frontend
```

### Problème de connexion API
Vérifier que tous les services backend sont démarrés :
```bash
docker-compose ps
```

### WebSocket ne se connecte pas
Vérifier que le service `ws` est actif :
```bash
docker-compose logs ws
```

## 📚 Ressources

- [Vue 3 Documentation](https://vuejs.org/)
- [Vite Documentation](https://vitejs.dev/)
- [Vue Router](https://router.vuejs.org/)
- [Pinia](https://pinia.vuejs.org/)
