<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionActionsMiddleware
{
    
    /**
     * Universal action-to-permission suffix map
     * Used by all modules
     */
    protected array $map = [
        // Standard REST actions
        'index' => 'list',
        'create' => 'create',
        'edit' => 'edit',
        'store' => 'create',
        'show' => 'view',
        'update' => 'edit',
        'destroy' => 'delete',

        // Common extended CRUD actions
        'bulkDelete' => 'bulk-delete',
        'bulkRestore' => 'bulk-restore',
        'permanentlyDestroy' => 'permanent-delete',
        'forceDelete' => 'force-delete',
        'restore' => 'restore',
        'exportCsv' => 'export',
        'export' => 'export',
        'download' => 'download',

        // User-specific
        'directPermission' => 'direct_permission',
        'statusUpdate' => 'status',
        'updateStatus' => 'status',
        'status' => 'status',
        'payment' => 'payment',
        'storePayment' => 'payment',
        'paymentContent' => 'create',
        'order' => 'order',
        'storeOrder' => 'order',
        'returnOrder' => 'return-order',
        'storeReturnOrder' => 'return-order',
        'invoice' => 'invoice',
        'events' => 'events',
        'orders' => 'orders',
        'payments' => 'payments',
        'settlements' => 'settlements',
        'inventory' => 'inventory',
        'customers' => 'customers',
        'getCategoryInventoryItems' => 'list',
        'latest' => 'view',
        'markAllRead' => 'view',
        'sendCredentials' => 'change_password',
        'editPassword' => 'change_password',
        'changePassword' => 'change_password',

        // Team-specific
        'assignUsers' => 'assign-user',
        'removeUser' => 'remove-user',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $prefix): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $action = $request->route()->getActionMethod();
        $suffix = $this->map[$action] ?? null;
        if (
            $action === 'changePassword' &&
            !str_ends_with((string) $request->route()->getControllerClass(), '\\UserController')
        ) {
            $suffix = null;
        }
        if ($suffix) {
            $permission = "{$prefix}-{$suffix}";
            if (!$user->can($permission)) {
                return response()->json([
                    'success' => false,
                    "status_code" => 403,
                    'message' => "You do not have required authorization: {$permission}.",
                ], 403);
            }
        }

        return $next($request);
    }
}
