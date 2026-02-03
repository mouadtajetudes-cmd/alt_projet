# Alt Project - Réseau Social Étudiant

Projet de réseau social avec marketplace et chat en temps réel. Architecture micro-services avec Docker.

## 🚀 Prérequis

- Docker Desktop
- Git

## 📦 Installation

1. Cloner le projet :
```bash
git clone <repo-url>
cd Alt_Project
```

2. Lancer les services :
```bash
docker-compose up -d
```

3. Arrêter les services :
```bash
docker-compose down
```

## 🏗️ Architecture

Le projet est composé de plusieurs micro-services :

- **Gateway** : Point d'entrée unique (API Gateway)
- **Auth** : Authentification et gestion utilisateurs/groupes/publicités
- **Social** : Posts, commentaires, interactions sociales
- **Marketplace** : Gestion des produits et transactions
- **Avatar** : Gestion des avatars et niveaux
- **Chat** : Messagerie en temps réel (MongoDB + WebSocket)
- **WS** : Serveur WebSocket pour le chat
- **Frontend** : Interface utilisateur (Vite.js)

## 🔗 Services

- **Gateway** : http://localhost:6080
- **Frontend** : http://localhost:3000
- **Auth Service** : http://localhost:6081
- **Social Service** : http://localhost:6085
- **Marketplace** : http://localhost:6082
- **Chat** : http://localhost:6084   (Pas encore)
- **Avatar Service** : http://localhost:6083
- **WebSocket** : ws://localhost:3001
- **Mongo DB** : http://localhost:27017

## 📝 Fonctionnalités

### Service Auth
- Authentification JWT
- CRUD Utilisateurs
- CRUD Groupes
- CRUD Publicités (Ads)

### Service Social
- Posts et commentaires
- Likes et partages
- Timeline

### Service Marketplace
- Produits
- Transactions

### Service Chat
- Messages en temps réel
- Conversations

### Service Avatar
- Gestion avatars
- Système de niveaux

## 👥 Équipe

Projet étudiant - Architecture micro-services

ASHRAFI Hanan
HMEM Wiem
NIKIEMA Faozia
TAJ Mouad