@component('mail::message')
# 取引完了のお知らせ

{{ $purchase->product->title }} の取引が完了しました。

- 商品名：{{ $purchase->product->title }}
- 購入者：{{ $purchase->buyer->name ?? '購入者' }}
- 金額：¥{{ number_format($purchase->amount) }}

ご利用ありがとうございました。

@endcomponent