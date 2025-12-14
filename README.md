# Laravel Design Likes

Facebook-style reaction components with animated Lottie emojis for Laravel. Supports Livewire, Blade, and Vue 3.

## Installation

```bash
composer require laraveldesign/likes
```

For Vue components:
```bash
npm install @laraveldesign/likes
```

Publish and run migrations:
```bash
php artisan vendor:publish --tag=ld-likes-migrations
php artisan migrate
```

Publish Lottie assets (for animations):
```bash
php artisan vendor:publish --tag=ld-likes-assets
```

## Setup

Add the `Reactable` trait to any model you want to support reactions:

```php
use LaravelDesign\Likes\Traits\Reactable;

class Post extends Model
{
    use Reactable;
}
```

## Usage

### Livewire Component

```blade
<livewire:ld-reactions :model="$post" />

{{-- With options --}}
<livewire:ld-reactions
    :model="$post"
    size="lg"
    picker-position="bottom"
    :show-counts="true"
    :show-summary="true"
    :animated="true"
    :guest-reactions="false"
/>
```

### Blade Component

```blade
<x-ld-reactions :model="$post" />

<x-ld-reactions
    :model="$post"
    size="sm"
    :show-counts="true"
    :guest-reactions="true"
/>
```

### Vue 3 Component

```vue
<script setup>
import { LdReactions } from '@laraveldesign/likes'
</script>

<template>
  <LdReactions
    model-type="App\Models\Post"
    :model-id="post.id"
    :initial-reaction="post.user_reaction"
    :initial-count="post.reactions_count"
    :initial-top-reactions="post.top_reactions"
    @reaction-updated="handleReaction"
    @login-required="showLoginModal"
  />
</template>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `model` | Model | required | The Eloquent model to attach reactions to |
| `size` | string | `'md'` | Button size: `xs`, `sm`, `md`, `lg`, `xl` |
| `pickerPosition` | string | `'top'` | Picker position: `top`, `bottom` |
| `showCounts` | boolean | `true` | Show total reaction count |
| `showSummary` | boolean | `true` | Show top 3 reaction emoji icons |
| `animated` | boolean | `true` | Use Lottie animations |
| `guestReactions` | boolean | `false` | Allow non-authenticated users to react |
| `reactions` | array | config | Override available reactions |

## Available Reactions

Default reactions (Facebook-style):

- `like` - 👍 Thumbs up (blue)
- `love` - ❤️ Heart (red)
- `haha` - 😂 Laughing (yellow)
- `wow` - 😮 Surprised (yellow)
- `sad` - 😢 Sad (yellow)
- `angry` - 😡 Angry (orange)

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=ld-likes-config
```

### Custom Reactions

```php
// config/ld-likes.php
'reactions' => [
    'thumbsup' => [
        'name' => 'Thumbs Up',
        'emoji' => '👍',
        'lottie' => 'thumbsup.json', // optional
        'color' => '#2078f4',
    ],
    'heart' => [
        'name' => 'Heart',
        'emoji' => '❤️',
        'color' => '#f33e58',
    ],
    // Add more reactions...
],
```

## Lottie Animations

The package includes some Lottie animations. For the full animated experience:

1. Get free animations from [LottieFiles](https://lottiefiles.com)
2. Place JSON files in `public/vendor/ld-likes/lottie/`
3. Name them to match config: `like.json`, `love.json`, etc.

If animations are not found, the component falls back to static emoji display.

## Events

### Livewire Events

```javascript
Livewire.on('reaction-updated', (data) => {
    console.log(data.modelType, data.modelId, data.reaction, data.totalCount);
});

Livewire.on('login-required', () => {
    // Show login modal
});
```

### Vue Events

```vue
<LdReactions
  @reaction-updated="({ reaction, totalCount }) => handleUpdate(reaction, totalCount)"
  @login-required="showLoginModal"
  @error="handleError"
/>
```

## API Endpoints

For Blade component (non-Livewire), the package provides:

- `POST /ld-likes/react` - Toggle a reaction
- `GET /ld-likes/status` - Get reaction status for a model

## Model Methods

The `Reactable` trait adds these methods:

```php
// Add a reaction
$post->react('like', $userId);

// Remove a reaction
$post->unreact($userId);

// Toggle reaction (add or remove)
$post->toggleReaction('love', $userId);

// Check if user reacted
$post->hasReacted($userId);

// Get user's reaction
$reaction = $post->getUserReaction($userId);

// Get all reactions
$reactions = $post->reactions;

// Get counts by type
$counts = $post->getReactionCounts(); // ['like' => 10, 'love' => 5, ...]

// Get total count
$total = $post->getTotalReactions();

// Get top 3 reactions
$top = $post->getTopReactions(3);
```

## License

MIT
