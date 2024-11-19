<?php

    include('conexao.php'); // Inclui a conexão com o banco de dados
    
    // Dados recebidos via GET
    $empresaId = $_GET['SCempresaId'];
    $tokenMesa = $_GET['SCtokenMesa'];
    $numeroMesa = $_GET['SCnumeroMesa'];
    
    // Consulta SQL para verificar se a mesa existe e o token está correto
    $stmt = $con->prepare("
        SELECT 1 
        FROM MESA
        WHERE EMPRESA_ID = ? AND NUMERO = ? AND TOKEN = ?
    ");
    $stmt->bind_param("iis", $empresaId, $numeroMesa, $tokenMesa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verifica se a consulta retornou resultados
    if ($result->num_rows > 0) {
       
        session_start();

        if (!isset($_SESSION['SCitems']) || empty($_SESSION['SCitems'])) {

            echo "<p>Carrinho vazio</p>";

        } else {
            
            foreach ($_SESSION['SCitems'] as $item){

                $precoTotal = $item['preco'];

                echo "
                <div class='col-8'>
                    <p class='nome-item'>{$item['nomePrato']}</p>
                    <p><span style='font-weight: bold;'>Descrição: </span>{$item['observacao']}</p>
                ";

                if (!empty($item['alergicos'])) {
                    echo "<p><strong>Alergicos:</strong> " . implode(", ", $item['alergicos']) . "</p>";
                } 

                if (!empty($item['adicionais'])) {
                    
                    $adicionaisTexto = [];
                
                    foreach ($item['adicionais'] as $adicional) {
                        // Separar a string em nome e preço
                        list($nome, $preco) = explode("&", $adicional);
                        $adicionaisTexto[] = $nome; // Adicionar o nome à lista
                        $precoTotal += (float)$preco; // Somar o preço ao total
                    }
                
                    echo "<p><strong>Adicionais:</strong> " . implode(", ", $adicionaisTexto) . "</p>";
                }

                $precoAPagar = $precoTotal * $item['quantidade'];

                echo "
                </div>
                <div class='col-4 text-end'>
                    <p class='quantidade'><span style='font-weight: bold;'>Quantidade: </span>{$item['quantidade']}</p>
                    <p class='preco'>R$ " . number_format($precoAPagar, 2, ',', '.') . "</p>
                    <div>
                        <a class='adicionar'><i class='bx bx-plus-medical'></i></a>
                        <a class='subtrair'><i class='bx bx-minus'></i></a>
                        <a class='remover'><i class='bx bx-trash'></i></a>
                    </div>
                </div>
                <hr>
                ";
            }
        }

    } else {
        // Retorna 404 se as condições não forem atendidas
        http_response_code(404);
    }
    
?>