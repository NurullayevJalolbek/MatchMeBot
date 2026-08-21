<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col" style="min-width: 200px;">Ikonka & Nomi</th>
                <th scope="col" style="min-width: 180px;">Guruhi / Toifasi</th>
                <th scope="col" style="min-width: 160px;">Bo'lim Turi</th>
                <th scope="col" style="width: 90px;">Tartibi</th>
                <th scope="col" style="width: 110px;">Status</th>
                <th scope="col" class="text-end" style="min-width: 120px;">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($datas as $index => $data)
                @php
                    $typeEnum = $data->type instanceof \App\Enums\Profile\ProfileOptionTypeEnum 
                        ? $data->type 
                        : \App\Enums\Profile\ProfileOptionTypeEnum::tryFrom($data->type);
                @endphp
                <tr id="option-row-{{ $data->id }}">
                    <td>
                        <span class="text-muted fw-medium">{{ $datas->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2.5">
                            @if($data->icon)
                                <div class="avatar-text avatar-md bg-light rounded-3 fs-20 border d-flex align-items-center justify-content-center">
                                    {{ $data->icon }}
                                </div>
                            @else
                                <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-3 fw-bold fs-14">
                                    {{ mb_substr($data->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold mb-0 text-dark fs-14">{{ $data->name }}</h6>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if ($data->category)
                            <span class="badge bg-light text-dark px-2.5 py-1.5 border fs-12 fw-medium">
                                <i class="feather-tag me-1 text-muted"></i> {{ $data->category }}
                            </span>
                        @else
                            <span class="text-muted fs-12">Umumiy</span>
                        @endif
                    </td>
                    <td>
                        @if ($typeEnum)
                            <span class="badge {{ $typeEnum->badgeClass() }} px-2.5 py-1.5 fs-12">
                                <i class="{{ $typeEnum->icon() }} me-1"></i> {{ $typeEnum->label() }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted">{{ $data->type }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark font-monospace px-2 py-1 fs-12 border">
                            {{ $data->order }}
                        </span>
                    </td>
                    <td>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input status-toggle cursor-pointer" 
                                   type="checkbox" 
                                   role="switch" 
                                   data-url="{{ route('admin.profile-options.toggle', $data) }}"
                                   {{ $data->is_active ? 'checked' : '' }}
                                   title="Holatni o'zgartirish">
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <a href="{{ route('admin.profile-options.edit', $data) }}" 
                               class="btn btn-sm btn-soft-primary border-0" 
                               title="Tahrirlash">
                                <i class="feather-edit-3"></i>
                            </a>

                            <button type="button" 
                                    data-url="{{ route('admin.profile-options.destroy', $data) }}" 
                                    data-message="Haqiqatan ham ushbu parametrni o'chirmoqchimisiz?" 
                                    class="btn btn-sm btn-soft-danger border-0 btn-delete ms-1" 
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
                            <i class="feather-list fs-1 d-block mb-3 opacity-25"></i>
                            <h6>Parametrlar topilmadi</h6>
                            <p class="small mb-0">Hozircha ushbu bo'limda hech qanday parametr kiritilmagan</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
