@php
    use App\Support\Format;
    $ratePct = Format::percentBR($rate);
@endphp

@component('mail::message')
# Resumo diário — {{ Format::dateBR($summary->date) }}

**Total de vendas:** {{ $summary->totalCount }}
**Valor total:** {{ Format::moneyBR($summary->totalAmount) }}
**Comissão total ({{ $ratePct }}):** {{ Format::moneyBR($summary->totalCommission) }}

@if (!empty($sellerSummaries))
@component('mail::table')
| Vendedor | # Vendas | Total | Comissão |
|:--------|---------:|------:|---------:|
@foreach ($sellerSummaries as $s)
| {{ $s->sellerName }} ({{ $s->sellerEmail }}) | {{ $s->count }} | {{ Format::moneyBR($s->totalAmount) }} | {{ Format::moneyBR($s->totalCommission) }} |
@endforeach
@endcomponent
@endif

Abs,
**{{ config('app.name') }}**
@endcomponent
