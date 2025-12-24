<div
    x-data="{
        showPicker: false,
        hoverTimeout: null,
        reactions: @js($reactions),
        userReaction: @entangle('userReaction'),
        animated: @js($animated),
        lottieInstances: {},

        openPicker() {
            clearTimeout(this.hoverTimeout);
            this.showPicker = true;
        },

        closePicker() {
            this.hoverTimeout = setTimeout(() => {
                this.showPicker = false;
            }, 300);
        },

        keepOpen() {
            clearTimeout(this.hoverTimeout);
        },

        async playAnimation(reaction, el) {
            if (!this.animated) return;

            const lottieFile = this.reactions[reaction]?.lottie;
            if (!lottieFile || typeof lottie === 'undefined') return;

            // Clear any existing animation
            if (this.lottieInstances[reaction]) {
                this.lottieInstances[reaction].destroy();
            }

            const container = el.querySelector('.lottie-container');
            if (!container) return;

            this.lottieInstances[reaction] = lottie.loadAnimation({
                container: container,
                renderer: 'svg',
                loop: false,
                autoplay: true,
                path: '/vendor/sb-likes/lottie/' + lottieFile
            });
        }
    }"
    class="sb-reactions inline-flex items-center gap-2"
>
    {{-- Main Button --}}
    <div class="relative" @mouseenter="openPicker()" @mouseleave="closePicker()">
        {{-- Reaction Picker (appears on hover) --}}
        <div
            x-show="showPicker"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
            @mouseenter="keepOpen()"
            @mouseleave="closePicker()"
            class="absolute z-50 {{ $pickerPosition === 'bottom' ? 'top-full mt-1' : 'bottom-full mb-1' }} left-0 flex items-center bg-white rounded-full shadow-lg border border-gray-200 {{ $sizeClasses['picker'] }}"
            style="display: none;"
        >
            @foreach($reactions as $key => $reaction)
                <button
                    type="button"
                    wire:click="react('{{ $key }}')"
                    x-ref="reaction_{{ $key }}"
                    @click="playAnimation('{{ $key }}', $refs.reaction_{{ $key }})"
                    class="relative flex items-center justify-center rounded-full hover:scale-125 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
                    :class="userReaction === '{{ $key }}' ? 'ring-2 ring-offset-1' : ''"
                    :style="userReaction === '{{ $key }}' ? 'ring-color: {{ $reaction['color'] }}' : ''"
                    title="{{ $reaction['name'] }}"
                >
                    <span class="{{ $sizeClasses['emoji'] }} flex items-center justify-center text-2xl">
                        {{ $reaction['emoji'] }}
                    </span>
                    <div class="lottie-container absolute inset-0 pointer-events-none"></div>
                </button>
            @endforeach
        </div>

        {{-- Main Like Button --}}
        <button
            type="button"
            wire:click="quickReact"
            class="inline-flex items-center gap-2 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $sizeClasses['button'] }}"
            :class="userReaction
                ? 'text-white hover:opacity-90'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            :style="userReaction ? 'background-color: ' + (reactions[userReaction]?.color || '#2078f4') : ''"
        >
            {{-- Icon or Emoji --}}
            <span x-show="!userReaction" style="display: inline-flex; align-items: center; font-size: 16px;">
                👍
            </span>
            <span x-show="userReaction" class="text-lg" x-text="reactions[userReaction]?.emoji || '👍'"></span>

            {{-- Label --}}
            <span x-show="!userReaction">Like</span>
            <span x-show="userReaction" x-text="reactions[userReaction]?.name || 'Like'"></span>
        </button>
    </div>

    {{-- Reaction Summary --}}
    @if($showSummary && count($topReactions) > 0)
        <div class="flex items-center -space-x-1">
            @foreach($topReactions as $top)
                @if(isset($reactions[$top['reaction']]))
                    <span
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white border border-gray-200 text-xs"
                        title="{{ $reactions[$top['reaction']]['name'] }}: {{ $top['count'] }}"
                    >
                        {{ $reactions[$top['reaction']]['emoji'] }}
                    </span>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Count --}}
    @if($showCounts && $totalCount > 0)
        <span class="text-sm text-gray-500">
            {{ number_format($totalCount) }}
        </span>
    @endif
</div>

@once
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" integrity="sha512-jEnuDt6jfAi0PLuPYCFzqVFzpF0pPfDdIBmFx0mIeKrAcKCF9ruHfHm03wFPK5fJPo3mL0qQRsYeGMdx6kQlkA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush
@endonce
