<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr class="border-b">
                <th scope="col" style="width: 80px;">Ikonka</th>
                <th scope="col">Afzallik Nomi (Sarlavha)</th>
                <th scope="col">Batafsil Tavsifi</th>
                <th scope="col">Status</th>
                <th scope="col">Tartib</th>
                <th scope="col" class="text-end">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $data)
                <tr>
                    <td>
                        @if ($data->icon)
                            <div class="avatar-image avatar-md rounded-3 bg-light d-flex align-items-center justify-content-center overflow-hidden border">
                                <img src="{{ asset($data->icon) }}" alt="{{ $data->title }}" style="width: 32px; height: 32px; object-fit: contain;">
                            </div>
                        @else
                            <div class="avatar-text avatar-md bg-soft-primary text-primary fs-4 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="feather-star"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <h6 class="fw-bold mb-0 text-dark">{{ $data->title }}</h6>
                    </td>
                    <td>
                        <span class="text-muted fs-13">{{ $data->description ?: 'Tavsif berilmagan' }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.subscription-features.toggle', $data) }}" method="POST" class="d-inline">
                            @csrf
                            @php
                                $statusVal = $data->status instanceof \App\Enums\Subscription\SubscriptionStatusEnum 
                                    ? $data->status->value 
                                    : ($data->status ?: ($data->is_active ? 'active' : 'inactive'));
                                $isActive = $statusVal === 'active';
                            @endphp
                            <button type="submit" class="badge border-0 {{ $isActive ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-3 py-2 cursor-pointer" title="Statusni o'zgartirish">
                                <i class="feather-{{ $isActive ? 'check-circle' : 'x-circle' }} me-1"></i>
                                {{ $isActive ? 'Faol' : 'Nofaol' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $data->order }}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <a href="{{ route('admin.subscription-features.edit', $data) }}" class="btn btn-sm btn-soft-warning border-0" title="Tahrirlash">
                                <i class="feather-edit-3"></i>
                            </a>
                            <button type="button" 
                                    data-url="{{ route('admin.subscription-features.destroy', $data) }}" 
                                    data-message="Haqiqatan ham ushbu Obuna afzalligini o'chirmoqchimisiz?" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete" 
                                    title="O'chirish">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="feather-check-circle fs-1 d-block mb-3 opacity-25"></i>
                            <h6>Hozircha hech qanday Obuna afzalligi mavjud emas</h6>
                            <p class="small mb-3">Yangi afzallik qo'shish uchun quyidagi tugmani bosing</p>
                            <a href="{{ route('admin.subscription-features.create') }}" class="btn btn-sm btn-primary">
                                <i class="feather-plus me-1"></i> Yangi Afzallik Qo'shish
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
