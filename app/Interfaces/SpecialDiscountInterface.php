<?php

namespace App\Interfaces;


interface SpecialDiscountInterface
{

    public function all();

    public function getAll();

    public function store($request);

    public function show($id);

    public function update($request);

    public function destroy($id);
}
