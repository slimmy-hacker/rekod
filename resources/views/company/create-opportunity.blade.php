@extends('layouts.my_app')

@section('content')
<div class="container">
    <h1>Create Opportunity</h1>

    <form action="{{ route('company.opportunities.store') }}" method="POST">
        @csrf
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description"></textarea>
        </div>

        <button type="submit">Save</button>
    </form>
</div>
@endsection
