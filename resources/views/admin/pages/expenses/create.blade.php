@include('admin.pages.expenses._form', [
    'route' => route('admin.expenses.store'),
    'label' => 'Yangi Xarajat Qo\'shish',
    'categories' => $categories ?? null,
])
