@extends('me::print')
@section('title', __('Print Daily Expenses'))
@section('contents')
    <table class="table-encodex table-bordered">
        <thead>
            <tr>
                <th class="text-center">@lang('Title')</th>
                <th class="text-center">@lang('Amount')</th>
                <th class="text-center">@lang('Date')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expense as $item)
                <tr>
                    <td class="text-center">{{ $item->title }}</td>
                    <td class="text-end">{{ toBanglaNumber($item->show_amount, 2) }}</td>
                    <td class="text-center">{{ formatDate($item->created_at) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-end">@lang('Total')</th>
                <th class="text-end">{{ toBanglaNumber($expense->sum('show_amount'), 2) }}</th>
                <th></th>
            </tr>
        </tfoot>

    </table>
@endsection
@php
    $backUrl = route('admin.events.expenses.index', request()->route('event'));
    $printTitle = __('Event Expenses') . ' - ' . $event->title;
    $printQr = '1111111111111111';
@endphp
