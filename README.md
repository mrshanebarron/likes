# Likes

A like button and reactions component for Laravel applications. Heart button with optional emoji reactions. Works with Livewire and Vue 3.

## Installation

```bash
composer require mrshanebarron/likes
```

## Livewire Usage

### Basic Usage

```blade
<livewire:sb-likes wire:model="liked" :count="$likesCount" />
```

### With Reactions

```blade
<livewire:sb-likes
    wire:model="liked"
    :count="$likesCount"
    :show-reactions="true"
/>
```

### Livewire Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `wire:model` | boolean | `false` | Liked state |
| `count` | number | `0` | Like count |
| `show-count` | boolean | `true` | Display count |
| `show-reactions` | boolean | `false` | Show emoji reactions |

## Vue 3 Usage

### Setup

```javascript
import { SbLikes } from './vendor/sb-likes';
app.component('SbLikes', SbLikes);
```

### Basic Usage

```vue
<template>
  <SbLikes v-model="liked" :count="likesCount" @like="handleLike" />
</template>

<script setup>
import { ref } from 'vue';

const liked = ref(false);
const likesCount = ref(42);

const handleLike = () => {
  likesCount.value++;
  // Save to backend
};
</script>
```

### With Reactions

```vue
<template>
  <SbLikes
    v-model="liked"
    :count="reactions.total"
    :show-reactions="true"
    @react="handleReaction"
  />
</template>

<script setup>
import { ref } from 'vue';

const liked = ref(false);
const reactions = ref({ total: 156, like: 100, love: 30, haha: 26 });

const handleReaction = (type) => {
  console.log('Reacted with:', type);
};
</script>
```

### Custom Reactions

```vue
<template>
  <SbLikes
    v-model="liked"
    :count="count"
    :show-reactions="true"
    :reactions="customReactions"
    @react="handleReaction"
  />
</template>

<script setup>
const customReactions = [
  { type: 'fire', emoji: '🔥', label: 'Fire' },
  { type: 'rocket', emoji: '🚀', label: 'Rocket' },
  { type: 'clap', emoji: '👏', label: 'Clap' },
  { type: 'heart', emoji: '💖', label: 'Heart' }
];
</script>
```

### Post Card Example

```vue
<template>
  <article class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-2">{{ post.title }}</h2>
    <p class="text-gray-600 mb-4">{{ post.excerpt }}</p>

    <div class="flex items-center justify-between pt-4 border-t">
      <SbLikes
        v-model="post.liked"
        :count="post.likesCount"
        @like="likePost"
        @unlike="unlikePost"
      />
      <span class="text-sm text-gray-500">{{ post.date }}</span>
    </div>
  </article>
</template>
```

### Vue Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modelValue` | Boolean | `false` | Liked state |
| `count` | Number | `0` | Total likes |
| `showCount` | Boolean | `true` | Display count |
| `showReactions` | Boolean | `false` | Show reaction options |
| `reactions` | Array | Default set | Custom reaction options |

### Events

| Event | Payload | Description |
|-------|---------|-------------|
| `update:modelValue` | `boolean` | Like state changed |
| `like` | - | Item liked |
| `unlike` | - | Item unliked |
| `react` | `type \| null` | Reaction selected/cleared |

## Default Reactions

```javascript
[
  { type: 'like', emoji: '👍', label: 'Like' },
  { type: 'love', emoji: '❤️', label: 'Love' },
  { type: 'haha', emoji: '😂', label: 'Haha' },
  { type: 'wow', emoji: '😮', label: 'Wow' },
  { type: 'sad', emoji: '😢', label: 'Sad' },
  { type: 'angry', emoji: '😠', label: 'Angry' }
]
```

## Features

- **Heart Button**: Toggle like with animation
- **Like Count**: Optional count display
- **Emoji Reactions**: Facebook-style reactions
- **Scale Animation**: Heart scales on like
- **Selected State**: Highlights selected reaction
- **Custom Reactions**: Define your own emojis

## Styling

Uses Tailwind CSS:
- Rounded pill button
- Red fill when liked
- Hover scale on reactions
- Ring highlight on selection
- Smooth transitions

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12
- Tailwind CSS 3.x

## License

MIT License
