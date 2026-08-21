<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col" style="min-width: 220px;">Foydalanuvchi (Telegram)</th>
                <th scope="col" style="min-width: 200px;">Xizmat (Tarif / Reja)</th>
                <th scope="col" style="min-width: 180px;">Tushum Kategoriyasi</th>
                <th scope="col" style="min-width: 100px;">Chek / Skrinshot</th>
                <th scope="col">Summasi</th>
                <th scope="col">Yuborilgan Sana</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end" style="min-width: 170px;">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $index => $data)
                @php
                    $statusEnum = $data->status instanceof \App\Enums\Finance\PaymentStatusEnum 
                        ? $data->status 
                        : \App\Enums\Finance\PaymentStatusEnum::tryFrom($data->status) ?? \App\Enums\Finance\PaymentStatusEnum::PENDING;
                    $user = $data->user;
                    $payable = $data->payable;
                    $category = $data->incomeCategory;
                    $parentCat = $category?->parent;
                    $isPending = $statusEnum === \App\Enums\Finance\PaymentStatusEnum::PENDING;
                    $isApproved = $statusEnum === \App\Enums\Finance\PaymentStatusEnum::APPROVED;
                    $isRejected = $statusEnum === \App\Enums\Finance\PaymentStatusEnum::REJECTED;

                    $displayName = $user?->name ?: trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? '')) ?: 'Noma\'lum';
                    $chatId = $user?->telegram_id ?: $user?->id;
                @endphp
                <tr id="payment-row-{{ $data->id }}">
                    <td>
                        <span class="text-muted fw-medium">{{ $datas->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle fw-bold fs-14">
                                {{ mb_substr($displayName, 0, 1) }}
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <h6 class="fw-bold mb-0 text-dark fs-13">{{ $displayName }}</h6>
                                    @if($user?->is_vip || $user?->is_premium)
                                        <span class="badge bg-soft-warning text-warning p-1" title="VIP Premium Foydalanuvchi">
                                            <i class="feather-award"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                    @if($user?->username)
                                        <a href="https://t.me/{{ $user->username }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none px-2 py-0.5 fs-11" title="Telegram profilini ochish">
                                            &#64;{{ $user->username }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted px-2 py-0.5 fs-11">Username yo'q</span>
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
                        @if ($payable)
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-20">{{ $payable->icon ?: ($payable instanceof \App\Models\BoostPlan ? '⚡' : '👑') }}</span>
                                <div>
                                    <span class="fw-bold text-dark fs-13 d-block">{{ $payable->title ?: $payable->name }}</span>
                                    <div class="d-flex align-items-center gap-1 mt-0.5">
                                        @if($payable instanceof \App\Models\BoostPlan)
                                            <span class="badge bg-soft-warning text-warning px-1.5 py-0.5 fs-11">
                                                <i class="feather-zap me-1"></i> Boost ({{ $payable->hours }} soat)
                                            </span>
                                        @else
                                            <span class="badge bg-soft-info text-info px-1.5 py-0.5 fs-11">
                                                <i class="feather-award me-1"></i> Obuna ({{ $payable->formatted_period ?: 'VIP' }})
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Umumiy To'lov</span>
                        @endif
                    </td>
                    <td>
                        @if ($category)
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-soft-success text-success px-2.5 py-1.5 fw-bold align-self-start fs-12">
                                    {{ $category->icon ?: '💎' }} {{ $category->name }}
                                </span>
                                @if ($parentCat)
                                    <small class="text-muted fs-11">
                                        <i class="feather-corner-down-right me-1 text-success"></i> {{ $parentCat->icon }} {{ $parentCat->name }}
                                    </small>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($data->receipt_image)
                            <button type="button" 
                                    class="btn p-0 border-0 btn-view-receipt position-relative group" 
                                    data-image="{{ $data->receipt_url }}"
                                    data-user="{{ $displayName }}"
                                    data-chat="{{ $chatId }}"
                                    data-amount="{{ format_price($data->amount) }}"
                                    title="Chekni to'liq ko'rish">
                                <img src="{{ $data->receipt_url }}" alt="Chek" class="rounded-3 border shadow-sm object-fit-cover" style="width: 48px; height: 48px; cursor: pointer;">
                                <span class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 16px; height: 16px; font-size: 8px;">
                                    <i class="feather-maximize-2"></i>
                                </span>
                            </button>
                        @else
                            <span class="badge bg-light text-muted px-2 py-1 fs-11">Cheksiz</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold text-success fs-14">
                            +{{ format_price($data->amount) }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-medium text-dark fs-13">
                            <i class="feather-clock me-1 text-muted"></i>
                            {{ format_datetime($data->created_at) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $statusEnum->badgeClass() }} px-2.5 py-1.5 fs-12">
                            <i class="{{ $statusEnum->icon() }} me-1"></i>
                            {{ $statusEnum->label() }}
                        </span>
                        @if($isRejected && $data->rejection_reason)
                            <small class="text-danger d-block mt-1 fs-11" title="{{ $data->rejection_reason }}">
                                <i class="feather-alert-triangle me-1"></i>{{ Str::limit($data->rejection_reason, 25) }}
                            </small>
                        @endif
                        @if($isApproved && $data->approver)
                            <small class="text-muted d-block mt-1 fs-10">
                                Admin: {{ $data->approver->name }}
                            </small>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            @if ($isPending)
                                <!-- Tasdiqlash Tugmasi (Yashil) -->
                                <button type="button" 
                                        data-url="{{ route('admin.payments.approve', $data) }}" 
                                        data-id="{{ $data->id }}"
                                        data-user="{{ $displayName }}"
                                        data-plan="{{ $payable?->title ?: 'Xizmat' }}"
                                        class="btn btn-sm btn-success border-0 btn-approve px-2.5 py-1.5 shadow-sm" 
                                        title="To'lovni Tasdiqlash">
                                    <i class="feather-check me-1"></i> Tasdiqlash
                                </button>

                                <!-- Rad etish Tugmasi (Qizil) -->
                                <button type="button" 
                                        data-url="{{ route('admin.payments.reject', $data) }}" 
                                        data-id="{{ $data->id }}"
                                        data-user="{{ $displayName }}"
                                        class="btn btn-sm btn-danger border-0 btn-reject px-2.5 py-1.5 shadow-sm" 
                                        title="To'lovni Rad etish / Qaytarish">
                                    <i class="feather-x me-1"></i> Qaytarish
                                </button>
                            @else
                                <span class="badge bg-light text-muted px-2 py-1 fs-11">Ko'rib chiqilgan</span>
                            @endif

                            <!-- O'chirish (Hoshiyasiz) -->
                            <button type="button" 
                                    data-url="{{ route('admin.payments.destroy', $data) }}" 
                                    data-message="Haqiqatan ham ushbu To'lov arizasini o'chirmoqchimisiz?" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete ms-1" 
                                    title="O'chirish">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="text-muted">
                            <i class="feather-inbox fs-1 d-block mb-3 opacity-25"></i>
                            <h6>To'lov arizalari topilmadi</h6>
                            <p class="small mb-0">Hozircha kutilayotgan yoki tasdiqlangan to'lovlar mavjud emas</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
