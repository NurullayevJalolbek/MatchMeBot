<form action="{{ route('admin.expense-categories.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
    @if(isset($parentId) && $parentId)
        <input type="hidden" name="parent_id" value="{{ $parentId }}">
    @endif

    <select name="status" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
        <option value="">Barcha statuslar</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nofaol</option>
    </select>

    <div class="input-group input-group-sm">
        <input type="text" name="search" class="form-control" placeholder="Kategoriya nomi, kodi..." value="{{ request('search') }}">
        <button class="btn btn-light-brand" type="submit"><i class="feather-search"></i></button>
    </div>
</form>
