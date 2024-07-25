<!DOCTYPE html>
<html>
<head>
  <title>Client Log</title>
</head>
<body>
<h1>ETAT DU CLIENT</h1>


{{--<table border="1" width="100%">--}}
{{--  <tr>--}}
{{--    <th>Date</th>--}}
{{--    <th>Amount</th>--}}
{{--    <th>Payment</th>--}}
{{--    <th>REST</th>--}}
{{--  </tr>--}}
{{--  @php $rest = 0; @endphp--}}
{{--  @foreach($logEntries as $entry)--}}
{{--    <tr>--}}
{{--      <td>{{ $entry->date }}</td>--}}
{{--      @if($entry->type == 'Invoice')--}}
{{--        <td>{{$entry->total_amount }}</td>--}}
{{--        @php $rest += $entry->total_amount @endphp--}}
{{--      @else--}}
{{--          <td> </td>--}}
{{--      @endif--}}

{{--      @if($entry->type == 'Payment')--}}
{{--        <td>{{  $entry->amount_paid  }}</td>--}}
{{--        @php $rest -= $entry->amount_paid @endphp--}}

{{--      @else--}}
{{--        <td> </td>--}}
{{--      @endif--}}

{{--      <td>{{ number_format(floatval($rest), 2, '.', '') }}</td>--}}
{{--    </tr>--}}
{{--  @endforeach--}}
{{--</table>--}}
</body>
</html>
