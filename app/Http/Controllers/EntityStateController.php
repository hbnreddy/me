<?php

namespace App\Http\Controllers;

use App\Eloquent\EntityState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntityStateController extends Controller
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function state($id)
    {
        $entityState = EntityState::query()
            ->where('entity_id', $id)
            ->first();

        $data = [
            'enabled' => isset($entityState) ? $entityState->enabled : false
        ];

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function states(Request $request)
    {
        $request->validate([
            'entity_ids' => 'required|array',
        ]);

        $entityStates = EntityState::query()
            ->whereIn('entity_id', $request->input('entity_ids'))
            ->get();

        $data = [
            'entity_states' => $entityStates
        ];

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setState(Request $request)
    {
        $request->validate([
            'entities' => 'required|array',
        ]);

        $success = true;
        $message = 'Success';
        $entities = $request->input('entities');

        foreach ($entities as $entity) {
            $status = EntityState::query()
                ->updateOrCreate([
                    'entity_id' => $entity['id'],
                ],
                [
                    'entity_id' => $entity['id'],
                    'enabled' => $entity['enabled'],
                ]);

            if (!$status) {
                $success = false;
            }
        }

        if (!$success) {
            $message = 'Some entities may not be saved.';
        }

        $data = [
            'success' => !!$success,
            'message' => $message,
        ];

        return response()->json($data);
    }
}
