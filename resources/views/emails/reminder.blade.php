@extends('emails.base')

@section('mailContent')
    <p>You have a reminder today for : <b>{{ $reminder->title }}</b></p>
@endsection
