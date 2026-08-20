@include('admin.pages.admins._form', [
    'method' => 'PUT',
    'model' => $admin ?? $model,
    'route' => route('admin.admins.update', $admin ?? $model),
    'label' => 'Admin Ma\'lumotlarini Tahrirlash',
])
