@include('admin.pages.expenses._form', [
    'method' => 'PUT',
    'model' => $expense ?? $model,
    'route' => route('admin.expenses.update', $expense ?? $model),
    'label' => 'Xarajatni Tahrirlash',
    'categories' => $categories ?? null,
])
