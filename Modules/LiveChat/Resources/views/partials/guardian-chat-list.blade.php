@foreach ($users as $user)
    @php
        $isStaff = $user instanceof \App\Models\Staff\Staff;
        $isAdminUser = $user instanceof \App\Models\User
            && in_array((int) $user->role_id, [\App\Enums\RoleEnum::SUPERADMIN, \App\Enums\RoleEnum::ADMIN], true);

        $partnerUid = $isStaff ? $user->user_id : $user->id;

        $displayName = $isStaff
            ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            : (string) ($user->name ?? '');

        $seenClass = '';
        if (! empty($user->lastMessage)) {
            if (@$user->lastMessage->is_seen == false) {
                $seenClass = 'has-unread-message';
            }
        }

        $unreadCount = $isAdminUser
            ? (int) ($user->_unread_count ?? 0)
            : (int) ($user->unreadMessages->count() ?? 0);
    @endphp
    <a class="single-chat  @if ($user->id == $user_id) active @endif {{ $seenClass }}"
        href="{{ route('guardian_livechat.chat', encryptFunction($partnerUid)) }}" id="receiver_id_{{ $partnerUid }}">
        <div class="chat-cap d-flex gap-3">
            <div class="user-img" aria-hidden="true"></div>
            <div class="user-chat-caption">
                <h5 class="user-name mb-0">{{ $displayName }}</h5>
                <p class="chat">{{ @$user->lastMessage->message ?? 'No Message' }}</p>
            </div>
        </div>
        @if ($unreadCount > 0)
            <div class="chat-timer mb-10">
                <span class="time">{{ optional($user->lastMessage)->created_at ? $user->lastMessage->created_at->diffForHumans() : '' }}</span>
                <span class="chat-count">{{ $unreadCount }}</span>
            </div>
        @endif
    </a>
@endforeach
{{-- Single Chat --}}
