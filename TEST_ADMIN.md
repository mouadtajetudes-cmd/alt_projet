# 🧪 Guide de Test - Protection Admin

## 📋 Prérequis

1. **Services démarrés** :

   ```bash
   docker-compose up -d
   ```

2. **Frontend démarré** :

   ```bash
   cd frontend
   npm run dev
   ```

3. **Ports actifs** :
   - Frontend : `http://localhost:5173`
   - API Gateway : `http://localhost:6090`
   - Service Avatar : `http://localhost:6083`

---

## 👥 Comptes de Test

### 🔴 Utilisateur ADMIN

- **Email** : `admin@alt.com`
- **Mot de passe** : `password`
- **Colonne DB** : `administrateur = TRUE`

### 🟢 Utilisateur NORMAL

- **Email** : `john@example.com`
- **Mot de passe** : `password`
- **Colonne DB** : `administrateur = FALSE`

---

## 🔧 Test 1 : Login et Vérification Token (Postman)

### 1️⃣ Login Admin

**Requête POST** : `http://localhost:6090/auth/login`

**Headers** :

```json
{
  "Content-Type": "application/json"
}
```

**Body (JSON)** :

```json
{
  "email": "admin@alt.com",
  "password": "password"
}
```

**Réponse attendue** :

```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id_utilisateur": 1,
    "nom": "Admin",
    "prenom": "System",
    "email": "admin@alt.com",
    "administrateur": true,
    "premium": true,
    "points": 1000
  }
}
```

✅ **Vérifier** : `administrateur: true`

---

### 2️⃣ Login Utilisateur Normal

**Requête POST** : `http://localhost:6090/auth/login`

**Body (JSON)** :

```json
{
  "email": "john@example.com",
  "password": "password"
}
```

**Réponse attendue** :

```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id_utilisateur": 2,
    "nom": "Doe",
    "prenom": "John",
    "email": "john@example.com",
    "administrateur": false,
    "premium": true,
    "points": 500
  }
}
```

✅ **Vérifier** : `administrateur: false`

---

## 🌐 Test 2 : Interface Web (localhost:5173)

### ✅ Scénario 1 : Admin peut créer un avatar

1. **Ouvrir** : `http://localhost:5173`

2. **Se connecter en ADMIN** :
   - Aller sur la page de login (si disponible)
   - Ou ouvrir la console du navigateur (F12) et exécuter :

   ```javascript
   // Simuler un login admin
   localStorage.setItem("token", "fake-token-12345");
   localStorage.setItem(
     "user",
     JSON.stringify({
       id_utilisateur: 1,
       nom: "Admin",
       email: "admin@alt.com",
       administrateur: true,
     }),
   );
   location.reload();
   ```

3. **Aller à** : `http://localhost:5173/avatar`

4. **Vérifier** :
   - ✅ Le bouton vert **"➕ Créer un avatar"** est **visible**
   - ✅ Cliquer dessus → accès accordé à `/avatar/create`
   - ✅ Le formulaire s'affiche correctement

5. **Créer un avatar** :
   - Sélectionner un type : `Fox 🦊`
   - Nom : `Test Admin Avatar`
   - Cliquer **"💾 Sauvegarder"**
   - ✅ Message de succès
   - ✅ Redirection vers `/avatar`

---

### ⛔ Scénario 2 : Utilisateur normal ne peut PAS créer

1. **Se déconnecter** (console du navigateur) :

   ```javascript
   localStorage.clear();
   location.reload();
   ```

2. **Se connecter en UTILISATEUR NORMAL** :

   ```javascript
   localStorage.setItem("token", "fake-token-67890");
   localStorage.setItem(
     "user",
     JSON.stringify({
       id_utilisateur: 2,
       nom: "John",
       email: "john@example.com",
       administrateur: false,
     }),
   );
   location.reload();
   ```

3. **Aller à** : `http://localhost:5173/avatar`

4. **Vérifier** :
   - ⛔ Le bouton **"➕ Créer un avatar"** est **invisible**

5. **Tenter d'accéder directement** : `http://localhost:5173/avatar/create`

6. **Résultat attendu** :
   - ⛔ Redirection automatique vers `/avatar?error=admin-required`
   - ⛔ Message d'erreur affiché :
     > "⛔ Accès refusé : seuls les administrateurs peuvent créer des avatars."

---

## 🔍 Test 3 : Vérification Console Développeur

### Pour ADMIN :

1. **Se connecter en admin**
2. **Ouvrir la console** (F12)
3. **Taper** :
   ```javascript
   JSON.parse(localStorage.getItem("user"));
   ```

**Résultat attendu** :

```javascript
{
  id_utilisateur: 1,
  nom: "Admin",
  email: "admin@alt.com",
  administrateur: true  // ← Important !
}
```

### Pour utilisateur normal :

```javascript
JSON.parse(localStorage.getItem("user"));
```

**Résultat attendu** :

```javascript
{
  id_utilisateur: 2,
  nom: "John",
  email: "john@example.com",
  administrateur: false  // ← Important !
}
```

---

## 📊 Test 4 : Créer un nouvel utilisateur admin (SQL)

Si vous voulez créer un autre admin :

```sql
INSERT INTO utilisateurs (nom, prenom, email, password, administrateur, premium, points)
VALUES (
  'Nouveau',
  'Admin',
  'newadmin@alt.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password
  TRUE,  -- administrateur
  TRUE,
  1000
);
```

---

## ✅ Checklist Complète

### Protection côté Frontend :

- ✅ Bouton "Créer avatar" visible uniquement pour admin
- ✅ Route `/avatar/create` protégée par `meta: { requiresAdmin: true }`
- ✅ Navigation guard vérifie `isAdmin` avant d'accéder
- ✅ Redirection automatique si non-autorisé
- ✅ Messages d'erreur explicites
- ✅ Double vérification dans `CreateAvatar.vue`

### Backend (à implémenter si nécessaire) :

- ⚠️ Vérifier que l'API `/avatars POST` vérifie aussi le rôle admin côté serveur
- ⚠️ Ajouter middleware d'authentification
- ⚠️ Vérifier le token JWT et le rôle avant de créer

---

## 🐛 Dépannage

### "Le bouton n'apparaît pas pour l'admin"

1. Vérifier le localStorage :

   ```javascript
   console.log(JSON.parse(localStorage.getItem("user")));
   ```

2. Vérifier que `administrateur: true`

3. Vérifier la console pour les erreurs

### "Redirection ne fonctionne pas"

1. Vérifier que le router est bien importé dans `main.js`
2. Vérifier les logs dans la console :
   ```javascript
   console.log("[ROUTER] isAdmin:", isAdmin.value);
   ```

### "Login ne fonctionne pas"

1. Vérifier que le service auth est démarré :

   ```bash
   docker-compose ps
   ```

2. Tester l'endpoint directement :
   ```bash
   curl -X POST http://localhost:6090/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@alt.com","password":"password"}'
   ```

---

## 🎯 Résumé

| Utilisateur  | Voir bouton | Accéder à `/avatar/create` | Créer avatar |
| ------------ | ----------- | -------------------------- | ------------ |
| Admin        | ✅ OUI      | ✅ OUI                     | ✅ OUI       |
| Normal       | ⛔ NON      | ⛔ NON (redirigé)          | ⛔ NON       |
| Non connecté | ⛔ NON      | ⛔ NON (redirigé)          | ⛔ NON       |

**Sécurité** : Protection à 3 niveaux

1. UI : Bouton caché
2. Router : Navigation guard
3. Component : Vérification dans le formulaire
