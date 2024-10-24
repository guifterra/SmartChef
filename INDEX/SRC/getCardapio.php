<?php
// ===================================================
// Não enxergo necessidade de passar o numero da mesa
// ===================================================
// $numeroMesa  = $_GET['SCnumeroMesa'];

//  Exemplo de Link: http://localhost/PI-Dev-Version/INDEX/SRC/getCardapio.php?SCempresaId=1&SCnumeroMesa=1&SCtokenMesa=TOKEN_MESA_1

if( isset( $_GET['SCempresaId'] ) && isset( $_GET['SCtokenMesa'] ) ){
    if( $_GET['SCempresaId'] != '' && $_GET['SCtokenMesa'] != '' ){

        include('conexao.php');

        $empresaId   = $_GET['SCempresaId'];
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

        // Agrupando os pratos por categoria
        while ($prato = $stmt->fetch_assoc()) {
            $categoria = $prato['Nome_Categoria'];
            if (!isset($cardapioAgrupado[$categoria])) {
                $cardapioAgrupado[$categoria] = [];
            }
            $cardapioAgrupado[$categoria][] = $prato;
        }

        // IMPRESSAO REFINADA ==============================================
        foreach ($cardapioAgrupado as $categoria => $pratos) {
            
            echo "
                <div class='categoria'>
                    <h2>$categoria</h2>
                    <hr>
                </div>
            ";

            foreach ($pratos as $prato) {
                // Transformar JSON dos ingredientes e adicionais em arrays
                $ingredientesArray = json_decode($prato['Ingredientes'], true);
                $adicionaisArray = json_decode($prato['Adicionais'], true);

                // Extrair apenas os nomes dos ingredientes
                $ingredientesNomes = array_column($ingredientesArray, 'nome');
                $ingredientesFormatados = implode(", ", $ingredientesNomes);

                echo "
                    <div class='row justify-content-center'>
                    <div class='item col-12 col-md-12 col-lg-6 d-flex flex-column align-items-center'>
                        <div class='row'>
                            <div class='col-4'>
                                <img src='https://picsum.photos/400/400' class='card-img-top img-fluid' alt='...'>
                            </div>
                            <div class='col-8'>
                                
                                <div class='card-body text-center'>
                                    <h5 class='nome-item'>{$prato['Nome_Prato']}</h5>
                                    <p class='card-text'><strong>Descrição: </strong>{$prato['Descricao_Prato']}</p>
                                    <p class='card-text'><strong>Ingredientes: </strong>{$ingredientesFormatados}</p>
                                    <h5 class='preco'>R$ {$prato['Preco_Prato']}</h5>
                                    <div class='d-flex justify-content-end pe-4'>
                                        <a href='#' class='btn botao' data-bs-toggle='modal' data-bs-target='#addToCartModal{$prato['Id_Prato']}'>Adicionar ao carrinho</a>
                                    </div>
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
                                        <div class='mb-3'>
                                            <label class='form-label'>Opções de Adicional</label><br>
                    ";

                    foreach ($adicionaisArray as $adicional) {
                        echo "
                            <div>
                                <input type='checkbox' name='adicionais[]' value='{$adicional['nome']}'>
                                <label for='option1'>{$adicional['nome']} (R$ {$adicional['preco']})</label>
                            </div>
                        ";
                    }

                    echo "              </div>
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
        }
        // =================================================================

        // Imprimindo os pratos agrupados por categoria
        // foreach ($cardapioAgrupado as $categoria => $pratos) {
            
        //     echo "<h2>$categoria</h2>"; // Título da categoria
        //     echo "<div class='categoria'>";

        //     foreach ($pratos as $prato) {
        //         // Transformar JSON dos ingredientes e adicionais em arrays
        //         $ingredientesArray = json_decode($prato['Ingredientes'], true);
        //         $adicionaisArray = json_decode($prato['Adicionais'], true);

        //         // Extrair apenas os nomes dos ingredientes
        //         $ingredientesNomes = array_column($ingredientesArray, 'nome');
        //         $ingredientesFormatados = implode(", ", $ingredientesNomes);

        //         echo "
        //             <div class='prato'>
        //                 <h3>{$prato['Nome_Prato']}</h3>
        //                 <p><strong>Descrição:</strong> {$prato['Descricao_Prato']}</p>
        //                 <p><strong>Ingredientes:</strong> $ingredientesFormatados</p>
        //                 <p><strong>Adicionais:</strong></p>
        //                 <div class='adicionais'>
        //         ";

        //         // Exibindo os adicionais como checkboxes
        //         foreach ($adicionaisArray as $adicional) {
        //             echo "
        //                 <label>
        //                     <input type='checkbox' name='adicionais[]' value='{$adicional['nome']}'>
        //                     {$adicional['nome']}
        //                 </label><br>
        //             ";
        //         }

        //         echo "
        //                 </div>
        //                 <p><strong>Preço:</strong> R$ {$prato['Preco_Prato']}</p>
        //                 <hr>
        //             </div>
        //         ";
        //     }

        //     echo "</div>"; // Fecha o div da categoria
        // }
    }
}
?>
