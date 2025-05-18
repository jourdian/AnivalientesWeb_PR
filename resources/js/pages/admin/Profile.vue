<script setup>
// Importación del layout principal de administración
import AdminLayout from '@/layouts/AdminLayout.vue'

// Importación de herramientas de Inertia.js
import { Head, useForm, usePage, router } from '@inertiajs/vue3'

// Importación de funciones reactivas y computadas desde Vue
import { ref, computed } from 'vue'

// Obtenemos los datos del usuario autenticado desde las props de la página
const user = usePage().props.auth.user

// Accedemos a los mensajes flash de éxito (ej. después de guardar)
const flash = computed(() => usePage().props.flash || {})
const success = computed(() => flash.value.success || null)

// Inicialización del formulario reactivo con los datos actuales del usuario
const form = useForm({
  _method: 'put',
  first_name: user.first_name ?? '',
  last_name: user.last_name ?? '',
  phone: user.phone ?? '',
  position: user.position ?? '',
  photo: null, 
})

// Preview local de la foto cargada o la imagen existente si ya había
const photoPreview = ref(user.photo_path ? `/storage/${user.photo_path}` : null)

/**
 * Evento ejecutado al seleccionar un nuevo archivo de imagen
 * - Guarda la imagen en el formulario
 * - Muestra una vista previa en la interfaz
 */
function onPhotoChange(e) {
  const file = e.target.files[0]
  if (!file) return
  form.photo = file
  photoPreview.value = URL.createObjectURL(file)
}

/**
 * Envía el formulario al backend para actualizar el perfil
 * - Usa método PUT hacia la ruta 'profile.update'
 * - Mantiene el scroll tras la acción
 * - Fuerza recarga de datos si la operación fue exitosa
 */
function submit() {
  form.first_name = form.first_name ?? ''
  form.last_name = form.last_name ?? ''
  form.phone = form.phone ?? ''
  form.position = form.position ?? ''

  form.post(route('profile.update'), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      // Recarga para asegurar que la imagen nueva está disponible
      window.location.reload()
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors)
    },
    data: {
      _method: 'put',
    },
  })
}


</script>

<template>
  <!-- Título de la página -->
  <Head title="Mi perfil" />

  <!-- Layout administrativo con título -->
  <AdminLayout title="Mi perfil">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow space-y-6 mt-6">

      <h2 class="text-xl font-semibold text-gray-800">Información personal</h2>
      <!-- Componente global para mostrar mensajes flash -->
      <FlashMessage />

      <!-- Aviso de éxito tras guardar -->
      <div v-if="success" class="p-4 bg-green-100 text-green-800 rounded border border-green-300">
        {{ success }}
      </div>

      <!-- FOTO DE PERFIL -->
      <div class="flex items-center gap-6">
        <div class="relative">
          <!-- Imagen actual o nueva -->
          <img v-if="photoPreview" :src="photoPreview" alt="Foto de perfil"
            class="w-24 h-24 rounded-full object-cover ring-2 ring-gray-300" />
          <div v-else class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
            Sin foto
          </div>

          <!-- Selector oculto de archivos + botón flotante -->
          <input type="file" accept="image/*" id="photo" class="hidden" @change="onPhotoChange" />
          <label for="photo"
            class="absolute bottom-0 right-0 bg-white border border-gray-300 rounded-full p-1 shadow cursor-pointer hover:bg-gray-100"
            title="Cambiar foto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586M16 3h5v5" />
            </svg>
          </label>
        </div>

        <!-- Email del usuario (solo lectura) -->
        <div class="flex-1 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Email (no editable)</label>
            <input type="email" :value="user.email" disabled
              class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" />
          </div>
        </div>
      </div>

      <!-- FORMULARIO DE EDICIÓN -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Nombre y apellidos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input v-model="form.first_name" type="text" class="w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Apellidos</label>
            <input v-model="form.last_name" type="text" class="w-full border rounded px-3 py-2" />
          </div>
        </div>

        <!-- Teléfono y cargo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input v-model="form.phone" type="text" class="w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Cargo / posición</label>
            <input v-model="form.position" type="text" class="w-full border rounded px-3 py-2" />
          </div>
        </div>

        <!-- Botón de envío -->
        <div class="pt-6 flex justify-between items-center">
          <button type="submit" :disabled="form.processing"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow disabled:opacity-50">
            {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
