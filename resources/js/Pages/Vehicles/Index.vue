<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Lista pojazdów</h1>

    <div class="flex gap-4 mb-4">
      <button @click="showModal = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        ➕ Dodaj pojazd
      </button>
      <button @click="simulateQRScan" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
        📷 Dodaj z QR
      </button>
    </div>

    <div v-if="notifications.length" class="mb-4 space-y-2">
      <div
        v-for="(note, index) in notifications"
        :key="index"
        class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 rounded flex justify-between"
      >
        <span>{{ note }}</span>
        <button @click="notifications.splice(index, 1)" class="ml-4 text-sm text-red-600 hover:underline">Usuń</button>
      </div>
    </div>

    <input v-model="search" type="text" placeholder="Szukaj marki lub modelu..." class="mb-4 p-2 border rounded w-full" />

    <table class="min-w-full bg-white rounded shadow">
      <thead>
        <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
          <th class="p-3 cursor-pointer" @click="sortBy('brand_model')">Marka i Model</th>
          <th class="p-3 cursor-pointer" @click="sortBy('year')">Rok</th>
          <th class="p-3 cursor-pointer" @click="sortBy('vin')">VIN</th>
          <th class="p-3 cursor-pointer" @click="sortBy('plate')">Tablica</th>
          <th class="p-3 cursor-pointer" @click="sortBy('inspection_date')">Przegląd</th>
          <th class="p-3 cursor-pointer" @click="sortBy('insurance_date')">Ubezpieczenie</th>
          <th class="p-3">Zdjęcie</th>
          <th class="p-3">Akcje</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="vehicle in filteredVehicles" :key="vehicle.id" class="border-b hover:bg-gray-50">
          <td class="p-3">{{ vehicle.brand_model }}</td>
          <td class="p-3">{{ vehicle.year }}</td>
          <td class="p-3">{{ vehicle.vin }}</td>
          <td class="p-3">{{ vehicle.plate }}</td>
          <td class="p-3">{{ vehicle.inspection_date }}</td>
          <td class="p-3">{{ vehicle.insurance_date }}</td>
          <td class="p-3">
            <img v-if="vehicle.photo" :src="vehicle.photo" class="h-12 rounded" />
            <span v-else class="text-gray-400 text-sm">Brak</span>
          </td>
          <td class="p-3">
            <button @click="startEdit(vehicle)" class="text-blue-600 hover:underline mr-2">Edytuj</button>
            <button @click="deleteVehicle(vehicle.id)" class="text-red-600 hover:underline">Usuń</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white p-6 rounded shadow max-w-md w-full">
        <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edytuj pojazd' : 'Nowy pojazd' }}</h2>

        <input v-model="newVehicle.brand_model" type="text" placeholder="Marka i model" class="mb-2 w-full border p-2 rounded" />
        <input v-model="newVehicle.year" type="number" placeholder="Rok" class="mb-2 w-full border p-2 rounded" />
        <input v-model="newVehicle.vin" type="text" placeholder="VIN" class="mb-2 w-full border p-2 rounded" />
        <input v-model="newVehicle.plate" type="text" placeholder="Tablica" class="mb-2 w-full border p-2 rounded" />
        <input v-model="newVehicle.inspection_date" type="date" class="mb-2 w-full border p-2 rounded" />
        <input v-model="newVehicle.insurance_date" type="date" class="mb-2 w-full border p-2 rounded" />

        <!-- Upload zdjęcia -->
        <label class="block mb-1 font-medium">Zdjęcie pojazdu</label>
        <input type="file" @change="handleFileUpload" accept="image/*" class="mb-2 w-full border p-2 rounded" />
        <div v-if="imagePreview" class="mb-4">
          <img :src="imagePreview" class="rounded border max-h-32" />
          <button @click="removePhoto" class="text-sm text-red-600 hover:underline mt-1">Usuń zdjęcie</button>
        </div>

        <div class="flex justify-end gap-2">
          <button @click="resetForm" class="px-4 py-2 bg-gray-300 rounded">Anuluj</button>
          <button @click="addVehicle" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Zapisz</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({ vehicles: Array })

const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)
const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const notifications = ref([])

const uploadedImage = ref(null)
const imagePreview = ref(null)

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.size <= 5 * 1024 * 1024) {
    uploadedImage.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const removePhoto = () => {
  uploadedImage.value = null
  imagePreview.value = null
}

const newVehicle = ref({
  brand_model: '',
  year: '',
  vin: '',
  plate: '',
  inspection_date: '',
  insurance_date: '',
  photo: null
})

const filteredVehicles = computed(() => {
  let list = props.vehicles.filter(v =>
    v.brand_model.toLowerCase().includes(search.value.toLowerCase())
  )
  if (sortKey.value) {
    list.sort((a, b) =>
      sortAsc.value
        ? String(a[sortKey.value]).localeCompare(String(b[sortKey.value]))
        : String(b[sortKey.value]).localeCompare(String(a[sortKey.value]))
    )
  }
  return list
})

const sortBy = (key) => {
  if (sortKey.value === key) {
    sortAsc.value = !sortAsc.value
  } else {
    sortKey.value = key
    sortAsc.value = true
  }
}

const resetForm = () => {
  showModal.value = false
  isEditing.value = false
  editingId.value = null
  uploadedImage.value = null
  imagePreview.value = null
  newVehicle.value = {
    brand_model: '',
    year: '',
    vin: '',
    plate: '',
    inspection_date: '',
    insurance_date: '',
    photo: null
  }
}

const addVehicle = () => {
  const id = isEditing.value ? editingId.value : Math.max(0, ...props.vehicles.map(v => v.id)) + 1
  const photoUrl = uploadedImage.value ? imagePreview.value : null

  const vehicleData = {
    id,
    ...newVehicle.value,
    photo: photoUrl
  }

  if (isEditing.value) {
    const index = props.vehicles.findIndex(v => v.id === id)
    if (index !== -1) props.vehicles[index] = vehicleData
  } else {
    props.vehicles.push(vehicleData)
  }

  checkNotifications(vehicleData)
  resetForm()
}

const startEdit = (vehicle) => {
  showModal.value = true
  isEditing.value = true
  editingId.value = vehicle.id
  newVehicle.value = { ...vehicle }
  imagePreview.value = vehicle.photo
}

const deleteVehicle = (id) => {
  if (confirm("Czy na pewno chcesz usunąć ten pojazd?")) {
    const index = props.vehicles.findIndex(v => v.id === id)
    if (index !== -1) props.vehicles.splice(index, 1)
  }
}

const checkNotifications = (vehicle) => {
  const now = new Date()
  const warnDays = 14

  const daysUntil = (dateStr) => {
    const diff = new Date(dateStr) - now
    return diff / (1000 * 60 * 60 * 24)
  }

  if (vehicle.inspection_date && daysUntil(vehicle.inspection_date) < warnDays) {
    notifications.value.push(`🔔 Przegląd dla ${vehicle.brand_model} zbliża się!`)
  }

  if (vehicle.insurance_date && daysUntil(vehicle.insurance_date) < warnDays) {
    notifications.value.push(`📢 Ubezpieczenie dla ${vehicle.brand_model} kończy się wkrótce!`)
  }
}

const simulateQRScan = () => {
  showModal.value = true
  imagePreview.value = null
  newVehicle.value = {
    brand_model: 'Volkswagen Passat',
    year: 2019,
    vin: 'WVWZZZ3CZEE123456',
    plate: 'GD1234X',
    inspection_date: '2025-06-12',
    insurance_date: '2025-06-25',
    photo: null
  }
}
</script>
