@extends('mail.layouts.message')

@section('content')
<h2>Verificación de Correo Electrónico</h2>
<p>Tu código de verificación es: {{ $token }}</p>
<p>Este código expirará en 5 minutos.</p>
@endsection
