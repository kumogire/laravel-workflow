<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model to use for workflow instances
    |
    */
    'user_model' => env('WORKFLOW_USER_MODEL', 'App\Models\User'),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'api_prefix' => 'api/workflows',
        'api_middleware' => ['api', 'auth:sanctum'],
        
        'admin_prefix' => 'admin/workflows',
        'admin_middleware' => ['web', 'auth', 'admin'],
    ],

    'enable_admin_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Action Handlers
    |--------------------------------------------------------------------------
    |
    | Register custom action handlers
    |
    */
    'action_handlers' => [
        'email' => \Kumogire\Workflow\Actions\Handlers\EmailActionHandler::class,
        'sms' => \Kumogire\Workflow\Actions\Handlers\SmsActionHandler::class,
        'webhook' => \Kumogire\Workflow\Actions\Handlers\WebhookActionHandler::class,
        'data_save' => \Kumogire\Workflow\Actions\Handlers\DataSaveActionHandler::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queue_actions' => true,
    'queue_connection' => env('WORKFLOW_QUEUE_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */
    'role_field' => 'role', // Field on user model for role
    
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache_workflows' => true,
    'cache_ttl' => 3600, // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        // API routes
        'api_prefix' => env('WORKFLOW_API_PREFIX', 'api/workflows'),
        'api_middleware' => ['api', 'auth:sanctum'],
        
        // Admin API routes
        'admin_prefix' => env('WORKFLOW_ADMIN_PREFIX', 'admin/workflows'),
        'admin_middleware' => ['web', 'auth', 'admin'],

        // Admin web interface routes
        'admin_web_prefix' => env('WORKFLOW_ADMIN_WEB_PREFIX', 'workflow-admin'),
        'admin_web_middleware' => ['web', 'auth'],
        'enable_admin_web' => env('WORKFLOW_ENABLE_ADMIN_WEB', true),
    ],

    'enable_admin_routes' => env('WORKFLOW_ENABLE_ADMIN_ROUTES', true),
];
