<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr class="border-b">
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col">Xarajat Nomi & Izoh</th>
                <th scope="col">Kategoriya</th>
                <th scope="col">Summasi</th>
                <th scope="col">To'lov Usuli</th>
                <th scope="col">Sana & Vaqt</th>
                <th scope="col">Kiritgan Admin</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $index => $data)
                @php
                    $statusEnum = $data->status instanceof \App\Enums\Finance\ExpenseStatusEnum 
                        ? $data->status 
                        : \App\Enums\Finance\ExpenseStatusEnum::tryFrom($data->status) ?? \App\Enums\Finance\ExpenseStatusEnum::APPROVED;
                    $category = $data->category;
                    $parent = $category?->parent;
                @endphp
                <tr>
                    <td>
                        <span class="text-muted fw-medium">{{ $datas->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <h6 class="fw-bold mb-0 text-dark">{{ $data->title }}</h6>
                        @if ($data->description)
                            <small class="text-muted d-block mt-1">{{ Str::limit($data->description, 60) }}</small>
                        @endif
                    </td>
                    <td>
                        @if ($category)
                            <div class="d-flex flex-column">
                                <span class="badge bg-soft-primary text-primary px-2.5 py-1.5 align-self-start fw-bold">
                                    {{ $category->icon ?: '📁' }} {{ $category->name }}
                                </span>
                                @if ($parent)
                                    <small class="text-muted fs-11 mt-1">
                                        {{ $parent->icon }} {{ $parent->name }}
                                    </small>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold text-danger fs-14">
                            -{{ format_price($data->amount) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $pmEnum = $data->payment_method instanceof \App\Enums\Finance\PaymentMethodEnum 
                                ? $data->payment_method 
                                : \App\Enums\Finance\PaymentMethodEnum::tryFrom($data->payment_method);
                        @endphp
                        <span class="badge bg-light text-dark px-2.5 py-1.5">
                            <i class="{{ $pmEnum ? $pmEnum->icon() : 'feather-credit-card' }} me-1"></i> 
                            {{ $pmEnum ? $pmEnum->label() : ($data->payment_method ?: 'Karta') }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-medium text-dark fs-13">
                            <i class="feather-clock me-1 text-muted"></i>
                            {{ format_datetime($data->spent_at) }}
                        </span>
                    </td>
                    <td>
                        @if ($data->author)
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-text avatar-sm bg-soft-secondary text-dark rounded-circle">
                                    {{ mb_substr($data->author->name ?: 'A', 0, 1) }}
                                </div>
                                <span class="fs-13 fw-medium text-dark">{{ $data->author->name }}</span>
                            </div>
                        @else
                            <span class="text-muted">Tizim</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.expenses.toggle', $data) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="badge border-0 {{ $statusEnum->badgeClass() }} px-3 py-2 cursor-pointer" title="Statusni o'zgartirish (Tasdiqlash / Kutilmoqda)">
                                <i class="{{ $statusEnum->icon() }} me-1"></i>
                                {{ $statusEnum->label() }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <!-- Tahrirlash (Hoshiyasiz) -->
                            <a href="{{ route('admin.expenses.edit', $data) }}" 
                               class="btn btn-sm btn-soft-warning border-0" 
                               title="Tahrirlash">
                                <i class="feather-edit-3"></i>
                            </a>

                            <!-- O'chirish (Hoshiyasiz) -->
                            <button type="button" 
                                    data-url="{{ route('admin.expenses.destroy', $data) }}" 
                                    data-message="Haqiqatan ham ushbu Xarajatni o'chirmoqchimisiz?" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete" 
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
                            <i class="feather-trending-down fs-1 d-block mb-3 opacity-25"></i>
                            <h6>Hozircha xarajatlar mavjud emas</h6>
                            <p class="small mb-3">Yangi xarajat qo'shish uchun quyidagi tugmani bosing</p>
                            <a href="{{ route('admin.expenses.create') }}" class="btn btn-sm btn-primary">
                                <i class="feather-plus me-1"></i> Yangi Xarajat Qo'shish
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
