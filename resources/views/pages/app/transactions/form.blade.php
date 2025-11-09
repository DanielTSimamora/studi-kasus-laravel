@extends('layouts.app')
@section('content')
  <livewire:transaction-form :id="request()->route('id')" />
@endsection
