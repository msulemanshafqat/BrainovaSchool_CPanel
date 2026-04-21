@extends('parent-panel.partials.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- Breadcrumb --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Appeal Details Card --}}
        <div class="col-12">
            <div class="card ot-card mb-4">
                <div class="card-body">
                    <div class="row">

                        {{-- Column 1 --}}
                        <div class="col-md-4">

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="fw-bold">{{ ___('behaviourrecord.Title') }}:</label>
                                <div>{{ $data['appeal']->title }}</div>
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="fw-bold">{{ ___('behaviourrecord.Description') }}:</label>
                                <div>{{ $data['appeal']->description ?? '-' }}</div>
                            </div>

                            {{-- Incident --}}
                            @if($data['appeal']->behaviourRecord?->incident)
                                <div class="mb-3">
                                    <label class="fw-bold">{{ ___('behaviourrecord.Incident') }}:</label>
                                    <div>
                                        @foreach($data['appeal']->student->behaviourRecord as $record)
                                            @if ($record->incident)
                                                <li>
                                                    <span
                                                        class="badge {{ $record->incident->type === 'positive' ? 'badge-basic-success-text' : 'badge-basic-danger-text' }}">
                                                        {{ $record->incident->title }} ({{ $record->incident->type === 'positive' ? '+' : '-' }}{{ $record->incident->points }})
                                                    </span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- Column 2 --}}
                        <div class="col-md-4">

                            {{-- Student Info --}}
                            <div class="mb-3">
                                <label class="fw-bold">{{ ___('student_info.Student') }}:</label>
                                <div class="">
                                    <a href="{{ route('student.show',$data['appeal']->student->id) }}" target="_blank">
                                        <div class="user-card">
                                            <div class="user-avatar">
                                                <img src="{{ @globalAsset($data['appeal']->student->user->upload->path, '40X40.webp') }}"
                                                     alt="{{ $data['appeal']->student->first_name }}">
                                            </div>
                                            <div class="user-info">
                                                {{ $data['appeal']->student->first_name }}
                                                {{ $data['appeal']->student->last_name }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            {{-- Appealed By --}}
                            <div class="mb-3">
                                <label class="fw-bold">{{ ___('behaviourrecord.Appeal By') }}:</label>
                                <div>{{ $data['appeal']->requestBy->name ?? '-' }}</div>
                            </div>

                            <div class="mb-3">
                                @php
                                    $status = strtolower($data['appeal']->status);
                                    $badgeClass = $status === 'pending' ? 'badge-warning' : 'badge-success';
                                @endphp

                                <label class="fw-bold">{{ ___('common.Status') }}:</label>
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                            </div>

                        </div>

                        {{-- Column 3 --}}
                        <div class="col-md-4">

                            {{-- Dates --}}
                            <div class="mb-3">
                                <label class="fw-bold">{{ ___('common.Created Date') }}:</label>
                                <div>{{ dateFormat($data['appeal']->created_at) }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold">{{ ___('common.Updated Date') }}:</label>
                                <div>{{ dateFormat($data['appeal']->updated_at) }}</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="card ot-card">
                <div class="card-header py-2">
                    <h6 class="mb-0">{{ ___('support_ticket.Conversation') }}</h6>
                </div>
                <div class="card-body d-flex flex-column p-3" style="height: 320px;">

                    {{-- Messages Container --}}
                    <div id="messages" class="flex-grow-1 overflow-auto mb-2" style="max-height: 250px; font-size: 0.9rem;">

                        @forelse($data['messages'] as $message)
                            {{-- Check if message sent by current user --}}
                            @php
                                $isCurrentUser = $message->sender_id == auth()->user()->id;
                                $senderName = $isCurrentUser ? __('You') : ($message->sender->name ?? 'Unknown');
                            @endphp

                            <div class="d-flex align-items-start mb-2 {{ $isCurrentUser ? 'justify-content-end' : '' }}">
                                @if(!$isCurrentUser)
                                    <div class="me-2">
                                        <div class="ot-btn-primary rounded-circle d-flex justify-content-center align-items-center text-white" style="width:28px; height:28px; font-weight:bold;">
                                            {{ strtoupper(substr($senderName, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <div class="{{ $isCurrentUser ? 'ot-btn-primary text-white' : 'ot-btn-primary text-white' }} p-2 rounded" style="max-width: 70%;">

                                        <p class="mb-1" style="line-height:1.2;">{{ $message->message }}</p>

                                        @if($message->attachment_id)
                                            <div class="mt-1">
                                                <a href="{{ asset($message->attachment->path) }}" target="_blank" class="text-info">
                                                    {{ ___('common.Attachment') }}
                                                </a>
                                            </div>
                                        @endif

                                        <small class="text-muted" style="font-size: 0.75rem;">{{ dateFormat($message->created_at) .' '.  timeFormat($message->created_at) }}</small>
                                    </div>
                                </div>

                                @if($isCurrentUser)
                                    <div class="ms-2">
                                        <div class="bg-info rounded-circle d-flex justify-content-center align-items-center text-white" style="width:28px; height:28px; font-weight:bold;">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                        @empty
                            <div class="text-center text-muted">{{ ___('No messages yet.') }}</div>
                        @endforelse

                    </div>

                    {{-- New Message Input --}}
                    <form action="{{route('parent-panel.appeal.conversation.store')}}" method="post" class="d-flex gap-2 align-items-center" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $data['appeal']->id }}" name="appeal_id">
                        <input type="hidden" value="{{ $data['appeal']->requestBy->id }}" name="receiver_id">

                        {{-- Attachment --}}
                        <div class="position-relative">
                            <label for="attachment" class="btn btn-light btn-sm p-2 border" style="cursor: pointer;">📎</label>
                            <input type="file" name="attachment" id="attachment" class="d-none">
                        </div>

                        <input type="text" name="message" class="form-control ot-input" placeholder="{{ ___('support_ticket.Type your message') }}" />
                        <button type="submit" class="btn btn ot-btn-primary px-3">{{ ___('common.Send') }}</button>
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
                success: function (response) {
                    toastr.success(response.message);
                },
                error: function (xhr) {
                    toastr.error("Something went wrong");
                }
            });
        });

    </script>

@endpush
