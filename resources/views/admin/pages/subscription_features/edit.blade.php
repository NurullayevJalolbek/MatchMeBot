@include('admin.pages.subscription_features._form', [
    'method' => 'PUT',
    'model' => $subscription_feature ?? $model,
    'route' => route('admin.subscription-features.update', $subscription_feature ?? $model),
    'label' => 'Afzallikni Tahrirlash',
])
