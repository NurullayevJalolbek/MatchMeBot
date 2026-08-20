<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr class="border-b">
                <th scope="col" style="width: 70px;">Avatar</th>
                <th scope="col">Admin Ism-familiyasi</th>
                <th scope="col">Foydalanuvchi Nomi (Username)</th>
                <th scope="col">Email Manzili</th>
                <th scope="col">Status</th>
                <th scope="col">Qo'shilgan Sana</th>
                <th scope="col" class="text-end">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $data)
                @php
                    $isSelf = auth()->id() === $data->id;
                    $statusVal = $data->status instanceof \App\Enums\Admin\AdminStatusEnum 
                        ? $data->status->value 
                        : ($data->status ?: 'active');
                    $isActive = $statusVal === 'active';
                @endphp
                <tr>
                    <td>
                        <div class="avatar-text avatar-md bg-soft-primary text-primary fs-5 rounded-circle d-flex align-items-center justify-content-center fw-bold">
                            {{ mb_strtoupper(mb_substr($data->name ?: ($data->username ?: 'A'), 0, 1)) }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold mb-0 text-dark">{{ $data->name ?: 'Ismsiz Admin' }}</h6>
                            @if ($isSelf)
                                <span class="badge bg-soft-success text-success fs-11">Siz</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark fw-medium">{{ $data->username ? '@' . $data->username : '—' }}</span>
                    </td>
                    <td>
                        <span class="text-muted">{{ $data->email ?: '—' }}</span>
                    </td>
                    <td>
                        @if ($isSelf)
                            <span class="badge bg-soft-success text-success px-3 py-2">
                                <i class="feather-check-circle me-1"></i> Faol (Siz)
                            </span>
                        @else
                            <form action="{{ route('admin.admins.toggle', $data) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="badge border-0 {{ $isActive ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-3 py-2 cursor-pointer" title="Statusni o'zgartirish">
                                    <i class="feather-{{ $isActive ? 'check-circle' : 'slash' }} me-1"></i>
                                    {{ $isActive ? 'Faol' : 'Bloklangan' }}
                                </button>
                            </form>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ $data->created_at ? $data->created_at->format('d.m.Y H:i') : '—' }}</small>
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <a href="{{ route('admin.admins.edit', $data) }}" class="btn btn-sm btn-soft-warning border-0" title="Tahrirlash">
                                <i class="feather-edit-3"></i>
                            </a>
                            @if (!$isSelf)
                                <button type="button" 
                                        data-url="{{ route('admin.admins.destroy', $data) }}" 
                                        data-message="Haqiqatan ham ushbu Administratorni o'chirmoqchimisiz?" 
                                        class="btn btn-sm btn-soft-danger border-0 btn-delete" 
                                        title="O'chirish">
                                    <i class="feather-trash-2"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="feather-shield fs-1 d-block mb-3 opacity-25"></i>
                            <h6>Hozircha boshqa admin mavjud emas</h6>
                            <p class="small mb-3">Yangi admin qo'shish uchun quyidagi tugmani bosing</p>
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-primary">
                                <i class="feather-plus me-1"></i> Yangi Admin Qo'shish
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
