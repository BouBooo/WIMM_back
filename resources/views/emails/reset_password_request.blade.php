@extends('emails.base')

@section('title')
    <div>
        Votre demande de changement de mot de passe
    </div>
@endsection

@section('description')
    <div>
        Pour modifier votre mot de passe, veuillez cliquer sur le lien ci-dessous :
    </div>
@endsection

@section('actions')
    <a href="{{ request()->getScheme() . '://' . request()->getHost() . '/reset-password/' . $token }}" target="_blank" style="font-family:'Roboto Slab',Arial,Helvetica,sans-serif;font-size:16px;line-height:19px;font-weight:700;font-style:normal;color:#000000;text-decoration:none;letter-spacing:0px;padding: 20px 50px 20px 50px;display: inline-block;"><span>Modifier mon mot de passe</span></a>
@endsection
