@extends('backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- Breadcrumb --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ @$data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ @$data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Appeal Details Card --}}
        {{-- Appeal Details --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0">{{ ___('behaviourrecord.Appeal Details') }}</h5>
                    <div class="d-flex align-items-center">
                        <label class="me-2 fw-semibold mb-0">{{ ___('common.Status') }}:</label>
                        <select class="nice-select niceSelect wide bordered_style" name="type" id="statusDropdown"
                            style="min-width: 150px;">
                            <option value="granted" {{ @$data['appeal']->status == 'granted' ? 'selected' : '' }}>
                                {{ ___('common.granted') }}</option>
                            <option value="withdraw" {{ @$data['appeal']->status == 'withdraw' ? 'selected' : '' }}>
                                {{ ___('common.withdraw') }}</option>
                            <option value="pending" {{ @$data['appeal']->status == 'pending' ? 'selected' : '' }}>
                                {{ ___('common.pending') }}</option>
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row gy-4">
                        {{-- Column 1 --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="fw-semibold">{{ ___('behaviourrecord.Title') }}:</label>
                                <div>{{ @$data['appeal']->title }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">{{ ___('behaviourrecord.Description') }}:</label>
                                <div>{{ @$data['appeal']->description ?? '-' }}</div>
                            </div>
                            @if (@$data['appeal']->behaviourRecord?->incident)
                                <div class="mb-3">
                                    <label class="fw-semibold">{{ ___('behaviourrecord.Incident') }}:</label>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($data['appeal']->student->behaviourRecord as $record)
                                            @if ($record->incident)
                                                <li>
                                                    <span
                                                        class="badge {{ $record->incident->type === 'positive' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                        {{ $record->incident->title }}
                                                        ({{ $record->incident->type === 'positive' ? '+' : '-' }}{{ $record->incident->points }})
                                                    </span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Column 2 --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="fw-semibold">{{ ___('student_info.Student') }}:</label>
                                <div class="d-flex align-items-center">

                                    <img src="{{ @globalAsset(@$data['appeal']->student->user->upload->path, '40X40.webp') }}"
                                        alt="Avatar" class="rounded-circle me-2" width="40" height="40">
                                    <a href="{{ route('student.show', @$data['appeal']->student?->id) }}" target="_blank"
                                        class="text-decoration-none">
                                        {{ @$data['appeal']->student->first_name }}
                                        {{ @$data['appeal']->student->last_name }}
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">{{ ___('behaviourrecord.Appeal By') }}:</label>
                                <div>{{ @$data['appeal']->requestBy->name ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- Column 3 --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="fw-semibold">{{ ___('common.Created Date') }}:</label>
                                <div>{{ dateFormat(@$data['appeal']->created_at) }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">{{ ___('common.Updated Date') }}:</label>
                                <div>{{ dateFormat(@$data['appeal']->updated_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conversation Section --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">

                <div class="card-body d-flex flex-column p-3" style="height: 360px;">
                    {{-- Messages --}}
                    <div id="messages" class="flex-grow-1 overflow-auto mb-3" style="max-height: 260px;">
                        @forelse(@$data['messages'] as $message)
                            @php
                                $isCurrentUser = $message->sender_id == auth()->user()->id;
                                $senderName = $isCurrentUser ? __('You') : $message->sender->name ?? 'Unknown';
                            @endphp

                            <div
                                class="d-flex mb-3 {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }}">
                                @if (!$isCurrentUser)
                                    <div class="me-2">
                                        <div class="profile-photo user-card">
                                            <img src="{{ @globalAsset($message->sender?->upload->path, '40X40.webp') }}"
                                                alt="{{@$senderName}}">
                                        </div>


                                    </div>
                                @endif

                                <div class="{{ $isCurrentUser ? 'ot-btn-primary text-white' : 'ot-btn-info text-white' }} p-3 rounded-3 shadow-sm"
                                    style="max-width: 65%;">

                                    <div>{{ $message->message }}</div>
                                    @if ($message->attachment_id)
                                        <div class="mt-2">
                                            <a href="{{ asset($message->attachment->path) }}" target="_blank"
                                                class="text-info text-decoration-underline">
                                                📎 {{ ___('common.Attachment') }}
                                            </a>
                                        </div>
                                    @endif
                                    <hr>

                                    <div class="mt-1">
                                        <span class="text-muted text-white small">{{ dateFormat($message->created_at) }}
                                            {{ timeFormat($message->created_at) }} </span>
                                        <span> - {{ $senderName }}</span>
                                    </div>
                                </div>

                                @if ($isCurrentUser)
                                    <div class="ms-2">
                                        <div class="profile-photo user-card">
                                            <img src="{{ @globalAsset(Auth::user()->upload->path, '40X40.webp') }}"
                                                alt="{{ Auth::user()->name }}">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted">{{ ___('No messages yet.') }}</div>
                        @endforelse
                    </div>

                    {{-- Message Input --}}
                    <form action="{{ route('incidents.appeal.conversation.store') }}" method="POST"
                        enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="appeal_id" value="{{ @$data['appeal']->id }}">
                        <input type="hidden" name="receiver_id" value="{{ @$data['appeal']->requestBy->id }}">

                        {{-- Attachment Button --}}
                        <div class="position-relative">
                            <label for="attachment" class="btn btn-outline-secondary btn-sm mb-0">📎</label>
                            <input type="file" name="attachment" id="attachment" class="d-none">
                        </div>

                        <input type="text" name="message" class="form-control"
                            placeholder="{{ ___('support_ticket.Type your message') }}" />
                        <button type="submit" class="btn ot-btn-success px-4">{{ ___('common.Send') }}</button>
                    </form>
                </div>
            </div>
        </div>




    </div>

@endsection
@push('script')
    <script>
        $('#statusDropdown').change(function() {
            let status = $(this).val();
            let appealId = {{ $data['appeal']->id }};

            $.ajax({
                url: "{{ route('incidents.appeal.changeStatus', ':id') }}".replace(':id', appealId),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message);
                    window.location.href = "{{ route('incidents.appeal.index') }}";
                },
                error: function(xhr) {
                    toastr.error("Something went wrong");
                }
            });
        });
    </script>
@endpush
