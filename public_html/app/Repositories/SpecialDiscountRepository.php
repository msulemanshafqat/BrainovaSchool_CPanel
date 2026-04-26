<?php

namespace App\Repositories;

use App\Enums\Settings;
use App\Models\StudentSpecialDiscount;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Interfaces\SpecialDiscountInterface;
use App\Models\SpecialDiscount;

class SpecialDiscountRepository implements SpecialDiscountInterface
{
    use ReturnFormatTrait;
    private $discount;


    public function __construct(SpecialDiscount $discount)
    {
        $this->discount = $discount;

    }

    public function all()
    {
        return $this->discount->active()->get();
    }

    public function getAll()
    {
        return $this->discount->latest()->paginate(Settings::PAGINATE);
    }

    public function store($request)
    {
        try {
            $discountStore              = new $this->discount;
            $discountStore->name        = $request->name;
            $discountStore->type        = $request->type;
            $discountStore->discount    = $request->discount_amount;

            $discountStore->min_discount_amount = $request->filled('min_amount') ? floatval($request->min_amount) : null;
            $discountStore->max_discount_amount = $request->filled('max_amount') ? floatval($request->max_amount) : null;
            $discountStore->min_eligible_amount = $request->filled('min_eligible_amount') ? floatval($request->min_eligible_amount) : null;
            $discountStore->max_eligible_amount = $request->filled('max_eligible_amount') ? floatval($request->max_eligible_amount) : null;

            $discountStore->short_description = $request->short_desc;
            $discountStore->active_status = $request->status;
            $discountStore->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function show($id)
    {
        return $this->discount->find($id);
    }

    public function update($request)
    {
        try {
            $discount              = $this->discount->findOrfail($request->id);
            $discount->name        = $request->name;
            $discount->type        = $request->type;
            $discount->discount    = $request->discount_amount;

            $discount->min_discount_amount = $request->filled('min_amount') ? floatval($request->min_amount) : null;
            $discount->max_discount_amount = $request->filled('max_amount') ? floatval($request->max_amount) : null;
            $discount->min_eligible_amount = $request->filled('min_eligible_amount') ? floatval($request->min_eligible_amount) : null;
            $discount->max_eligible_amount = $request->filled('max_eligible_amount') ? floatval($request->max_eligible_amount) : null;

            $discount->short_description = $request->short_desc;
            $discount->active_status = $request->status;
            $discount->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->discount->find($id);
            $data->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function assignDiscount($request)
    {
        $discounts = $request->input('discounts', []);
        $notes = $request->input('short_notes', []);

        try {
            foreach ($discounts as $studentId => $discountId) {
                if ($discountId) {
                    StudentSpecialDiscount::updateOrCreate(
                        ['student_id' => $studentId],
                        [
                            'special_discount_id' => $discountId,
                            'short_description' => $notes[$studentId] ?? null,
                            'assigned_by' => auth()->id(),
                            'assigned_date' => now(),
                        ]
                    );
                }
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }


}
