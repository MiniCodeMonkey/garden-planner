@extends('layouts.app')

@section('content')

<script>
window.DATES = JSON.parse('@json($dates)');
</script>

<div id="calendar"></div>

@endsection
