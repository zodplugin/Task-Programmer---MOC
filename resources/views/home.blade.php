@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Data User') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Nomor Telepon</th>
                                    <th scope="col">Provinsi</th>
                                    <th scope="col">Kota</th>
                                    <th scope="col">Kecamatan</th>
                                    <th scope="col">Referal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->no_telp }}</td>
                                        <td>{{ $user->provinsi }}</td>
                                        <td>{{ $user->kota }}</td>
                                        <td>{{ $user->kecamatan }}</td>
                                        <td>{{ $user->referal ? $user->referal : "Tidak Ada Referal" }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
