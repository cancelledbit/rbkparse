@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="p-2">
                    <h2>{{$article->title}}</h2>
                    <h6>{{$article->summary}}</h6>
                    @if ($article->image)
                        <img class="img-fluid" src="{{ asset('storage/'. $article->image) }}">
                    @endif
                    {!! $article->content !!}
                </div>
            </div>
        </div>
    </div>
@endsection
