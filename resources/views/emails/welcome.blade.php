@extends('emails.base')

@section('title')
    <div>
        Bienvenue {{ $user->firstName }}
    </div>
@endsection

@section('description')
    <div>
        Bienvenue sur le site Where Is My Money, qui vous permet de suivre vos dépenses et revenus très simplement.
    </div>
@endsection

@section('actions')
    <a href="{{ request()->getScheme() . '://' . request()->getHost() }}" target="_blank"    style="font-family:'Roboto Slab',Arial,Helvetica,sans-serif;font-size:16px;line-height:19px;font-weight:700;font-style:normal;color:#000000;text-decoration:none;letter-spacing:0px;padding: 20px 50px 20px 50px;display: inline-block;"><span>Commencer</span></a>
@endsection
