# Tests SQL - Marketplace

## 1. Tests Basiques

### Compter les enregistrements
```sql
SELECT COUNT(*) FROM categories;
SELECT COUNT(*) FROM produits;
SELECT COUNT(*) FROM medias;
SELECT COUNT(*) FROM produit_medias;
```

### Lister toutes les catégories
```sql
SELECT * FROM categories ORDER BY nom ASC;
```

### Lister tous les produits
```sql
SELECT * FROM produits ORDER BY date_publication DESC;
```

## 2. Tests Filtres

### Filtre par catégorie
```sql
SELECT * FROM produits 
WHERE id_categorie = 1 
ORDER BY date_publication DESC;
```

### Filtre par prix (min/max)
```sql
SELECT * FROM produits 
WHERE prix >= 50 AND prix <= 200 
ORDER BY prix ASC;
```

### Filtre par statut
```sql
SELECT * FROM produits 
WHERE statut = 'disponible' 
ORDER BY date_publication DESC;
```

### Filtre recherche textuelle
```sql
SELECT * FROM produits 
WHERE nom ILIKE '%laptop%' OR description ILIKE '%laptop%'
ORDER BY date_publication DESC;
```

### Filtre par utilisateur
```sql
SELECT * FROM produits 
WHERE id_utilisateur = 1 
ORDER BY date_publication DESC;
```

### Filtres combinés
```sql
SELECT * FROM produits 
WHERE id_categorie = 1 
  AND prix >= 100 
  AND prix <= 500 
  AND statut = 'disponible'
ORDER BY date_publication DESC;
```

## 3. Tests Pagination

### Page 1 (5 premiers)
```sql
SELECT * FROM produits 
ORDER BY date_publication DESC 
LIMIT 5 OFFSET 0;
```

### Page 2 (5 suivants)
```sql
SELECT * FROM produits 
ORDER BY date_publication DESC 
LIMIT 5 OFFSET 5;
```

### Page 3
```sql
SELECT * FROM produits 
ORDER BY date_publication DESC 
LIMIT 5 OFFSET 10;
```

## 4. Tests Jointures

### Produits avec leurs catégories
```sql
SELECT p.*, c.nom as categorie_nom 
FROM produits p
LEFT JOIN categories c ON p.id_categorie = c.id_categorie
ORDER BY p.date_publication DESC;
```

### Produits avec leurs médias
```sql
SELECT p.nom, m.titre, m.url, pm.ordre
FROM produits p
LEFT JOIN produit_medias pm ON p.id_produit = pm.id_produit
LEFT JOIN medias m ON pm.id_media = m.id_media
ORDER BY p.nom, pm.ordre;
```

### Compter médias par produit
```sql
SELECT p.nom, COUNT(pm.id_media) as nb_medias
FROM produits p
LEFT JOIN produit_medias pm ON p.id_produit = pm.id_produit
GROUP BY p.id_produit, p.nom
ORDER BY nb_medias DESC;
```

### Produits sans médias
```sql
SELECT p.* 
FROM produits p
LEFT JOIN produit_medias pm ON p.id_produit = pm.id_produit
WHERE pm.id_media IS NULL;
```

## 5. Tests Performance

### Vérifier utilisation des index
```sql
EXPLAIN ANALYZE 
SELECT * FROM produits 
WHERE id_categorie = 1 AND statut = 'disponible';
```

### Vérifier index sur recherche textuelle
```sql
EXPLAIN ANALYZE 
SELECT * FROM produits 
WHERE nom ILIKE '%laptop%';
```


## Accès Adminer

URL: http://localhost:8080
- System: PostgreSQL
- Server: alt-db
- Username: alt
- Password: alt
- Database: alt
