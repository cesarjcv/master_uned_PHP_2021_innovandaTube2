@extends('principal')

@section('contenido')
    <listas-horizontales-componente ref="carruseles"></listas-horizontales-componente>
    <dialogo-buscar-componente identificador="ventanabuscar" referenciabus="carruseles"></dialogo-buscar-componente>
@endsection