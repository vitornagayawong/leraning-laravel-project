<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .sec {
            width: 100%;
        }

        .sec span {
            display: inline-block;
            
        }
        .span1 {
            margin-right: 320px;
        }
    </style>
</head>

<body>
    <section class="sec">
        <span class="span1"> Pedido realizado por: {{ $usuario }} </span>         
        <span> Data: {{$dataHoraAtualFormatada}} </span> 
    </section>
    <br><br>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Descrição</th>
                <th>Qtd</th>
                <th>Preço unit</th>
                <th>Subtotal</th>
                <th>Desconto</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($arrayDePedidos as $key => $value)
            <tr>
                <td>{{$value->produto_id}}</td>
                <td>{{ $arrayDescricaoProdutos[$key] }}</td>
                <td>{{ $value->quantidade_do_produto }}</td>
                <td>R$ {{ number_format($value->preco_unitario, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($value->valor_do_produto, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($value->desconto, 2, ',', '.')}}</td>
                <td>R$ {{ number_format($value->valor_total, 2, ',', '.')}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>

    <h2>Valor total da compra: R$ {{$valorTotalCompra}}</h2>

</body>

</html>