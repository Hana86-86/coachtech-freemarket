@extends('layouts.app')

@section('content')
<div class="container container--narrow">

@include('profile._form', [
    'action'     => route('profile.update'),
    'method'     => 'PUT',
    'submitText' => '更新する',
    'isEdit'     => true,
    'profile'    => $profile ?? null,
])
</div>
@endsection