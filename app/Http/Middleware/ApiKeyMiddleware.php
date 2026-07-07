<?php

namespace App\Http\Middleware;

use App\Models\Vertical;
use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $cleApi = $request->header('x-api-key');

        if (!$cleApi) {
            return response()->json([
                'success' => false,
                'error' => 'Header x-api-key manquant',
            ], 401);
        }

        $verticalSlug = $request->route('vertical');

        $vertical = Vertical::getBySlug($verticalSlug);

        if (!$vertical) {
            return response()->json([
                'success' => false,
                'error' => "Verticale \"{$verticalSlug}\" non trouvée ou inactive",
            ], 404);
        }

        if (!$vertical->verifieCleApi($cleApi)) {
            return response()->json([
                'success' => false,
                'error' => 'Clé API invalide',
            ], 401);
        }

        $request->attributes->set('vertical', $vertical);

        return $next($request);
    }
}