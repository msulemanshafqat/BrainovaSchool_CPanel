@extends('student-panel.partials.master')
@section('title', @$title)
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/livechat/css/app.css') }}">
    <style>
        .lc-student-chat-msg .chat-box-area.card { overflow: hidden; }
        .lc-student-chat-msg .row { align-items: stretch; }
        .lc-student-chat-msg .chat-left-sidebar {
            display: flex;
            flex-direction: column;
            min-height: 0;
            height: 100%;
        }
        .lc-student-chat-msg .chat-list-wrapper {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }
        .lc-student-chat-msg .chat-list { flex: 1; min-height: 0; overflow-y: auto; }
        .lc-student-chat-msg .current-chat-user .chat-cap {
            align-items: center;
            gap: 12px;
        }
        .lc-student-chat-msg .lc-brainova-chip {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 4px 8px;
            line-height: 1;
            flex-shrink: 0;
        }
    </style>
@endpush
@section('content')
    <div class="lc-student-chat-msg page-content">
    <audio id="message_sound">
        <source src="{{ asset('modules/livechat/file/sound.mp3') }}" type="audio/mpeg">
    </audio>
    <div class="chat-box-area card card-body border-0">
        <div class="row g-3 g-lg-4">
            <div class="col-12 col-lg-4 col-xl-4">
                <!-- Chat left side-bar -->
                <div class="chat-left-sidebar h-100">
                    <!-- Admin Profile -->
                    <div class="chat-admin">
                        <!-- Profile -->
                        <div class="profile-wrap">
                            <div class="user-img">
                                <img src="{{ @globalAsset(auth()->user()->student->upload->path, '40X40.webp') }}"
                                    alt="" class="img-cover">
                            </div>
                            <div class="user-chat-caption">
                                <h5 class="user-name mb-0">{{ auth()->user()->name }}</h5>
                                <p class="chat-status mb-0">{{ ___('live_chat.Active') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- My Listings -->
                    <div class="chat-list-wrapper">
                        <div class="search-chat mb-3">
                            <input class="form-control ot-input" type="text" id="chat_search" onkeyup="searchChat()"
                                placeholder="{{ ___('live_chat.Search') }}">
                        </div>
                        <div class="chat-list" data-url="{{ route('livechat.student_chat_list') }}">
                        </div>
                    </div>

                    <input type="hidden" id="receiver_id" value="{{ auth()->id() }}">
                    <input type="hidden" id="app_key" value="{{ env('PUSHER_APP_KEY') }}">
                    <input type="hidden" id="cluster" value="{{ env('PUSHER_APP_CLUSTER') }}">
                    <input type="hidden" id="chat_active_user" value="{{ decryptFunction(request('id')) }}">

                </div>
            </div>
            <div class="col-12 col-lg-8 col-xl-8">
                <div class="chat-body h-100">
                    <!-- curren-chat-user -->
                    <div class="current-chat-user">
                        <div class="chat-cap d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                <div class="user-img flex-shrink-0">
                                    <img src="{{ @globalAsset($user->upload->path) }}" alt="" class="img-cover">
                                </div>
                                <div class="user-chat-caption min-w-0" id="current_user" data-id="{{ encryptFunction(@$user->id) }}">
                                    <span class="lc-brainova-chip">Brainova</span>
                                    <h4 class="mb-0 mt-1">
                                        <span class="user-name text-break">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}</span>
                                    </h4>
                                    <small class="text-muted">{{ ___('live_chat.Active') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End-of Show product -->
                    <!-- MessageBox -->
                    <div class="chatBox-message">
                        <div class="chatShow">
                            <!-- userMessage -->

                            @foreach ($messages as $message)
                                <!-- adminMessage -->
                                @if (@$message->sender->id == auth()->user()->id)
                                    <div class="single-chat adminMessage" id="message-{{ $message->id }}">
                                        <div class="chatText">
                                            <div class="chatImg">
                                                <img src="{{ @globalAsset(@$message->sender->upload->path, '40X40.webp') }}"
                                                    alt="img" class="img-cover">
                                            </div>
                                            <div class="chatCaption">
                                                <p class="chatPera"> {{ $message->message }}</p>
                                                <small class="time"> {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="single-chat userMessage" id="message-{{ $message->id }}">
                                        <div class="chatText">
                                            <div class="chatImg">
                                                <img src="{{ @globalAsset(@$message->receiver->upload->path, '40X40.webp') }}"
                                                    alt="img" class="img-cover">
                                            </div>
                                            <div class="chatCaption">
                                                <p class="chatPera">{{ $message->message }}</p>
                                                <small class="time"> {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <!-- chatSend input box-->
                        <div class="chatSend-wrapper">
                            <div class="sendMessage">
                                <input class="input" type="text" id="chat_text" name="chat"
                                    placeholder="Write your chat...">
                                <div class="btn-wrapper form-icon">
                                    <button class="btn-rounded2" type="submit" name="submit" id="message_sent"
                                        data-url="{{ route('student_livechat.store', encryptFunction(@$user->user_id)) }}">{{ ___('live_chat.Send') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End-of Chat Box Area -->
    </div>

@endsection

@push('script')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('modules/livechat/js/app.js') }}"></script>
@endpush
