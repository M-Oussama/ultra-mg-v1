<!DOCTYPE html>
<html>
<head>
  <title>ETAT DU CLIENT : {{ $client->name }}</title>
</head>
<style>
  td{
    text-align: center;
  }
</style>
<body>
<h1 style="text-align: center;">ETAT DU CLIENT : {{ $client->name }}</h1>


<table border="1" width="100%">
  <tr>
    <th>Date</th>
    <th>Montant Achat</th>
    <th>Paiement</th>
    <th>Retour</th>
    <th>REST</th>
  </tr>
  @php $rest = 0; @endphp
  @foreach($logEntries as $entry)
    <tr>
      <td>{{ $entry->date }}</td>
      @if($entry->type == 'Invoice')
        <td>{{ number_format(floatval($entry->total_amount), 2, '.', '') }}  <small>Da</small></td>
        @php $rest += $entry->total_amount @endphp
      @else
          <td> </td>
      @endif

      @if($entry->type == 'Payment')
        <td>{{ number_format(floatval($entry->amount_paid), 2, '.', '') }} <small>Da</small></td>
        @php $rest -= $entry->amount_paid @endphp

      @else
        <td> </td>
      @endif

      @if($entry->type == 'Return')
        <td>{{ number_format(floatval($entry->total_amount), 2, '.', '') }}  <small>Da</small></td>
        @php $rest -= $entry->total_amount @endphp

      @else
        <td> </td>
      @endif

      <td>{{ number_format(floatval($rest), 2, '.', '') }} <small>Da</small></td>
    </tr>
  @endforeach
</table>
</body>
</html>
