@foreach ($students as $item)
<tr id="document-file">
    <td><input class="form-check-input student" type="checkbox" name="student_ids[]" value="{{$item->id}}"></td>
    <td>{{ @$item->student->admission_no }}</td>
    <td>{{ @$item->student->first_name }} {{ @$item->student->last_name }}</td>
    <td>{{ @$item->class->name }} ({{ @$item->section->name }})</td>
    <td>{{ @$item->student->parent->guardian_name }}</td>
    <td>{{ @$item->student->mobile }}</td>
    <td>
        {{ @$item->student->specialDiscount->discount->name ?? '-' }}
        <br>
        @if(isset($item->student->specialDiscount->discount))
            @php
                $discount = $item->student->specialDiscount->discount;
            @endphp

            @if($discount->type == 'P')
                {{ $discount->discount }}%
            @else
                {{ setting('currency_symbol') }}{{ $discount->discount }}
            @endif
        @endif
    </td>
</tr>
@endforeach

