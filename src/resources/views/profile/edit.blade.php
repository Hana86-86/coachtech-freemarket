@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="form-page" novalidate>
    @csrf
    @method('PUT')

    <h1 class="page-title">プロフィール設定</h1>

    {{-- アバター＆アップロード --}}
    <div class="profile-head">
        <img id="avatarPreview"
        src="{{ $user->avatar_url }}"
        alt="{{ $user->name }}"
        class="avatar" />
        <label for="profile_image" class="btn btn-outline btn-md">画像を選択する</label>
        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="visually-hidden">
        @error('profile_image') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- 入力欄 --}}
    <div class="form form--narrow">
        <div class="form_group">
            <label class="form_label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form_control" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form_group">
            <label class="form_label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}"
                class="form_control" placeholder="123-4567" inputmode="numeric">
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

        <button type="submit" class="btn btn-primary btn-lg btn-block">更新する</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
  // 画像プレビュー
document.getElementById('profile_image')?.addEventListener('change', (e) => {
    const f = e.target.files?.[0];
    if (!f) return;
    const url = URL.createObjectURL(f);
    const img = document.getElementById('avatarPreview');
    img.src = url;
});
</script>
@endpush