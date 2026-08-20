@include('admin.pages.expense_categories._form', [
    'method' => 'PUT',
    'model' => $expense_category ?? $model,
    'parentCategory' => $parentCategory ?? null,
    'parentId' => $parentId ?? null,
    'route' => route('admin.expense-categories.update', $expense_category ?? $model),
    'label' => 'Xarajat Kategoriyasini Tahrirlash',
])
