<form action="{{ route('admin.admins.index') }}" method="GET" class="d-flex align-items-center gap-2">
    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">Barcha statuslar</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
        <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Bloklangan</option>
    </select>
    <div class="input-group input-group-sm">
        <input type="text" name="search" class="form-control" placeholder="Ism, username, email..." value="{{ request('search') }}">
        <button class="btn btn-light-brand" type="submit"><i class="feather-search"></i></button>
    </div>
</form>
