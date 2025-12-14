<template>
  <div class="flex items-center gap-2">
    <button
      @click="toggle"
      :class="['flex items-center gap-1 px-3 py-1.5 rounded-full border transition-colors', liked ? 'bg-red-50 border-red-200 text-red-600' : 'border-gray-200 hover:bg-gray-50 text-gray-600']"
    >
      <svg :class="['w-5 h-5 transition-transform', liked && 'scale-110']" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
      </svg>
      <span v-if="showCount" class="text-sm font-medium">{{ count }}</span>
    </button>

    <div v-if="showReactions && reactions.length" class="flex items-center -space-x-1">
      <button
        v-for="reaction in reactions"
        :key="reaction.type"
        @click="react(reaction.type)"
        :class="['w-8 h-8 rounded-full flex items-center justify-center text-lg transition-transform hover:scale-125 hover:z-10', selectedReaction === reaction.type && 'ring-2 ring-blue-500']"
        :title="reaction.label"
      >
        {{ reaction.emoji }}
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';

export default {
  name: 'LdLikes',
  props: {
    modelValue: { type: Boolean, default: false },
    count: { type: Number, default: 0 },
    showCount: { type: Boolean, default: true },
    showReactions: { type: Boolean, default: false },
    reactions: {
      type: Array,
      default: () => [
        { type: 'like', emoji: '👍', label: 'Like' },
        { type: 'love', emoji: '❤️', label: 'Love' },
        { type: 'haha', emoji: '😂', label: 'Haha' },
        { type: 'wow', emoji: '😮', label: 'Wow' },
        { type: 'sad', emoji: '😢', label: 'Sad' },
        { type: 'angry', emoji: '😠', label: 'Angry' }
      ]
    }
  },
  emits: ['update:modelValue', 'like', 'unlike', 'react'],
  setup(props, { emit }) {
    const liked = computed(() => props.modelValue);
    const selectedReaction = ref(null);

    const toggle = () => {
      const newValue = !liked.value;
      emit('update:modelValue', newValue);
      emit(newValue ? 'like' : 'unlike');
    };

    const react = (type) => {
      selectedReaction.value = selectedReaction.value === type ? null : type;
      emit('react', selectedReaction.value);
    };

    return { liked, selectedReaction, toggle, react };
  }
};
</script>
