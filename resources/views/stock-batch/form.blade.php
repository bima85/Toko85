@extends('layouts.app')

@section('title', 'Manajemen Stok Tumpukan - Form')

@section('content')
  <div class="container-fluid">
    <div class="row mb-4">
      <div class="col-md-12">
        <h1 class="h3">Form Manajemen Stok</h1>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <div class="row">
      <div class="col-md-12">
        <livewire:admin.stock-batch-form />
      </div>
    </div>
  </div>
@endsection
