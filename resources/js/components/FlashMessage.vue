<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const flash = computed(() => usePage().props.flash || {})
const visible = ref(true)

watch(flash, (val) => {
  visible.value = true
  if (Object.values(val).some(Boolean)) {
    setTimeout(() => {
      visible.value = false
    }, 4000)
  }
})
</script>

<template>
  <div v-if="visible" class="space-y-2 transition-opacity duration-300">
    <div
      v-if="flash.success"
      class="p-4 bg-green-100 text-green-800 border border-green-300 rounded"
    >
      {{ flash.success }}
    </div>
    <div
      v-if="flash.error"
      class="p-4 bg-red-100 text-red-800 border border-red-300 rounded"
    >
      {{ flash.error }}
    </div>
    <div
      v-if="flash.warning"
      class="p-4 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded"
    >
      {{ flash.warning }}
    </div>
    <div
      v-if="flash.info"
      class="p-4 bg-blue-100 text-blue-800 border border-blue-300 rounded"
    >
      {{ flash.info }}
    </div>
  </div>
</template>
