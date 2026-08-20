<form action="{{ route('admin.subscriptions.index') }}" method="GET" class="d-flex align-items-center gap-2">
    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">Barcha statuslar</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nofaol</option>
    </select>
    <div class="input-group input-group-sm">
        <input type="text" name="search" class="form-control" placeholder="Qidirish..." value="{{ request('search') }}">
        <button class="btn btn-light-brand" type="submit"><i class="feather-search"></i></button>
    </div>
</form>
