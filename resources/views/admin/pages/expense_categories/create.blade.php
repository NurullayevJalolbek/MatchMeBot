@include('admin.pages.expense_categories._form', [
    'route' => route('admin.expense-categories.store'),
    'parentCategory' => $parentCategory ?? null,
    'parentId' => $parentId ?? null,
    'label' => isset($parentCategory) && $parentCategory ? '"' . $parentCategory->name . '" ichiga yangi bo\'lim' : 'Yangi Xarajat Kategoriyasi',
])
