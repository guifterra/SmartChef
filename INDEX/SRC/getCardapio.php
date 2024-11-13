<?php

if (isset($_GET['SCempresaId']) && isset($_GET['SCtokenMesa'])) {

    if ($_GET['SCempresaId'] != '' && $_GET['SCtokenMesa'] != '') {

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
            
            $empresaId = $_GET['SCempresaId'];
            $tokenDaMesa = $_GET['SCtokenMesa'];

            $checkTokenMesa = $con->prepare("SELECT ID FROM MESA WHERE TOKEN = ?");
            $checkTokenMesa->bind_param("s", $tokenDaMesa);
            $checkTokenMesa->execute();
            $count = $checkTokenMesa->get_result()->num_rows;
            if ($count != 1) {
                die(header("HTTP/1.0 O link para a Mesa está invalido! Peça o QRCode ao Garçom para acessar a mesa"));
            }

            $stmt = $con->prepare("
            SELECT 
                ID AS Id_Prato,
                NOME AS Nome_Prato,
                DESCRICAO AS Descricao_Prato,
                INGREDIENTES AS Ingredientes,
                ADICIONAIS AS Adicionais,
                PRECO AS Preco_Prato,
                IMG AS Imagem_Prato,
                CATEGORIA AS Nome_Categoria
            FROM 
                PI22024.CARDAPIO
            WHERE 
                EMPRESA_ID = ?
            ORDER BY 
                CATEGORIA, NOME;
            ");

            $stmt->bind_param("i", $empresaId);
            $stmt->execute();
            $stmt = $stmt->get_result();

            $cardapioAgrupado = [];

            while ($prato = $stmt->fetch_assoc()) {
                $categoria = $prato['Nome_Categoria'];
                if (!isset($cardapioAgrupado[$categoria])) {
                    $cardapioAgrupado[$categoria] = [];
                }
                $cardapioAgrupado[$categoria][] = $prato;
            }

            foreach ($cardapioAgrupado as $categoria => $pratos) {

                echo "
                    <div class='categoria'>
                        <h2>$categoria</h2>
                        <hr>
                    </div>

                    <div class='row'>
                ";

                foreach ($pratos as $prato) {
                    $ingredientesArray = json_decode($prato['Ingredientes'], true);
                    $adicionaisArray = json_decode($prato['Adicionais'], true);

                    $ingredientesNomes = array_column($ingredientesArray, 'nome');
                    $ingredientesFormatados = implode(", ", $ingredientesNomes);

                    $precoFormatado = number_format($prato['Preco_Prato'], 2, ',', '.');

                    echo "
                        <div class='item col-12 col-md-12 col-lg-6 d-flex flex-column align-items-center'>
                            <div class='row'>
                                <div class='col-4 container-img-cardapio'>
                                    <img src='./FILES/CARDAPIO/{$prato['Imagem_Prato']}' class='img-fluid' alt='...'>
                                </div>
                                <div class='col-8'>
                                    <div class='card-body text-center ps-4'>
                                        <h5 class='nome-item'>{$prato['Nome_Prato']}</h5>
                                        <p class='card-text'><strong>Descrição: </strong>{$prato['Descricao_Prato']}</p>
                                        <p class='card-text'><strong>Ingredientes: </strong>{$ingredientesFormatados}</p>
                                        <h5 class='preco'>R$ {$precoFormatado}</h5>
                                        <div class='d-flex justify-content-end pe-4'>
                                            <a href='#' class='btn botao' data-bs-toggle='modal' data-bs-target='#addToCartModal{$prato['Id_Prato']}'>Adicionar ao carrinho</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";

                    echo "
                        <div class='modal fade' id='addToCartModal{$prato['Id_Prato']}' tabindex='-1' aria-labelledby='addToCartModalLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='addToCartModalLabel'>Adicionar ao Carrinho</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <form>
                                            <div class='mb-3'>
                                                <label for='quantity' class='form-label'>Quantidade</label>
                                                <input type='number' class='form-control' id='quantity' min='1' value='1'>
                                            </div>
                                            <div class='mb-3'>
                                                <label for='observation' class='form-label'>Observação</label>
                                                <textarea class='form-control' id='observation' rows='3' placeholder='Insira suas observações/mudanças aqui'></textarea>
                                            </div>
                    ";

                    // Exibir Alergênicos, caso existam
                    $alergenicosExistem = array_filter($ingredientesArray, fn($item) => !empty($item['alergicos']));
                    if (!empty($alergenicosExistem)) {
                        echo "
                            <div class='mb-3'>
                                <label class='form-label'>Alergênicos</label><br>
                        ";
                        foreach ($ingredientesArray as $ingrediente) {
                            if (!empty($ingrediente['alergicos'])) {
                                foreach ($ingrediente['alergicos'] as $alergico) {
                                    echo "
                                        <div>
                                            <input type='checkbox' name='alergenicos[]' value='{$alergico}'>
                                            <label>{$alergico}</label>
                                        </div>
                                    ";
                                }
                            }
                        }
                        echo "</div>";
                    }

                    // Exibir Opções de Adicional, caso existam
                    if (!empty($adicionaisArray)) {
                        echo "
                            <div class='mb-3'>
                                <label class='form-label'>Opções de Adicional</label><br>
                        ";
                        foreach ($adicionaisArray as $adicional) {
                            $precoAdicionalFormatado = number_format($adicional['preco'], 2, ',', '.');
                            echo "
                                <div>
                                    <input type='checkbox' name='adicionais[]' value='{$adicional['nome']}'>
                                    <label>{$adicional['nome']} (R$ {$precoAdicionalFormatado})</label>
                                </div>
                            ";
                        }
                        echo "</div>";
                    }

                    echo "
                                        </form>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
                                        <button type='button' class='btn btn-primary'>Adicionar ao Carrinho</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";
                }

                echo "</div>";
            }

        } else {
            // Retorna 404 se as condições não forem atendidas
            http_response_code(404);
        }

    }else{
        http_response_code(404);
    }
    
}else{
    http_response_code(404);
}
?>
