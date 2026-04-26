<?php

namespace App\Repositories\Homework;

interface HomeworkInterface
{
    public function all();
    public function getPaginateAll();
    public function search($request);
    public function store($request);
    public function show($id);
    public function update($request, $id);
    public function destroy($id);

    /** Teacher manually assigns marks + optional feedback. Awards E6 Points. */
    public function evaluationSubmit($request);

    /** Answer-sheet marking for uploaded file submissions. Awards E6 Points. */
    public function markSubmit($request);

    /** Aggregated statistics for teacher dashboard charts. */
    public function getStats(): array;

    /** All homework across all teachers — admin only, no subject scope. */
    public function getAllForAdmin($request = null);

    /** Per-question accuracy analytics for a quiz homework. */
    public function getQuizAnalytics(int $homeworkId): array;

    /** Streams a CSV of all student results for a homework. */
    public function exportResultsCsv(int $homeworkId);
}
