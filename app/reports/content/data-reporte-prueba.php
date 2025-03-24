<!---
codigos de los colores que se utilizan en el logo de fix
azul:#01122c;
verde:b1fc40;
blanco:f2f5f8;
todo en español para q no c note que es de chat
--->


<style>

  .cabezera{
    background-color:#01122c; 
    height:200px; 
    width:1000px;
    display:flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
  }

 .direccion{
  color:#fefefc;
  margin-left:20px ;
  font-size: 14px;
 }

 .numcotizacion{
  font-size: 18px;
  font-weight:bold ;
  margin-left: 420px;
  display: flex;
  
 }
 
 .blanco{
  color: #f2f5f8;
 }

 .verde{
  color: #b1fc40;
 }

 .azul{
  color: #01122c;
 }
 

 .img-logo{
  width:200px;
  margin-top:30px;
  
 }
</style>

<div class="cabezera">
<div class="numcotizacion blanco">
  <h1>Cotizacion N°001-000010</h1>
</div>
<img class="img-logo" src="<?php echo realpath(__DIR__ . '/../../../images/logofix360.png'); ?>" alt="" >

<div class="direccion">
  <p>Panamerica Sur Km 199 Puerta 201 - CHINCHA</p>
  <p>Ref: Por la bajada de la Molina</p>
</div>


</div>

