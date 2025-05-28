<?php

// app/Services/TaskService.php

namespace App\Services;

use App\Models\Document;
use App\Models\Task;

class TaskService
{
    public function CreateTaskForDocument(Document $document)
    {
        return $document->tasks()->create([
            'description' => 'Review the uploaded document.',
            'status' => Task::STATUS_PENDING,
            'urgency' => Task::URGENCY_MEDIUM,
            'due_date' => now()->addDays(3)
        ]);
    }
}
