<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr class="border-b">
                <th scope="col" style="width: 70px;">Ikonka</th>
                <th scope="col">Nomi & Tavsifi</th>
                <th scope="col">Davomiyligi</th>
                <th scope="col">Narxi</th>
                <th scope="col">Nishon (Badge)</th>
                <th scope="col">Status</th>
                <th scope="col">Tartib</th>
                <th scope="col" class="text-end">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $data)
                <tr>
                    <td>
                        <div class="avatar-text avatar-md bg-soft-warning text-warning fs-4 rounded-3 d-flex align-items-center justify-content-center">
                            {{ $data->icon ?: '⚡' }}
                        </div>
                    </td>
                    <td>
                        <h6 class="fw-bold mb-1">{{ $data->display_title }}</h6>
                        <small class="text-muted">{{ $data->description ?: ($data->subtitle ?: 'Qo\'shimcha tavsif yo\'q') }}</small>
                    </td>
                    <td>
                        <span class="badge bg-soft-primary text-primary fw-bold px-2.5 py-1.5">
                            <i class="feather-clock me-1"></i> {{ $data->hours }} soat
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $data->formatted_price }}</div>
                        @if ($data->original_price)
                            <small class="text-muted text-decoration-line-through">{{ $data->formatted_original_price }}</small>
                        @endif
                    </td>
                    <td>
                        @if ($data->badge)
                            <span class="badge bg-soft-info text-info">{{ $data->badge }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.boosts.toggle', $data) }}" method="POST" class="d-inline">
                            @csrf
                            @php
                                $statusVal = $data->status instanceof \App\Enums\Boost\BoostStatusEnum 
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
                            <a href="{{ route('admin.boosts.edit', $data) }}" class="btn btn-sm btn-soft-warning border-0" title="Tahrirlash">
                                <i class="feather-edit-3"></i>
                            </a>
                            <button type="button" 
                                    data-url="{{ route('admin.boosts.destroy', $data) }}" 
                                    data-message="Haqiqatan ham ushbu Boost rejasini o'chirmoqchimisiz?" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete" 
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
                            <i class="feather-zap fs-1 d-block mb-3 opacity-25"></i>
                            <h6>Hozircha hech qanday Boost rejasi mavjud emas</h6>
                            <p class="small mb-3">Yangi reja qo'shish uchun quyidagi tugmani bosing</p>
                            <a href="{{ route('admin.boosts.create') }}" class="btn btn-sm btn-primary">
                                <i class="feather-plus me-1"></i> Yangi Boost Qo'shish
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
