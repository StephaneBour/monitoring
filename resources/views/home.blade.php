@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <table class="table table-hover table-light table-striped">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>UUID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($checks as $check)
                        <tr>
                            <td>{{ $check['_source']['status'] }}</td>
                            <td>{{ $check['_source']['uuid'] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
