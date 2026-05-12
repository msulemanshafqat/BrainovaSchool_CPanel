<?php

namespace App\Interfaces\Academic;

interface ClassesInterface
{

    public function assignedAll();

    /**
     * Active class setups for the current session, scoped to the logged-in teacher’s
     * subject assignments (non-admins). Admins get the full session list.
     */
    public function assignedForHomework();

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
