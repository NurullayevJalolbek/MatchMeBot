<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr class="border-b">
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col" style="width: 60px;">Ikonka</th>
                <th scope="col">Kategoriya Nomi & Tavsifi</th>
                <th scope="col">Ichki Bo'limlar</th>
                <th scope="col">Status</th>
                <th scope="col" style="width: 70px;">Tartib</th>
                <th scope="col" class="text-end">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $index => $data)
                @php
                    $statusVal = $data->status instanceof \App\Enums\Finance\FinanceStatusEnum 
                        ? $data->status->value 
                        : ($data->status ?: 'active');
                    $isActive = $statusVal === 'active';
                    $childrenCount = $data->children->count();
                @endphp
                <tr>
                    <td>
                        <span class="text-muted fw-medium">{{ $datas->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <div class="avatar-text avatar-md rounded-3 d-flex align-items-center justify-content-center fs-4 bg-light shadow-sm">
                            {{ $data->icon ?: '📁' }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('admin.expense-categories.index', ['parent_id' => $data->id]) }}" class="text-dark fw-bold fs-14 text-decoration-none hover-primary">
                                {{ $data->name }}
                            </a>
                        </div>
                        @if ($data->description)
                            <small class="text-muted d-block mt-1">{{ Str::limit($data->description, 80) }}</small>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.expense-categories.index', ['parent_id' => $data->id]) }}" class="badge {{ $childrenCount > 0 ? 'bg-soft-primary text-primary' : 'bg-light text-muted' }} px-3 py-2 text-decoration-none" title="Ichiga kirish">
                            <i class="feather-folder me-1"></i>
                            {{ $childrenCount > 0 ? $childrenCount . ' ta ichki bo\'lim' : 'Bo\'sh (0 ta)' }}
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('admin.expense-categories.toggle', $data) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="badge border-0 {{ $isActive ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-3 py-2 cursor-pointer" title="Statusni o'zgartirish">
                                <i class="feather-{{ $isActive ? 'check-circle' : 'slash' }} me-1"></i>
                                {{ $isActive ? 'Faol' : 'Nofaol' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $data->order }}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <!-- Ko'zcha (Ichiga kirish / Bola kategoriyalar) -->
                            <a href="{{ route('admin.expense-categories.index', ['parent_id' => $data->id]) }}" 
                               class="btn btn-sm btn-soft-primary border-0" 
                               title="Ichiga kirish (Bola kategoriyalar)">
                                <i class="feather-eye"></i>
                            </a>

                            <!-- Tahrirlash (Chegarasiz / Borderless) -->
                            <a href="{{ route('admin.expense-categories.edit', $data) }}" 
                               class="btn btn-sm btn-soft-warning border-0" 
                               title="Tahrirlash">
                                <i class="feather-edit-3"></i>
                            </a>

                            <!-- O'chirish (Chegarasiz / Borderless) -->
                            <button type="button" 
                                    data-url="{{ route('admin.expense-categories.destroy', $data) }}" 
                                    data-message="{{ $childrenCount > 0 ? 'DIQQAT: Ushbu kategoriyaning ichida ' . $childrenCount . ' ta ichki bo\'lim mavjud! O\'chirishni xohlaysizmi?' : 'Haqiqatan ham ushbu Xarajat kategoriyasini o\'chirmoqchimisiz?' }}" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete" 
                                    title="O'chirish">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="feather-folder fs-1 d-block mb-3 opacity-25"></i>
                            <h6>{{ isset($parentCategory) && $parentCategory ? '"' . $parentCategory->name . '" ichida hali ichki kategoriya yaratilmagan' : 'Hozircha xarajat kategoriyasi qo\'shilmagan' }}</h6>
                            <p class="small mb-3">Yangi kategoriya qo'shish uchun quyidagi tugmani bosing</p>
                            @if(isset($parentCategory) && $parentCategory)
                                <a href="{{ route('admin.expense-categories.create', ['parent_id' => $parentCategory->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="feather-plus me-1"></i> Ushbu Guruhga Ichki Kategoriya Qo'shish
                                </a>
                            @else
                                <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-sm btn-primary">
                                    <i class="feather-plus me-1"></i> Yangi Kategoriya Qo'shish
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
