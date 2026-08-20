@include('admin.pages.income_categories._form', [
    'method' => 'PUT',
    'model' => $income_category ?? $model,
    'parentCategory' => $parentCategory ?? null,
    'parentId' => $parentId ?? null,
    'route' => route('admin.income-categories.update', $income_category ?? $model),
    'label' => 'Tushum Kategoriyasini Tahrirlash',
])
