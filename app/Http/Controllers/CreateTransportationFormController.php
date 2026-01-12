<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Ramsey\Uuid\Uuid;

class CreateTransportationFormController extends Controller
{
    public function __invoke(Request $request)
    {
        $draftId = Uuid::uuid7()->toString();

        // Load user's drafts
        $userID = $request->user()?->id;
        $drafts = [];

        if ($userID) {
            $draftIds = Cache::get("{$userID}_transportation_draft_ids", []);
            $cachePrefix = "{$userID}_transportation_draft_";

            foreach ($draftIds as $id) {
                $draft = Cache::get("{$cachePrefix}{$id}");
                if ($draft) {
                    $drafts[] = [
                        'id' => $id,
                        'name' => $draft['data']['name'] ?? 'Untitled Transportation',
                        'step' => $draft['step'] ?? 'transportationStep',
                        'updated_at' => $draft['updated_at'] ?? now()->toIso8601String(),
                    ];
                }
            }
        }

        return Inertia::render('transportations/Create', [
            'draftId' => $draftId,
            'drafts' => $drafts,
        ]);
    }
}
