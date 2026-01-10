<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    // Allow users to override this model
    public static function getModel()
    {
        return config('workflow.models.workflow', static::class);
    }

    // Your model code here
}