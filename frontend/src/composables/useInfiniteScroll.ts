import { ref, onMounted, onUnmounted } from 'vue'

interface UseInfiniteScrollOptions {
  threshold?: number
  root?: Element | null
  rootMargin?: string
}

export function useInfiniteScroll(
  loadMore: () => Promise<void>,
  options: UseInfiniteScrollOptions = {}
) {
  const {
    threshold = 0.5,
    root = null,
    rootMargin = '0px'
  } = options

  const isLoading = ref(false)
  const hasMore = ref(true)
  const observer = ref<IntersectionObserver | null>(null)
  const target = ref<Element | null>(null)

  const handleIntersect = async (entries: IntersectionObserverEntry[]) => {
    const [entry] = entries
    if (entry.isIntersecting && !isLoading.value && hasMore.value) {
      isLoading.value = true
      try {
        await loadMore()
      } finally {
        isLoading.value = false
      }
    }
  }

  const setTarget = (element: Element | null) => {
    target.value = element
    if (element && observer.value) {
      observer.value.observe(element)
    }
  }

  onMounted(() => {
    observer.value = new IntersectionObserver(handleIntersect, {
      threshold,
      root,
      rootMargin
    })

    if (target.value) {
      observer.value.observe(target.value)
    }
  })

  onUnmounted(() => {
    if (observer.value) {
      observer.value.disconnect()
    }
  })

  return {
    isLoading,
    hasMore,
    setTarget,
    setHasMore: (value: boolean) => {
      hasMore.value = value
    }
  }
} 