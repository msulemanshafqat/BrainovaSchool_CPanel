@extends('backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">
        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.Dashboard') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('studymaterial.Study Material') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}
        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="row">
                {{--                second card --}}
                <div
                    class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $data['title'] }}</h4>
                            @if (hasPermission('id_card_create'))
                                <button class="btn btn-lg ot-btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                                    <span><i class="fa-solid fa-plus"></i> </span>
                                    <span>{{ ___('common.add') }}</span>
                                </button>

                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered role-table">
                                    <thead class="thead">
                                    <tr>
                                        <th class="serial">{{ ___('common.Si') }}</th>
                                        <th class="purchase">{{___('common.title')}}</th>
                                        <th class="purchase">{{ ___('common.Points') }} </th>
                                        @if (hasPermission('content_update') || hasPermission('content_delete'))
                                            <th class="action">{{ ___('common.action') }}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="tbody">
                                    @forelse ($data['contents'] ?? [] as $key => $row)
                                        <tr id="row_{{ $row->id }}">
                                            <td class="serial">{{ ++$key }}</td>
                                            <td>{{ $row->title }}</td>
                                            <td>{{ $row->points . ($row->type == 'positive' ? ' +' : ' -') }}</td>
                                                <td class="action">
                                                    <div class="dropdown dropdown-action">
                                                        <button type="button" class="btn-dropdown"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-solid fa-ellipsis"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end ">
                                                                <li>
                                                                    <a data-id="{{$row->id}}" class="dropdown-item open-edit-offcanvas" data-bs-toggle="offcanvas" data-bs-target="#editoffcanvasRight" aria-controls="offcanvasRight" ><span
                                                                            class="icon mr-8"><i
                                                                                class="fa-solid fa-pen-to-square"></i></span>
                                                                        {{ ___('common.edit') }}</a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                       href="javascript:void(0);"
                                                                       onclick="delete_row('incidents/delete', {{ $row->id }})">
                                                                            <span class="icon mr-8"><i
                                                                                    class="fa-solid fa-trash-can"></i></span>
                                                                        <span>{{ ___('common.delete') }}</span>
                                                                    </a>
                                                                </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center gray-color">
                                                <img src="{{ asset('images/no_data.svg') }}" alt=""
                                                     class="mb-primary" width="100">
                                                <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                                                <p class="mb-0 text-center text-secondary font-size-90">
                                                    {{ ___('common.please_add_new_entity_regarding_this_table') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!--  table end -->
                            <!--  pagination start -->
                            <!--  pagination end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Offcanvas Right -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ ___('incidents.Add Incidents') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form id="shareContentForm" action="{{ route('incidents.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="shareTitle" class="form-label">{{ ___('incidents.Title') }}</label>
                            <input class="form-control ot-input @error('title') is-invalid @enderror"
                                   name="title" id="shareTitle" type="text"
                                   placeholder="Enter share title" value="{{ old('title') }}">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3 mt-3">
                            <label class="form-label">{{ ___('incidents.type') }} <span class="fillable">*</span></label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type') is-invalid @enderror"
                                       type="radio"
                                       name="type"
                                       id="type_positive"
                                       value="positive"
                                    {{ old('type') === 'positive' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_positive">{{ ___('incidents.positive') }}</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type') is-invalid @enderror"
                                       type="radio"
                                       name="type"
                                       id="type_negative"
                                       value="negative"
                                    {{ old('type') === 'negative' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_negative">{{ ___('incidents.negative') }}</label>
                            </div>

                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Points -->
                        <div class="mb-4">
                            <label for="points" class="form-label">{{ ___('incidents.Points') }}</label>
                            <input class="form-control ot-input @error('points') is-invalid @enderror"
                                   name="points" id="points" type="number"
                                   placeholder="Enter points" value="{{ old('points') }}">
                            @error('points')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">{{ ___('common.Description') }}</label>
                            <textarea class="form-control ot-textarea mt-0 mb-1 @error('description') is-invalid @enderror"
                                      name="description" id="description" placeholder="Enter description">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Footer Buttons -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">{{ ___('common.Cancel') }}</button>
                            <button class="btn ot-btn-primary" type="submit">{{ ___('common.Submit') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="offcanvas offcanvas-end" tabindex="-1" id="editoffcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ ___('incidents.Edit Incidents') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <form id="shareContentForm" action="{{ route('incidents.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="edit_shareTitle" class="form-label">{{ ___('incidents.Title') }}</label>
                            <input class="form-control ot-input @error('title') is-invalid @enderror"
                                   name="title" id="edit_shareTitle" type="text"
                                   placeholder="Enter share title" value="{{ old('title') }}">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="incident_id" id="incident_id" value="{{ old('incident_id') }}">

                        <!-- Type -->
                        <div class="mb-3 mt-3">
                            <label class="form-label">{{ ___('incidents.type') }} <span class="fillable">*</span></label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type') is-invalid @enderror"
                                       type="radio"
                                       name="type"
                                       id="edit_type_positive"
                                       value="positive"
                                    {{ old('type', $yourModel->type ?? '') === 'positive' ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_type_positive">{{ ___('incidents.positive') }}</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type') is-invalid @enderror"
                                       type="radio"
                                       name="type"
                                       id="edit_type_negative"
                                       value="negative"
                                    {{ old('type', $yourModel->type ?? '') === 'negative' ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_type_negative">{{ ___('incidents.negative') }}</label>
                            </div>

                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Points -->
                        <div class="mb-4">
                            <label for="edit_points" class="form-label">{{ ___('incidents.Points') }}</label>
                            <input class="form-control ot-input @error('points') is-invalid @enderror"
                                   name="points" id="edit_points" type="number"
                                   placeholder="Enter points" value="{{ old('points') }}">
                            @error('points')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="edit_description" class="form-label">{{ ___('common.Description') }}</label>
                            <textarea class="form-control ot-textarea mt-0 mb-1 @error('description') is-invalid @enderror"
                                      name="description" id="edit_description"
                                      placeholder="Enter description">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Footer Buttons -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">{{ ___('common.Cancel') }}</button>
                            <button class="btn ot-btn-primary" type="submit">{{ ___('common.Submit') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection
@push('script')
    @include('backend.partials.delete-ajax')
    <script>
        @if($errors->any())
            var myOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasRight'));
            myOffcanvas.show();
        @endif

        $(document).ready(function () {
            $('.open-edit-offcanvas').on('click', function (e) {
                e.preventDefault();

                var id = $(this).data('id');

                $.ajax({
                    url: '/incidents/edit/' + id,
                    type: 'GET',
                    success: function (response) {
                        console.log(response)
                        // Populate the form inside the offcanvas with response data
                        $('#editoffcanvasRight input[name="title"]').val(response.title);
                        $('#editoffcanvasRight input[name="incident_id"]').val(response.id);
                        $('#editoffcanvasRight input[name="type"][value="' + response.type + '"]').prop('checked', true);
                        $('#editoffcanvasRight input[name="points"]').val(response.points);
                        $('#editoffcanvasRight textarea[name="description"]').val(response.description);

                    },
                    error: function () {
                        $('#editoffcanvasRight .offcanvas-body').html('<p>Error loading data.</p>');
                    }
                });
            });
        });
    </script>


@endpush
