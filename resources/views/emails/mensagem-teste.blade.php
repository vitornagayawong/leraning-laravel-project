@component('mail::message')
# Sua nota fiscal meu caro!

<!-- @component('mail::button', ['url' => ''])
Botão
@endcomponent -->

Valeu pela compra,<br>
{{ config('app.name') }}
@endcomponent
