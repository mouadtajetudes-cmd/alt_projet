# ALT WebSocket Service

Service WebSocket temps réel pour la plateforme ALT.

## 🚀 Fonctionnalités

- ✅ Authentification JWT
- ✅ Messages instantanés en temps réel
- ✅ Salles de chat (rooms/conversations)
- ✅ Indicateurs de frappe (typing...)
- ✅ Statuts de présence (online/offline/away/busy)
- ✅ Accusés de réception (delivered/read)
- ✅ Broadcast dans les rooms
- ✅ Gestion des pièces jointes
- ✅ Connexion PostgreSQL + MongoDB

## 📋 Prérequis

- Node.js >= 20.0.0
- npm >= 10.0.0
- PostgreSQL (pour la structure)
- MongoDB (pour les messages)

## 🔧 Installation

```bash
npm install
```

## ⚙️ Configuration

Créer un fichier `.env` à la racine :

```env
NODE_ENV=development
WS_PORT=3001
JWT_SECRET=votre_secret_jwt
MONGO_URI=mongodb://user:pass@host:27017/db
DB_HOST=postgres
DB_PORT=5432
DB_NAME=alt
DB_USER=alt
DB_PASS=altpass
```

## 🏃 Démarrage

```bash
# Production
npm start

# Développement (avec nodemon)
npm run dev
```

## 📡 Événements Socket.IO

### Client → Serveur

- `room:join` - Rejoindre une conversation
- `room:leave` - Quitter une conversation
- `message:send` - Envoyer un message
- `typing:start` - Commencer à taper
- `typing:stop` - Arrêter de taper
- `message:delivered` - Marquer comme livré
- `message:read` - Marquer comme lu
- `presence:update` - Mettre à jour le statut

### Serveur → Client

- `room:joined` - Confirmation de join
- `user:joined` - Un utilisateur a rejoint
- `user:left` - Un utilisateur est parti
- `message:new` - Nouveau message
- `message:ack` - Accusé de réception
- `message:status` - Changement de statut
- `typing:user` - Quelqu'un tape
- `user:online` - Utilisateur en ligne
- `user:offline` - Utilisateur hors ligne
- `presence:changed` - Statut changé
- `error` - Erreur

## 🔐 Authentification

Envoyer le JWT lors de la connexion :

```javascript
const socket = io('http://localhost:3001', {
  auth: {
    token: 'votre_jwt_token'
  }
});
```

## 📊 Health Check

```
GET http://localhost:3001/health
```

## 🏗️ Architecture

```
ws/
├── server.js       # Serveur principal
├── package.json    # Dépendances
├── .env           # Configuration
└── README.md      # Documentation
```

## 📦 Dépendances principales

- **socket.io** - WebSocket temps réel
- **express** - Serveur HTTP
- **mongodb** - Client MongoDB
- **pg** - Client PostgreSQL
- **jsonwebtoken** - Validation JWT
- **winston** - Logging

## 🐳 Docker

Le service est configuré dans docker-compose.yml :

```yaml
websocket:
  image: node:20-alpine
  ports:
    - "3001:3001"
  volumes:
    - ./ws:/app
  environment:
    - WS_PORT=3001
    - JWT_SECRET=...
```

## 📝 Logs

Les logs sont affichés dans la console avec winston :

- `error` - Erreurs critiques
- `warn` - Avertissements
- `info` - Informations générales
- `debug` - Debug détaillé

## 🤝 Contribuer

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

MIT
