{{-- プロフィール フォーム（create / edit 共通） --}}
@php
$avatarUrl = !empty($profile?->profile_image)
    ? asset('storage/'.$profile->profile_image)
    : asset('images/avatar-placeholder.png');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="form-page">
@csrf
@if ($isEdit ?? false)
@method('PUT')
@endif

{{-- アバター＆タイトル --}}
<h1 class="page-title">プロフィール設定</h1>

<div class="profile-head">
    <img id="avatarPreview"
    src="{{ $avatarUrl ?? asset('images/avatar-placeholder.png') }}" alt="avatar" class="avatar"/>

<label for="profile_image" class="btn btn-outline btn-md">
    画像を選択する
</label>
    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="visually-hidden">
    @error('profile_image') <p class="error">{{ $message }}</p> @enderror
</div>

{{-- 入力欄 --}}
<div class="form form--narrow">
    <div class="form_group">
    <label class="form_label">ユーザー名</label>
    <input type="text" name="name" value="{{ old('name', $profile->name ?? auth()->user()->name) }}" class="form_control" required>
    @error('name') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="form_group">
    <label class="form_label">郵便番号</label>
    <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}" class="form_control" placeholder="123-4567">
    @error('postal_code') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="form_group">
    <label class="form_label">住所</label>
    <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}" class="form_control" required>
    @error('address') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="form_group">
    <label class="form_label">建物（任意）</label>
    <input type="text" name="building" value="{{ old('building', $profile->building ?? '') }}" class="form_control">
    @error('building') <p class="error">{{ $message }}</p> @enderror
</div>

<button type="submit" class="btn btn-primary btn-lg btn-block">
    {{ $submitText }}
</button>
</div>

@push('scripts')
<script>
  // アバタープレビュー
document.getElementById('profile_image')?.addEventListener('change', (e) => {
    const f = e.target.files?.[0];
    if (!f) return;
    const url = URL.createObjectURL(f);
    document.getElementById('avatarPreview').src = url;
});
</script>
@endpush