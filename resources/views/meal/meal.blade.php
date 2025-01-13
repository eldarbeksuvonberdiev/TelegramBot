@extends('meal.main')

@section('title', 'Main')

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('meal.create') }}" class="btn btn-primary">Create</a>
        </div>
        <div class="col-12">
            <table class="table table-bordered table-hover table-striped mt-5">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        <th>Show</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($meals as $meal)
                        <tr>
                            <td>{{ $meal->id }}</td>
                            <td>{{ $meal->name }}</td>
                            <td>Show</td>
                            <td>Update</td>
                            <td>Delete</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
