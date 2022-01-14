@extends('emails.base')

@section('mailContent')
    <hi>My reminder for : {{ $reminder->title }}</hi>
@endsection
