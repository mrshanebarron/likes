<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

interface ReactionConfig {
  name: string
  emoji: string
  lottie?: string
  color: string
}

interface TopReaction {
  reaction: string
  count: number
}

interface Props {
  modelType: string
  modelId: number
  reactions?: Record<string, ReactionConfig>
  initialReaction?: string | null
  initialCount?: number
  initialTopReactions?: TopReaction[]
  showCounts?: boolean
  showSummary?: boolean
  pickerPosition?: 'top' | 'bottom'
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  animated?: boolean
  guestReactions?: boolean
  reactEndpoint?: string
  lottieBasePath?: string
}

const props = withDefaults(defineProps<Props>(), {
  reactions: () => ({
    like: { name: 'Like', emoji: '👍', lottie: 'like.json', color: '#2078f4' },
    love: { name: 'Love', emoji: '❤️', lottie: 'love.json', color: '#f33e58' },
    haha: { name: 'Haha', emoji: '😂', lottie: 'haha.json', color: '#f7b125' },
    wow: { name: 'Wow', emoji: '😮', lottie: 'wow.json', color: '#f7b125' },
    sad: { name: 'Sad', emoji: '😢', lottie: 'sad.json', color: '#f7b125' },
    angry: { name: 'Angry', emoji: '😡', lottie: 'angry.json', color: '#e9710f' },
  }),
  initialReaction: null,
  initialCount: 0,
  initialTopReactions: () => [],
  showCounts: true,
  showSummary: true,
  pickerPosition: 'top',
  size: 'md',
  animated: true,
  guestReactions: false,
  reactEndpoint: '/ld-likes/react',
  lottieBasePath: '/vendor/ld-likes/lottie/',
})

const emit = defineEmits<{
  (e: 'reaction-updated', data: { reaction: string | null; totalCount: number }): void
  (e: 'login-required'): void
  (e: 'error', error: Error): void
}>()

// State
const showPicker = ref(false)
const userReaction = ref<string | null>(props.initialReaction)
const totalCount = ref(props.initialCount)
const topReactions = ref<TopReaction[]>(props.initialTopReactions)
const loading = ref(false)
let hoverTimeout: ReturnType<typeof setTimeout> | null = null

// Computed
const sizeClasses = computed(() => {
  const sizes = {
    xs: { button: 'px-2 py-1 text-xs', icon: 'w-3 h-3', picker: 'p-1 gap-1', emoji: 'w-5 h-5' },
    sm: { button: 'px-3 py-1.5 text-sm', icon: 'w-4 h-4', picker: 'p-1.5 gap-1.5', emoji: 'w-6 h-6' },
    md: { button: 'px-4 py-2 text-sm', icon: 'w-5 h-5', picker: 'p-2 gap-2', emoji: 'w-8 h-8' },
    lg: { button: 'px-5 py-3 text-base', icon: 'w-6 h-6', picker: 'p-3 gap-3', emoji: 'w-10 h-10' },
    xl: { button: 'px-6 py-4 text-lg', icon: 'w-7 h-7', picker: 'p-4 gap-4', emoji: 'w-12 h-12' },
  }
  return sizes[props.size]
})

const buttonStyle = computed(() => {
  if (userReaction.value && props.reactions[userReaction.value]) {
    return { backgroundColor: props.reactions[userReaction.value].color }
  }
  return {}
})

const currentReactionConfig = computed(() => {
  if (userReaction.value) {
    return props.reactions[userReaction.value] || null
  }
  return null
})

// Methods
function openPicker() {
  if (hoverTimeout) clearTimeout(hoverTimeout)
  showPicker.value = true
}

function closePicker() {
  hoverTimeout = setTimeout(() => {
    showPicker.value = false
  }, 300)
}

function keepOpen() {
  if (hoverTimeout) clearTimeout(hoverTimeout)
}

async function react(reaction: string) {
  if (loading.value) return

  // Check auth
  if (!props.guestReactions && !(window as any).__authenticated) {
    emit('login-required')
    return
  }

  loading.value = true

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

    const response = await fetch(props.reactEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        model_type: props.modelType,
        model_id: props.modelId,
        reaction: reaction,
      }),
    })

    const data = await response.json()

    if (data.success) {
      userReaction.value = data.user_reaction
      totalCount.value = data.total_count
      topReactions.value = data.top_reactions

      emit('reaction-updated', {
        reaction: userReaction.value,
        totalCount: totalCount.value,
      })
    }
  } catch (error) {
    emit('error', error as Error)
  } finally {
    loading.value = false
    showPicker.value = false
  }
}

function quickReact() {
  react('like')
}

// Lottie animation helper
let lottie: any = null

onMounted(async () => {
  if (props.animated && typeof window !== 'undefined') {
    try {
      lottie = await import('lottie-web')
    } catch {
      // Lottie not available
    }
  }
})

function playAnimation(reaction: string, el: HTMLElement) {
  if (!props.animated || !lottie || !props.reactions[reaction]?.lottie) return

  const container = el.querySelector('.lottie-container') as HTMLElement
  if (!container) return

  lottie.default.loadAnimation({
    container,
    renderer: 'svg',
    loop: false,
    autoplay: true,
    path: props.lottieBasePath + props.reactions[reaction].lottie,
  })
}
</script>

<template>
  <div class="ld-reactions inline-flex items-center gap-2">
    <!-- Main Button -->
    <div
      class="relative"
      @mouseenter="openPicker"
      @mouseleave="closePicker"
    >
      <!-- Reaction Picker -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95 translate-y-1"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-1"
      >
        <div
          v-show="showPicker"
          @mouseenter="keepOpen"
          @mouseleave="closePicker"
          class="absolute z-50 left-0 flex items-center bg-white rounded-full shadow-lg border border-gray-200"
          :class="[
            pickerPosition === 'bottom' ? 'top-full mt-1' : 'bottom-full mb-1',
            sizeClasses.picker
          ]"
        >
          <button
            v-for="(config, key) in reactions"
            :key="key"
            type="button"
            @click="react(key as string); playAnimation(key as string, $event.currentTarget as HTMLElement)"
            class="relative flex items-center justify-center rounded-full hover:scale-125 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
            :class="{ 'ring-2 ring-offset-1': userReaction === key }"
            :style="userReaction === key ? { ringColor: config.color } : {}"
            :title="config.name"
          >
            <span :class="[sizeClasses.emoji, 'flex items-center justify-center text-2xl']">
              {{ config.emoji }}
            </span>
            <div class="lottie-container absolute inset-0 pointer-events-none"></div>
          </button>
        </div>
      </Transition>

      <!-- Main Like Button -->
      <button
        type="button"
        @click="quickReact"
        :disabled="loading"
        class="inline-flex items-center gap-2 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50"
        :class="[
          sizeClasses.button,
          userReaction ? 'text-white hover:opacity-90' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
        :style="buttonStyle"
      >
        <!-- Icon or Emoji -->
        <span v-if="!userReaction" :class="sizeClasses.icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m7.594 2.052a9.05 9.05 0 0 1-3.68 1.798m3.68-1.798a4.478 4.478 0 0 0 1.423.23h3.114a1.83 1.83 0 0 0 1.605-.729 11.95 11.95 0 0 0 2.649-7.521c0-.435-.023-.863-.068-1.285-.109-1.021-1.028-1.715-2.054-1.715h-3.126m-7.594 9.27c0 .806-.446 1.533-1.08 2.031a9.04 9.04 0 0 1-2.4 2.861c-.384.723-.956 1.35-1.715 1.653a4.498 4.498 0 0 1-1.672.322H2.75a.75.75 0 0 1-.75-.75v-1.25a2.25 2.25 0 0 1 2.25-2.25c1.152 0 2.243.26 3.218.723.558.266 1.282-.107 1.282-.725Z" />
          </svg>
        </span>
        <span v-else class="text-lg">{{ currentReactionConfig?.emoji || '👍' }}</span>

        <!-- Label -->
        <span v-if="!loading">
          {{ userReaction ? currentReactionConfig?.name || 'Like' : 'Like' }}
        </span>
        <svg v-else class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </button>
    </div>

    <!-- Reaction Summary -->
    <div
      v-if="showSummary && topReactions.length > 0"
      class="flex items-center -space-x-1"
    >
      <span
        v-for="top in topReactions.slice(0, 3)"
        :key="top.reaction"
        class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white border border-gray-200 text-xs"
        :title="`${reactions[top.reaction]?.name || top.reaction}: ${top.count}`"
      >
        {{ reactions[top.reaction]?.emoji || '👍' }}
      </span>
    </div>

    <!-- Count -->
    <span
      v-if="showCounts && totalCount > 0"
      class="text-sm text-gray-500"
    >
      {{ totalCount.toLocaleString() }}
    </span>
  </div>
</template>
