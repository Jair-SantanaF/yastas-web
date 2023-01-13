<div class="row contenedor_detalle">
    <?php
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 11 mar 2018
     *	Nota: Se instancia el model para obtener los datos de la funcion,
     *          ubicada en la rama de models.
     ***********************************************************************/
    $KCO =& get_instance();
    $KCO->load->model('demo_mdl');
    ?>
    <!--Funciona <?php echo _("bienvenido a mi app"); ?>-->
    <div class="col-md-12 col-12">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-12">
                <table class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modulos</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = $KCO->demo_mdl->fetchAll();
                    foreach ($result as $index => $value){
                        ?>
                        <tr id="<?php echo $value['id'] ?>" class="fila">
                            <td><?php echo $value['id'] ?></td>
                            <td><?php echo $value['nombre'] ?></td>
                            <td><?php echo $value['apellido'] ?></td>
                            <td><?php echo $value['email'] ?></td>
                            <td class="tr-botones">
                                <a class="btn btn-primary btn-xs editar" href="admitidos.php?editar=1&id=1"><i class="fas fa-pencil-alt"></i></a>
                                <a class="btn btn-danger btn-xs" onclick="eliminar(<?php echo $value['id'] ?>,'correos_admitidos');"><i class="fas fa-times text-white"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url() ?>/assets/js/usuarios.js"></script>