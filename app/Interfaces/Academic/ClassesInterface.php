<?php

namespace App\Interfaces\Academic;

interface ClassesInterface
{

    public function assignedAll();

    public function all();

    public function getAll();

    /** Paginated classes the staff member teaches this session (subject assignment), distinct by class id. */
    public function getAllForAssignedTeacher(int $staffId);

    public function staffTeachesClass(int $staffId, int $classId): bool;

    public function store($request);

    public function show($id);

    public function update($request, $id);

    public function destroy($id);

    public function translates($id);

    public function translateUpdate($request, $id);
}
