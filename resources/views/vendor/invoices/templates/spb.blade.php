<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            html {
                font-family: sans-serif;
                line-height: 1.15;
                margin: 0;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 10px;
                margin: 24pt;
            }

            h4 {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            p {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            strong {
                font-weight: bolder;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }

            table {
                border-collapse: collapse;
            }

            th {
                text-align: inherit;
            }

            h4, .h4 {
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }

            h4, .h4 {
                font-size: 11px;
            }

            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #212529;
            }

            .table th,
            .table td {
                padding: 2.5px;
                vertical-align: top;
            }

            .table.table-items td {
                border-top: 1.5px solid #dee2e6;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 1.5px solid #dee2e6;
            }

            .mt-5 {
                margin-top: 3rem !important;
            }

            .mb-5 {
                margin-bottom: 4rem !important;
            }

            .pr-0,
            .px-0 {
                padding-right: 0 !important;
            }

            .pl-0,
            .px-0 {
                padding-left: 0 !important;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-uppercase {
                text-transform: uppercase !important;
            }
            * {
                font-family: "DejaVu Sans";
            }
            body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
                line-height: 1;
            }
            .party-header {
                font-size: 12px;
                font-weight: 400;
            }
            .total-amount {
                font-size: 10px;
                font-weight: 700;
            }
            .border-0 {
                border: none !important;
            }
            .cool-gray {
                color: #6B7280;
            }
        </style>
    </head>

    <body>
        {{-- Header --}}
        @if($invoice->logo)
            <img src="{{ $invoice->getLogo() }}" alt="logo" height="30">
        @endif
        @if($invoice->seller->name)
            <h4 class="text-uppercase">
                <strong>{{ $invoice->seller->name }}</strong>
            </h4>
        @endif

        <table class="table">
            <tbody>
                <tr>
                    <td class="border-0 pl-0 text-center" width="100%">
                        <h4 class="text-uppercase">
                            <strong><u>{{ $invoice->name }}</u></strong>
                        </h4>
                        <p>{{ __('invoices::invoice.serial') }} <strong>{{ $invoice->getSerialNumber() }}</strong></p>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Seller - Buyer --}}
        <table class="table">
            <tbody>
                <tr>
                    <td class="px-0">
                        @foreach($invoice->seller->custom_fields as $key => $value)
                            <p class="seller-custom-field">
                                {{ ucfirst($key) }}: {{ $value }}
                            </p>
                        @endforeach
                    </td>
                    <td class="border-0" width="15%"></td>
                    <td class="px-0">
                        @foreach($invoice->buyer->custom_fields as $key => $value)
                            <p class="buyer-custom-field">
                                {{ ucfirst($key) }}: {{ $value }}
                            </p>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th width="4%" scope="col" class="border-0 pl-0">{{ __('invoices::invoice.serial') }}</th>
                    <th scope="col" class="border-0 pl-0">{{ __('invoices::invoice.register_number') }}</th>
                    <th width="30%" scope="col" class="border-0 pl-0">{{ __('invoices::invoice.name_product') }}</th>
                    <th scope="col" class="text-center border-0">{{ __('invoices::invoice.quantity') }}</th>
                    @if($invoice->hasItemUnits)
                        <th width="10%" scope="col" class="text-center border-0">{{ __('invoices::invoice.units') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                {{-- Items --}}
                @foreach($invoice->items as $key => $item)
                <tr>
                    <td class="pl-0">
                        {{++$key}}
                    </td>
                    <td class="pl-0">
                        {{ $item->register_number }}
                    </td>
                    <td class="pl-0">
                        {{ $item->title }}

                        @if($item->description)
                            <p class="cool-gray">{{ $item->description }}</p>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    @if($invoice->hasItemUnits)
                        <td class="text-center">{{ $item->units }}</td>
                    @endif
                </tr>
                @endforeach
                <tr class="table table-items">
                    <td colspan="{{ $invoice->table_columns }}" class="border-0"></td>
                </tr>
            </tbody>
        </table>

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT >= 0) {
                $text = "{{ $invoice->seller->user }} - {{date('Y-m-d H:i:s')}} - {{$invoice->getSerialNumber()}} - Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 7;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 1.3;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
