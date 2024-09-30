<!DOCTYPE html>
<html>
<head>
  <title>PRODUCT LOG : {{ $client->name }}</title>
</head>
<style>
  td{
    text-align: center;
  }
</style>
<body>
<h1 style="text-align: center;">PRODUCT LOG : {{ $client->name }}</h1>


<table border="1" width="100%">
  <tr>

    <th>Date</th>
    <th>Produit</th>
    <th>Quantité</th>

  </tr>
  @php $rest = 0; @endphp
  @foreach($invoices as $entry)
    <tr>
      <td>{{ $entry->sale_date }}</td>

      <td> {{$entry->product->name}}</td>
      <td> {{$entry->quantity}}</td>


    </tr>
  @endforeach
</table>
</body>
</html>
