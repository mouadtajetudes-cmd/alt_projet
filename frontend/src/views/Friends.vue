<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Mes Amis</h1>

      <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'px-6 py-4 text-sm font-medium border-b-2 transition-colors',
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              {{ tab.label }}
              <span
                v-if="tab.count > 0"
                class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-600"
              >
                {{ tab.count }}
              </span>
            </button>
          </nav>
        </div>

        <div class="p-6">
          <div v-if="activeTab === 'friends'" class="space-y-4">
            <div v-if="loading" class="text-center py-8 text-gray-500">
              Chargement...
            </div>

            <div v-else-if="friends.length === 0" class="text-center py-8 text-gray-500">
              Vous n'avez pas encore d'amis. Commencez par envoyer des demandes !
            </div>

            <div
              v-else
              v-for="friend in friends"
              :key="friend.id_utilisateur"
              class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center space-x-4">
                <div
                  class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xl font-bold"
                >
                  {{ friend.prenom?.[0] }}{{ friend.nom?.[0] }}
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">
                    {{ friend.prenom }} {{ friend.nom }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ friend.email }}</p>
                  <p class="text-xs text-gray-400 mt-1">
                    Amis depuis {{ formatDate(friend.date_reponse) }}
                  </p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button
                  @click="startChat(friend.id_utilisateur)"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                >
                  Message
                </button>
                <button
                  @click="removeFriend(friend.id_amitie)"
                  class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors text-sm"
                >
                  Retirer
                </button>
              </div>
            </div>
          </div>

          <div v-if="activeTab === 'received'" class="space-y-4">
            <div v-if="loading" class="text-center py-8 text-gray-500">
              Chargement...
            </div>

            <div v-else-if="receivedRequests.length === 0" class="text-center py-8 text-gray-500">
              Aucune demande reçue
            </div>

            <div
              v-else
              v-for="request in receivedRequests"
              :key="request.id_amitie"
              class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center space-x-4">
                <div
                  class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xl font-bold"
                >
                  {{ request.prenom?.[0] }}{{ request.nom?.[0] }}
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">
                    {{ request.prenom }} {{ request.nom }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ request.email }}</p>
                  <p class="text-xs text-gray-400 mt-1">
                    Demandé le {{ formatDate(request.date_demande) }}
                  </p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button
                  @click="acceptRequest(request.id_amitie)"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                >
                  Accepter
                </button>
                <button
                  @click="refuseRequest(request.id_amitie)"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm"
                >
                  Refuser
                </button>
              </div>
            </div>
          </div>

          <div v-if="activeTab === 'sent'" class="space-y-4">
            <div v-if="loading" class="text-center py-8 text-gray-500">
              Chargement...
            </div>

            <div v-else-if="sentRequests.length === 0" class="text-center py-8 text-gray-500">
              Aucune demande envoyée
            </div>

            <div
              v-else
              v-for="request in sentRequests"
              :key="request.id_amitie"
              class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center space-x-4">
                <div
                  class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xl font-bold"
                >
                  {{ request.prenom?.[0] }}{{ request.nom?.[0] }}
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">
                    {{ request.prenom }} {{ request.nom }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ request.email }}</p>
                  <p class="text-xs text-gray-400 mt-1">
                    Envoyé le {{ formatDate(request.date_demande) }}
                  </p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm">
                  En attente
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const activeTab = ref('friends');
const loading = ref(false);

const friends = ref([]);
const receivedRequests = ref([]);
const sentRequests = ref([]);

const tabs = computed(() => [
  { id: 'friends', label: 'Mes Amis', count: friends.value.length },
  { id: 'received', label: 'Demandes reçues', count: receivedRequests.value.length },
  { id: 'sent', label: 'Demandes envoyées', count: sentRequests.value.length }
]);

const currentUserId = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  return user.id_utilisateur || user.id;
});

onMounted(() => {
  loadAllData();
});

async function loadAllData() {
  await Promise.all([
    loadFriends(),
    loadReceivedRequests(),
    loadSentRequests()
  ]);
}

async function loadFriends() {
  loading.value = true;
  try {
    const response = await fetch(`http://localhost:3001/friends/${currentUserId.value}`);
    if (response.ok) {
      friends.value = await response.json();
    }
  } catch (error) {
    console.error('Erreur chargement amis:', error);
  } finally {
    loading.value = false;
  }
}

async function loadReceivedRequests() {
  loading.value = true;
  try {
    const response = await fetch(`http://localhost:3001/friends/requests/received/${currentUserId.value}`);
    if (response.ok) {
      receivedRequests.value = await response.json();
    }
  } catch (error) {
    console.error('Erreur chargement demandes reçues:', error);
  } finally {
    loading.value = false;
  }
}

async function loadSentRequests() {
  loading.value = true;
  try {
    const response = await fetch(`http://localhost:3001/friends/requests/sent/${currentUserId.value}`);
    if (response.ok) {
      sentRequests.value = await response.json();
    }
  } catch (error) {
    console.error('Erreur chargement demandes envoyées:', error);
  } finally {
    loading.value = false;
  }
}

async function acceptRequest(friendshipId) {
  try {
    const response = await fetch(`http://localhost:3001/friends/accept/${friendshipId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ami_id: currentUserId.value })
    });

    if (response.ok) {
      await loadAllData();
      activeTab.value = 'friends';
    }
  } catch (error) {
    console.error('Erreur acceptation demande:', error);
  }
}

async function refuseRequest(friendshipId) {
  try {
    const response = await fetch(`http://localhost:3001/friends/refuse/${friendshipId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ami_id: currentUserId.value })
    });

    if (response.ok) {
      await loadReceivedRequests();
    }
  } catch (error) {
    console.error('Erreur refus demande:', error);
  }
}

async function removeFriend(friendshipId) {
  if (!confirm('Êtes-vous sûr de vouloir retirer cet ami ?')) {
    return;
  }

  try {
    const response = await fetch(`http://localhost:3001/friends/${friendshipId}`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ userId: currentUserId.value })
    });

    if (response.ok) {
      await loadFriends();
    }
  } catch (error) {
    console.error('Erreur suppression ami:', error);
  }
}

function startChat(userId) {
  router.push({ name: 'Chat', query: { userId } });
}

function formatDate(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 0) return "aujourd'hui";
  if (diffDays === 1) return 'hier';
  if (diffDays < 7) return `il y a ${diffDays} jours`;
  if (diffDays < 30) return `il y a ${Math.floor(diffDays / 7)} semaines`;
  if (diffDays < 365) return `il y a ${Math.floor(diffDays / 30)} mois`;
  
  return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>
