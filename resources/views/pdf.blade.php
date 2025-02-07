<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style > <!--Style tem que ficar dentro do head
        .tablerow {
            margin-right: 200px;
        }
    </style>
</head>
<body>
        <section>
            <span class="tablerow">Pedido realizado por:</span> {{ $usuario }}
            <br>
            Data: {{$dataHoraAtual}}
        </section>
        <br><br>
        <table >
            <thead>
                <tr >
                    <th>Id Produto</th>
                    <th >Qtd</th>
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
                        <td>{{$value->quantidade_do_produto}}</td>
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
