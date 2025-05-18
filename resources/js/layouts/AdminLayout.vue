<script setup>
// Importación de helpers de Inertia y Vue
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import Cookies from 'js-cookie'

// Props del componente (título de la pestaña del navegador)
defineProps({
  title: {
    type: String,
    default: 'AniValientes',
  },
})

// Acceso a los datos de la página y usuario actual
const { url, props } = usePage()
const user = reactive({ ...(props.auth?.user || {}) })

// Guardamos el usuario en una variable global para que otros componentes puedan acceder/actualizar
window.__layoutUser = user

// Estado del menú lateral (colapsado o expandido), persistido en cookies
const defaultCollapsed = Cookies.get('sidebar_state') === 'true'
const isCollapsed = ref(defaultCollapsed)

const toggleMenu = () => {
  isCollapsed.value = !isCollapsed.value
  Cookies.set('sidebar_state', isCollapsed.value.toString())
}

// Control del menú desplegable del perfil
const profileOpen = ref(false)
const toggleProfileMenu = () => (profileOpen.value = !profileOpen.value)

// Acción de logout
const logout = () => router.post(route('logout'))

// Menú lateral principal con icono, etiqueta y ruta
const menu = [
  { label: 'Dashboard', href: '/dashboard', icon: '📊' },
  { label: 'Denuncias', href: '/reports', icon: '📝' },
  { label: 'Configuración', href: '/settings', icon: '⚙️' },
]
</script>

<template>
  <!-- Título de la pestaña del navegador -->
  <Head :title="title" />

  <!-- Contenedor principal -->
  <div class="min-h-screen flex bg-[#FFEAC2] text-[#003D34] font-sans">
    
    <!-- Menú lateral -->
    <aside :class="[
      'bg-[#EBD19D] flex flex-col transition-all duration-300 ease-in-out shadow-lg',
      isCollapsed ? 'w-20 items-center' : 'w-64 px-6'
    ]">
      
      <!-- Encabezado del menú -->
      <div>
        <!-- Vista expandida -->
        <div v-if="!isCollapsed" class="flex items-center justify-between h-20 px-4 mb-6 border-b border-[#d4ba8c]">
          <img src="/images/Logo_Anivalientes_Horizontal_transparente.png" alt="AniValientes" class="w-32 h-auto" />
          <button @click="toggleMenu" class="p-2 rounded-md bg-[#004C43] text-white hover:bg-[#006b61]" title="Colapsar menú">
            <!-- Icono de hamburguesa -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>

        <!-- Vista colapsada -->
        <div v-else class="flex flex-col items-center gap-2 py-4 border-b border-[#d4ba8c]">
          <img src="/images/Isotipo_Anivalientes_transparente.png" alt="Logo compacto" class="h-10 w-auto" />
          <button @click="toggleMenu" class="p-2 rounded-md bg-[#004C43] text-white hover:bg-[#006b61]" title="Expandir menú">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Navegación lateral -->
      <nav class="flex flex-col gap-4 w-full">
        <Link
          v-for="item in menu"
          :key="item.href"
          :href="item.href"
          class="flex items-center transition-all duration-200"
          :class="[
            isCollapsed
              ? 'justify-center py-2'
              : 'gap-3 px-4 py-2 rounded-lg text-sm font-medium tracking-wide w-full',
            url.startsWith(item.href) && !isCollapsed ? 'bg-[#003D34] text-white shadow-md' : '',
            !url.startsWith(item.href) && !isCollapsed ? 'text-[#003D34] hover:bg-[#dcc089]' : ''
          ]"
        >
          <!-- Icono del ítem -->
          <span
            class="text-xl flex items-center justify-center rounded-md transition-all duration-200"
            :class="[
              isCollapsed && url.startsWith(item.href) ? 'bg-[#003D34] text-white p-2 shadow-md' : '',
              isCollapsed && !url.startsWith(item.href) ? 'text-[#003D34]' : ''
            ]"
          >
            {{ item.icon }}
          </span>

          <!-- Texto solo en vista expandida -->
          <span v-if="!isCollapsed" class="whitespace-nowrap" :class="url.startsWith(item.href) ? 'font-bold' : ''">
            {{ item.label }}
          </span>
        </Link>
      </nav>
    </aside>

    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col">
      
      <!-- Cabecera superior -->
      <header class="flex justify-end items-center p-4 border-b border-[#ccc] bg-[#FFEAC2] relative">
        
        <!-- Botón de notificaciones (placeholder) -->
        <button class="mr-6" title="Notificaciones">
          <svg class="w-6 h-6 text-[#003D34]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
            stroke-linecap="round" stroke-linejoin="round">
            <path
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-5-5.917V5a2 2 0 00-4 0v.083A6.002 6.002 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </button>

        <!-- Menú desplegable del usuario -->
        <div class="relative">
          <button @click="toggleProfileMenu" class="flex items-center gap-2 focus:outline-none hover:underline">
            <!-- Imagen del perfil o por defecto -->
            <img
              :src="user.photo_path
                ? `/storage/${user.photo_path}?v=${Date.now()}`
                : '/images/def_profile.png'"
              alt=""
              class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-300"
            />
            <span class="text-sm font-semibold">{{ user.first_name }} {{ user.last_name }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Opciones del menú de usuario -->
          <div v-if="profileOpen" class="absolute right-0 mt-2 w-48 bg-white rounded shadow z-50 border">
            <Link href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-100">Editar perfil</Link>
            <button @click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
              Cerrar sesión
            </button>
          </div>
        </div>
      </header>

      <!-- Contenido dinámico de cada vista -->
      <main class="flex-1 p-10">
        <slot />
      </main>
    </div>
  </div>
</template>
