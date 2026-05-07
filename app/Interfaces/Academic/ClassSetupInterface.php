<?php

namespace App\Interfaces\Academic;

interface ClassSetupInterface
{

    public function getSections($id);

    public function assignedClassSetupsForTeacher(int $staffId);

    public function getSectionsForTeacher($classId, int $staffId);

    public function all();

    public function getPaginateAll();

    public function store($request);

    public function show($id);

    public function update($request, $id);

    public function destroy($id);
}
