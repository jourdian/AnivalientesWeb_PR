<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Iniciar sesión" />

  <div class="min-h-screen flex flex-col items-center justify-center bg-[#F5E1B9] px-6">
    <!-- Logotipo -->
    <img src="/images/Logo_Anivalientes_transparente.png" alt="Logo AniValientes" class="w-48 h-auto mb-6" />



    <!-- Formulario -->
    <form @submit.prevent="submit" class="w-full max-w-sm space-y-6">
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-[#003D34] mb-1">Correo electrónico</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          autofocus
          class="w-full rounded-md p-2 shadow-sm border border-[#003D34] bg-white text-[#003D34] placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#003D34]"
        />
        <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-[#003D34] mb-1">Contraseña</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          autocomplete="current-password"
          class="w-full rounded-md p-2 shadow-sm border border-[#003D34] bg-white text-[#003D34] placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#003D34]"
        />
        <div v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}</div>
      </div>

      <!-- Recordarme + Link -->
      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center text-[#003D34]">
          <input type="checkbox" v-model="form.remember" class="rounded border-[#003D34] text-[#003D34] focus:ring-[#003D34]" />
          <span class="ml-2">Recordarme</span>
        </label>
<!--         <Link :href="route('password.request')" class="text-[#003D34] hover:underline">¿Olvidaste tu contraseña?</Link>
 -->      </div>

      <!-- Botón -->
      <div>
        <button
          type="submit"
          class="w-full py-2 px-4 rounded-md bg-[#003D34] text-white font-semibold hover:bg-[#012622] transition"
          :disabled="form.processing"
        >
          Iniciar sesión
        </button>
      </div>
    </form>
  </div>
</template>
