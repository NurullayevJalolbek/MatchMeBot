@include('admin.pages.income_categories._form', [
    'route' => route('admin.income-categories.store'),
    'parentCategory' => $parentCategory ?? null,
    'parentId' => $parentId ?? null,
    'label' => isset($parentCategory) && $parentCategory ? '"' . $parentCategory->name . '" ichiga yangi bo\'lim' : 'Yangi Tushum Kategoriyasi',
])
