<template>
  <main class="create-product-page">
    <div class="container">
      <h1>Créer une annonce</h1>

      <form class="form" @submit.prevent="createProduct">
        <label>
          Titre *
          <input v-model="formData.nom" type="text" required />
        </label>

        <label>
          Description *
          <textarea v-model="formData.description" rows="5" required />
        </label>

        <div class="row">
          <label>
            Prix (€) *
            <input v-model.number="formData.prix" type="number" min="0" step="0.01" required />
          </label>

          <label>
            Quantité *
            <input v-model.number="formData.quantite" type="number" min="0" required />
          </label>
        </div>

        <label>
          Catégorie
            <select v-model="formData.id_categorie" required>
              <option value="">Choisir une catégorie</option>
            <option
              v-for="c in categories"
              :key="c.id_categorie"
              :value="String(c.id_categorie)"
            >
              {{ c.nom }}
            </option>
          </select>
        </label>

        <label>
          Images du produit
          <input type="file" multiple accept="image/*" @change="onFilesSelected" />
        </label>

        <div v-if="previewUrls.length" class="previews">
          <img v-for="(u, i) in previewUrls" :key="i" :src="u" alt="preview" />
        </div>

        <label>
          Statut
          <select v-model="formData.statut">
            <option value="disponible">Disponible</option>
            <option value="reserve">Réservé</option>
            <option value="indisponible">Indisponible</option>
          </select>
        </label>

        <div class="actions">
          <button type="button" class="btn btn-cancel" @click="router.push('/marketplace')">
            Annuler
          </button>
          <button type="submit" class="btn btn-submit" :disabled="loading">
            {{ loading ? 'Création...' : "Créer l'annonce" }}
          </button>
        </div>
      </form>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(false)
const categories = ref([])
const selectedFiles = ref([])
const previewUrls = ref([])

const formData = ref({
  nom: '',
  description: '',
  prix: 0,
  quantite: 0,
  id_categorie: '',
  statut: 'disponible'
})

const API_BASE = 'http://localhost:6090/marketplace'
const UPLOAD_BASE = 'http://localhost:3001'

const onFilesSelected = (e) => {
  const files = Array.from(e.target.files || [])
  selectedFiles.value = files
  previewUrls.value = files.map((f) => URL.createObjectURL(f))
}

const loadCategories = async () => {
  try {
    const res = await axios.get(`${API_BASE}/categories`)
    categories.value = res?.data?.data || []
  } catch (e) {
    console.error('Erreur catégories:', e)
  }
}

const extractUploadedUrl = (data) => {
  return (
    data?.url ||
    data?.data?.url ||
    data?.file?.url ||
    data?.files?.[0]?.url ||
    data?.data?.files?.[0]?.url ||
    null
  )
}

const uploadOneFile = async (file) => {
const keysToTry = ['file']
let lastError = null

  for (const key of keysToTry) {
    try {
      const fd = new FormData()
      fd.append(key, file)
      const res = await axios.post(`${UPLOAD_BASE}/upload`, fd)
      const url = extractUploadedUrl(res.data)
      if (url) return url
    } catch (e) {
      lastError = e
    }
  }

  throw lastError || new Error('Upload impossible')
}

const createProduct = async () => {
  try {
    loading.value = true

    const mediaUrls = []
    for (const file of selectedFiles.value) {
      const url = await uploadOneFile(file)
      mediaUrls.push(url)
    }

    const authUser = JSON.parse(localStorage.getItem('user') || '{}')
    const idUtilisateur = Number(
      authUser?.id_utilisateur ?? authUser?.id ?? 1
    )

    const payload = {
      nom: formData.value.nom?.trim(),
      description: formData.value.description?.trim(),
      prix: Number(formData.value.prix || 0),
      quantite: Number(formData.value.quantite || 0),
      statut: formData.value.statut || 'disponible',
      id_categorie: formData.value.id_categorie
        ? Number(formData.value.id_categorie)
        : null,
      id_utilisateur: idUtilisateur,
      media_urls: mediaUrls
    }

    await axios.post(`${API_BASE}/products`, payload)
    router.push('/marketplace')
  } catch (e) {
    console.error('Erreur création annonce:', e)
    const msg =
      e?.response?.data?.message ||
      e?.response?.data?.error ||
      e?.message ||
      "Erreur lors de la création de l'annonce"
    alert(msg)
  } finally {
    loading.value = false
  }
}

onMounted(loadCategories)
</script>

<style scoped>
.create-product-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
    linear-gradient(180deg, #f7f8fc 0%, #f1f4fb 100%);
  padding: 2.5rem 0 3rem;
}

.container {
  max-width: 880px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.page-header h1 {
  margin: 0;
  color: #0f172a;
  font-size: 2rem;
  font-weight: 800;
}

.form-card {
  background: rgba(255,255,255,0.92);
  border: 1px solid #e7edf6;
  border-radius: 24px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
  padding: 1.6rem;
}

.form-grid {
  display: grid;
  gap: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

label {
  display: block;
  margin-bottom: 0.45rem;
  color: #334155;
  font-weight: 700;
}

input,
textarea,
select {
  width: 100%;
  border: 1px solid #dbe3ef;
  background: white;
  border-radius: 14px;
  padding: 0.9rem 1rem;
  font-size: 0.96rem;
  color: #0f172a;
  transition: all 0.2s ease;
}

input:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.10);
}

.previews {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.previews img {
  width: 96px;
  height: 96px;
  object-fit: cover;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.85rem;
  margin-top: 1.2rem;
}

.btn-submit,
.btn-cancel {
  border: none;
  border-radius: 14px;
  padding: 0.9rem 1.25rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-submit {
  color: white;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  box-shadow: 0 10px 24px rgba(99, 102, 241, 0.24);
}

.btn-cancel {
  background: #eef2f7;
  color: #475569;
}

.btn-submit:hover,
.btn-cancel:hover {
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }
}
</style>