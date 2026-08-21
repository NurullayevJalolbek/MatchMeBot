<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col" style="min-width: 220px;">Foydalanuvchi (Telegram)</th>
                <th scope="col">Obuna Tarifi</th>
                <th scope="col">To'lov Summasi</th>
                <th scope="col">Boshlangan Vaqt</th>
                <th scope="col">Tugash Vaqti</th>
                <th scope="col">Holati</th>
                <th scope="col" class="text-end" style="min-width: 120px;">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $index => $data)
                @php
                    $statusEnum = $data->status instanceof \App\Enums\Subscription\UserServiceStatusEnum 
                        ? $data->status 
                        : \App\Enums\Subscription\UserServiceStatusEnum::tryFrom($data->status) ?? \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE;
                    $user = $data->user;
                    $plan = $data->plan;
                    $payment = $data->payment;
                    $isActive = $statusEnum === \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE && $data->ends_at && $data->ends_at->isFuture();
                    
                    $displayName = $user?->name ?: trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? '')) ?: 'Noma\'lum';
                    $chatId = $user?->telegram_id ?: $user?->id;
                @endphp
                <tr id="sub-row-{{ $data->id }}">
                    <td>
                        <span class="text-muted fw-medium">{{ $datas->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle fw-bold fs-14">
                                {{ mb_substr($displayName, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark fs-13">{{ $displayName }}</h6>
                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                    @if($user?->username)
                                        <a href="https://t.me/{{ $user->username }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none px-2 py-0.5 fs-11">
                                            &#64;{{ $user->username }}
                                        </a>
                                    @endif
                                    @if($chatId)
                                        <span class="badge bg-light text-dark font-monospace px-2 py-0.5 fs-11 copy-chat-id cursor-pointer" 
                                              data-chat-id="{{ $chatId }}" 
                                              title="Chat ID dan nusxa olish">
                                            <i class="feather-hash me-0.5"></i>{{ $chatId }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if ($plan)
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-18">{{ $plan->icon ?: '👑' }}</span>
                                <div>
                                    <span class="fw-bold text-dark fs-13 d-block">{{ $plan->title }}</span>
                                    <span class="badge bg-soft-info text-info px-1.5 py-0.5 fs-11">
                                        {{ $plan->formatted_period ?: 'VIP Paket' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold text-dark fs-13">
                            {{ $payment ? format_price($payment->amount) : ($plan ? format_price($plan->price) : '—') }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-medium text-dark fs-13">
                            <i class="feather-calendar me-1 text-muted"></i>
                            {{ format_datetime($data->starts_at) }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-medium text-dark fs-13">
                            <i class="feather-clock me-1 text-muted"></i>
                            {{ format_datetime($data->ends_at) }}
                        </span>
                    </td>
                    <td>
                        @if($isActive)
                            <span class="badge bg-soft-success text-success px-2.5 py-1.5 fs-12">
                                <i class="feather-check-circle me-1"></i> Faol
                            </span>
                            <small class="text-muted d-block mt-1 fs-11">
                                {{ $data->ends_at->diffForHumans() }}
                            </small>
                        @elseif($statusEnum === \App\Enums\Subscription\UserServiceStatusEnum::CANCELLED)
                            <span class="badge bg-soft-danger text-danger px-2.5 py-1.5 fs-12">
                                <i class="feather-x-circle me-1"></i> Bekor qilingan
                            </span>
                        @else
                            <span class="badge bg-soft-secondary text-secondary px-2.5 py-1.5 fs-12">
                                <i class="feather-clock me-1"></i> Tugagan
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            @if ($isActive)
                                <button type="button" 
                                        data-url="{{ route('admin.user-subscriptions.cancel', $data) }}" 
                                        data-user="{{ $displayName }}"
                                        class="btn btn-sm btn-soft-warning border-0 btn-cancel-sub px-2.5 py-1.5" 
                                        title="Obunani muddatidan oldin bekor qilish">
                                    <i class="feather-slash me-1"></i> Bekor qilish
                                </button>
                            @endif

                            <button type="button" 
                                    data-url="{{ route('admin.user-subscriptions.destroy', $data) }}" 
                                    data-message="Haqiqatan ham ushbu obuna tarixini o'chirmoqchimisiz?" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete ms-1" 
                                    title="O'chirish">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                            <i class="feather-award fs-1 d-block mb-3 opacity-25"></i>
                            <h6>Obunalar tarixi mavjud emas</h6>
                            <p class="small mb-0">Hozircha hech qanday foydalanuvchi obunasi qayd etilmagan</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
