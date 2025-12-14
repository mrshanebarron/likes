<?php

namespace MrShaneBarron\Likes\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Reactions extends Component
{
    public string $modelType;
    public int $modelId;
    public bool $showCounts;
    public bool $showSummary;
    public string $pickerPosition;
    public string $size;
    public bool $animated;
    public bool $guestReactions;
    public array $reactions;
    public ?string $userReaction;
    public int $totalCount;
    public array $reactionCounts;
    public array $topReactions;

    // API endpoints
    public string $reactEndpoint;
    public string $statusEndpoint;

    public function __construct(
        Model $model,
        ?bool $showCounts = null,
        ?bool $showSummary = null,
        string $pickerPosition = 'top',
        string $size = 'md',
        bool $animated = true,
        bool $guestReactions = false,
        array $reactions = [],
        ?string $reactEndpoint = null,
        ?string $statusEndpoint = null
    ) {
        $this->modelType = get_class($model);
        $this->modelId = $model->getKey();

        $this->showCounts = $showCounts ?? config('sb-likes.show_counts', true);
        $this->showSummary = $showSummary ?? config('sb-likes.show_summary', true);
        $this->pickerPosition = $pickerPosition;
        $this->size = $size;
        $this->animated = $animated;
        $this->guestReactions = $guestReactions;
        $this->reactions = !empty($reactions) ? $reactions : config('sb-likes.reactions', []);

        // API endpoints for JavaScript interactions
        $this->reactEndpoint = $reactEndpoint ?? route('sb-likes.react');
        $this->statusEndpoint = $statusEndpoint ?? route('sb-likes.status');

        $this->loadState($model);
    }

    protected function loadState(Model $model): void
    {
        // Check if model uses Reactable trait
        if (!method_exists($model, 'getUserReaction')) {
            $this->userReaction = null;
            $this->totalCount = 0;
            $this->reactionCounts = [];
            $this->topReactions = [];
            return;
        }

        $userReaction = $model->getUserReaction();
        $this->userReaction = $userReaction?->reaction;
        $this->totalCount = $model->getTotalReactions();
        $this->reactionCounts = $model->getReactionCounts()->toArray();
        $this->topReactions = $model->getTopReactions(3)->toArray();
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
            default => [
                'button' => 'px-4 py-2 text-sm',
                'icon' => 'w-5 h-5',
                'picker' => 'p-2 gap-2',
                'emoji' => 'w-8 h-8',
            ],
        };
    }

    public function render(): View
    {
        return view('sb-likes::components.blade-reactions', [
            'sizeClasses' => $this->getSizeClasses(),
        ]);
    }
}
