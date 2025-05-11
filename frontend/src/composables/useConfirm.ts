import { ref } from 'vue'

interface ConfirmOptions {
  title: string
  message: string
  type?: 'danger' | 'warning'
  confirmText?: string
  cancelText?: string
}

export function useConfirm() {
  const isOpen = ref(false)
  const options = ref<ConfirmOptions>({
    title: '',
    message: '',
    type: 'warning',
    confirmText: 'Подтвердить',
    cancelText: 'Отмена'
  })

  let resolvePromise: ((value: boolean) => void) | null = null

  const confirm = (confirmOptions: ConfirmOptions): Promise<boolean> => {
    options.value = {
      ...options.value,
      ...confirmOptions
    }
    isOpen.value = true

    return new Promise((resolve) => {
      resolvePromise = resolve
    })
  }

  const handleConfirm = () => {
    if (resolvePromise) {
      resolvePromise(true)
      resolvePromise = null
    }
    isOpen.value = false
  }

  const handleCancel = () => {
    if (resolvePromise) {
      resolvePromise(false)
      resolvePromise = null
    }
    isOpen.value = false
  }

  return {
    isOpen,
    options,
    confirm,
    handleConfirm,
    handleCancel
  }
} 