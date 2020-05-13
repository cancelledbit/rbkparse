@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <table class="table table-striped">
                    <tbody>
                    @foreach($articles as $article)
                        <tr>
                            <th scope="row">{{$article->getDateCreated()}}</th>
                            <td><b>{{$article->title}}</b>
                                <br>
                                <br>
                                {{$article->summary}}
                            </td>
                            <td></td>
                            <td><a href="/show/{{$article->id}}">Подробнее</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
