@include('admin.pages.boosts._form', [
    'method' => 'PUT',
    'model' => $model ?? $boost,
    'route' => route('admin.boosts.update', $model ?? $boost),
    'label' => 'Boostni Tahrirlash',
])
