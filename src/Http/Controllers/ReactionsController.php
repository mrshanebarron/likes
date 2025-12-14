<?php

namespace MrShaneBarron\Likes\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReactionsController extends Controller
{
    public function react(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'reaction' => 'required|string',
        ]);

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $reaction = $request->input('reaction');

        // Validate reaction type exists in config
        $validReactions = array_keys(config('ld-likes.reactions', []));
        if (!in_array($reaction, $validReactions)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reaction type',
            ], 422);
        }

        // Find the model
        if (!class_exists($modelType)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid model type',
            ], 422);
        }

        $model = $modelType::find($modelId);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model not found',
            ], 404);
        }

        // Check if model uses Reactable trait
        if (!method_exists($model, 'toggleReaction')) {
            return response()->json([
                'success' => false,
                'message' => 'Model does not support reactions',
            ], 422);
        }

        // Toggle the reaction
        $result = $model->toggleReaction($reaction);

        return response()->json([
            'success' => true,
            'user_reaction' => $result?->reaction,
            'total_count' => $model->getTotalReactions(),
            'reaction_counts' => $model->getReactionCounts()->toArray(),
            'top_reactions' => $model->getTopReactions(3)->toArray(),
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');

        if (!class_exists($modelType)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid model type',
            ], 422);
        }

        $model = $modelType::find($modelId);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model not found',
            ], 404);
        }

        if (!method_exists($model, 'getUserReaction')) {
            return response()->json([
                'success' => false,
                'message' => 'Model does not support reactions',
            ], 422);
        }

        $userReaction = $model->getUserReaction();

        return response()->json([
            'success' => true,
            'user_reaction' => $userReaction?->reaction,
            'total_count' => $model->getTotalReactions(),
            'reaction_counts' => $model->getReactionCounts()->toArray(),
            'top_reactions' => $model->getTopReactions(3)->toArray(),
        ]);
    }
}
