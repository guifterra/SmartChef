<?php $numero = 1; ?>

<style>
  main {
    display: flex;
    flex-direction: row;
    <?php  if ($numero <= 5) { echo 'justify-content: space-around;'; } ?>
    /* justify-content: space-around; */
    align-items: center;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
  }

  li{
    display:flex;
    flex-direction: row;
    width: 100%;
    align-items: center;
    justify-content: space-between;
  }
  li p{
    margin: 0;
  }
  .xxx {
    min-width: 300px;
    max-width: 50%;
    width: calc((100% - (25px * 2 * <?php  if ($numero > 5) { echo '5'; } else { echo $numero; } ?>)) / <?php  if ($numero > 5) { echo '5'; } else { echo $numero; } ?>);
    <?php  if ($numero > 5) { echo 'margin: 25px'; } ?>
  }
</style>

<div class="xxx">
  <div class="card produto" style="">
    <div class="card-body">
      <h5 class="card-title" style="font-weight: bold;">Mesa 1</h5>
      <h5 style="font-size: 20px;">Pedidos</h5>
      <ul>
        <li><p><i class='bx bx-right-arrow-alt'></i>Parmegiana</p><p>R$ 22,99</p> </li>
        <li><i class='bx bx-right-arrow-alt'></i> Picanha R$ 770,00</li>
        <li><i class='bx bx-right-arrow-alt'></i> Pepino R$ 7,01</li>
      </ul>
      <div class='d-flex justify-content-around'>
        <h5 style="font-size: 30px;">R$ 800,00</h5>
      </div>
      <div class='d-flex justify-content-between'>
        <a href="#" class="btn botao-entrega">Finalizar comanda</a>
        <a href="#" class="btn botao-cancelar">Cancelar</a>
      </div>
    </div>
  </div>
</div>

<script></script>