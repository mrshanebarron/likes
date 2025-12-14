<?php

namespace MrShaneBarron\Likes\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class Reactions extends Component
{
    // Model reference
    public string $modelType;
    public int $modelId;

    // Display options
    public bool $showCounts = true;
    public bool $showSummary = true;
    public string $pickerPosition = 'top';
    public string $size = 'md';
    public bool $animated = true;
    public bool $guestReactions = false;

    // Available reactions (can override config)
    public array $reactions = [];

    // State
    public ?string $userReaction = null;
    public int $totalCount = 0;
    public array $reactionCounts = [];
    public array $topReactions = [];

    public function mount(
        Model $model,
        ?bool $showCounts = null,
        ?bool $showSummary = null,
        string $pickerPosition = 'top',
        string $size = 'md',
        bool $animated = true,
        bool $guestReactions = false,
        array $reactions = []
    ): void {
        $this->modelType = get_class($model);
        $this->modelId = $model->getKey();

        $this->showCounts = $showCounts ?? config('ld-likes.show_counts', true);
        $this->showSummary = $showSummary ?? config('ld-likes.show_summary', true);
        $this->pickerPosition = $pickerPosition;
        $this->size = $size;
        $this->animated = $animated;
        $this->guestReactions = $guestReactions;
        $this->reactions = !empty($reactions) ? $reactions : config('ld-likes.reactions', []);

        $this->loadState();
    }

    protected function getModel(): Model
    {
        return $this->modelType::findOrFail($this->modelId);
    }

    protected function loadState(): void
    {
        $model = $this->getModel();

        // Get user's current reaction
        $userReaction = $model->getUserReaction();
        $this->userReaction = $userReaction?->reaction;

        // Get counts
        $this->totalCount = $model->getTotalReactions();
        $this->reactionCounts = $model->getReactionCounts()->toArray();
        $this->topReactions = $model->getTopReactions(3)->toArray();
    }

    public function react(string $reaction): void
    {
        // Check if guest reactions allowed
        if (!auth()->check() && !$this->guestReactions) {
            $this->dispatch('login-required');
            return;
        }

        // Validate reaction type
        if (!isset($this->reactions[$reaction])) {
            return;
        }

        $model = $this->getModel();
        $result = $model->toggleReaction($reaction);

        $this->loadState();

        $this->dispatch('reaction-updated', [
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
            'reaction' => $this->userReaction,
            'totalCount' => $this->totalCount,
        ]);
    }

    public function quickReact(): void
    {
        $defaultReaction = config('ld-likes.default_reaction', 'like');
        $this->react($defaultReaction);
    }

    public function render(): View
    {
        return view('ld-likes::components.reactions', [
            'sizeClasses' => $this->getSizeClasses(),
        ]);
    }

    protected function getSizeClasses(): array
    {
        return match ($this->size) {
            'xs' => [
                'button' => 'px-2 py-1 text-xs',
                'icon' => 'w-3 h-3',
                'picker' => 'p-1 gap-1',
                'emoji' => 'w-5 h-5',
            ],
            'sm' => [
                'button' => 'px-3 py-1.5 text-sm',
                'icon' => 'w-4 h-4',
                'picker' => 'p-1.5 gap-1.5',
                'emoji' => 'w-6 h-6',
            ],
            'lg' => [
                'button' => 'px-5 py-3 text-base',
                'icon' => 'w-6 h-6',
                'picker' => 'p-3 gap-3',
                'emoji' => 'w-10 h-10',
            ],
            'xl' => [
                'button' => 'px-6 py-4 text-lg',
                'icon' => 'w-7 h-7',
                'picker' => 'p-4 gap-4',
                'emoji' => 'w-12 h-12',
            ],
            default => [ // md
                'button' => 'px-4 py-2 text-sm',
                'icon' => 'w-5 h-5',
                'picker' => 'p-2 gap-2',
                'emoji' => 'w-8 h-8',
            ],
        };
    }
}
