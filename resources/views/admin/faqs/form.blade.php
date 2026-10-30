@extends('admin.orderable.form')

@section('fields')
    <x-admin.field name="question" label="Pregunta" :value="$item->question" required />
    <x-admin.field name="answer" label="Respuesta" :value="$item->answer" rows="6" required />
@endsection
