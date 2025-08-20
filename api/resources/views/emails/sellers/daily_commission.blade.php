@php
    use App\Support\Format;
    $ratePct = Format::percentBR($rate);
@endphp

@component('mail::message')
# Olá, {{ $summary->sellerName }}!

Aqui está o seu **resumo diário** de vendas em **{{ Format::dateBR($summary->date) }}**:

@component('mail::table')
| Métrica              | Valor                          |
|:---------------------|:-------------------------------|
| Vendas no dia        | {{ $summary->count }}          |
| Total vendido        | {{ Format::moneyBR($summary->totalAmount) }} |
| Comissão ({{ $ratePct }}) | {{ Format::moneyBR($summary->totalCommission) }} |
@endcomponent

Obrigado,
**{{ config('app.name') }}**
@endcomponent
