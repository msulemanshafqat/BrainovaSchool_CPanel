@extends('student-panel.partials.master')
@section('title', @$data['title'])
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/livechat/css/app.css') }}">
    <style>
        /* Student live chat index — layout + empty state (replaces greetings artwork) */
        .lc-student-live .chat-box-area.card {
            overflow: hidden;
        }
        .lc-student-live .row {
            align-items: stretch;
        }
        .lc-student-live .chat-left-sidebar {
            display: flex;
            flex-direction: column;
            min-height: 0;
            height: 100%;
        }
        .lc-student-live .chat-list-wrapper {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }
        .lc-student-live .chat-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
        }
        .lc-empty-panel {
            min-height: min(70vh, 520px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            text-align: center;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .lc-brainova-logo {
            max-width: min(280px, 85vw);
            height: auto;
            margin-bottom: 1rem;
        }
        .lc-brainova-tag {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #64748b;
            margin-bottom: 1.25rem;
        }
        .lc-empty-teacher-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }
        .lc-empty-hint {
            font-size: 0.875rem;
            color: #64748b;
            max-width: 22rem;
            line-height: 1.45;
            margin: 0;
        }
    </style>
@endpush
@section('content')

    <div class="lc-student-live page-content">
        <div class="chat-box-area card card-body border-0">
            <div class="row g-3 g-lg-4">
                <div class="col-12 col-lg-4 col-xl-4">
                    <div class="chat-left-sidebar h-100">
                        <div class="chat-admin">
                            <div class="profile-wrap">
                                <div class="user-img" aria-hidden="true"></div>
                                <div class="user-chat-caption">
                                    <h5 class="user-name mb-0">{{ auth()->user()->name }}</h5>
                                    <p class="chat-status mb-0">{{ ___('live_chat.Active') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="chat-list-wrapper">
                            <div class="search-chat mb-3">
                                <input class="form-control ot-input" type="text" id="chat_search" onkeyup="searchChat()"
                                    placeholder="{{ ___('live_chat.Search') }}">
                            </div>
                            <div class="chat-list" data-url="{{ route('livechat.student_chat_list') }}"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8 col-xl-8">
                    <div class="chat-body h-100">
                        <div class="lc-empty-panel" id="lc-empty-panel">
                            <img class="lc-brainova-logo" src="{{ asset('images/brainova-live-chat-logo.png') }}"
                                width="280" height="auto" alt="Brainova">
                            <div class="lc-brainova-tag">Live chat</div>
                            <h4 id="lc-empty-teacher-name" class="lc-empty-teacher-name"
                                data-placeholder="Select a contact">Select a teacher or parent</h4>
                            <p class="lc-empty-hint">
                                Point to a teacher or parent in the list to see their name here, or click to open the conversation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('modules/livechat/js/app.js') }}"></script>
    <script>
        (function () {
            var $name = $('#lc-empty-teacher-name');
            var placeholder = $name.data('placeholder') || 'Select a teacher or parent';

            $(document).on('mouseenter', '.chat-list a.single-chat', function () {
                var n = $(this).data('contactName');
                if (n && String(n).trim() !== '') {
                    $name.text(String(n).trim());
                }
            });

            $(document).on('mouseleave', '.chat-list', function (e) {
                var rel = e.relatedTarget;
                if (rel && $(rel).closest('.chat-list').length) {
                    return;
                }
                $name.text(placeholder);
            });
        })();
    </script>
@endpush
