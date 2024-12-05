CREATE DEFINER=`root`@`localhost` PROCEDURE `SalvarHistoricoELimparDados`(
    IN p_idDaMesa INT,
    IN p_idDaEmpresa INT
)
BEGIN
    DECLARE v_comandaId INT;
    DECLARE v_historico JSON;
    DECLARE v_token VARCHAR(45);

    -- Iniciar Transação
    START TRANSACTION;

    -- Obter ID da Comanda vinculada à Mesa
    SELECT ID INTO v_comandaId
    FROM COMANDA
    WHERE MESA_ID = p_idDaMesa;

    -- Construir JSON para o histórico (simplificado para evitar problemas)
    SET v_historico = JSON_OBJECT(
        'Comanda', (SELECT JSON_OBJECT('ID', ID, 'MesaID', MESA_ID) 
                    FROM COMANDA 
                    WHERE ID = v_comandaId),
        'Pedidos', (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'PedidoID', ID, 
                        'Data', DATA, 
                        'Itens', (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                                      'ItemID', i.ID, 
                                      'Nome', c.NOME, 
                                      'Preco', c.PRECO))
                                  FROM ITENS i 
                                  JOIN CARDAPIO c ON i.CARDAPIO_ID = c.ID 
                                  WHERE i.PEDIDO_ID = p.ID)))
                    FROM PEDIDO p 
                    WHERE p.COMANDA_ID = v_comandaId),
        'DataGeracao', NOW()
    );

    -- Salvar histórico
    INSERT INTO HISTORICO (EMPRESA_ID, DATA, HISTORICO)
    VALUES (p_idDaEmpresa, CURDATE(), v_historico);

    -- Atualizar token da mesa
    SET v_token = HEX(RANDOM_BYTES(20));
    UPDATE MESA SET TOKEN = v_token WHERE ID = p_idDaMesa;

    -- Deletar itens vinculados aos pedidos
    DELETE FROM ITENS WHERE PEDIDO_ID IN (SELECT ID FROM PEDIDO WHERE COMANDA_ID = v_comandaId);

    -- Deletar pedidos vinculados à comanda
    DELETE FROM PEDIDO WHERE COMANDA_ID = v_comandaId;

    -- Deletar a comanda vinculada à mesa
    DELETE FROM COMANDA WHERE ID = v_comandaId;

    -- Commitar as mudanças
    COMMIT;

END