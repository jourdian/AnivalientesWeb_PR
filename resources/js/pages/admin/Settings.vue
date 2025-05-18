<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import { ref, computed, nextTick } from 'vue'

import { ImageIcon } from 'lucide-vue-next'

const page = usePage()

const administration = computed(() => page.props.administration)
const users = computed(() => page.props.users)

const filters = ref({ ...page.props.filters })

const preferences = ref({
  showLogo: true,
  alwaysNotify: false,
  confirmReception: true,
  allowRegistration: false,
  includeLogoInEmail: false,
})


const logoFile = ref(null)
const logoPreview = ref(null)


function triggerFileInput() {
  document.getElementById('logo-input').click()
}

/**
 * Control de selección de logo 
 */
function onLogoChange(e) {
  const file = e.target.files[0]
  if (!file) return
  logoFile.value = file
  logoPreview.value = URL.createObjectURL(file)
  submitLogo()
}

/**
 * Enviamos nuevo logo y recargamos la información 
 */
function submitLogo() {
  if (!logoFile.value) return
  const formData = new FormData()
  formData.append('logo', logoFile.value)

  router.post('/settings/logo', formData, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      nextTick(() => {
        setTimeout(() => {
          logoFile.value = null
          logoPreview.value = null
        }, 300)
      })
      router.reload({ only: ['administration'] })
    }
  })
}

/**
 * Filtro de búsqueda
 */
function getFilteredUsers() {
  router.get('/settings', filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>


<template>
  <Head title="Ajustes" />

  <AdminLayout title="Ajustes">
    <div class="flex flex-col gap-6">

      <section class="bg-gray-100 p-6 rounded-lg flex justify-between items-center">
        <!-- Info de la administración -->
        <div>
          <h2 class="text-lg font-semibold mb-2">
            Administración: {{ administration.name }}
          </h2>
          <p>Teléfono: {{ administration.phone }}</p>
          <p>Email: {{ administration.email }}</p>
          <p>Dirección: {{ administration.address }}</p>
          <p>{{ administration.city }}, {{ administration.province }}</p>
        </div>

        <!-- Cambio de logo -->
        <div class="relative flex flex-col items-center">          
          <img
            v-if="logoPreview"
            :src="logoPreview"
            alt="Vista previa del logo"
            class="h-20 object-contain mb-2"
          />
          <img
            v-else
            :src="administration.logo_path
                    ? `/storage/${administration.logo_path}?v=${Date.now()}`
                    : '/images/Isotipo_Anivalientes_transparente.png'"
            alt="Logo actual"
            class="h-20 object-contain mb-2"
          />
          <!-- Botón de subida -->
          <button
            type="button"
            @click="triggerFileInput"
            class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 bg-white border border-gray-300 rounded-full p-2 shadow hover:bg-gray-100"
            title="Subir logo"
          >
            <ImageIcon class="w-5 h-5 text-gray-700" />
          </button>
          <input
            id="logo-input"
            type="file"
            accept="image/*"
            class="hidden"
            @change="onLogoChange"
          />
        </div>
      </section>

      
      <section class="flex gap-6">

        <!-- TABLA DE USUARIOS -->
        <div class="w-full bg-white shadow rounded-lg overflow-hidden">
          <div class="border-b p-4 font-semibold text-sm">Usuarios registrados</div>

          
          <div class="flex gap-4 p-4 items-center">
            <input
              v-model="filters.search"
              @input="getFilteredUsers"
              type="text"
              placeholder="Buscar por nombre o email"
              class="border rounded px-3 py-1 text-sm w-1/3"
            />
            <select
              v-model="filters.role"
              @change="getFilteredUsers"
              class="border rounded px-3 py-1 text-sm"
            >
              <option value="">Todos los roles</option>
              <option value="Institutional">Institutional</option>
              <option value="Admin">Admin</option>
            </select>
          </div>

          <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-3">Fecha</th>
                <th class="p-3">Nombre</th>
                <th class="p-3">Email</th>
                <th class="p-3">Rol</th>
                <th class="p-3">Cargo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in users.data" :key="u.id" class="border-t hover:bg-gray-50">
                <td class="p-3">{{ new Date(u.created_at).toLocaleDateString() }}</td>
                <td class="p-3">{{ u.first_name }} {{ u.last_name }}</td>
                <td class="p-3">{{ u.email }}</td>
                <td class="p-3 capitalize text-green-600">{{ u.role }}</td>
                <td class="p-3">{{ u.position || '—' }}</td>
              </tr>
            </tbody>
          </table>

          <!-- Paginación -->
          <div class="p-4">
            <ul class="flex space-x-1 text-sm">
              <li v-for="link in users.links" :key="link.label">
                <button
                  v-if="link.url"
                  @click="router.get(link.url, {}, { preserveScroll: true })"
                  v-html="link.label"
                  :class="[
                    'px-3 py-1 border rounded',
                    link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
                  ]"
                ></button>
              </li>
            </ul>
          </div>
        </div>

        <!-- PREFERENCIAS (no se han implementado. En realidad solo está ahí para rellenar el hueco :P )-->
        <div class="w-1/3 bg-gray-100 p-6 rounded-lg shadow text-sm space-y-4">
          <h3 class="text-base font-semibold mb-2">Preferencias</h3>

          <!-- Each setting with a description and toggle -->
          <label title="Enviar notificación en cada cambio de estado de la denuncia" class="flex justify-between items-center border-b py-2">
            <span>Notificar cambio de estado</span>
            <input type="checkbox" v-model="preferences.alwaysNotify" checked />
          </label>

          <label title="Enviar notificación para confirmar la recepción de la denuncia" class="flex justify-between items-center border-b py-2">
            <span>Confirmar recepción</span>
            <input type="checkbox" v-model="preferences.confirmReception" checked />
          </label>

          <label title="Incluir el logotipo de la administración en el cuerpo del email" class="flex justify-between items-center py-2">
            <span>Incluir logo en email</span>
            <input type="checkbox" v-model="preferences.includeLogoInEmail" checked />
          </label>

          <p class="text-red-600 mt-2">⚠️ Las preferencias no han sido implementadas en esta versión demo.</p>
        </div>

      </section>
    </div>
  </AdminLayout>
</template>
