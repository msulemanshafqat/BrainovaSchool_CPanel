@foreach ($users as $user)
    @php
        $isStaff = $user instanceof \App\Models\Staff\Staff;

        $seenClass = '';
        if (! empty($user->lastMessage)) {
            if (@$user->lastMessage->is_seen == false) {
                $seenClass = 'has-unread-message';
            }
        }

        $displayName = $isStaff
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : trim(($user->guardian_name ?? '') ?: (optional($user->user)->name ?? '') ?: 'Parent');

        $unreadCount = $isStaff
            ? (int) ($user->unreadMessages->count() ?? 0)
            : (int) ($user->_unread_count ?? 0);

        $imgPath = $isStaff
            ? (optional($user->upload)->path ?? null)
            : (optional($user->user)->upload->path ?? null);
    @endphp

    <a class="single-chat  @if ($user->id == $user_id) active @endif {{ $seenClass }}"
       id="receiver_id_{{ $user->user_id }}"
        href="{{ route('student_livechat.chat', encryptFunction(@$user->user_id)) }}"
        data-contact-name="{{ e($displayName) }}">
        <div class="chat-cap d-flex gap-3">
            <div class="user-img">
                <img src="{{ @globalAsset($imgPath, '40X40.webp') }}" height="40" width="40" alt="" class="img-cover">
            </div>
            <div class="user-chat-caption">
                <h5 class="user-name mb-0">{{ $displayName }}</h5>
                <p class="chat mb-0">{{ @$user->lastMessage->message ?? 'No Message' }}</p>
            </div>
        </div>
        @if ($unreadCount > 0)
            <div class="chat-timer mb-10">
                <span class="time">{{ optional($user->lastMessage)->created_at ? $user->lastMessage->created_at->diffForHumans() : '' }}</span>
                <span class="chat-count">{{ $unreadCount }}</span>
            </div>
        @else
            <div class="chat-timer mb-10">
                <span class="chat-count bg-transparent text-primary"></span>
            </div>
        @endif
    </a>
@endforeach
