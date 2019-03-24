@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col text-center">
                            <button
                                type="button"
                                class="btn btn-primary"
                                onclick="document.getElementById('logout-form').submit();"
                            >
                                {{ __('Logout') }}
                            </button>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-center">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
