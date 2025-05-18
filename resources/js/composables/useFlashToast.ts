import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { SharedData } from '@/types'

export function useFlashToast(duration = 4000) {
  const toastMessage = ref('')
  const toastType = ref<'success' | 'error' | 'warning' | 'info'>('success')
  const showToast = ref(false)

  const page = usePage<SharedData>()

  onMounted(() => {
    const flash = page.props.flash
    if (flash?.success) {
      toastType.value = 'success'
      toastMessage.value = flash.success
      showToast.value = true
    } else if (flash?.error) {
      toastType.value = 'error'
      toastMessage.value = flash.error
      showToast.value = true
    } else if (flash?.warning) {
      toastType.value = 'warning'
      toastMessage.value = flash.warning
      showToast.value = true
    } else if (flash?.info) {
      toastType.value = 'info'
      toastMessage.value = flash.info
      showToast.value = true
    }

    if (showToast.value) {
      setTimeout(() => {
        showToast.value = false
      }, duration)
    }
  })

  return {
    toastMessage,
    toastType,
    showToast,
  }
}
