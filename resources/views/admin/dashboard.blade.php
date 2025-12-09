@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
  <div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, {{ auth()->user()?->name ?? 'Admin' }}.</p>
    <p>
      This area is protected; only users with the
      <strong>admin</strong>
      role can access it.
    </p>
    <ul>
      <li><a href="#">Manage users</a></li>
      <li><a href="#">Manage settings</a></li>
    </ul>
  </div>
@endsection
