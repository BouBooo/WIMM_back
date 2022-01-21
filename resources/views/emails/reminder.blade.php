@extends('emails.base')

@section('title')
    <p>Nouveau rappel</p>
@endsection

@section('description')
    <p>Vous avez un rappel pour : <b>{{ $reminder->title }}</b></p>
@endsection
