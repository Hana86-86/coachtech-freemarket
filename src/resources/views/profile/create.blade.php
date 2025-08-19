@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<div class="form-container" style="max-width:560px">
    <h2 class="form-title">プロフィール設定</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0;padding-left:18px">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif

    {{-- 登録フォーム --}}
    <form method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- アバター（丸型プレビュー & 削除ボタン） --}}
    @php
    $avatarUrl = ($profile?->profile_image)
            ? Storage::url($profile->profile_image)
            : asset('images/avatar-placeholder.png');
    @endphp

    <div style="display:flex;gap:16px;align-items:center;margin-bottom:16px">
        <img
        id="avatarPreview"
        src="{{ $avatarUrl }}"
        alt="プロフィール画像"
        style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:1px solid #eee;background:#fafafa" />
    <div>
        <label class="btn btn--primary" style="cursor:pointer;">
            画像を選択
        <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display:none">
        </label>
        <p style="margin:.5rem 0 0;color:#666;font-size:12px;">JPEG / PNG /WebP、 最大 <strong>15MB</strong></p>
        @error('profile_image') <p class="error">{{ $message }}</p> @enderror
    </div>

    @if($profile?->profile_image)
        <button type="button"
                class="btn"
                style="background:#f3f4f6;border:1px solid #ddd;margin-left:auto"
                onclick="if (confirm('プロフィール画像を削除しますか？')) document.getElementById('avatar-delete-form').submit();">
        画像を削除
    </button>
@endif
</div>

    <label>ユーザー名</label>
    <input type="text" class="input" name="name"
        value="{{ old('name', optional($profile)->name ?? auth()->user()->name) }}" required>
    @error('name') <p class="error">{{ $message }}</p> @enderror

    <label>郵便番号</label>
    <input type="text" class="input" name="postal_code"
        value="{{ old('postal_code', optional($profile)->postal_code ?? '') }}"
        placeholder="123-4567">
    @error('postal_code') <p class="error">{{ $message }}</p> @enderror

    <label>住所</label>
    <input type="text" class="input" name="address"
        value="{{ old('address', optional($profile)->address ?? '') }}" required>
    @error('address') <p class="error">{{ $message }}</p> @enderror

    <label>建物名（任意）</label>
    <input type="text" class="input" name="building"
        value="{{ old('building', optional($profile)->building ?? '') }}">
    @error('building') <p class="error">{{ $message }}</p> @enderror

    <button type="submit" class="btn btn--primary" style="width:100%;margin-top:16px">登録する</button>
</form>
</div>
    @if ($profile?->profile_image)
    <form id="avatar-delete-form" action="{{ route('profile.avatar.destroy') }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endif

@push('scripts')
<script>
  // 画像即時プレビュー
    document.getElementById('profile_image')?.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    document.getElementById('avatarPreview').src = url;
    });
</script>
@endpush
@endsection