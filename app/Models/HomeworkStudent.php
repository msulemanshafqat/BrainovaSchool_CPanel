<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeworkStudent extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'extra_upload_ids' => 'array',
    ];

    public function homeworkUpload()
    {
        return $this->belongsTo(Upload::class, 'homework', 'id');
    }

    /**
     * Ordered collection of all uploaded submission files (primary + extras).
     */
    public function allSubmissionUploads()
    {
        $ids = $this->allSubmissionUploadIds();
        if ($ids === []) {
            return collect();
        }

        return Upload::whereIn('id', $ids)
            ->get()
            ->sortBy(function ($upload) use ($ids) {
                return array_search((int) $upload->id, $ids, true);
            })
            ->values();
    }

    /**
     * @return int[]
     */
    public function allSubmissionUploadIds(): array
    {
        $ids = [];
        if ($this->homework) {
            $ids[] = (int) $this->homework;
        }
        foreach ($this->extra_upload_ids ?? [] as $id) {
            if ($id !== null && $id !== '') {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
