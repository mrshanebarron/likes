<div
    x-data="{
        showPicker: false,
        hoverTimeout: null,
        reactions: @js($reactions),
        userReaction: @js($userReaction),
        totalCount: @js($totalCount),
        topReactions: @js($topReactions),
        animated: @js($animated),
        guestReactions: @js($guestReactions),
        modelType: @js($modelType),
        modelId: @js($modelId),
        reactEndpoint: @js($reactEndpoint),
        lottieInstances: {},
        loading: false,

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

        async react(reaction) {
            if (this.loading) return;

            // Check auth for non-guest reactions
            if (!this.guestReactions && !document.body.dataset.authenticated) {
                window.dispatchEvent(new CustomEvent('login-required'));
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(this.reactEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        model_type: this.modelType,
                        model_id: this.modelId,
                        reaction: reaction
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.userReaction = data.user_reaction;
                    this.totalCount = data.total_count;
                    this.topReactions = data.top_reactions;

                    window.dispatchEvent(new CustomEvent('reaction-updated', {
                        detail: {
                            modelType: this.modelType,
                            modelId: this.modelId,
                            reaction: this.userReaction,
                            totalCount: this.totalCount
                        }
                    }));
                }
            } catch (error) {
                console.error('Failed to update reaction:', error);
            } finally {
                this.loading = false;
                this.showPicker = false;
            }
        },

        quickReact() {
            const defaultReaction = '{{ config('sb-likes.default_reaction', 'like') }}';
            this.react(defaultReaction);
        },

        async playAnimation(reaction, el) {
            if (!this.animated) return;

            const lottieFile = this.reactions[reaction]?.lottie;
            if (!lottieFile || typeof lottie === 'undefined') return;

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
        {{-- Reaction Picker --}}
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
            <template x-for="(config, key) in reactions" :key="key">
                <button
                    type="button"
                    @click="react(key); playAnimation(key, $el)"
                    class="relative flex items-center justify-center rounded-full hover:scale-125 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
                    :class="userReaction === key ? 'ring-2 ring-offset-1' : ''"
                    :style="userReaction === key ? 'ring-color: ' + config.color : ''"
                    :title="config.name"
                >
                    <span class="{{ $sizeClasses['emoji'] }} flex items-center justify-center text-2xl" x-text="config.emoji"></span>
                    <div class="lottie-container absolute inset-0 pointer-events-none"></div>
                </button>
            </template>
        </div>

        {{-- Main Like Button --}}
        <button
            type="button"
            @click="quickReact()"
            :disabled="loading"
            class="inline-flex items-center gap-2 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $sizeClasses['button'] }} disabled:opacity-50"
            :class="userReaction
                ? 'text-white hover:opacity-90'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            :style="userReaction ? 'background-color: ' + (reactions[userReaction]?.color || '#2078f4') : ''"
        >
            {{-- Icon or Emoji --}}
            <span x-show="!userReaction" class="{{ $sizeClasses['icon'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m7.594 2.052a9.05 9.05 0 0 1-3.68 1.798m3.68-1.798a4.478 4.478 0 0 0 1.423.23h3.114a1.83 1.83 0 0 0 1.605-.729 11.95 11.95 0 0 0 2.649-7.521c0-.435-.023-.863-.068-1.285-.109-1.021-1.028-1.715-2.054-1.715h-3.126m-7.594 9.27c0 .806-.446 1.533-1.08 2.031a9.04 9.04 0 0 1-2.4 2.861c-.384.723-.956 1.35-1.715 1.653a4.498 4.498 0 0 1-1.672.322H2.75a.75.75 0 0 1-.75-.75v-1.25a2.25 2.25 0 0 1 2.25-2.25c1.152 0 2.243.26 3.218.723.558.266 1.282-.107 1.282-.725Z" />
                </svg>
            </span>
            <span x-show="userReaction" class="text-lg" x-text="reactions[userReaction]?.emoji || '👍'"></span>

            {{-- Label --}}
            <span x-show="!userReaction && !loading">Like</span>
            <span x-show="userReaction && !loading" x-text="reactions[userReaction]?.name || 'Like'"></span>
            <span x-show="loading">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </div>

    {{-- Reaction Summary --}}
    <template x-if="topReactions.length > 0">
        <div class="flex items-center -space-x-1" x-show="{{ $showSummary ? 'true' : 'false' }}">
            <template x-for="top in topReactions.slice(0, 3)" :key="top.reaction">
                <span
                    class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white border border-gray-200 text-xs"
                    :title="reactions[top.reaction]?.name + ': ' + top.count"
                    x-text="reactions[top.reaction]?.emoji"
                ></span>
            </template>
        </div>
    </template>

    {{-- Count --}}
    <span
        x-show="{{ $showCounts ? 'true' : 'false' }} && totalCount > 0"
        class="text-sm text-gray-500"
        x-text="totalCount.toLocaleString()"
    ></span>
</div>

@once
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" integrity="sha512-jEnuDt6jfAi0PLuPYCFzqVFzpF0pPfDdIBmFx0mIeKrAcKCF9ruHfHm03wFPK5fJPo3mL0qQRsYeGMdx6kQlkA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush
@endonce
