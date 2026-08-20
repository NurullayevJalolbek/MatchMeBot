@include('admin.pages.subscriptions._form', [
    'method' => 'PUT',
    'model' => $subscription ?? $model,
    'route' => route('admin.subscriptions.update', $subscription ?? $model),
    'label' => 'Obunani Tahrirlash',
])
